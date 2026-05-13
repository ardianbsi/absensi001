<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use App\Services\AttendanceService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AttendanceApiController extends Controller
{
    protected AttendanceService $attendanceService;

    public function __construct(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $query = Attendance::with(['employee.user', 'employee.department'])
                ->where('employee_id', auth()->user()->employee?->id);

            if ($request->filled('date_from')) {
                $query->whereDate('date', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->whereDate('date', '<=', $request->date_to);
            }

            if ($request->filled('month')) {
                $month = $request->month;
                $year = $request->year ?? now()->year;
                $query->whereMonth('date', $month)
                      ->whereYear('date', $year);
            }

            $attendances = $query->orderBy('date', 'desc')->paginate($request->per_page ?? 15);

            return response()->json([
                'success' => true,
                'data' => $attendances,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get attendances: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function checkIn(Request $request): JsonResponse
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'selfie' => 'required|string',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            $employee = auth()->user()->employee;

            if (!$employee) {
                return response()->json([
                    'success' => false,
                    'message' => 'No employee record found.',
                ], 404);
            }

            $existing = Attendance::where('employee_id', $employee->id)
                ->where('date', today()->toDateString())
                ->whereNotNull('check_in')
                ->first();

            if ($existing) {
                return response()->json([
                    'success' => false,
                    'message' => 'Already checked in today.',
                ], 409);
            }

            $data = $request->only(['latitude', 'longitude', 'notes']);

            if ($request->has('selfie') && str_starts_with($request->selfie, 'data:image')) {
                $imageData = base64_decode(explode(',', $request->selfie)[1] ?? $request->selfie);
                $filename = 'attendances/checkin/' . uniqid() . '_' . time() . '.jpg';
                \Storage::disk('public')->put($filename, $imageData);
                $data['selfie'] = $filename;
            }

            $attendance = $this->attendanceService->checkIn($employee, $data);

            return response()->json([
                'success' => true,
                'message' => 'Check-in successful.',
                'data' => $attendance->fresh()->load('employee.user'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Check-in failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function checkOut(Request $request): JsonResponse
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'selfie' => 'required|string',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            $employee = auth()->user()->employee;

            if (!$employee) {
                return response()->json([
                    'success' => false,
                    'message' => 'No employee record found.',
                ], 404);
            }

            $attendance = Attendance::where('employee_id', $employee->id)
                ->where('date', today()->toDateString())
                ->whereNotNull('check_in')
                ->whereNull('check_out')
                ->first();

            if (!$attendance) {
                return response()->json([
                    'success' => false,
                    'message' => 'No active check-in found for today.',
                ], 404);
            }

            $data = $request->only(['latitude', 'longitude', 'notes']);

            if ($request->has('selfie') && str_starts_with($request->selfie, 'data:image')) {
                $imageData = base64_decode(explode(',', $request->selfie)[1] ?? $request->selfie);
                $filename = 'attendances/checkout/' . uniqid() . '_' . time() . '.jpg';
                \Storage::disk('public')->put($filename, $imageData);
                $data['selfie'] = $filename;
            }

            $this->attendanceService->checkOut($attendance, $data);

            return response()->json([
                'success' => true,
                'message' => 'Check-out successful.',
                'data' => $attendance->fresh()->load('employee.user'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Check-out failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function today(): JsonResponse
    {
        try {
            $employee = auth()->user()->employee;

            if (!$employee) {
                return response()->json([
                    'success' => false,
                    'message' => 'No employee record found.',
                ], 404);
            }

            $attendance = Attendance::with(['employee.user', 'employee.department'])
                ->where('employee_id', $employee->id)
                ->where('date', today()->toDateString())
                ->first();

            return response()->json([
                'success' => true,
                'data' => $attendance,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get today attendance: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function history(Request $request): JsonResponse
    {
        try {
            $employee = auth()->user()->employee;

            if (!$employee) {
                return response()->json([
                    'success' => false,
                    'message' => 'No employee record found.',
                ], 404);
            }

            $attendances = Attendance::with(['employee.user', 'employee.department'])
                ->where('employee_id', $employee->id)
                ->orderBy('date', 'desc')
                ->paginate($request->per_page ?? 20);

            return response()->json([
                'success' => true,
                'data' => $attendances,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get history: ' . $e->getMessage(),
            ], 500);
        }
    }
}
