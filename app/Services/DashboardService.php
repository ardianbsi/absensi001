<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\Attendance;
use App\Models\LeaveRequest;
use App\Models\OvertimeRequest;
use App\Repositories\AttendanceRepository;
use App\Repositories\EmployeeRepository;
use App\Repositories\LeaveRequestRepository;
use App\Repositories\OvertimeRequestRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    protected AttendanceRepository $attendanceRepository;
    protected EmployeeRepository $employeeRepository;
    protected LeaveRequestRepository $leaveRequestRepository;
    protected OvertimeRequestRepository $overtimeRequestRepository;

    public function __construct(
        AttendanceRepository $attendanceRepository,
        EmployeeRepository $employeeRepository,
        LeaveRequestRepository $leaveRequestRepository,
        OvertimeRequestRepository $overtimeRequestRepository
    ) {
        $this->attendanceRepository = $attendanceRepository;
        $this->employeeRepository = $employeeRepository;
        $this->leaveRequestRepository = $leaveRequestRepository;
        $this->overtimeRequestRepository = $overtimeRequestRepository;
    }

    public function getHrDashboard(): array
    {
        return Cache::remember('dashboard.hr', 300, function () {
            return [
                'today_stats' => $this->getTodayStats(),
                'pending_approvals' => $this->getPendingApprovals(),
                'attendance_chart' => $this->getAttendanceChartData(30),
                'department_stats' => $this->getDepartmentStats(),
                'employee_count' => Employee::active()->count(),
                'new_employees' => Employee::whereMonth('join_date', now()->month)
                    ->whereYear('join_date', now()->year)
                    ->count(),
            ];
        });
    }

    public function getManagerDashboard(int $managerId): array
    {
        $teamIds = Employee::where('manager_id', $managerId)->pluck('id');

        $todayAttendances = Attendance::whereIn('employee_id', $teamIds)
            ->where('date', now()->toDateString())
            ->get();

        $pendingLeaves = LeaveRequest::whereIn('employee_id', $teamIds)
            ->where('status', 'pending')
            ->count();

        $pendingOvertimes = OvertimeRequest::whereIn('employee_id', $teamIds)
            ->where('status', 'pending')
            ->count();

        return [
            'team_count' => $teamIds->count(),
            'today_attendance' => $todayAttendances,
            'today_stats' => [
                'hadir' => $todayAttendances->whereIn('status', ['hadir', 'telat'])->count(),
                'alpha' => $todayAttendances->where('status', 'alpha')->count(),
                'cuti' => $todayAttendances->where('status', 'cuti')->count(),
            ],
            'pending_approvals' => [
                'leave' => $pendingLeaves,
                'overtime' => $pendingOvertimes,
            ],
        ];
    }

    public function getEmployeeDashboard(int $employeeId): array
    {
        $todayAttendance = $this->attendanceRepository->findByEmployeeAndDate($employeeId, now()->toDateString());

        $thisMonth = Attendance::where('employee_id', $employeeId)
            ->whereYear('date', now()->year)
            ->whereMonth('date', now()->month)
            ->get();

        $pendingLeave = LeaveRequest::where('employee_id', $employeeId)
            ->where('status', 'pending')
            ->count();

        $recentAttendances = Attendance::where('employee_id', $employeeId)
            ->orderBy('date', 'desc')
            ->take(5)
            ->get();

        return [
            'today' => $todayAttendance,
            'monthly_summary' => [
                'hadir' => $thisMonth->whereIn('status', ['hadir', 'telat'])->count(),
                'alpha' => $thisMonth->where('status', 'alpha')->count(),
                'sakit' => $thisMonth->where('status', 'sakit')->count(),
                'cuti' => $thisMonth->where('status', 'cuti')->count(),
                'total_late' => $thisMonth->sum('late_minutes'),
            ],
            'pending_leave' => $pendingLeave,
            'recent_attendance' => $recentAttendances,
        ];
    }

    public function getTodayStats(): array
    {
        $date = now()->toDateString();
        $stats = $this->attendanceRepository->getAttendanceStats($date);

        $totalEmployees = Employee::active()->count();
        $stats['total_employees'] = $totalEmployees;
        $stats['not_clocked'] = $totalEmployees - $stats['total'];

        $stats['lembur'] = OvertimeRequest::where('date', $date)
            ->where('status', 'approved')
            ->count();

        return $stats;
    }

    public function getPendingApprovals(): array
    {
        return [
            'leave' => $this->leaveRequestRepository->getPendingRequests()->count(),
            'overtime' => $this->overtimeRequestRepository->getPendingRequests()->count(),
        ];
    }

    public function getAttendanceChartData(int $days = 30): array
    {
        $data = [];
        $startDate = now()->subDays($days - 1);

        for ($i = 0; $i < $days; $i++) {
            $date = $startDate->copy()->addDays($i)->toDateString();
            $stats = $this->attendanceRepository->getAttendanceStats($date);

            $data[] = [
                'date' => $date,
                'hadir' => $stats['hadir'],
                'telat' => $stats['telat'],
                'izin' => $stats['izin'],
                'sakit' => $stats['sakit'],
                'cuti' => $stats['cuti'],
                'alpha' => $stats['alpha'],
            ];
        }

        return $data;
    }

    public function getDepartmentStats(): array
    {
        $departments = \App\Models\Department::withCount(['employees' => function ($query) {
            $query->active();
        }])->get();

        $date = now()->toDateString();

        $stats = [];
        foreach ($departments as $department) {
            $attendanceCount = Attendance::where('date', $date)
                ->whereHas('employee', function ($query) use ($department) {
                    $query->where('department_id', $department->id);
                })
                ->count();

            $hadirCount = Attendance::where('date', $date)
                ->whereIn('status', ['hadir', 'telat'])
                ->whereHas('employee', function ($query) use ($department) {
                    $query->where('department_id', $department->id);
                })
                ->count();

            $stats[] = [
                'department' => $department->name,
                'total_employees' => $department->employees_count,
                'clocked_in' => $attendanceCount,
                'present' => $hadirCount,
                'present_percentage' => $department->employees_count > 0
                    ? round(($hadirCount / $department->employees_count) * 100, 1)
                    : 0,
            ];
        }

        return $stats;
    }

    public function getWeeklyTrend(): array
    {
        $data = [];
        $startDate = now()->startOfWeek();

        for ($i = 0; $i < 7; $i++) {
            $date = $startDate->copy()->addDays($i)->toDateString();
            $data[] = [
                'date' => $date,
                'day_name' => $startDate->copy()->addDays($i)->format('l'),
                'stats' => $this->attendanceRepository->getAttendanceStats($date),
            ];
        }

        return $data;
    }

    public function flushCache(): void
    {
        Cache::forget('dashboard.hr');
    }
}
