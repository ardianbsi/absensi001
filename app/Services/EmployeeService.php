<?php

namespace App\Services;

use App\Models\Employee;
use App\Repositories\EmployeeRepository;
use Illuminate\Database\Eloquent\Collection;

class EmployeeService
{
    protected EmployeeRepository $employeeRepository;

    public function __construct(EmployeeRepository $employeeRepository)
    {
        $this->employeeRepository = $employeeRepository;
    }

    public function create(array $data): Employee
    {
        if (isset($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        }

        return $this->employeeRepository->create($data);
    }

    public function update(Employee $employee, array $data): Employee
    {
        $employee->update($data);
        return $employee->fresh();
    }

    public function delete(Employee $employee): bool
    {
        return $employee->delete();
    }

    public function getAttendanceSummary(Employee $employee)
    {
        return $employee->attendances()
            ->whereDate('date', '>=', now()->subDays(30)->toDateString())
            ->whereDate('date', '<=', now()->toDateString())
            ->orderByDesc('date')
            ->get();
    }

    public function getLeaveHistory(Employee $employee): Collection
    {
        return $employee->leaveRequests()
            ->with('leaveType')
            ->latest()
            ->take(10)
            ->get();
    }

    public function exportExcel()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\EmployeeExport(),
            'employees.xlsx'
        );
    }

    public function importExcel($file): void
    {
        \Maatwebsite\Excel\Facades\Excel::import(
            new \App\Imports\EmployeeImport(),
            $file
        );
    }
}
