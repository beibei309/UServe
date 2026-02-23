<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ServiceWarningMail extends Mailable
{
    use Queueable, SerializesModels;

    public $service;
    public $reason;

    // Kita pass data service dan reason masa panggil class ni
    public function __construct($service, $reason)
    {
        $this->service = $service;
        $this->reason = $reason;
    }

    public function build()
    {
        return $this->subject('Action Required: Warning Issued for Your Service')
                    ->view('emails.service_warning'); // Ini nama file view nanti
    }
}