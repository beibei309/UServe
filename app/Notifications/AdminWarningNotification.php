<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminWarningNotification extends Notification
{
    use Queueable;

    public $warningCount;
    public $message;

    /**
     * Create a new notification instance.
     */
    public function __construct(int $warningCount, string $message)
    {
        $this->warningCount = $warningCount;
        $this->message = $message;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Admin Warning',
            'message' => $this->message,
            'warning_count' => $this->warningCount,
            'action_url' => '#', 
            'type' => 'admin_warning'
        ];
    }
}
