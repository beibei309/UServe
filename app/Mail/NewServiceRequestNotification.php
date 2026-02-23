<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewServiceRequestNotification extends Mailable
{
    public $serviceRequest;
    public $recipientRole;

    public function __construct($serviceRequest, $recipientRole)
    {
        $this->serviceRequest = $serviceRequest;
        $this->recipientRole = $recipientRole;
    }

    public function build()
    {
        return $this->subject('New Service Request Notification')
                    ->view('emails.service_request');
    }
}
