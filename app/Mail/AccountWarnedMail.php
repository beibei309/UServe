<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use App\Models\ServiceRequest;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class AccountWarnedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $reason;

    public function __construct(User $user, $reason)
    {
        $this->user = $user;
        $this->reason = $reason;
    }

    public function build()
    {
        return $this->subject('Account Warning Issued')
                    ->view('emails.account_warned'); // You will need to create this view
    }
}