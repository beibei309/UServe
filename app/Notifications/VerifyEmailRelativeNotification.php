<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\URL;

class VerifyEmailRelativeNotification extends VerifyEmail
{
    protected function verificationUrl($notifiable): string
    {
        // Build relative signed path (only the path + query, no host)
        $signedRelativePath = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes((int) config('auth.verification.expire', 60)),
            [
                'id'   => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ],
            absolute: false
        );

        // Use the actual incoming request host so the link works on any server
        // (dev: 127.0.0.1:8000, live: 10.99.4.79:8145, or any future domain)
        $base = request()->getSchemeAndHttpHost();

        return rtrim($base, '/') . '/' . ltrim($signedRelativePath, '/');
    }

    public function toMail($notifiable): MailMessage
    {
        $verificationUrl = $this->verificationUrl($notifiable);

        if (static::$toMailCallback) {
            return call_user_func(static::$toMailCallback, $notifiable, $verificationUrl);
        }

        // Use the branded Blade template
        return (new MailMessage)
            ->subject('Verify Your Email – U-Serve')
            ->view('emails.verify-email', [
                'url'  => $verificationUrl,
                'name' => $notifiable->hu_name ?? 'User',
            ]);
    }
}
