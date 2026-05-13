<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Employee;
use App\Exports\AttendanceExport;
use App\Repositories\AttendanceRepository;
use App\Repositories\EmployeeRepository;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class ReportService
{
    protected AttendanceRepository $attendanceRepository;
    protected EmployeeRepository $employeeRepository;

    public function __construct(
        AttendanceRepository $attendanceRepository,
        EmployeeRepository $employeeRepository
    ) {
        $this->attendanceRepository = $attendanceRepository;
        $this->employeeRepository = $employeeRepository;
    }

    public function generateDailyReport($date, array $filters = []): array
    {
        $dateStr = $date instanceof Carbon ? $date->toDateString() : $date;

        $query = Attendance::where('date', $dateStr)
            ->with(['employee.department', 'employee.position', 'shift']);

        if (!empty($filters['department_id'])) {
            $query->whereHas('employee', function ($q) use ($filters) {
                $q->where('department_id', $filters['department_id']);
            });
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['employee_id'])) {
            $query->where('employee_id', $filters['employee_id']);
        }

        $attendances = $query->orderBy('employee.full_name')->get();

        $summary = [
            'total' => $attendances->count(),
            'hadir' => $attendances->whereIn('status', ['hadir', 'telat'])->count(),
            'telat' => $attendances->where('status', 'telat')->count(),
            'izin' => $attendances->where('status', 'izin')->count(),
            'sakit' => $attendances->where('status', 'sakit')->count(),
            'cuti' => $attendances->where('status', 'cuti')->count(),
            'alpha' => $attendances->where('status', 'alpha')->count(),
        ];

        return [
            'date' => $dateStr,
            'records' => $attendances,
            'summary' => $summary,
        ];
    }

    public function generateMonthlyReport(int $year, int $month, array $filters = []): array
    {
        $query = Attendance::whereYear('date', $year)
            ->whereMonth('date', $month)
            ->with(['employee.department', 'employee.position', 'shift']);

        if (!empty($filters['department_id'])) {
            $query->whereHas('employee', function ($q) use ($filters) {
                $q->where('department_id', $filters['department_id']);
            });
        }

        if (!empty($filters['employee_id'])) {
            $query->where('employee_id', $filters['employee_id']);
        }

        $attendances = $query->orderBy('date')->orderBy('employee_id')->get();

        $summary = [
            'total_records' => $attendances->count(),
            'hadir' => $attendances->whereIn('status', ['hadir', 'telat'])->count(),
            'telat' => $attendances->where('status', 'telat')->count(),
            'izin' => $attendances->where('status', 'izin')->count(),
            'sakit' => $attendances->where('status', 'sakit')->count(),
            'cuti' => $attendances->where('status', 'cuti')->count(),
            'alpha' => $attendances->where('status', 'alpha')->count(),
            'total_late_minutes' => $attendances->sum('late_minutes'),
            'total_work_hours' => $attendances->sum('total_work_hours'),
        ];

        return [
            'year' => $year,
            'month' => $month,
            'records' => $attendances,
            'summary' => $summary,
        ];
    }

    public function generateEmployeeReport(int $employeeId, $startDate, $endDate): array
    {
        $employee = Employee::with(['department', 'position'])->findOrFail($employeeId);

        $start = $startDate instanceof Carbon ? $startDate->toDateString() : $startDate;
        $end = $endDate instanceof Carbon ? $endDate->toDateString() : $endDate;

        $attendances = $this->attendanceRepository->getByEmployeeBetweenDates($employeeId, $start, $end);

        $summary = [
            'total_days' => $attendances->count(),
            'hadir' => $attendances->whereIn('status', ['hadir', 'telat'])->count(),
            'telat' => $attendances->where('status', 'telat')->count(),
            'izin' => $attendances->where('status', 'izin')->count(),
            'sakit' => $attendances->where('status', 'sakit')->count(),
            'cuti' => $attendances->where('status', 'cuti')->count(),
            'alpha' => $attendances->where('status', 'alpha')->count(),
            'total_late_minutes' => $attendances->sum('late_minutes'),
            'total_work_hours' => $attendances->sum('total_work_hours'),
        ];

        return [
            'employee' => $employee,
            'start_date' => $start,
            'end_date' => $end,
            'records' => $attendances,
            'summary' => $summary,
        ];
    }

    public function generateLeaveReport($startDate, $endDate, array $filters = [])
    {
        $query = \App\Models\LeaveRequest::with(['employee.department', 'leaveType'])
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate]);
            });

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['department_id'])) {
            $query->whereHas('employee', function ($q) use ($filters) {
                $q->where('department_id', $filters['department_id']);
            });
        }

        if (!empty($filters['leave_type_id'])) {
            $query->where('leave_type_id', $filters['leave_type_id']);
        }

        return $query->orderBy('start_date')->get();
    }

    public function exportToExcel(string $type, array $data)
    {
        switch ($type) {
            case 'daily':
                return Excel::download(
                    new AttendanceExport($data['records']),
                    "attendance_{$data['date']}.xlsx"
                );
            case 'monthly':
                return Excel::download(
                    new AttendanceExport($data['records']),
                    "attendance_{$data['year']}_{$data['month']}.xlsx"
                );
            case 'employee':
                return Excel::download(
                    new AttendanceExport($data['records']),
                    "attendance_employee_{$data['employee']->id}_{$data['start_date']}_{$data['end_date']}.xlsx"
                );
            default:
                throw new \InvalidArgumentException("Unknown export type: {$type}");
        }
    }

    public function exportToPDF(string $type, array $data)
    {
        switch ($type) {
            case 'daily':
                $pdf = Pdf::loadView('reports.daily', $data);
                return $pdf->download("attendance_{$data['date']}.pdf");
            case 'monthly':
                $pdf = Pdf::loadView('reports.monthly', $data);
                return $pdf->download("attendance_{$data['year']}_{$data['month']}.pdf");
            case 'employee':
                $pdf = Pdf::loadView('reports.employee', $data);
                return $pdf->download("attendance_employee_{$data['employee']->id}.pdf");
            default:
                throw new \InvalidArgumentException("Unknown export type: {$type}");
        }
    }

    public function streamPDF(string $type, array $data)
    {
        switch ($type) {
            case 'daily':
                return Pdf::loadView('reports.daily', $data)->stream();
            case 'monthly':
                return Pdf::loadView('reports.monthly', $data)->stream();
            case 'employee':
                return Pdf::loadView('reports.employee', $data)->stream();
            default:
                throw new \InvalidArgumentException("Unknown export type: {$type}");
        }
    }
}
