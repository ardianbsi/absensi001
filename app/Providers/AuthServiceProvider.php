<?php

namespace App\Providers;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\OvertimeRequest;
use App\Policies\AttendancePolicy;
use App\Policies\EmployeePolicy;
use App\Policies\LeaveRequestPolicy;
use App\Policies\OvertimeRequestPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Employee::class => EmployeePolicy::class,
        Attendance::class => AttendancePolicy::class,
        LeaveRequest::class => LeaveRequestPolicy::class,
        OvertimeRequest::class => OvertimeRequestPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
