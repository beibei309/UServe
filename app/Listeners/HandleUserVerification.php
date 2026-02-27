<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Verified;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class HandleUserVerification
{
    /**
     * Handle the event.
     */
    public function handle(Verified $event): void
    {
        /** @var \App\Models\User $user */
        $user = $event->user;

        // Auto-approve Students
        if ($user->hu_role === 'student') {
            if (str_ends_with($user->hu_email, '@siswa.upsi.edu.my')) {
                $user->update([
                    'hu_verification_status' => 'approved',
                    'hu_public_verified_at' => now(),
                ]);
            }
        }
        
        // Auto-approve Staff (Community)
        // Check if the user has a staff role (community) and uses a valid UPSI email
           if ($user->hu_role === 'community') {
             $pattern = '/^[a-zA-Z0-9._%+-]+@(upsi\.edu\.my|fsskj\.upsi\.edu\.my|fpm\.upsi\.edu\.my|fsmt\.upsi\.edu\.my|fskik\.upsi\.edu\.my|meta\.upsi\.edu\.my|fbk\.upsi\.edu\.my|fpe\.upsi\.edu\.my|fmsp\.upsi\.edu\.my|ftv\.upsi\.edu\.my|fsk\.upsi\.edu\.my|bendahari\.upsi\.edu\.my|ict\.upsi\.edu\.my)$/';
               $isStaffEmail = preg_match($pattern, $user->hu_email);
             
             if ($isStaffEmail) {
                 $user->update([
                     'hu_verification_status' => 'approved',
                     // 'hu_staff_verified_at' => now(), // Optional, if we want to track staff specifically
                     'hu_public_verified_at' => now(), 
                 ]);
             }
        }
        
        // Community Public: Do NOTHING. They remain 'pending' until Doc Upload + Admin Approve.
    }
}
