<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class SellerStatusRevokedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Your Seller Status Has Been Revoked')
            ->greeting('Hello ' . $notifiable->hu_name . ',')
            ->line('Your seller status on the platform has been revoked by the admin. You are now a regular student and can no longer offer services as a seller.')
            ->line('If you believe this is a mistake, please contact support.');
    }

    public function toArray($notifiable)
    {
        return [
            'message' => 'Your seller status has been revoked. You are now a regular student.'
        ];
    }
}
