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
                $message = 'Your service request has been accepted by ' . $this->serviceRequest->provider->hu_name;
                break;
            case 'rejected':
                $title = 'Request Rejected';
                $message = 'Your service request was rejected by ' . $this->serviceRequest->provider->hu_name;
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
            'service_name' => $this->serviceRequest->studentService->hss_title,
            'request_id' => $this->serviceRequest->hsr_id,
            'action_url' => route('service-requests.show', $this->serviceRequest->hsr_id),
            'status' => $this->status,
            'type' => 'status_update'
        ];
    }

    /**
     * Email notification
     */
    public function toMail($notifiable)
    {
        $serviceTitle = $this->serviceRequest->studentService->hss_title;
        $providerName = $this->serviceRequest->provider->hu_name;
        $rawDate = $this->serviceRequest->hsr_selected_dates;
        $date = is_array($rawDate) ? ($rawDate[0] ?? null) : $rawDate;
        $formattedDate = $date
            ? \Carbon\Carbon::parse($date)->format('d M Y')
            : '-';
        
        switch ($this->status) {
            case 'accepted':
                $subject = 'Good News: Request Accepted!';
                $title = 'Request Accepted';
                $intro = "Great news! {$providerName} has accepted your request.";
                $instruction = "Please communicate with the helper to discuss further details or wait for the service to start.";
                $theme = 'success';
                break;

            case 'rejected':
                $subject = 'Request Update: Rejected';
                $title = 'Request Rejected';
                $intro = "{$providerName} is unable to accept your request at this time.";
                $instruction = "You may look for other helpers offering similar services on our platform.";
                $theme = 'error';
                break;

            case 'in_progress':
                $subject = 'Service Started: ' . $serviceTitle;
                $title = 'Service Started';
                $intro = "Your service request is now in progress.";
                $instruction = "The helper has started working on your request.";
                $theme = 'primary';
                break;

            case 'completed':
                $subject = 'Service Completed: ' . $serviceTitle;
                $title = 'Service Completed';
                $intro = "The service has been marked as completed by the helper.";
                $instruction = "Please leave a review for the student seller. Your feedback helps the UPSI2u community choose trusted helpers.";
                $theme = 'success';
                break;

            case 'cancelled':
                $subject = 'Request Cancelled';
                $title = 'Request Cancelled';
                $intro = "This service request has been cancelled.";
                $instruction = "If this was a mistake, please make a new request.";
                $theme = 'gray';
                break;

            default:
                $subject = 'Update on your Service Request';
                $title = 'Request Update';
                $intro = "The status of your request has been updated to " . str_replace('_', ' ', $this->status) . ".";
                $instruction = "Please check your dashboard for more details.";
                $theme = 'primary';
        }

        return (new MailMessage)
            ->subject($subject)
            ->view('emails.service_request_status', [
                'notifiable' => $notifiable,
                'serviceRequest' => $this->serviceRequest,
                'status' => $this->status,
                'theme' => $theme,
                'title' => $title,
                'intro' => $intro,
                'instruction' => $instruction,
                'serviceTitle' => $serviceTitle,
                'providerName' => $providerName,
                'formattedDate' => $formattedDate,
                'price' => number_format((float) $this->serviceRequest->hsr_offered_price, 2),
                'actionUrl' => route('service-requests.show', $this->serviceRequest->hsr_id),
            ]);
    }
}
