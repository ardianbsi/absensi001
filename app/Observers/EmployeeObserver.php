<?php

namespace App\Observers;

use App\Models\Employee;
use App\Services\ActivityLogService;

class EmployeeObserver
{
    protected ActivityLogService $activityLog;

    public function __construct(ActivityLogService $activityLog)
    {
        $this->activityLog = $activityLog;
    }

    public function created(Employee $employee): void
    {
        $this->activityLog->logModelEvent('employee', $employee->id, 'created', null, $employee->toArray());
    }

    public function updated(Employee $employee): void
    {
        if ($employee->isDirty()) {
            $this->activityLog->logModelEvent('employee', $employee->id, 'updated', $employee->getOriginal(), $employee->getChanges());
        }
    }

    public function deleted(Employee $employee): void
    {
        $this->activityLog->logModelEvent('employee', $employee->id, 'deleted', $employee->toArray(), null);
    }

    public function restored(Employee $employee): void
    {
        $this->activityLog->logModelEvent('employee', $employee->id, 'restored', null, $employee->toArray());
    }

    public function forceDeleted(Employee $employee): void
    {
        $this->activityLog->logModelEvent('employee', $employee->id, 'force_deleted', $employee->toArray(), null);
    }
}
