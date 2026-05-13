<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Spatie\Activitylog\Models\Activity;

class ActivityLogService
{
    public function log(string $type, string $action, string $description, $oldValues = null, $newValues = null): ?Activity
    {
        try {
            $user = Auth::user();

            $properties = [];
            if ($oldValues !== null) {
                $properties['old'] = $oldValues;
            }
            if ($newValues !== null) {
                $properties['new'] = $newValues;
            }

            $activity = activity()
                ->causedBy($user)
                ->withProperties($properties)
                ->event($action)
                ->log($description);

            if ($activity) {
                $activity->update([
                    'type' => $type,
                ]);
            }

            return $activity;
        } catch (\Throwable $e) {
            Log::error("Failed to log activity: {$e->getMessage()}");
            return null;
        }
    }

    public function logLogin($user): void
    {
        $this->log('auth', 'login', "User {$user->name} logged in.", null, [
            'user_id' => $user->id,
            'email' => $user->email,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    public function logLogout($user): void
    {
        $this->log('auth', 'logout', "User {$user->name} logged out.");
    }

    public function logAttendance(Attendance $attendance, string $action): void
    {
        $employee = $attendance->employee;

        $clockIn = $attendance->clock_in ? $attendance->clock_in->format('Y-m-d H:i:s') : '-';
        $clockOut = $attendance->clock_out ? $attendance->clock_out->format('Y-m-d H:i:s') : '-';

        $descriptions = [
            'check_in' => "Employee {$employee->full_name} checked in at {$clockIn}",
            'check_out' => "Employee {$employee->full_name} checked out at {$clockOut}",
            'mark_absent' => "Employee {$employee->full_name} marked as absent",
        ];

        $description = $descriptions[$action] ?? "Attendance {$action} for employee {$employee->full_name}";

        $this->log('attendance', $action, $description, null, $attendance->toArray());
    }

    public function logApproval($request, string $type, string $action): void
    {
        $requestId = $request->id;
        $employeeName = $request->employee?->full_name ?? 'Unknown';

        $descriptions = [
            'leave' => [
                'approved' => "Leave request #{$requestId} for {$employeeName} has been approved.",
                'rejected' => "Leave request #{$requestId} for {$employeeName} has been rejected.",
            ],
            'overtime' => [
                'approved' => "Overtime request #{$requestId} for {$employeeName} has been approved.",
                'rejected' => "Overtime request #{$requestId} for {$employeeName} has been rejected.",
            ],
        ];

        $description = $descriptions[$type][$action] ?? "{$type} request #{$requestId} {$action}";

        $this->log($type, $action, $description, [
            'status' => $request->getOriginal('status'),
        ], [
            'status' => $request->status,
            'approved_by' => Auth::id(),
            'approval_reason' => $request->approval_reason,
        ]);
    }

    public function logModelEvent(string $modelType, $modelId, string $action, $oldValues = null, $newValues = null): void
    {
        $this->log($modelType, $action, "{$modelType} #{$modelId} {$action}", $oldValues, $newValues);
    }

    public function logError(string $type, \Throwable $exception, array $context = []): void
    {
        $this->log($type, 'error', $exception->getMessage(), $context, [
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }

    public function getRecentActivities(int $limit = 20)
    {
        return Activity::with('causer')
            ->latest()
            ->take($limit)
            ->get();
    }

    public function getActivitiesByType(string $type, int $limit = 20)
    {
        return Activity::with('causer')
            ->where('type', $type)
            ->latest()
            ->take($limit)
            ->get();
    }

    public function clearOldLogs(int $days = 90): int
    {
        $cutoff = now()->subDays($days);
        return Activity::where('created_at', '<', $cutoff)->delete();
    }
}
