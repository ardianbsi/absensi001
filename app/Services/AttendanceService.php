<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\Shift;
use App\Models\Attendance;
use App\Models\AttendanceLog;
use App\Repositories\AttendanceRepository;
use App\Repositories\EmployeeRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AttendanceService extends BaseService
{
    protected AttendanceRepository $attendanceRepository;
    protected EmployeeRepository $employeeRepository;
    protected ActivityLogService $activityLogService;

    public function __construct(
        AttendanceRepository $attendanceRepository,
        EmployeeRepository $employeeRepository,
        ActivityLogService $activityLogService
    ) {
        parent::__construct($attendanceRepository);
        $this->attendanceRepository = $attendanceRepository;
        $this->employeeRepository = $employeeRepository;
        $this->activityLogService = $activityLogService;
    }

    public function checkIn(array $data): Attendance
    {
        $employee = $this->employeeRepository->find($data['employee_id']);
        if (!$employee) {
            throw new \RuntimeException('Employee not found.');
        }

        if (!$employee->is_active) {
            throw new \RuntimeException('Employee is not active.');
        }

        $existing = $this->attendanceRepository->findByEmployeeAndDate($employee->id, now()->toDateString());
        if ($existing && $existing->clock_in) {
            throw new \RuntimeException('Already checked in today.');
        }

        $shift = $employee->currentShift;
        $clockInTime = now();

        if ($existing) {
            $attendance = $existing;
        } else {
            $attendance = new Attendance();
            $attendance->employee_id = $employee->id;
            $attendance->date = now()->toDateString();
        }

        $attendance->shift_id = $shift?->id;
        $attendance->clock_in = $clockInTime;
        $attendance->clock_in_latitude = $data['latitude'] ?? null;
        $attendance->clock_in_longitude = $data['longitude'] ?? null;
        $attendance->clock_in_selfie = $data['selfie'] ?? null;
        $attendance->clock_in_note = $data['note'] ?? null;
        $attendance->clock_in_ip = $data['ip'] ?? request()->ip();
        $attendance->clock_in_device = $data['device'] ?? null;

        $attendance->status = $this->calculateStatus($employee, $clockInTime);
        $attendance->is_late = $attendance->status === 'telat';
        $attendance->late_minutes = $this->calculateLateMinutes($employee, $clockInTime);

        $attendance->save();

        AttendanceLog::create([
            'attendance_id' => $attendance->id,
            'action' => 'check_in',
            'timestamp' => $clockInTime,
            'latitude' => $data['latitude'] ?? null,
            'longitude' => $data['longitude'] ?? null,
            'selfie_photo' => $data['selfie'] ?? null,
            'ip_address' => $data['ip'] ?? request()->ip(),
            'device_info' => $data['device'] ?? null,
            'notes' => $data['note'] ?? null,
        ]);

        $this->activityLogService->logAttendance($attendance, 'check_in');

        return $attendance->fresh();
    }

    public function checkOut(int $attendanceId, array $data): Attendance
    {
        $attendance = $this->attendanceRepository->find($attendanceId);
        if (!$attendance) {
            throw new \RuntimeException('Attendance record not found.');
        }

        if ($attendance->clock_out) {
            throw new \RuntimeException('Already checked out.');
        }

        $clockOutTime = now();
        $attendance->clock_out = $clockOutTime;
        $attendance->clock_out_latitude = $data['latitude'] ?? null;
        $attendance->clock_out_longitude = $data['longitude'] ?? null;
        $attendance->clock_out_selfie = $data['selfie'] ?? null;
        $attendance->clock_out_note = $data['note'] ?? null;
        $attendance->clock_out_ip = $data['ip'] ?? request()->ip();
        $attendance->clock_out_device = $data['device'] ?? null;

        $shift = $attendance->shift ?? $attendance->employee?->currentShift;
        if ($shift) {
            $attendance->is_early_leave = $this->isEarlyLeave($clockOutTime, $shift);
            $attendance->early_leave_minutes = $this->calculateEarlyLeaveMinutes($clockOutTime, $shift);
        }

        if ($attendance->clock_in) {
            $start = Carbon::parse($attendance->clock_in);
            $end = Carbon::parse($clockOutTime);
            $attendance->total_work_hours = round($start->diffInMinutes($end) / 60, 2);
        }

        $attendance->save();

        AttendanceLog::create([
            'attendance_id' => $attendance->id,
            'action' => 'check_out',
            'timestamp' => $clockOutTime,
            'latitude' => $data['latitude'] ?? null,
            'longitude' => $data['longitude'] ?? null,
            'selfie_photo' => $data['selfie'] ?? null,
            'ip_address' => $data['ip'] ?? request()->ip(),
            'device_info' => $data['device'] ?? null,
            'notes' => $data['note'] ?? null,
        ]);

        $this->activityLogService->logAttendance($attendance, 'check_out');

        return $attendance->fresh();
    }

    public function calculateStatus(Employee $employee, $clockInTime): string
    {
        $shift = $employee->currentShift;
        if (!$shift || !$shift->clock_in_time) {
            return 'hadir';
        }

        $clockIn = Carbon::parse($clockInTime);
        $shiftStart = Carbon::parse($shift->clock_in_time)->setDateFrom($clockIn);
        $tolerance = $shift->late_tolerance_minutes ?? 15;

        if ($clockIn->gt($shiftStart->copy()->addMinutes($tolerance))) {
            return 'telat';
        }

        return 'hadir';
    }

    public function calculateLateMinutes(Employee $employee, $clockInTime): int
    {
        $shift = $employee->currentShift;
        if (!$shift || !$shift->clock_in_time) {
            return 0;
        }

        $clockIn = Carbon::parse($clockInTime);
        $shiftStart = Carbon::parse($shift->clock_in_time)->setDateFrom($clockIn);
        $tolerance = $shift->late_tolerance_minutes ?? 15;

        $lateMinutes = (int) $clockIn->diffInMinutes($shiftStart, false);

        return max(0, $lateMinutes - $tolerance);
    }

    public function isEarlyLeave($clockOutTime, Shift $shift): bool
    {
        if (!$shift->clock_out_time) {
            return false;
        }

        $clockOut = Carbon::parse($clockOutTime);
        $shiftEnd = Carbon::parse($shift->clock_out_time)->setDateFrom($clockOut);

        return $clockOut->lt($shiftEnd);
    }

    public function calculateEarlyLeaveMinutes($clockOutTime, Shift $shift): int
    {
        if (!$shift->clock_out_time) {
            return 0;
        }

        $clockOut = Carbon::parse($clockOutTime);
        $shiftEnd = Carbon::parse($shift->clock_out_time)->setDateFrom($clockOut);

        $earlyMinutes = (int) $clockOut->diffInMinutes($shiftEnd, false);

        return max(0, $earlyMinutes);
    }

    public function isWithinRadius($lat1, $lon1, $lat2, $lon2, $radius = 100): bool
    {
        if (!$lat1 || !$lon1 || !$lat2 || !$lon2) {
            return false;
        }

        $earthRadius = 6371000;

        $lat1 = deg2rad((float) $lat1);
        $lon1 = deg2rad((float) $lon1);
        $lat2 = deg2rad((float) $lat2);
        $lon2 = deg2rad((float) $lon2);

        $dLat = $lat2 - $lat1;
        $dLon = $lon2 - $lon1;

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos($lat1) * cos($lat2) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return ($earthRadius * $c) <= $radius;
    }

    public function getTodayStatus(Employee $employee): ?Attendance
    {
        return $this->attendanceRepository->findByEmployeeAndDate($employee->id, now()->toDateString());
    }

    public function getTodayAttendanceByEmployee(int $employeeId): ?Attendance
    {
        return $this->attendanceRepository->findByEmployeeAndDate($employeeId, now()->toDateString());
    }

    public function markAbsent(int $employeeId, string $date): Attendance
    {
        $existing = $this->attendanceRepository->findByEmployeeAndDate($employeeId, $date);
        if ($existing) {
            return $existing;
        }

        $attendance = $this->attendanceRepository->create([
            'employee_id' => $employeeId,
            'date' => $date,
            'status' => 'alpha',
            'is_late' => false,
            'late_minutes' => 0,
        ]);

        $this->activityLogService->log('attendance', 'mark_absent', "Employee {$employeeId} marked absent for {$date}");

        return $attendance;
    }

    public function updateAttendanceStatus(int $id, string $status): ?Attendance
    {
        $validStatuses = ['hadir', 'telat', 'izin', 'sakit', 'cuti', 'alpha', 'lembur'];
        if (!in_array($status, $validStatuses)) {
            throw new \InvalidArgumentException("Invalid status: {$status}");
        }

        return $this->attendanceRepository->update($id, [
            'status' => $status,
            'is_late' => $status === 'telat',
        ]);
    }

    public function getDateRangeReport(string $startDate, string $endDate)
    {
        return $this->attendanceRepository->getDateRangeReport($startDate, $endDate);
    }
}
