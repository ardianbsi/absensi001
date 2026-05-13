<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Schedule;
use App\Models\Shift;
use App\Services\ScheduleService;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    protected ScheduleService $scheduleService;

    public function __construct(ScheduleService $scheduleService)
    {
        $this->scheduleService = $scheduleService;
    }

    public function index(Request $request)
    {
        $query = Schedule::with(['employee.user', 'shift', 'employee.department']);

        if ($request->filled('department_id')) {
            $query->whereHas('employee', function ($q) use ($request) {
                $q->where('department_id', $request->department_id);
            });
        }

        if ($request->filled('date')) {
            $query->whereDate('schedule_date', $request->date);
        }

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        $schedules = $query->orderBy('schedule_date')->paginate(20);
        $departments = Department::pluck('name', 'id');
        $employees = Employee::pluck('full_name', 'id');
        $shifts = Shift::pluck('name', 'id');

        return view('schedules.index', compact('schedules', 'departments', 'employees', 'shifts'));
    }

    public function assign(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'shift_id' => 'required|exists:shifts,id',
            'schedule_date' => 'required|date',
        ]);

        try {
            $this->scheduleService->assignShift(
                $request->employee_id,
                $request->shift_id,
                $request->schedule_date
            );

            return redirect()->route('schedules.index')
                ->with('toast_success', 'Shift assigned successfully.');
        } catch (\Exception $e) {
            return back()->with('toast_error', 'Failed to assign shift: ' . $e->getMessage());
        }
    }

    public function massAssign(Request $request)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'shift_id' => 'required|exists:shifts,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        try {
            $count = $this->scheduleService->massAssignShift(
                $request->department_id,
                $request->shift_id,
                $request->start_date,
                $request->end_date
            );

            return redirect()->route('schedules.index')
                ->with('toast_success', "Shift assigned to {$count} employees successfully.");
        } catch (\Exception $e) {
            return back()->with('toast_error', 'Mass assign failed: ' . $e->getMessage());
        }
    }

    public function override(Request $request, $scheduleId)
    {
        $request->validate([
            'shift_id' => 'required|exists:shifts,id',
            'reason' => 'required|string',
        ]);

        try {
            $this->scheduleService->overrideSchedule(
                $scheduleId,
                $request->shift_id,
                $request->reason
            );

            return redirect()->route('schedules.index')
                ->with('toast_success', 'Schedule overridden successfully.');
        } catch (\Exception $e) {
            return back()->with('toast_error', 'Override failed: ' . $e->getMessage());
        }
    }
}
