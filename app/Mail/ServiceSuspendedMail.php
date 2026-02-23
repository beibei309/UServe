<?php

namespace App\Mail;

use App\Models\StudentService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ServiceSuspendedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $service;

    public function __construct(StudentService $service)
    {
        $this->service = $service;
    }

    public function build()
    {
        return $this->subject('Your Service Has Been Suspended')
                    ->markdown('emails.service.suspended');
    }
}
