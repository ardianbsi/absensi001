<?php

namespace App\Providers;

use App\Models\Attendance;
use App\Models\Employee;
use App\Observers\AttendanceObserver;
use App\Observers\EmployeeObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    public function boot(): void
    {
        Employee::observe(EmployeeObserver::class);
        Attendance::observe(AttendanceObserver::class);
    }

    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
