<?php

namespace App\Repositories;

use App\Models\Attendance;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class AttendanceRepository extends BaseRepository
{
    public function __construct(Attendance $model)
    {
        parent::__construct($model);
    }

    public function findByDate(string $date): Collection
    {
        return $this->model->where('date', $date)->with('employee')->get();
    }

    public function findByEmployeeAndDate(int $employeeId, string $date): ?Attendance
    {
        return $this->model
            ->where('employee_id', $employeeId)
            ->where('date', $date)
            ->first();
    }

    public function getTodayAttendances(): Collection
    {
        return $this->model->today()->with('employee.department')->get();
    }

    public function getLateAttendances(string $date): Collection
    {
        return $this->model
            ->where('date', $date)
            ->where('is_late', true)
            ->with('employee')
            ->get();
    }

    public function getMonthlyReport(int $year, int $month): Collection
    {
        return $this->model
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->with('employee.department')
            ->orderBy('date')
            ->get();
    }

    public function getDailyReport(string $date): Collection
    {
        return $this->model
            ->where('date', $date)
            ->with(['employee.department', 'employee.position'])
            ->orderBy('employee.full_name')
            ->get();
    }

    public function getAttendanceStats(string $date): array
    {
        $total = $this->model->where('date', $date)->count();
        $hadir = $this->model->where('date', $date)->where('status', 'hadir')->count();
        $telat = $this->model->where('date', $date)->where('status', 'telat')->count();
        $izin = $this->model->where('date', $date)->where('status', 'izin')->count();
        $sakit = $this->model->where('date', $date)->where('status', 'sakit')->count();
        $cuti = $this->model->where('date', $date)->where('status', 'cuti')->count();
        $alpha = $this->model->where('date', $date)->where('status', 'alpha')->count();

        return compact('total', 'hadir', 'telat', 'izin', 'sakit', 'cuti', 'alpha');
    }

    public function getByEmployeeBetweenDates(int $employeeId, string $startDate, string $endDate): Collection
    {
        return $this->model
            ->where('employee_id', $employeeId)
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date')
            ->get();
    }

    public function getEmployeeMonthlyReport(int $employeeId, int $year, int $month): Collection
    {
        return $this->model
            ->where('employee_id', $employeeId)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->orderBy('date')
            ->get();
    }

    public function getLateCountByEmployee(int $employeeId, string $startDate, string $endDate): int
    {
        return $this->model
            ->where('employee_id', $employeeId)
            ->where('is_late', true)
            ->whereBetween('date', [$startDate, $endDate])
            ->count();
    }

    public function getAbsentEmployees(string $date): Collection
    {
        return $this->model->where('date', $date)->where('status', 'alpha')->with('employee')->get();
    }

    public function getDateRangeReport(string $startDate, string $endDate): Collection
    {
        return $this->model
            ->whereBetween('date', [$startDate, $endDate])
            ->with('employee.department')
            ->orderBy('date')
            ->orderBy('employee_id')
            ->get();
    }
}
