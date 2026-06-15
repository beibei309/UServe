<?php

if (! function_exists('upsi2u_avatar_url')) {
    function upsi2u_avatar_url(?string $name = 'User'): string
    {
        $name = trim((string) $name);

        if ($name === '') {
            $name = 'User';
        }

        return route('avatar.initials', ['name' => $name]);
    }
}
