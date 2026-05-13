<?php

namespace App\Notifications;

use App\Models\Employee;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AttendanceReminder extends Notification
{
    use Queueable;

    protected Employee $employee;

    public function __construct(Employee $employee)
    {
        $this->employee = $employee;
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Attendance Reminder')
            ->line("Hello {$this->employee->full_name},")
            ->line('You have not yet recorded your attendance for today.')
            ->line('Please check in as soon as possible.')
            ->action('Record Attendance', url('/attendance'));
    }

    public function toArray($notifiable): array
    {
        return [
            'employee_id' => $this->employee->id,
            'employee_name' => $this->employee->full_name,
            'message' => 'Please record your attendance for today.',
        ];
    }
}
