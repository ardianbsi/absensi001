<?php

namespace App\Policies;

use App\Models\OvertimeRequest;
use App\Models\User;

class OvertimeRequestPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, OvertimeRequest $overtimeRequest): bool
    {
        if ($user->employee && $overtimeRequest->employee_id === $user->employee->id) {
            return true;
        }
        return $user->can('overtime-read');
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, OvertimeRequest $overtimeRequest): bool
    {
        if ($user->employee && $overtimeRequest->employee_id === $user->employee->id) {
            return $overtimeRequest->status === 'pending';
        }
        return $user->can('overtime-update');
    }

    public function delete(User $user, OvertimeRequest $overtimeRequest): bool
    {
        if ($user->employee && $overtimeRequest->employee_id === $user->employee->id) {
            return $overtimeRequest->status === 'pending';
        }
        return $user->can('overtime-delete');
    }

    public function approve(User $user, OvertimeRequest $overtimeRequest): bool
    {
        return $user->can('overtime-approve');
    }

    public function reject(User $user, OvertimeRequest $overtimeRequest): bool
    {
        return $user->can('overtime-approve');
    }

    public function cancel(User $user, OvertimeRequest $overtimeRequest): bool
    {
        if ($user->employee && $overtimeRequest->employee_id === $user->employee->id) {
            return $overtimeRequest->status === 'pending';
        }
        return $user->can('overtime-approve');
    }

    public function export(User $user): bool
    {
        return $user->can('overtime-read');
    }
}
