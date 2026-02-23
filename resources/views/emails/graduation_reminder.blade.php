@component('mail::message')
# Hello {{ $user->name }},

We noticed that your graduation date is approaching on **{{ \Carbon\Carbon::parse($user->studentStatus->graduation_date)->format('d M Y') }}**.

As you prepare for this exciting milestone, please remember to **wrap up your active services** on the platform. This ensures a smooth transition for your current clients.

@component('mail::button', ['url' => route('dashboard')])
Manage My Services
@endcomponent

Congratulations in advance!

Thanks,<br>
{{ config('app.name') }} Admin
@endcomponent