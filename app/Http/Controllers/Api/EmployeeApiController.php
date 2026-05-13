<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Services\EmployeeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmployeeApiController extends Controller
{
    protected EmployeeService $employeeService;

    public function __construct(EmployeeService $employeeService)
    {
        $this->employeeService = $employeeService;
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $query = Employee::with(['user', 'department', 'position']);

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('full_name', 'like', "%{$search}%")
                      ->orWhere('nik', 'like', "%{$search}%");
                });
            }

            if ($request->filled('department_id')) {
                $query->where('department_id', $request->department_id);
            }

            $employees = $query->paginate($request->per_page ?? 15);

            return response()->json([
                'success' => true,
                'data' => $employees,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get employees: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $employee = Employee::with(['user', 'department', 'position', 'shift'])
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $employee,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Employee not found.',
            ], 404);
        }
    }

    public function update(Request $request, $id): JsonResponse
    {
        $request->validate([
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'photo' => 'nullable|string',
        ]);

        try {
            $employee = Employee::findOrFail($id);

            $employeeId = auth()->user()->employee?->id;
            if ($employeeId && $employee->id !== $employeeId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to update this profile.',
                ], 403);
            }

            $data = $request->only(['phone', 'address']);

            if ($request->has('photo') && str_starts_with($request->photo, 'data:image')) {
                $imageData = base64_decode(explode(',', $request->photo)[1] ?? $request->photo);
                $filename = 'employees/photos/' . uniqid() . '_' . time() . '.jpg';
                \Storage::disk('public')->put($filename, $imageData);
                $data['photo'] = $filename;
            }

            $employee->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully.',
                'data' => $employee->fresh()->load(['user', 'department', 'position']),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Update failed: ' . $e->getMessage(),
            ], 500);
        }
    }
}
