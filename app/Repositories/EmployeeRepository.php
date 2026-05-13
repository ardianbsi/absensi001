<?php

namespace App\Repositories;

use App\Models\Employee;
use App\Models\Attendance;
use Illuminate\Database\Eloquent\Collection;

class EmployeeRepository extends BaseRepository
{
    public function __construct(Employee $model)
    {
        parent::__construct($model);
    }

    public function findByNIK(string $nik): ?Employee
    {
        return $this->model->where('nik', $nik)->first();
    }

    public function findByDepartment(int $departmentId): Collection
    {
        return $this->model->where('department_id', $departmentId)->get();
    }

    public function findByManager(int $managerId): Collection
    {
        return $this->model->where('manager_id', $managerId)->get();
    }

    public function search(string $keyword): Collection
    {
        return $this->model->where(function ($query) use ($keyword) {
            $query->where('full_name', 'like', "%{$keyword}%")
                ->orWhere('nik', 'like', "%{$keyword}%")
                ->orWhere('email', 'like', "%{$keyword}%")
                ->orWhere('phone', 'like', "%{$keyword}%");
        })->get();
    }

    public function getActiveEmployees(): Collection
    {
        return $this->model->active()->get();
    }

    public function getAttendanceToday(): Collection
    {
        return $this->model->whereHas('attendances', function ($query) {
            $query->where('date', now()->toDateString());
        })->with(['attendances' => function ($query) {
            $query->where('date', now()->toDateString());
        }])->get();
    }

    public function getEmployeesByStatus(string $status): Collection
    {
        return $this->model->where('employment_status', $status)->get();
    }

    public function getByDepartmentWithAttendance(int $departmentId, string $date): Collection
    {
        return $this->model->where('department_id', $departmentId)
            ->with(['attendances' => function ($query) use ($date) {
                $query->where('date', $date);
            }])
            ->get();
    }
}
