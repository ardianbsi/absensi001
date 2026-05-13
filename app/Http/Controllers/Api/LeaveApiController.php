<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Services\LeaveService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LeaveApiController extends Controller
{
    protected LeaveService $leaveService;

    public function __construct(LeaveService $leaveService)
    {
        $this->leaveService = $leaveService;
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $query = LeaveRequest::with(['leaveType', 'employee.user']);

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            $employee = auth()->user()->employee;
            if ($employee) {
                $query->where('employee_id', $employee->id);
            }

            $leaveRequests = $query->latest()->paginate($request->per_page ?? 15);

            return response()->json([
                'success' => true,
                'data' => $leaveRequests,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get leaves: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string',
            'attachment' => 'nullable|string',
        ]);

        try {
            $employee = auth()->user()->employee;

            if (!$employee) {
                return response()->json([
                    'success' => false,
                    'message' => 'No employee record found.',
                ], 404);
            }

            $data['employee_id'] = $employee->id;

            if ($request->has('attachment') && str_starts_with($request->attachment, 'data:')) {
                $mimeMap = [
                    'data:application/pdf' => 'pdf',
                    'data:image/jpeg' => 'jpg',
                    'data:image/png' => 'png',
                ];

                $mime = substr($request->attachment, 0, strpos($request->attachment, ';'));
                $ext = $mimeMap[$mime] ?? 'bin';

                $fileData = base64_decode(explode(',', $request->attachment)[1] ?? $request->attachment);
                $filename = 'leaves/attachments/' . uniqid() . '_' . time() . '.' . $ext;
                \Storage::disk('public')->put($filename, $fileData);
                $data['attachment'] = $filename;
            }

            $leaveRequest = $this->leaveService->submit($data);

            return response()->json([
                'success' => true,
                'message' => 'Leave request submitted successfully.',
                'data' => $leaveRequest->load('leaveType'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit leave: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $leaveRequest = LeaveRequest::with(['leaveType', 'employee.user', 'approver'])
                ->findOrFail($id);

            $employee = auth()->user()->employee;
            if ($employee && $leaveRequest->employee_id !== $employee->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to view this leave request.',
                ], 403);
            }

            return response()->json([
                'success' => true,
                'data' => $leaveRequest,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get leave: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function approve($id): JsonResponse
    {
        try {
            $this->leaveService->approve($id, auth()->id());

            return response()->json([
                'success' => true,
                'message' => 'Leave request approved.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Approval failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function reject(Request $request, $id): JsonResponse
    {
        $request->validate(['rejection_reason' => 'required|string']);

        try {
            $this->leaveService->reject($id, auth()->id(), $request->rejection_reason);

            return response()->json([
                'success' => true,
                'message' => 'Leave request rejected.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Rejection failed: ' . $e->getMessage(),
            ], 500);
        }
    }
}
