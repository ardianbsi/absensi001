<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckInRequest;
use App\Http\Requests\CheckOutRequest;
use App\Models\Attendance;
use App\Models\Department;
use App\Models\Employee;
use App\Services\AttendanceService;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    protected AttendanceService $attendanceService;

    public function __construct(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    public function index(Request $request)
    {
        $query = Attendance::with(['employee.user', 'employee.department']);

        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        }

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('department_id')) {
            $query->whereHas('employee', function ($q) use ($request) {
                $q->where('department_id', $request->department_id);
            });
        }

        $attendances = $query->latest()->paginate(15);
        $departments = Department::pluck('name', 'id');
        $employees = Employee::pluck('full_name', 'id');

        return view('attendances.index', compact('attendances', 'departments', 'employees'));
    }

    public function scan()
    {
        $activeAttendance = Attendance::where('employee_id', auth()->user()->employee?->id)
            ->whereNull('check_out')
            ->where('date', today()->toDateString())
            ->first();

        return view('attendances.scan', compact('activeAttendance'));
    }

    public function checkIn(CheckInRequest $request)
    {
        try {
            $employee = auth()->user()->employee;

            if (!$employee) {
                return back()->with('toast_error', 'No employee record found.');
            }

            $existing = Attendance::where('employee_id', $employee->id)
                ->where('date', today()->toDateString())
                ->whereNotNull('check_in')
                ->first();

            if ($existing) {
                return back()->with('toast_error', 'You have already checked in today.');
            }

            $data = $request->validated();
            $data['selfie'] = $request->file('selfie')->store('attendances/checkin', 'public');

            $this->attendanceService->checkIn($employee, $data);

            return redirect()->route('attendances.scan')
                ->with('toast_success', 'Check-in successful.');
        } catch (\Exception $e) {
            return back()->with('toast_error', 'Check-in failed: ' . $e->getMessage());
        }
    }

    public function checkOut(CheckOutRequest $request, Attendance $attendance)
    {
        try {
            if ($attendance->employee_id !== auth()->user()->employee?->id) {
                return back()->with('toast_error', 'Unauthorized action.');
            }

            if ($attendance->check_out) {
                return back()->with('toast_error', 'You have already checked out.');
            }

            $data = $request->validated();
            $data['selfie'] = $request->file('selfie')->store('attendances/checkout', 'public');

            $this->attendanceService->checkOut($attendance, $data);

            return redirect()->route('attendances.scan')
                ->with('toast_success', 'Check-out successful.');
        } catch (\Exception $e) {
            return back()->with('toast_error', 'Check-out failed: ' . $e->getMessage());
        }
    }

    public function show(Attendance $attendance)
    {
        $attendance->load(['employee.user', 'employee.department', 'employee.position']);
        return view('attendances.show', compact('attendance'));
    }

    public function export(Request $request)
    {
        try {
            return $this->attendanceService->exportReport($request->all());
        } catch (\Exception $e) {
            return back()->with('toast_error', 'Export failed: ' . $e->getMessage());
        }
    }
}
