<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\OvertimeRequest;
use App\Repositories\OvertimeRequestRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OvertimeService extends BaseService
{
    protected OvertimeRequestRepository $overtimeRequestRepository;
    protected ActivityLogService $activityLogService;
    protected NotificationService $notificationService;

    public function __construct(
        OvertimeRequestRepository $overtimeRequestRepository,
        ActivityLogService $activityLogService,
        NotificationService $notificationService
    ) {
        parent::__construct($overtimeRequestRepository);
        $this->overtimeRequestRepository = $overtimeRequestRepository;
        $this->activityLogService = $activityLogService;
        $this->notificationService = $notificationService;
    }

    public function submitOvertime(array $data): OvertimeRequest
    {
        $employee = Employee::findOrFail($data['employee_id']);

        $startTime = Carbon::parse($data['start_time']);
        $endTime = Carbon::parse($data['end_time']);

        if ($endTime->lte($startTime)) {
            throw new \RuntimeException('End time must be after start time.');
        }

        $totalHours = $this->calculateTotalHours($startTime, $endTime);
        $data['total_hours'] = $totalHours;
        $data['status'] = 'pending';
        $data['date'] = $startTime->toDateString();

        $this->validateOvertime($employee->id, $startTime, $endTime);

        $overtimeRequest = $this->overtimeRequestRepository->create($data);

        $this->activityLogService->log('overtime', 'submit', "Overtime request submitted for employee {$employee->id}", null, $data);

        $this->notificationService->sendOvertimeNotification($overtimeRequest, 'submitted');

        return $overtimeRequest->fresh(['employee']);
    }

    public function approve(int $overtimeId, int $approvedBy, ?string $reason = null): OvertimeRequest
    {
        $overtimeRequest = $this->overtimeRequestRepository->find($overtimeId);
        if (!$overtimeRequest) {
            throw new \RuntimeException('Overtime request not found.');
        }

        if ($overtimeRequest->status !== 'pending') {
            throw new \RuntimeException('Only pending overtime requests can be approved.');
        }

        $overtimeRequest->update([
            'status' => 'approved',
            'approved_by' => $approvedBy,
            'approval_reason' => $reason,
        ]);

        $this->activityLogService->logApproval($overtimeRequest, 'overtime', 'approved');
        $this->notificationService->sendOvertimeNotification($overtimeRequest->fresh(), 'approved');

        return $overtimeRequest->fresh(['employee', 'approvedBy']);
    }

    public function reject(int $overtimeId, int $approvedBy, string $reason): OvertimeRequest
    {
        $overtimeRequest = $this->overtimeRequestRepository->find($overtimeId);
        if (!$overtimeRequest) {
            throw new \RuntimeException('Overtime request not found.');
        }

        if ($overtimeRequest->status !== 'pending') {
            throw new \RuntimeException('Only pending overtime requests can be rejected.');
        }

        $overtimeRequest->update([
            'status' => 'rejected',
            'approved_by' => $approvedBy,
            'approval_reason' => $reason,
        ]);

        $this->activityLogService->logApproval($overtimeRequest, 'overtime', 'rejected');
        $this->notificationService->sendOvertimeNotification($overtimeRequest->fresh(), 'rejected');

        return $overtimeRequest->fresh(['employee', 'approvedBy']);
    }

    public function calculateTotalHours($startTime, $endTime): float
    {
        $start = Carbon::parse($startTime);
        $end = Carbon::parse($endTime);

        return round($start->diffInMinutes($end) / 60, 2);
    }

    public function validateOvertime($employeeId, $startTime, $endTime): void
    {
        $start = Carbon::parse($startTime);
        $end = Carbon::parse($endTime);

        if ($this->overtimeRequestRepository->hasOverlap($employeeId, $start, $end)) {
            throw new \RuntimeException('Overtime request overlaps with an existing request.');
        }

        $workStart = Carbon::parse($start->toDateString() . ' 08:00:00');
        $workEnd = Carbon::parse($start->toDateString() . ' 17:00:00');

        if ($start->between($workStart, $workEnd) && $end->between($workStart, $workEnd)) {
            throw new \RuntimeException('Overtime must be outside regular working hours (08:00-17:00).');
        }

        $maxHours = 12;
        if ($start->diffInHours($end) > $maxHours) {
            throw new \RuntimeException("Overtime cannot exceed {$maxHours} hours.");
        }
    }

    public function getTotalOvertimeHours(int $employeeId, string $startDate, string $endDate): float
    {
        return $this->overtimeRequestRepository->getTotalOvertimeHours($employeeId, $startDate, $endDate);
    }

    public function getPendingOvertimeCount(): int
    {
        return $this->overtimeRequestRepository->getPendingRequests()->count();
    }

    public function getEmployeeOvertimes(int $employeeId)
    {
        return $this->overtimeRequestRepository->findByEmployee($employeeId);
    }

    public function getPendingByManager(int $managerId)
    {
        return $this->overtimeRequestRepository->getPendingByManager($managerId);
    }
}
