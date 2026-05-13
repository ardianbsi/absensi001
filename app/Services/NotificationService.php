<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\OvertimeRequest;
use App\Notifications\LeaveNotification;
use App\Notifications\OvertimeNotification;
use App\Notifications\AttendanceReminder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class NotificationService
{
    protected ActivityLogService $activityLogService;

    public function __construct(ActivityLogService $activityLogService)
    {
        $this->activityLogService = $activityLogService;
    }

    public function sendLeaveNotification($leaveRequest, string $type): void
    {
        try {
            $employee = $leaveRequest->employee;
            if (!$employee || !$employee->user) {
                return;
            }

            $messages = [
                'submitted' => 'Your leave request has been submitted and is pending approval.',
                'approved' => 'Your leave request has been approved.',
                'rejected' => 'Your leave request has been rejected.',
                'cancelled' => 'Your leave request has been cancelled.',
            ];

            $message = $messages[$type] ?? 'Leave request status updated.';

            $channels = [];
            if ($employee->user->email) {
                $channels[] = 'mail';
            }

            if (!empty($channels)) {
                $employee->user->notify(new LeaveNotification($leaveRequest, $type, $message));
            }

            if ($type === 'submitted') {
                $manager = $employee->manager;
                if ($manager && $manager->user) {
                    $manager->user->notify(
                        new LeaveNotification($leaveRequest, 'pending_approval', "A leave request from {$employee->full_name} needs your approval.")
                    );
                }
            }

            $this->activityLogService->log('notification', 'send_leave', "Leave {$type} notification sent for request #{$leaveRequest->id}");
        } catch (\Throwable $e) {
            Log::error("Failed to send leave notification: {$e->getMessage()}");
        }
    }

    public function sendOvertimeNotification($overtimeRequest, string $type): void
    {
        try {
            $employee = $overtimeRequest->employee;
            if (!$employee || !$employee->user) {
                return;
            }

            $messages = [
                'submitted' => 'Your overtime request has been submitted and is pending approval.',
                'approved' => 'Your overtime request has been approved.',
                'rejected' => 'Your overtime request has been rejected.',
            ];

            $message = $messages[$type] ?? 'Overtime request status updated.';

            $employee->user->notify(new OvertimeNotification($overtimeRequest, $type, $message));

            if ($type === 'submitted') {
                $manager = $employee->manager;
                if ($manager && $manager->user) {
                    $manager->user->notify(
                        new OvertimeNotification($overtimeRequest, 'pending_approval', "An overtime request from {$employee->full_name} needs your approval.")
                    );
                }
            }

            $this->activityLogService->log('notification', 'send_overtime', "Overtime {$type} notification sent for request #{$overtimeRequest->id}");
        } catch (\Throwable $e) {
            Log::error("Failed to send overtime notification: {$e->getMessage()}");
        }
    }

    public function sendAttendanceReminder($employee): void
    {
        try {
            if (!$employee || !$employee->user) {
                return;
            }

            $employee->user->notify(new AttendanceReminder($employee));

            $this->activityLogService->log('notification', 'attendance_reminder', "Attendance reminder sent to {$employee->full_name}");
        } catch (\Throwable $e) {
            Log::error("Failed to send attendance reminder: {$e->getMessage()}");
        }
    }

    public function sendBulkAttendanceReminder(): int
    {
        $employees = Employee::active()
            ->whereDoesntHave('attendances', function ($query) {
                $query->where('date', now()->toDateString());
            })
            ->get();

        $sent = 0;
        foreach ($employees as $employee) {
            $this->sendAttendanceReminder($employee);
            $sent++;
        }

        return $sent;
    }

    public function notifyApproval(string $type, $request): void
    {
        switch ($type) {
            case 'leave':
                $this->sendLeaveNotification($request, 'approved');
                break;
            case 'overtime':
                $this->sendOvertimeNotification($request, 'approved');
                break;
        }
    }

    public function sendInAppNotification($user, string $title, string $message, ?string $type = 'info'): void
    {
        try {
            $user->notifications()->create([
                'type' => 'App\Notifications\InAppNotification',
                'data' => [
                    'title' => $title,
                    'message' => $message,
                    'type' => $type,
                ],
            ]);
        } catch (\Throwable $e) {
            Log::error("Failed to send in-app notification: {$e->getMessage()}");
        }
    }
}
