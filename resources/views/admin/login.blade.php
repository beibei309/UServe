<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ upsi2u_platform_title('Admin Login') }}</title>
    <link rel="icon" type="image/png" href="{{ upsi2u_platform_favicon_url() }}">

    @vite(['resources/css/app.css'])

    <style>
        .admin-auth {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 18px 46px;
            position: relative;
            overflow: hidden;
            color: #0f172a;
        }

        .admin-auth video {
            position: fixed;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: -3;
        }

        .admin-auth::before {
            content: "";
            position: fixed;
            inset: 0;
            z-index: -2;
            background: rgba(239, 246, 255, .78);
            backdrop-filter: blur(4px);
        }

        .admin-shell {
            width: min(100%, 920px);
            min-height: 560px;
            display: grid;
            grid-template-columns: .85fr 1.15fr;
            overflow: hidden;
            border-radius: 22px;
            background: #fff;
            box-shadow: 0 28px 90px rgba(15, 23, 42, .18);
        }

        .admin-brand {
            padding: 34px;
            color: #fff;
            background:
                linear-gradient(rgba(255, 255, 255, .08) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, .08) 1px, transparent 1px),
                linear-gradient(145deg, #0f172a 0%, #1e3a8a 100%);
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
            color: inherit;
            text-decoration: none;
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
            display: block;
            font-size: 24px;
            line-height: 1;
            font-weight: 800;
        }

        .brand-subtitle {
            display: block;
            margin-top: 4px;
            color: rgba(255, 255, 255, .78);
            font-size: 13px;
        }

        .brand-copy h1 {
            max-width: 420px;
            font-size: clamp(30px, 3.2vw, 38px);
            line-height: 1.12;
            font-weight: 800;
            letter-spacing: -.04em;
        }

        .brand-copy p {
            max-width: 400px;
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

        .admin-panel {
            padding: 44px 44px 34px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .admin-pill {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            width: fit-content;
            padding: 6px 12px;
            border-radius: 999px;
            background: #e0f2fe;
            color: #0369a1;
            font-size: 13px;
            font-weight: 800;
        }

        .admin-pill::before {
            content: "";
            width: 9px;
            height: 9px;
            border-radius: 50%;
            background: #0284c7;
        }

        .admin-title {
            margin-top: 22px;
            color: #0f172a;
            font-size: clamp(34px, 4vw, 42px);
            line-height: 1.05;
            font-weight: 800;
            letter-spacing: -.05em;
        }

        .admin-desc {
            margin-top: 14px;
            color: #475569;
            font-size: 16px;
            line-height: 1.5;
        }

        .admin-alert {
            margin-top: 22px;
            padding: 14px 16px;
            border-radius: 14px;
            background: #fff1f2;
            border: 1px solid #fecdd3;
            color: #be123c;
            font-size: 14px;
            font-weight: 600;
        }

        .admin-form {
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
            font-weight: 700;
        }

        .input-wrap {
            position: relative;
        }

        .field input[type="email"],
        .field input[type="password"],
        .field input[type="text"] {
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
            border-color: #0284c7;
            box-shadow: 0 0 0 4px rgba(14, 165, 233, .14);
        }

        .input-icon,
        .password-toggle {
            position: absolute;
            right: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #6b7280;
        }

        .password-toggle {
            cursor: pointer;
            user-select: none;
        }

        .admin-row {
            display: flex;
            justify-content: flex-end;
            margin: 4px 0 26px;
            font-size: 14px;
        }

        .admin-row a {
            color: #0369a1;
            font-weight: 700;
            text-decoration: none;
        }

        .admin-primary {
            width: 100%;
            height: 54px;
            border: 0;
            border-radius: 14px;
            background: #0f172a;
            color: #fff;
            font-size: 18px;
            font-weight: 800;
            cursor: pointer;
            box-shadow: 0 14px 24px rgba(15, 23, 42, .2);
            transition: transform .18s ease, background .18s ease;
        }

        .admin-primary:hover {
            background: #1e3a8a;
            transform: translateY(-1px);
        }

        .back-link {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 24px;
            color: #111827;
            font-size: 15px;
            font-weight: 700;
            text-decoration: none;
        }

        .admin-footer {
            position: absolute;
            bottom: 18px;
            display: flex;
            gap: 24px;
            color: #64748b;
            font-size: 14px;
        }

        .admin-footer a {
            color: #64748b;
            text-decoration: none;
        }

        @media (max-width: 900px) {
            .admin-auth {
                align-items: flex-start;
                padding: 18px 12px 28px;
            }

            .admin-shell {
                min-height: auto;
                grid-template-columns: 1fr;
                border-radius: 18px;
            }

            .admin-brand {
                display: none;
            }

            .admin-panel {
                padding: 28px 20px 24px;
            }

            .admin-title {
                margin-top: 20px;
                font-size: 32px;
            }

            .admin-desc {
                font-size: 15px;
            }

            .admin-form {
                margin-top: 26px;
            }

            .field input[type="email"],
            .field input[type="password"],
            .field input[type="text"] {
                height: 52px;
                font-size: 15px;
            }

            .admin-primary {
                height: 56px;
                font-size: 18px;
            }

            .admin-footer {
                display: none;
            }
        }
    </style>
</head>

<body>
    <main class="admin-auth">
        <video autoplay muted loop playsinline>
            <source src="{{ asset('videos/background-myupsi-small.mp4') }}" type="video/mp4">
            Your browser does not support the video tag.
        </video>

        <section class="admin-shell">
            <aside class="admin-brand">
                <a href="{{ url('/') }}" class="brand-lockup">
                    <span class="brand-icon">
                        <img src="{{ upsi2u_platform_favicon_url() }}" alt="{{ upsi2u_platform_name() }} Logo">
                    </span>
                    <span>
                        <span class="brand-name">{{ upsi2u_platform_name() }}</span>
                        <span class="brand-subtitle">Admin console</span>
                    </span>
                </a>

                <div class="brand-copy">
                    <h1>Manage platform trust and operations.</h1>
                    <p>Review services, monitor users, handle reports, and keep the UPSI2u community running safely.</p>
                </div>

                <p class="brand-note">Restricted access for authorised UPSI2u administrators.</p>
            </aside>

            <section class="admin-panel">
                <span class="admin-pill">Admin console</span>
                <h2 class="admin-title">Welcome back</h2>
                <p class="admin-desc">Sign in with your administrator account to continue.</p>

                @if ($errors->any())
                    <div class="admin-alert">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                @if(session('error'))
                    <div class="admin-alert">{{ session('error') }}</div>
                @endif

                <form method="POST" action="{{ route('admin.login.submit') }}" class="admin-form">
                    @csrf

                    <div class="field">
                        <label for="email">Email address</label>
                        <div class="input-wrap">
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="admin@upsi2u.com">
                            <span class="input-icon">@</span>
                        </div>
                        @error('email')
                            <p class="mt-2 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="field">
                        <label for="password">Password</label>
                        <div class="input-wrap">
                            <input id="password" type="password" name="password" required autocomplete="current-password" placeholder="Enter your password">
                            <button type="button" id="passwordToggle" class="password-toggle" aria-label="Show or hide password">
                                <svg id="eyeIcon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </button>
                        </div>
                        @error('password')
                            <p class="mt-2 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="admin-row">
                        <a href="{{ route('password.request') }}">Forgot password?</a>
                    </div>

                    <button type="submit" class="admin-primary">Sign in</button>

                    <a href="{{ url('/') }}" class="back-link">
                        <span>&larr;</span>
                        <span>Back to Home</span>
                    </a>
                </form>
            </section>
        </section>

        <footer class="admin-footer">
            <span>&copy; {{ now()->year }} {{ upsi2u_platform_name() }} Admin.</span>
            <a href="{{ route('privacy') }}">Privacy Policy</a>
            <a href="{{ route('terms') }}">Terms of Use</a>
        </footer>
    </main>

    <script src="{{ asset('js/admin-login.js') }}"></script>
</body>

</html>
