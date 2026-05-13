<?php

namespace App\Policies;

use App\Models\Attendance;
use App\Models\User;

class AttendancePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('attendance-read');
    }

    public function view(User $user, Attendance $attendance): bool
    {
        if ($user->employee && $attendance->employee_id === $user->employee->id) {
            return true;
        }
        return $user->can('attendance-read');
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Attendance $attendance): bool
    {
        return $user->can('attendance-update');
    }

    public function delete(User $user, Attendance $attendance): bool
    {
        return $user->can('attendance-delete');
    }

    public function checkIn(User $user): bool
    {
        return true;
    }

    public function checkOut(User $user, Attendance $attendance): bool
    {
        if ($user->employee && $attendance->employee_id === $user->employee->id) {
            return true;
        }
        return $user->can('attendance-update');
    }

    public function export(User $user): bool
    {
        return $user->can('attendance-read');
    }
}
