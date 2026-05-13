<?php

namespace App\Observers;

use App\Models\Attendance;
use App\Services\ActivityLogService;

class AttendanceObserver
{
    protected ActivityLogService $activityLog;

    public function __construct(ActivityLogService $activityLog)
    {
        $this->activityLog = $activityLog;
    }

    public function created(Attendance $attendance): void
    {
        $action = $attendance->clock_in && !$attendance->clock_out ? 'check_in' : 'check_out';
        $this->activityLog->logAttendance($attendance, $action);
    }

    public function updated(Attendance $attendance): void
    {
        if ($attendance->isDirty('clock_out')) {
            $this->activityLog->logAttendance($attendance, 'check_out');
        }

        if ($attendance->isDirty()) {
            $this->activityLog->logModelEvent('attendance', $attendance->id, 'updated', $attendance->getOriginal(), $attendance->getChanges());
        }
    }

    public function deleted(Attendance $attendance): void
    {
        $this->activityLog->logModelEvent('attendance', $attendance->id, 'deleted', $attendance->toArray(), null);
    }

    public function restored(Attendance $attendance): void
    {
        $this->activityLog->logModelEvent('attendance', $attendance->id, 'restored', null, $attendance->toArray());
    }

    public function forceDeleted(Attendance $attendance): void
    {
        $this->activityLog->logModelEvent('attendance', $attendance->id, 'force_deleted', $attendance->toArray(), null);
    }
}
