<?php

namespace App\Notifications;

use App\Models\LeaveRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LeaveNotification extends Notification
{
    use Queueable;

    protected LeaveRequest $leaveRequest;
    protected string $type;
    protected string $message;

    public function __construct(LeaveRequest $leaveRequest, string $type, string $message)
    {
        $this->leaveRequest = $leaveRequest;
        $this->type = $type;
        $this->message = $message;
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Leave Request {$this->type}")
            ->line($this->message)
            ->line("Employee: {$this->leaveRequest->employee?->full_name}")
            ->line("Type: {$this->leaveRequest->leaveType?->name}")
            ->line("Dates: {$this->leaveRequest->start_date} to {$this->leaveRequest->end_date}")
            ->line("Total Days: {$this->leaveRequest->total_days}")
            ->action('View Leave Request', url("/leave-requests/{$this->leaveRequest->id}"));
    }

    public function toArray($notifiable): array
    {
        return [
            'leave_request_id' => $this->leaveRequest->id,
            'type' => $this->type,
            'message' => $this->message,
            'employee_name' => $this->leaveRequest->employee?->full_name,
            'leave_type' => $this->leaveRequest->leaveType?->name,
        ];
    }
}
