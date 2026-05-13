<?php

namespace App\Policies;

use App\Models\LeaveRequest;
use App\Models\User;

class LeaveRequestPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, LeaveRequest $leaveRequest): bool
    {
        if ($user->employee && $leaveRequest->employee_id === $user->employee->id) {
            return true;
        }
        return $user->can('leave-read');
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, LeaveRequest $leaveRequest): bool
    {
        if ($user->employee && $leaveRequest->employee_id === $user->employee->id) {
            return $leaveRequest->status === 'pending';
        }
        return $user->can('leave-update');
    }

    public function delete(User $user, LeaveRequest $leaveRequest): bool
    {
        if ($user->employee && $leaveRequest->employee_id === $user->employee->id) {
            return $leaveRequest->status === 'pending';
        }
        return $user->can('leave-delete');
    }

    public function approve(User $user, LeaveRequest $leaveRequest): bool
    {
        return $user->can('leave-approve');
    }

    public function reject(User $user, LeaveRequest $leaveRequest): bool
    {
        return $user->can('leave-approve');
    }

    public function cancel(User $user, LeaveRequest $leaveRequest): bool
    {
        if ($user->employee && $leaveRequest->employee_id === $user->employee->id) {
            return $leaveRequest->status === 'pending';
        }
        return $user->can('leave-approve');
    }

    public function export(User $user): bool
    {
        return $user->can('leave-read');
    }
}
