<?php

namespace App\Notifications;

use App\Models\ServiceRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ServiceRequestStatusUpdated extends Notification
{
    use Queueable;

    public $serviceRequest;
    public $status;

    /**
     * Create a new notification instance.
     */
    public function __construct(ServiceRequest $serviceRequest, string $status)
    {
        $this->serviceRequest = $serviceRequest;
        $this->status = $status;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail']; 
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $message = '';
        $title = 'Request Update';

        switch ($this->status) {
            case 'accepted':
                $title = 'Request Accepted';
                $message = 'Your service request has been accepted by ' . $this->serviceRequest->provider->name;
                break;
            case 'rejected':
                $title = 'Request Rejected';
                $message = 'Your service request was rejected by ' . $this->serviceRequest->provider->name;
                break;
            case 'in_progress':
                $title = 'Service Started';
                $message = 'Your service request is now in progress!';
                break;
            case 'completed':
                $title = 'Service Completed';
                $message = 'Service completed! Please leave a review.';
                break;
            default:
                $message = 'Your service request status has been updated to ' . $this->status;
        }

        return [
            'title' => $title,
            'message' => $message,
            'service_name' => $this->serviceRequest->studentService->title,
            'request_id' => $this->serviceRequest->id,
            'action_url' => route('service-requests.show', $this->serviceRequest->id),
            'status' => $this->status,
            'type' => 'status_update'
        ];
    }

    /**
     * Email notification
     */
    /**
     * Email notification
     */
    public function toMail($notifiable)
    {
        $serviceTitle = $this->serviceRequest->studentService->title;
        $providerName = $this->serviceRequest->provider->name;
        $formattedDate = \Carbon\Carbon::parse($this->serviceRequest->selected_dates)->format('d M Y');
        
        // Tetapkan Subject, Intro Message, dan Next Step mengikut status
        switch ($this->status) {
            case 'accepted':
                $subject = 'Good News: Request Accepted!';
                $intro = "Great news! **{$providerName}** has accepted your request for **{$serviceTitle}**.";
                $instruction = "Please communicate with the provider to discuss further details or wait for the service to start.";
                $color = 'success'; // Hijau
                break;

            case 'rejected':
                $subject = 'Request Update: Rejected';
                $intro = "We are sorry to inform you that **{$providerName}** is unable to accept your request for **{$serviceTitle}** at this time.";
                $instruction = "You may look for other providers offering similar services on our platform.";
                $color = 'error'; // Merah
                break;

            case 'in_progress':
                $subject = 'Service Started: ' . $serviceTitle;
                $intro = "The service **{$serviceTitle}** has been marked as **In Progress**.";
                $instruction = "The provider has started working on your request.";
                $color = 'primary'; // Biru
                break;

            case 'completed':
                $subject = 'Service Completed: ' . $serviceTitle;
                $intro = "The service **{$serviceTitle}** has been marked as **Completed** by the provider.";
                $instruction = "Please log in to confirm the completion and **leave a review** for the student seller. Your feedback helps our community!";
                $color = 'success'; // Hijau
                break;

            case 'cancelled':
                $subject = 'Request Cancelled';
                $intro = "The service request for **{$serviceTitle}** has been cancelled.";
                $instruction = "If this was a mistake, please make a new request.";
                $color = 'gray';
                break;

            default:
                $subject = 'Update on your Service Request';
                $intro = "The status of your request for **{$serviceTitle}** has been updated to **{$this->status}**.";
                $instruction = "Please check your dashboard for more details.";
                $color = 'primary';
        }

        return (new MailMessage)
            ->subject($subject)
            ->greeting('Hi ' . $notifiable->name . ',')
            ->line($intro)
            
            // Paparkan kotak detail ringkas
            ->line('__Request Details:__')
            ->line('Date: ' . $formattedDate)
            ->line('Seller: ' . $providerName)
            ->line('Price: RM' . number_format($this->serviceRequest->offered_price, 2))
            
            ->line($instruction)
            ->action('View Request & Chat', route('service-requests.show', $this->serviceRequest->id))
            ->line('Thank you for using S2U - UPSI Connect.')
            ->salutation('Regards, The S2U Team');
    }
}
