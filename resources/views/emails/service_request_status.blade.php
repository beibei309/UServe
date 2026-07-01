@php
    $platformName = upsi2u_platform_name();
    $accentMap = [
        'success' => ['main' => '#10b981', 'dark' => '#047857', 'soft' => '#ecfdf5', 'text' => '#065f46'],
        'error' => ['main' => '#ef4444', 'dark' => '#b91c1c', 'soft' => '#fef2f2', 'text' => '#991b1b'],
        'primary' => ['main' => '#4f46e5', 'dark' => '#3730a3', 'soft' => '#eef2ff', 'text' => '#312e81'],
        'gray' => ['main' => '#64748b', 'dark' => '#334155', 'soft' => '#f1f5f9', 'text' => '#334155'],
    ];
    $accent = $accentMap[$theme] ?? $accentMap['primary'];
    $statusLabel = ucwords(str_replace('_', ' ', $status));
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
</head>
<body style="margin:0; padding:0; background:#f3f6fb; font-family:Arial, Helvetica, sans-serif; color:#172033;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f3f6fb; padding:28px 14px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:640px; background:#ffffff; border-radius:18px; overflow:hidden; box-shadow:0 18px 45px rgba(15,23,42,0.10);">
                    <tr>
                        <td style="padding:0; background:{{ $accent['main'] }};">
                            <div style="padding:26px 30px; background:linear-gradient(135deg, {{ $accent['main'] }} 0%, {{ $accent['dark'] }} 100%);">
                                <p style="margin:0 0 8px; color:rgba(255,255,255,0.78); font-size:12px; font-weight:700; letter-spacing:1.8px; text-transform:uppercase;">
                                    {{ $platformName }} service request
                                </p>
                                <h1 style="margin:0; color:#ffffff; font-size:28px; line-height:1.2; font-weight:800;">
                                    {{ $title }}
                                </h1>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:30px;">
                            <p style="margin:0 0 14px; color:#172033; font-size:16px; line-height:1.6;">
                                Hi {{ $notifiable->hu_name }},
                            </p>
                            <p style="margin:0 0 22px; color:#42526b; font-size:16px; line-height:1.7;">
                                {{ $intro }}
                            </p>

                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border:1px solid #e2e8f0; border-radius:14px; overflow:hidden; margin:0 0 22px;">
                                <tr>
                                    <td style="padding:16px 18px; background:{{ $accent['soft'] }}; border-left:5px solid {{ $accent['main'] }};">
                                        <p style="margin:0; color:{{ $accent['text'] }}; font-size:12px; font-weight:800; letter-spacing:1px; text-transform:uppercase;">
                                            Request details
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:18px;">
                                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td style="padding:8px 0; color:#64748b; font-size:13px; width:130px;">Status</td>
                                                <td style="padding:8px 0; color:#172033; font-size:14px; font-weight:700;">{{ $statusLabel }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding:8px 0; color:#64748b; font-size:13px;">Service</td>
                                                <td style="padding:8px 0; color:#172033; font-size:14px; font-weight:700;">{{ $serviceTitle }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding:8px 0; color:#64748b; font-size:13px;">Seller</td>
                                                <td style="padding:8px 0; color:#172033; font-size:14px; font-weight:700;">{{ $providerName }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding:8px 0; color:#64748b; font-size:13px;">Date</td>
                                                <td style="padding:8px 0; color:#172033; font-size:14px; font-weight:700;">{{ $formattedDate }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding:8px 0; color:#64748b; font-size:13px;">Price</td>
                                                <td style="padding:8px 0; color:#172033; font-size:14px; font-weight:700;">RM{{ $price }}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:0 0 24px; color:#42526b; font-size:15px; line-height:1.7;">
                                {{ $instruction }}
                            </p>

                            <a href="{{ $actionUrl }}" style="display:inline-block; background:#4f46e5; color:#ffffff; text-decoration:none; padding:13px 22px; border-radius:10px; font-size:14px; font-weight:800;">
                                View Request
                            </a>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:20px 30px; background:#f8fafc; border-top:1px solid #e2e8f0;">
                            <p style="margin:0; color:#64748b; font-size:12px; line-height:1.6; text-align:center;">
                                This email was sent by {{ $platformName }}. Please keep important service agreements inside the platform so admin can review the request record if support is needed.
                            </p>
                            <p style="margin:10px 0 0; color:#94a3b8; font-size:12px; text-align:center;">
                                &copy; {{ date('Y') }} UPSI2u | UPSI Service Circle.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
