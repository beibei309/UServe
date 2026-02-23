@component('mail::message')
# Service Suspended

Hi {{ $service->user->name }},

We would like to inform you that your service,
“{{ $service->title }}”, has been temporarily suspended by the administrator.

This decision was made after multiple warnings and concerns were raised regarding this service. During the suspension period, your service will not be visible or accessible to users.

If you believe this action was taken in error or you would like to appeal, please contact the administrator or submit an explanation through the platform. We encourage you to review and improve your service to ensure it complies with our platform guidelines.

Thank you for your understanding and cooperation.

Best regards,  
{{ config('app.name') }}
@endcomponent
