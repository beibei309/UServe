@component('mail::message')
# Account Blacklisted

Hello {{ $user->name }},

We are writing to inform you that your account on the S2U platform has been blacklisted.

**Reason for Blacklist:**
> {{ $reason }}

If you believe this is a mistake, please contact our support team.

Thanks,<br>
{{ config('app.name') }} Admin Team
@endcomponent