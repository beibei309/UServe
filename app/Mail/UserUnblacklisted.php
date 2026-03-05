<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserUnblacklisted extends Mailable
{
    use Queueable, SerializesModels;
    public $user;
    public $reason;

    public function __construct($user)
    {
    $this->user = $user;
    }

    public function build()
    {
        return $this->subject('Important: Your account has been reactivated')
                    ->view('emails.unblacklisted');
    }
}
