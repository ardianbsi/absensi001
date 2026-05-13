<?php

namespace App\Repositories;

use App\Models\OvertimeRequest;
use Illuminate\Database\Eloquent\Collection;

class OvertimeRequestRepository extends BaseRepository
{
    public function __construct(OvertimeRequest $model)
    {
        parent::__construct($model);
    }

    public function findByEmployee(int $employeeId): Collection
    {
        return $this->model->byEmployee($employeeId)->with('approvedBy')->orderBy('date', 'desc')->get();
    }

    public function getPendingRequests(): Collection
    {
        return $this->model->pending()->with('employee', 'approvedBy')->orderBy('date', 'asc')->get();
    }

    public function getByDate(string $date): Collection
    {
        return $this->model->byDate($date)->with('employee')->orderBy('start_time')->get();
    }

    public function getApprovedRequests(): Collection
    {
        return $this->model->approved()->with('employee')->orderBy('date', 'desc')->get();
    }

    public function getByEmployeeAndDate(int $employeeId, string $date): ?OvertimeRequest
    {
        return $this->model
            ->where('employee_id', $employeeId)
            ->where('date', $date)
            ->first();
    }

    public function getByDateRange(string $startDate, string $endDate): Collection
    {
        return $this->model
            ->whereBetween('date', [$startDate, $endDate])
            ->with('employee')
            ->orderBy('date')
            ->get();
    }

    public function getByEmployeeAndDateRange(int $employeeId, string $startDate, string $endDate): Collection
    {
        return $this->model
            ->where('employee_id', $employeeId)
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date')
            ->get();
    }

    public function getPendingByManager(int $managerId): Collection
    {
        return $this->model
            ->where('status', 'pending')
            ->whereHas('employee', function ($query) use ($managerId) {
                $query->where('manager_id', $managerId);
            })
            ->with('employee')
            ->orderBy('date', 'asc')
            ->get();
    }

    public function getTotalOvertimeHours(int $employeeId, string $startDate, string $endDate): float
    {
        return (float) $this->model
            ->where('employee_id', $employeeId)
            ->where('status', 'approved')
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('total_hours');
    }

    public function hasOverlap(int $employeeId, $startTime, $endTime, ?int $excludeId = null): bool
    {
        $query = $this->model
            ->where('employee_id', $employeeId)
            ->where(function ($q) use ($startTime, $endTime) {
                $q->whereBetween('start_time', [$startTime, $endTime])
                    ->orWhereBetween('end_time', [$startTime, $endTime])
                    ->orWhere(function ($inner) use ($startTime, $endTime) {
                        $inner->where('start_time', '<=', $startTime)
                            ->where('end_time', '>=', $endTime);
                    });
            });

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }
}
