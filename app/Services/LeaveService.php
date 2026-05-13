<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Repositories\LeaveRequestRepository;
use App\Repositories\AttendanceRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LeaveService extends BaseService
{
    protected LeaveRequestRepository $leaveRequestRepository;
    protected AttendanceRepository $attendanceRepository;
    protected ActivityLogService $activityLogService;
    protected NotificationService $notificationService;

    public function __construct(
        LeaveRequestRepository $leaveRequestRepository,
        AttendanceRepository $attendanceRepository,
        ActivityLogService $activityLogService,
        NotificationService $notificationService
    ) {
        parent::__construct($leaveRequestRepository);
        $this->leaveRequestRepository = $leaveRequestRepository;
        $this->attendanceRepository = $attendanceRepository;
        $this->activityLogService = $activityLogService;
        $this->notificationService = $notificationService;
    }

    public function submitLeave(array $data): LeaveRequest
    {
        $employee = Employee::findOrFail($data['employee_id']);
        $leaveType = LeaveType::findOrFail($data['leave_type_id']);

        $startDate = Carbon::parse($data['start_date']);
        $endDate = Carbon::parse($data['end_date']);
        $totalDays = $startDate->diffInDays($endDate) + 1;

        if ($this->checkOverlap($employee->id, $startDate, $endDate)) {
            throw new \RuntimeException('Leave request overlaps with an existing approved or pending request.');
        }

        if ($leaveType->quota > 0) {
            $balance = $this->getBalance($employee->id, $leaveType->id);
            if ($totalDays > $balance) {
                throw new \RuntimeException(
                    "Insufficient leave balance. You have {$balance} day(s) remaining, but requesting {$totalDays} day(s)."
                );
            }
        }

        $data['total_days'] = $totalDays;
        $data['status'] = 'pending';

        $leaveRequest = $this->leaveRequestRepository->create($data);

        $this->activityLogService->log('leave', 'submit', "Leave request submitted for employee {$employee->id}", null, $data);

        $this->notificationService->sendLeaveNotification($leaveRequest, 'submitted');

        return $leaveRequest->fresh(['employee', 'leaveType']);
    }

    public function approve(int $leaveId, int $approvedBy, ?string $reason = null): LeaveRequest
    {
        $leaveRequest = $this->leaveRequestRepository->find($leaveId);
        if (!$leaveRequest) {
            throw new \RuntimeException('Leave request not found.');
        }

        if ($leaveRequest->status !== 'pending') {
            throw new \RuntimeException('Only pending requests can be approved.');
        }

        DB::beginTransaction();
        try {
            $leaveRequest->update([
                'status' => 'approved',
                'approved_by' => $approvedBy,
                'approval_reason' => $reason,
            ]);

            $currentDate = Carbon::parse($leaveRequest->start_date);
            $endDate = Carbon::parse($leaveRequest->end_date);

            while ($currentDate <= $endDate) {
                $dateStr = $currentDate->toDateString();
                $existing = $this->attendanceRepository->findByEmployeeAndDate($leaveRequest->employee_id, $dateStr);

                if (!$existing) {
                    $this->attendanceRepository->create([
                        'employee_id' => $leaveRequest->employee_id,
                        'date' => $dateStr,
                        'status' => 'cuti',
                        'is_late' => false,
                        'late_minutes' => 0,
                    ]);
                } elseif ($existing->status === 'alpha') {
                    $existing->update(['status' => 'cuti']);
                }

                $currentDate->addDay();
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Failed to approve leave: ' . $e->getMessage());
            throw $e;
        }

        $this->activityLogService->logApproval($leaveRequest, 'leave', 'approved');
        $this->notificationService->sendLeaveNotification($leaveRequest->fresh(), 'approved');

        return $leaveRequest->fresh(['employee', 'leaveType', 'approvedBy']);
    }

    public function reject(int $leaveId, int $approvedBy, string $reason): LeaveRequest
    {
        $leaveRequest = $this->leaveRequestRepository->find($leaveId);
        if (!$leaveRequest) {
            throw new \RuntimeException('Leave request not found.');
        }

        if ($leaveRequest->status !== 'pending') {
            throw new \RuntimeException('Only pending requests can be rejected.');
        }

        $leaveRequest->update([
            'status' => 'rejected',
            'approved_by' => $approvedBy,
            'approval_reason' => $reason,
            'rejected_at' => now(),
        ]);

        $this->activityLogService->logApproval($leaveRequest, 'leave', 'rejected');
        $this->notificationService->sendLeaveNotification($leaveRequest->fresh(), 'rejected');

        return $leaveRequest->fresh(['employee', 'leaveType', 'approvedBy']);
    }

    public function cancel(int $leaveId): LeaveRequest
    {
        $leaveRequest = $this->leaveRequestRepository->find($leaveId);
        if (!$leaveRequest) {
            throw new \RuntimeException('Leave request not found.');
        }

        if (!in_array($leaveRequest->status, ['pending', 'approved'])) {
            throw new \RuntimeException('This leave request cannot be cancelled.');
        }

        $leaveRequest->update(['status' => 'cancelled']);

        if ($leaveRequest->status === 'approved') {
            $currentDate = Carbon::parse($leaveRequest->start_date);
            $endDate = Carbon::parse($leaveRequest->end_date);

            while ($currentDate <= $endDate) {
                $attendance = $this->attendanceRepository->findByEmployeeAndDate(
                    $leaveRequest->employee_id,
                    $currentDate->toDateString()
                );
                if ($attendance && $attendance->status === 'cuti') {
                    $attendance->update(['status' => 'alpha']);
                }
                $currentDate->addDay();
            }
        }

        $this->activityLogService->log('leave', 'cancel', "Leave request #{$leaveId} cancelled");

        return $leaveRequest->fresh();
    }

    public function checkOverlap(int $employeeId, $startDate, $endDate): bool
    {
        $start = Carbon::parse($startDate)->toDateString();
        $end = Carbon::parse($endDate)->toDateString();

        return $this->leaveRequestRepository->getByDateRange($start, $end)
            ->where('employee_id', $employeeId)
            ->whereIn('status', ['pending', 'approved'])
            ->isNotEmpty();
    }

    public function getBalance(int $employeeId, int $leaveTypeId): int
    {
        $leaveType = LeaveType::findOrFail($leaveTypeId);

        if ($leaveType->quota <= 0) {
            return PHP_INT_MAX;
        }

        $usedDays = $this->leaveRequestRepository->countApprovedByType(
            $employeeId,
            $leaveTypeId,
            now()->year
        );

        return max(0, $leaveType->quota - $usedDays);
    }

    public function getLeaveBalances(Employee $employee): array
    {
        $leaveTypes = LeaveType::where('is_active', true)->get();
        $balances = [];

        foreach ($leaveTypes as $leaveType) {
            $quota = $leaveType->quota;
            $used = 0;
            if ($quota > 0) {
                $usedDays = $this->leaveRequestRepository->countApprovedByType(
                    $employee->id,
                    $leaveType->id,
                    now()->year
                );
                $used = min($usedDays, $quota);
            }

            $balances[] = [
                'name' => $leaveType->name,
                'leave_type' => $leaveType->name,
                'quota' => $quota,
                'used' => $used,
                'remaining' => max(0, $quota - $used),
            ];
        }

        return $balances;
    }

    public function getPendingLeaveCount(): int
    {
        return $this->leaveRequestRepository->getPendingRequests()->count();
    }

    public function getEmployeeLeaves(int $employeeId)
    {
        return $this->leaveRequestRepository->findByEmployee($employeeId);
    }
}
