<?php

namespace App\Notifications;

use App\Models\ServiceRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewServiceRequest extends Notification
{
    use Queueable;

    public $serviceRequest;

    /**
     * Create a new notification instance.
     */
    public function __construct(ServiceRequest $serviceRequest)
    {
        $this->serviceRequest = $serviceRequest;
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
            'title' => 'New Service Request',
            'message' => 'You have received a new service request from ' . $this->serviceRequest->requester->name,
            'service_name' => $this->serviceRequest->studentService->title,
            'request_id' => $this->serviceRequest->id,
            'action_url' => route('service-requests.show', $this->serviceRequest->id),
            'type' => 'new_request'
        ];
    }
}
