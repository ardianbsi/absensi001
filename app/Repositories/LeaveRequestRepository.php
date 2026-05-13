<?php

namespace App\Repositories;

use App\Models\LeaveRequest;
use Illuminate\Database\Eloquent\Collection;

class LeaveRequestRepository extends BaseRepository
{
    public function __construct(LeaveRequest $model)
    {
        parent::__construct($model);
    }

    public function findByEmployee(int $employeeId): Collection
    {
        return $this->model->byEmployee($employeeId)->with('leaveType')->orderBy('created_at', 'desc')->get();
    }

    public function findByStatus(string $status): Collection
    {
        return $this->model->byStatus($status)->with('employee', 'leaveType')->orderBy('created_at', 'desc')->get();
    }

    public function getPendingRequests(): Collection
    {
        return $this->model->pending()->with('employee', 'leaveType')->orderBy('created_at', 'asc')->get();
    }

    public function getApprovedRequests(): Collection
    {
        return $this->model->approved()->with('employee', 'leaveType')->orderBy('created_at', 'desc')->get();
    }

    public function getByDateRange(string $startDate, string $endDate): Collection
    {
        return $this->model
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate])
                    ->orWhere(function ($q) use ($startDate, $endDate) {
                        $q->where('start_date', '<=', $startDate)
                            ->where('end_date', '>=', $endDate);
                    });
            })
            ->with('employee', 'leaveType')
            ->orderBy('start_date')
            ->get();
    }

    public function getByEmployeeAndStatus(int $employeeId, string $status): Collection
    {
        return $this->model
            ->where('employee_id', $employeeId)
            ->where('status', $status)
            ->with('leaveType')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getApprovedInPeriod(int $employeeId, string $startDate, string $endDate): Collection
    {
        return $this->model
            ->where('employee_id', $employeeId)
            ->where('status', 'approved')
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate]);
            })
            ->get();
    }

    public function countApprovedByType(int $employeeId, int $leaveTypeId, int $year): int
    {
        return $this->model
            ->where('employee_id', $employeeId)
            ->where('leave_type_id', $leaveTypeId)
            ->where('status', 'approved')
            ->whereYear('start_date', $year)
            ->sum('total_days');
    }
}
