<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Position;
use App\Models\Shift;
use App\Services\EmployeeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EmployeeController extends Controller
{
    protected EmployeeService $employeeService;

    public function __construct(EmployeeService $employeeService)
    {
        $this->employeeService = $employeeService;
    }

    public function index(Request $request)
    {
        $query = Employee::with(['department', 'position', 'user']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('status')) {
            $query->where('employment_status', $request->status);
        }

        if ($request->filled('position_id')) {
            $query->where('position_id', $request->position_id);
        }

        $employees = $query->latest()->paginate(15);
        $departments = Department::pluck('name', 'id');
        $positions = Position::pluck('name', 'id');

        return view('employees.index', compact('employees', 'departments', 'positions'));
    }

    public function create()
    {
        $departments = Department::pluck('name', 'id');
        $positions = Position::pluck('name', 'id');
        $shifts = Shift::pluck('name', 'id');
        $managers = Employee::whereNotNull('manager_id')->orWhere('employment_status', 'permanent')->pluck('full_name', 'id');

        return view('employees.create', compact('departments', 'positions', 'shifts', 'managers'));
    }

    public function store(StoreEmployeeRequest $request)
    {
        try {
            $data = $request->validated();

            if ($request->hasFile('photo')) {
                $data['photo'] = $request->file('photo')->store('employees/photos', 'public');
            }

            $employee = $this->employeeService->create($data);

            return redirect()->route('employees.index')
                ->with('toast_success', 'Employee created successfully.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('toast_error', 'Failed to create employee: ' . $e->getMessage());
        }
    }

    public function show(Employee $employee)
    {
        $employee->load(['department', 'position', 'shift', 'manager', 'user']);

        $attendanceSummary = $this->employeeService->getAttendanceSummary($employee);
        $leaveHistory = $this->employeeService->getLeaveHistory($employee);

        return view('employees.show', compact('employee', 'attendanceSummary', 'leaveHistory'));
    }

    public function edit(Employee $employee)
    {
        $departments = Department::pluck('name', 'id');
        $positions = Position::pluck('name', 'id');
        $shifts = Shift::pluck('name', 'id');
        $managers = Employee::where('id', '!=', $employee->id)
            ->where(function ($q) {
                $q->whereNotNull('manager_id')->orWhere('employment_status', 'permanent');
            })
            ->pluck('full_name', 'id');

        return view('employees.edit', compact('employee', 'departments', 'positions', 'shifts', 'managers'));
    }

    public function update(UpdateEmployeeRequest $request, Employee $employee)
    {
        try {
            $data = $request->validated();

            if ($request->hasFile('photo')) {
                if ($employee->photo) {
                    Storage::disk('public')->delete($employee->photo);
                }
                $data['photo'] = $request->file('photo')->store('employees/photos', 'public');
            }

            $this->employeeService->update($employee, $data);

            return redirect()->route('employees.index')
                ->with('toast_success', 'Employee updated successfully.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('toast_error', 'Failed to update employee: ' . $e->getMessage());
        }
    }

    public function destroy(Employee $employee)
    {
        try {
            $this->employeeService->delete($employee);

            return redirect()->route('employees.index')
                ->with('toast_success', 'Employee soft-deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('toast_error', 'Failed to delete employee: ' . $e->getMessage());
        }
    }

    public function restore($id)
    {
        try {
            $employee = Employee::withTrashed()->findOrFail($id);
            $employee->restore();

            return redirect()->route('employees.index')
                ->with('toast_success', 'Employee restored successfully.');
        } catch (\Exception $e) {
            return back()->with('toast_error', 'Failed to restore employee: ' . $e->getMessage());
        }
    }

    public function forceDelete($id)
    {
        try {
            $employee = Employee::withTrashed()->findOrFail($id);

            if ($employee->photo) {
                Storage::disk('public')->delete($employee->photo);
            }

            $employee->forceDelete();

            return redirect()->route('employees.index')
                ->with('toast_success', 'Employee permanently deleted.');
        } catch (\Exception $e) {
            return back()->with('toast_error', 'Failed to permanently delete employee: ' . $e->getMessage());
        }
    }

    public function export()
    {
        try {
            return $this->employeeService->exportExcel();
        } catch (\Exception $e) {
            return back()->with('toast_error', 'Failed to export: ' . $e->getMessage());
        }
    }

    public function import(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:xlsx,xls,csv']);

        try {
            $this->employeeService->importExcel($request->file('file'));

            return redirect()->route('employees.index')
                ->with('toast_success', 'Employees imported successfully.');
        } catch (\Exception $e) {
            return back()->with('toast_error', 'Failed to import: ' . $e->getMessage());
        }
    }
}
