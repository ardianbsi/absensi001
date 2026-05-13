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

    public function getAttendanceSummary(Employee $employee): array
    {
        $attendances = $employee->attendances()
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->get();

        return [
            'total' => $attendances->count(),
            'hadir' => $attendances->where('status', 'hadir')->count(),
            'telat' => $attendances->where('status', 'telat')->count(),
            'izin' => $attendances->where('status', 'izin')->count(),
            'sakit' => $attendances->where('status', 'sakit')->count(),
            'alpha' => $attendances->where('status', 'alpha')->count(),
        ];
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
