<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class StaffEmailVerificationNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly User $user) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $verificationUrl = URL::temporarySignedRoute(
            'profile.staff.verify',
            now()->addMinutes(60),
            [
                'id' => $this->user->hu_id,
                'hash' => sha1(strtolower((string) $this->user->hu_staff_email)),
            ]
        );

        return (new MailMessage)
            ->subject('Verify your UPSI staff email')
            ->greeting('Hello '.($this->user->hu_name ?? 'there').',')
            ->line('Please verify this staff email address to continue using staff-restricted features.')
            ->line('This verification link expires in 60 minutes.')
            ->action('Verify Staff Email', $verificationUrl)
            ->line('If you did not request this change, please ignore this email.');
    }
}
