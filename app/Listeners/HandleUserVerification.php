<?php

namespace App\Listeners;

use App\Support\UpsiStaffEmail;
use Illuminate\Auth\Events\Verified;

class HandleUserVerification
{
    /**
     * Handle the event.
     */
    public function handle(Verified $event): void
    {
        /** @var \App\Models\User $user */
        $user = $event->user;
        $email = strtolower(trim((string) ($user->hu_email ?? '')));

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return;
        }

        $updates = [];

        if ($user->hu_role === 'student' && str_ends_with($email, '@siswa.upsi.edu.my')) {
            $updates['hu_verification_status'] = 'approved';
            $updates['hu_public_verified_at'] = now();
        }

        if ($this->isUpsiStaffDomainEmail($email)) {
            if (empty($user->hu_staff_email)) {
                $updates['hu_staff_email'] = $email;
            }

            $updates['hu_staff_verified_at'] = now();
            $updates['hu_verification_status'] = 'approved';
            $updates['hu_public_verified_at'] = now();
        }

        if (!empty($updates)) {
            $user->forceFill($updates)->save();
        }

        $this->triggerPermissionSync($user);
    }

    private function isUpsiStaffDomainEmail(string $email): bool
    {
        return UpsiStaffEmail::isValid($email);
    }

    private function triggerPermissionSync(object $user): void
    {
        if (method_exists($user, 'syncPermissionsAfterVerification')) {
            $user->syncPermissionsAfterVerification();
            return;
        }

        if (method_exists($user, 'syncRolesAfterVerification')) {
            $user->syncRolesAfterVerification();
            return;
        }
    }
}
