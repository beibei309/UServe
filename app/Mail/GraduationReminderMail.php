<?php

namespace App\Mail;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class GraduationReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $graduationDateDisplay;

    public function __construct(User $user)
    {
        $this->user = $user;
        $graduationDate = $user->studentStatus->graduation_date ?? null;
        $this->graduationDateDisplay = $graduationDate ? Carbon::parse($graduationDate)->format('d M Y') : '';
    }

    public function build()
    {
        return $this->subject('Action Required: Preparing for Graduation')
                    ->view('emails.graduation_reminder'); 
    }
}
