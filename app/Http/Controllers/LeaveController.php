<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLeaveRequest;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Services\LeaveService;
use Illuminate\Http\Request;

class LeaveController extends Controller
{
    protected LeaveService $leaveService;

    public function __construct(LeaveService $leaveService)
    {
        $this->leaveService = $leaveService;
    }

    public function index(Request $request)
    {
        $query = LeaveRequest::with(['employee.user', 'leaveType']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('leave_type_id')) {
            $query->where('leave_type_id', $request->leave_type_id);
        }

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('start_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('end_date', '<=', $request->date_to);
        }

        $leaveRequests = $query->latest()->paginate(15);
        $leaveTypes = LeaveType::pluck('name', 'id');

        return view('leaves.index', compact('leaveRequests', 'leaveTypes'));
    }

    public function create()
    {
        $leaveTypes = LeaveType::where('is_active', true)->get();
        $employee = auth()->user()->employee;
        $leaveBalances = [];

        if ($employee) {
            $leaveBalances = $this->leaveService->getLeaveBalances($employee);
        }

        return view('leaves.create', compact('leaveTypes', 'leaveBalances'));
    }

    public function store(StoreLeaveRequest $request)
    {
        try {
            $employee = auth()->user()->employee;

            if (!$employee) {
                return back()->with('toast_error', 'No employee record found.');
            }

            $data = $request->validated();
            $data['employee_id'] = $employee->id;

            if ($request->hasFile('attachment')) {
                $data['attachment'] = $request->file('attachment')->store('leaves/attachments', 'public');
            }

            $this->leaveService->submit($data);

            return redirect()->route('leaves.index')
                ->with('toast_success', 'Leave request submitted successfully.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('toast_error', 'Failed to submit leave: ' . $e->getMessage());
        }
    }

    public function show(LeaveRequest $leaveRequest)
    {
        $leaveRequest->load(['employee.user', 'leaveType', 'approver']);
        return view('leaves.show', compact('leaveRequest'));
    }

    public function approve($id)
    {
        try {
            $this->leaveService->approve($id, auth()->id());

            return redirect()->route('leaves.index')
                ->with('toast_success', 'Leave request approved.');
        } catch (\Exception $e) {
            return back()->with('toast_error', 'Approval failed: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, $id)
    {
        $request->validate(['rejection_reason' => 'required|string']);

        try {
            $this->leaveService->reject($id, auth()->id(), $request->rejection_reason);

            return redirect()->route('leaves.index')
                ->with('toast_success', 'Leave request rejected.');
        } catch (\Exception $e) {
            return back()->with('toast_error', 'Rejection failed: ' . $e->getMessage());
        }
    }

    public function cancel($id)
    {
        try {
            $this->leaveService->cancel($id, auth()->user()->employee?->id);

            return redirect()->route('leaves.index')
                ->with('toast_success', 'Leave request cancelled.');
        } catch (\Exception $e) {
            return back()->with('toast_error', 'Cancellation failed: ' . $e->getMessage());
        }
    }

    public function export(Request $request)
    {
        try {
            return $this->leaveService->exportReport($request->all());
        } catch (\Exception $e) {
            return back()->with('toast_error', 'Export failed: ' . $e->getMessage());
        }
    }
}
