@component('mail::message')
# Your Account Has Been Re-Activated

Hello **{{ $user->hu_name }}**, 

Good news! We’re happy to inform you that the restriction (blacklist) on your account has been lifted. You can now access the **S2U / UpsiConnect** platform again.

@component('mail::button', ['url' => config('app.url') . '/login'])
Login Now
@endcomponent

If you have any questions, please feel free to contact our support team.

Thank you,<br>
**{{ config('app.name') }} Admin Team**
@endcomponent
