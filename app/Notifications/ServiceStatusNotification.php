<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ServiceStatusNotification extends Notification
{
    use Queueable;

    public $type;
    public $service;
    public $reason;

    public function __construct($type, $service, $reason = null)
    {
        $this->type = $type; // 'approved', 'rejected', 'warning', 'blocked'
        $this->service = $service;
        $this->reason = $reason;
    }

    // We only use 'database' here because we will send email manually in the controller
    // to keep it consistent with your existing warning logic.
    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        $messages = [
            'approved' => "Your service '{$this->service->title}' has been approved.",
            'rejected' => "Your service '{$this->service->title}' has been rejected.",
            'warning'  => "Warning received for '{$this->service->title}': {$this->reason}",
            'blocked'  => "Your service '{$this->service->title}' has been blocked due to multiple warnings."
        ];

        return [
            'service_id' => $this->service->id,
            'title'      => ucfirst($this->type) . ' Update',
            'message'    => $messages[$this->type] ?? 'Status updated',
            'url' => url('/'),
            'created_at' => now(),
        ];
    }
}