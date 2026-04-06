<?php

namespace App\Support;

class UpsiStaffEmail
{
    /**
     * UPSI official staff email domains.
     *
     * @return array<int, string>
     */
    public static function domains(): array
    {
        return [
            'upsi.edu.my',
            'fsskj.upsi.edu.my',
            'fbk.upsi.edu.my',
            'fsmt.upsi.edu.my',
            'bendahari.upsi.edu.my',
            'ict.upsi.edu.my',
            'fskik.upsi.edu.my',
            'meta.upsi.edu.my',
            'fpm.upsi.edu.my',
            'fpe.upsi.edu.my',
            'ftv.upsi.edu.my',
            'fmsp.upsi.edu.my',
            'fsk.upsi.edu.my',
            'jpphb.upsi.edu.my',
        ];
    }

    public static function isValid(?string $email): bool
    {
        $value = strtolower(trim((string) $email));
        if ($value === '' || !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        $domain = substr(strrchr($value, '@') ?: '', 1);
        if ($domain === '') {
            return false;
        }

        return in_array($domain, self::domains(), true);
    }

    public static function humanReadableDomains(): string
    {
        return implode(', ', array_map(static fn (string $domain): string => '@' . $domain, self::domains()));
    }
}
