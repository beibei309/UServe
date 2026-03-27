<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Email – U-Serve</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { background-color: #f1f5f9; font-family: 'Segoe UI', Arial, sans-serif; color: #1e293b; }
        .wrapper { max-width: 600px; margin: 40px auto; }
        .card { background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.08); }
        .header { background: linear-gradient(135deg, #4f46e5 0%, #0ea5e9 100%); padding: 40px 40px 32px; text-align: center; }
        .header img { width: 60px; height: 60px; border-radius: 12px; margin-bottom: 16px; }
        .header h1 { color: #ffffff; font-size: 26px; font-weight: 700; letter-spacing: -0.5px; }
        .header p { color: rgba(255,255,255,0.85); font-size: 14px; margin-top: 4px; }
        .body { padding: 40px; }
        .greeting { font-size: 17px; font-weight: 600; color: #0f172a; margin-bottom: 12px; }
        .message { font-size: 14px; color: #475569; line-height: 1.7; margin-bottom: 28px; }
        .btn-wrap { text-align: center; margin-bottom: 28px; }
        .btn { display: inline-block; padding: 14px 40px; background: linear-gradient(135deg, #4f46e5, #0ea5e9); color: #ffffff !important; text-decoration: none; border-radius: 10px; font-size: 15px; font-weight: 700; letter-spacing: 0.3px; }
        .note { background: #f8fafc; border-left: 4px solid #4f46e5; border-radius: 0 8px 8px 0; padding: 14px 16px; font-size: 13px; color: #64748b; line-height: 1.6; margin-bottom: 24px; }
        .link-fallback { font-size: 12px; color: #94a3b8; word-break: break-all; text-align: center; }
        .footer { background: #f8fafc; border-top: 1px solid #e2e8f0; padding: 24px 40px; text-align: center; }
        .footer p { font-size: 12px; color: #94a3b8; line-height: 1.6; }
        .footer a { color: #4f46e5; text-decoration: none; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="card">
            <div class="header">
                <h1>Home2U</h1>
                <p>UPSI Service Circle</p>
            </div>
            <div class="body">
                <p class="greeting">Hi {{ $name }},</p>
                <p class="message">
                    Thank you for registering on <strong>U-Serve</strong>, the official UPSI student service platform.
                    To activate your account and start exploring services, please verify your email address by clicking the button below.
                </p>

                <div class="btn-wrap">
                    <a href="{{ $url }}" class="btn">✅ Verify My Email</a>
                </div>

                <div class="note">
                    ⏳ This link will expire in <strong>60 minutes</strong>. If the button above doesn't work, copy and paste the link below into your browser.
                </div>

                <p class="link-fallback">{{ $url }}</p>
            </div>
            <div class="footer">
                <p>
                    If you did not create an account, you can safely ignore this email.<br>
                    Need help? Contact us at <a href="mailto:noreply@d09103.schtg.com">support@u-serve.upsi.edu.my</a>
                </p>
                <p style="margin-top: 12px;">© {{ date('Y') }} U-Serve · Home2U · UPSI Muallim</p>
            </div>
        </div>
    </div>
</body>
</html>
