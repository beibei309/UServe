<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ upsi2u_platform_title() }}</title>
    <link rel="icon" type="image/png" href="{{ upsi2u_platform_favicon_url() }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .upsi-auth {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 18px 46px;
            position: relative;
            overflow: hidden;
            color: #0f172a;
        }

        .upsi-auth video {
            position: fixed;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: -3;
        }

        .upsi-auth::before {
            content: "";
            position: fixed;
            inset: 0;
            z-index: -2;
            background: rgba(239, 246, 255, .78);
            backdrop-filter: blur(4px);
        }

        .upsi-shell {
            width: min(100%, 920px);
            min-height: 560px;
            display: grid;
            grid-template-columns: .85fr 1.15fr;
            overflow: hidden;
            border-radius: 22px;
            background: #fff;
            box-shadow: 0 28px 90px rgba(15, 23, 42, .18);
        }

        .upsi-brand {
            padding: 34px;
            color: #fff;
            background:
                linear-gradient(rgba(255, 255, 255, .08) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, .08) 1px, transparent 1px),
                linear-gradient(145deg, #4f46e5 0%, #4338ca 100%);
            background-size: 74px 74px, 74px 74px, auto;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: 28px;
        }

        .brand-lockup {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .brand-icon {
            width: 46px;
            height: 46px;
            border-radius: 12px;
            background: #fff;
            display: grid;
            place-items: center;
            box-shadow: 0 12px 25px rgba(30, 41, 59, .18);
        }

        .brand-icon img {
            width: 35px;
            height: 35px;
            object-fit: contain;
        }

        .brand-name {
            font-size: 24px;
            line-height: 1;
            font-weight: 800;
            letter-spacing: -.02em;
        }

        .brand-subtitle {
            margin-top: 4px;
            color: rgba(255, 255, 255, .82);
            font-size: 13px;
        }

        .brand-copy h1 {
            max-width: 430px;
            font-size: clamp(30px, 3.2vw, 38px);
            line-height: 1.12;
            font-weight: 800;
            letter-spacing: -.04em;
        }

        .brand-copy p {
            max-width: 410px;
            margin-top: 18px;
            color: rgba(255, 255, 255, .74);
            font-size: 16px;
            line-height: 1.55;
        }

        .brand-note {
            color: rgba(255, 255, 255, .9);
            font-size: 14px;
            font-weight: 600;
        }

        .auth-panel {
            padding: 44px 44px 34px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .pill {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            width: fit-content;
            padding: 6px 12px;
            border-radius: 999px;
            background: #eef2ff;
            color: #1d4ed8;
            font-size: 13px;
            font-weight: 700;
        }

        .pill::before {
            content: "";
            width: 9px;
            height: 9px;
            border-radius: 50%;
            background: #4f46e5;
        }

        .auth-title {
            margin-top: 22px;
            font-size: clamp(34px, 4vw, 42px);
            line-height: 1.05;
            font-weight: 800;
            letter-spacing: -.05em;
            color: #0f172a;
        }

        .auth-desc {
            margin-top: 14px;
            color: #475569;
            font-size: 16px;
            line-height: 1.5;
        }

        .auth-form {
            margin-top: 30px;
        }

        .field {
            margin-bottom: 18px;
        }

        .field label {
            display: block;
            margin-bottom: 10px;
            color: #111827;
            font-size: 14px;
            font-weight: 600;
        }

        .input-wrap {
            position: relative;
        }

        .field input[type="email"],
        .field input[type="password"] {
            width: 100%;
            height: 50px;
            border-radius: 13px;
            border: 1px solid #c7c9dd;
            padding: 0 52px 0 20px;
            outline: none;
            color: #111827;
            font-size: 15px;
            transition: border-color .18s ease, box-shadow .18s ease;
        }

        .field input:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 4px rgba(79, 70, 229, .12);
        }

        .input-icon {
            position: absolute;
            right: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #6b7280;
            font-size: 20px;
            font-weight: 700;
        }

        .auth-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            margin: 4px 0 26px;
            color: #111827;
            font-size: 14px;
        }

        .auth-row label {
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .auth-row input {
            width: 18px;
            height: 18px;
            border-radius: 5px;
        }

        .auth-row a,
        .auth-link {
            color: #1d1fd8;
            font-weight: 600;
            text-decoration: none;
        }

        .auth-primary {
            width: 100%;
            height: 54px;
            border: 0;
            border-radius: 14px;
            background: #4f46e5;
            color: #fff;
            font-size: 19px;
            font-weight: 800;
            cursor: pointer;
            box-shadow: 0 14px 24px rgba(79, 70, 229, .24);
            transition: transform .18s ease, background .18s ease;
        }

        .auth-primary:hover {
            background: #4338ca;
            transform: translateY(-1px);
        }

        .provider-button {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            width: 100%;
            height: 52px;
            margin-top: 26px;
            border: 1px solid #c7c9dd;
            border-radius: 14px;
            color: #111827;
            background: #fff;
            font-size: 16px;
            font-weight: 800;
            text-decoration: none;
        }

        .provider-dot {
            display: grid;
            place-items: center;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: #eef2ff;
            color: #1d4ed8;
            font-size: 14px;
            font-weight: 900;
        }

        .provider-divider {
            display: flex;
            align-items: center;
            gap: 14px;
            margin: 20px 0 0;
            color: #94a3b8;
            font-size: 13px;
            font-weight: 800;
        }

        .provider-divider::before,
        .provider-divider::after {
            content: "";
            flex: 1;
            border-top: 1px solid #d8daea;
        }

        .divider {
            display: flex;
            align-items: center;
            gap: 18px;
            margin: 30px 0 24px;
            color: #c4c4d8;
            font-weight: 700;
        }

        .divider::before,
        .divider::after {
            content: "";
            flex: 1;
            border-top: 1px solid #d8daea;
        }

        .auth-secondary {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            height: 54px;
            border: 1px solid #c7c9dd;
            border-radius: 14px;
            color: #111827;
            font-size: 18px;
            font-weight: 700;
            text-decoration: none;
        }

        .back-link {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 24px;
            color: #111827;
            font-size: 15px;
            text-decoration: none;
        }

        .auth-footer {
            position: absolute;
            bottom: 18px;
            display: flex;
            gap: 24px;
            color: #64748b;
            font-size: 14px;
        }

        .auth-footer a {
            color: #64748b;
            text-decoration: none;
        }

        @media (max-width: 900px) {
            .upsi-auth {
                align-items: flex-start;
                padding: 18px 12px 28px;
            }

            .upsi-shell {
                min-height: auto;
                grid-template-columns: 1fr;
                border-radius: 18px;
            }

            .upsi-brand {
                display: none;
            }

            .auth-panel {
                padding: 28px 20px 24px;
            }

            .auth-title {
                margin-top: 20px;
                font-size: 32px;
            }

            .auth-desc {
                font-size: 15px;
            }

            .auth-form {
                margin-top: 26px;
            }

            .field {
                margin-bottom: 18px;
            }

            .field input[type="email"],
            .field input[type="password"] {
                height: 52px;
                font-size: 15px;
            }

            .auth-row {
                align-items: flex-start;
                flex-direction: column;
                font-size: 14px;
            }

            .auth-primary,
            .auth-secondary {
                height: 56px;
                font-size: 18px;
            }

            .divider {
                margin: 28px 0 22px;
                font-size: 13px;
            }

            .auth-footer {
                display: none;
            }
        }

        @media (max-height: 760px) and (min-width: 901px) {
            .upsi-auth {
                align-items: flex-start;
            }

            .upsi-shell {
                min-height: 520px;
            }

            .brand-copy h1 {
                font-size: 32px;
            }

            .brand-copy p {
                font-size: 15px;
            }

            .auth-panel {
                padding-top: 34px;
                padding-bottom: 28px;
            }
        }
    </style>
</head>

<body>
    <main class="upsi-auth">
        <video autoplay muted loop playsinline>
            <source src="{{ asset('videos/background-myupsi-small.mp4') }}" type="video/mp4">
            Your browser does not support the video tag.
        </video>

        <section class="upsi-shell">
            <aside class="upsi-brand">
                <a href="{{ url('/') }}" class="brand-lockup">
                    <span class="brand-icon">
                        <img src="{{ upsi2u_platform_favicon_url() }}" alt="{{ upsi2u_platform_name() }} Logo">
                    </span>
                    <span>
                        <span class="brand-name">{{ upsi2u_platform_name() }}</span>
                        <span class="brand-subtitle">{{ upsi2u_platform_tagline() }}</span>
                    </span>
                </a>

                <div class="brand-copy">
                    <h1>UPSI student services start here.</h1>
                    <p>Find trusted UPSI student helpers for academic, creative, event, delivery, and daily community needs.</p>
                </div>

                <p class="brand-note">Connecting students and the Muallim community through verified services.</p>
            </aside>

            <section class="auth-panel">
                <span class="pill">{{ upsi2u_platform_tagline() }}</span>
                <h2 class="auth-title">Welcome back</h2>
                <p class="auth-desc">Sign in with your registered email to continue.</p>

                <x-auth-session-status class="mb-4" :status="session('status')" />

                <a href="{{ route('auth.google.redirect') }}" class="provider-button">
                    <span class="provider-dot">G</span>
                    <span>Continue with Google</span>
                </a>

                <div class="provider-divider">or sign in with email</div>

                <form method="POST" action="{{ route('login') }}" class="auth-form">
                    @csrf

                    <div class="field">
                        <label for="email">Email address</label>
                        <div class="input-wrap">
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="you@student.upsi.edu.my">
                            <span class="input-icon">@</span>
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-2 text-xs text-red-500" />
                    </div>

                    <div class="field">
                        <label for="password">Password</label>
                        <div class="input-wrap">
                            <input id="password" type="password" name="password" required autocomplete="current-password" placeholder="Enter your password">
                            <span class="input-icon">◉</span>
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2 text-xs text-red-500" />
                    </div>

                    <div class="auth-row">
                        <label for="remember_me">
                            <input id="remember_me" type="checkbox" name="remember">
                            <span>Remember me</span>
                        </label>

                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}">Forgot password?</a>
                        @endif
                    </div>

                    <button type="submit" class="auth-primary">Sign in</button>

                    <div class="divider">New to {{ upsi2u_platform_name() }}?</div>

                    <a href="{{ route('register') }}" class="auth-secondary">Create an account</a>

                    <a href="{{ url('/') }}" class="back-link">
                        <span>←</span>
                        <span>Back to Home</span>
                    </a>
                </form>
            </section>
        </section>

        <footer class="auth-footer">
            <span>© {{ now()->year }} {{ upsi2u_platform_name() }} Campus Services.</span>
            <a href="{{ route('privacy') }}">Privacy Policy</a>
            <a href="{{ route('terms') }}">Terms of Use</a>
        </footer>
    </main>

    <div id="loginConfig"
        data-session-error="@json(session('error') ?? '')"
        data-email-error="@json($errors->first('email') ?? '')"></div>
    <script src="{{ asset('js/auth-login.js') }}"></script>
</body>

</html>
