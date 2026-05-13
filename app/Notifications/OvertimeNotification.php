<?php

namespace App\Notifications;

use App\Models\OvertimeRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OvertimeNotification extends Notification
{
    use Queueable;

    protected OvertimeRequest $overtimeRequest;
    protected string $type;
    protected string $message;

    public function __construct(OvertimeRequest $overtimeRequest, string $type, string $message)
    {
        $this->overtimeRequest = $overtimeRequest;
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
            ->subject("Overtime Request {$this->type}")
            ->line($this->message)
            ->line("Employee: {$this->overtimeRequest->employee?->full_name}")
            ->line("Date: {$this->overtimeRequest->date}")
            ->line("Hours: {$this->overtimeRequest->total_hours}")
            ->action('View Overtime Request', url("/overtime-requests/{$this->overtimeRequest->id}"));
    }

    public function toArray($notifiable): array
    {
        return [
            'overtime_request_id' => $this->overtimeRequest->id,
            'type' => $this->type,
            'message' => $this->message,
            'employee_name' => $this->overtimeRequest->employee?->full_name,
        ];
    }
}
