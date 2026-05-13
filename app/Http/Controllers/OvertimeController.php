<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOvertimeRequest;
use App\Models\Overtime;
use App\Services\OvertimeService;
use Illuminate\Http\Request;

class OvertimeController extends Controller
{
    protected OvertimeService $overtimeService;

    public function __construct(OvertimeService $overtimeService)
    {
        $this->overtimeService = $overtimeService;
    }

    public function index(Request $request)
    {
        $query = Overtime::with(['employee.user', 'approver']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        $overtimes = $query->latest()->paginate(15);
        $employees = \App\Models\Employee::pluck('full_name', 'id');

        return view('overtimes.index', compact('overtimes', 'employees'));
    }

    public function create()
    {
        return view('overtimes.create');
    }

    public function store(StoreOvertimeRequest $request)
    {
        try {
            $employee = auth()->user()->employee;

            if (!$employee) {
                return back()->with('toast_error', 'No employee record found.');
            }

            $data = $request->validated();
            $data['employee_id'] = $employee->id;

            if ($request->hasFile('attachment')) {
                $data['attachment'] = $request->file('attachment')->store('overtimes/attachments', 'public');
            }

            $this->overtimeService->submit($data);

            return redirect()->route('overtimes.index')
                ->with('toast_success', 'Overtime request submitted successfully.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('toast_error', 'Failed to submit overtime: ' . $e->getMessage());
        }
    }

    public function show(Overtime $overtime)
    {
        $overtime->load(['employee.user', 'approver']);
        return view('overtimes.show', compact('overtime'));
    }

    public function approve($id)
    {
        try {
            $this->overtimeService->approve($id, auth()->id());

            return redirect()->route('overtimes.index')
                ->with('toast_success', 'Overtime approved.');
        } catch (\Exception $e) {
            return back()->with('toast_error', 'Approval failed: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, $id)
    {
        $request->validate(['rejection_reason' => 'required|string']);

        try {
            $this->overtimeService->reject($id, auth()->id(), $request->rejection_reason);

            return redirect()->route('overtimes.index')
                ->with('toast_success', 'Overtime rejected.');
        } catch (\Exception $e) {
            return back()->with('toast_error', 'Rejection failed: ' . $e->getMessage());
        }
    }

    public function cancel($id)
    {
        try {
            $this->overtimeService->cancel($id, auth()->user()->employee?->id);

            return redirect()->route('overtimes.index')
                ->with('toast_success', 'Overtime cancelled.');
        } catch (\Exception $e) {
            return back()->with('toast_error', 'Cancellation failed: ' . $e->getMessage());
        }
    }

    public function export(Request $request)
    {
        try {
            return $this->overtimeService->exportReport($request->all());
        } catch (\Exception $e) {
            return back()->with('toast_error', 'Export failed: ' . $e->getMessage());
        }
    }
}
