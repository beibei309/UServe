<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ upsi2u_platform_title('Register') }}</title>
    <link rel="icon" type="image/png" href="{{ upsi2u_platform_favicon_url() }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .hidden {
            display: none !important;
        }

        .upsi-register {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 18px;
            position: relative;
            overflow: hidden;
            color: #0f172a;
        }

        .upsi-register video {
            position: fixed;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: -3;
        }

        .upsi-register::before {
            content: "";
            position: fixed;
            inset: 0;
            z-index: -2;
            background: rgba(239, 246, 255, .72);
            backdrop-filter: blur(5px);
        }

        .register-shell {
            width: min(100%, 980px);
            display: grid;
            grid-template-columns: .75fr 1.25fr;
            overflow: hidden;
            border-radius: 22px;
            background: #fff;
            box-shadow: 0 28px 90px rgba(15, 23, 42, .18);
        }

        .register-brand {
            padding: 34px;
            background: #eef2ff;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: 28px;
        }

        .brand-lockup {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .brand-icon {
            width: 46px;
            height: 46px;
            border-radius: 12px;
            background: #fff;
            display: grid;
            place-items: center;
            box-shadow: 0 10px 24px rgba(15, 23, 42, .08);
        }

        .brand-icon img {
            width: 35px;
            height: 35px;
            object-fit: contain;
        }

        .brand-title {
            color: #1d1fd8;
            font-size: 21px;
            line-height: 1;
            font-weight: 800;
            letter-spacing: -.02em;
        }

        .register-brand h1 {
            max-width: 360px;
            font-size: clamp(28px, 3.2vw, 36px);
            line-height: 1.16;
            font-weight: 800;
            letter-spacing: -.04em;
        }

        .register-brand p {
            max-width: 355px;
            margin-top: 16px;
            color: #334155;
            font-size: 16px;
            line-height: 1.45;
        }

        .quote-card {
            max-width: 360px;
            padding: 22px;
            border-radius: 18px;
            background: rgba(255, 255, 255, .64);
            box-shadow: inset 0 0 0 1px rgba(255, 255, 255, .65);
        }

        .stars {
            color: #eab308;
            font-size: 20px;
            letter-spacing: 6px;
        }

        .quote-card blockquote {
            margin: 16px 0 14px;
            color: #334155;
            font-size: 15px;
            line-height: 1.5;
            font-style: italic;
        }

        .quote-card cite {
            color: #111827;
            font-size: 14px;
            font-style: normal;
            font-weight: 800;
        }

        .register-panel {
            padding: 38px 46px 34px;
        }

        .top-link {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 14px;
        }

        .top-link a,
        .signin-line a {
            color: #1d1fd8;
            font-weight: 700;
            text-decoration: none;
        }

        .eyebrow {
            color: #1d1fd8;
            font-size: 14px;
            font-weight: 800;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .register-panel h2 {
            margin-top: 10px;
            color: #0f172a;
            font-size: clamp(30px, 3.6vw, 38px);
            line-height: 1.05;
            font-weight: 800;
            letter-spacing: -.04em;
        }

        .register-panel .intro {
            margin-top: 6px;
            color: #475569;
            font-size: 16px;
        }

        .register-form {
            margin-top: 24px;
        }

        .form-field {
            margin-bottom: 16px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }

        .form-field label,
        .form-label {
            display: block;
            margin-bottom: 10px;
            color: #111827;
            font-size: 14px;
            font-weight: 600;
        }

        .form-field input[type="text"],
        .form-field input[type="email"],
        .form-field input[type="tel"],
        .form-field input[type="password"] {
            width: 100%;
            height: 50px;
            border-radius: 13px;
            border: 1px solid #c7c9dd;
            padding: 0 18px;
            outline: none;
            color: #111827;
            font-size: 15px;
            transition: border-color .18s ease, box-shadow .18s ease;
        }

        .form-field input:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 4px rgba(79, 70, 229, .12);
        }

        .role-tabs {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0;
            padding: 5px;
            border-radius: 13px;
            background: #eef2ff;
        }

        .role-tab {
            border-radius: 10px;
            padding: 12px 10px;
            text-align: center;
            color: #111827;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: background .18s ease, color .18s ease, box-shadow .18s ease;
        }

        .role-tab.is-active {
            background: #3730a3;
            color: #fff;
            box-shadow: 0 10px 20px rgba(55, 48, 163, .18);
        }

        .role-check {
            display: none;
        }

        .role-section {
            margin-bottom: 16px;
            padding: 16px;
            border-radius: 16px;
            background: #eef2ff;
            color: #312e81;
            font-size: 13px;
            line-height: 1.5;
        }

        .role-section strong {
            display: block;
            margin-bottom: 10px;
            color: #312e81;
            font-size: 12px;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .community-options {
            display: flex;
            flex-wrap: wrap;
            gap: 18px;
            margin-bottom: 12px;
        }

        .community-options label,
        .terms-row {
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }

        .terms-row {
            margin: 4px 0 22px;
            color: #475569;
            font-size: 14px;
            line-height: 1.45;
        }

        .terms-row a {
            color: #1d1fd8;
            font-weight: 700;
            text-decoration: none;
        }

        .register-primary {
            width: 100%;
            height: 54px;
            border: 0;
            border-radius: 14px;
            background: #3730a3;
            color: #fff;
            font-size: 19px;
            font-weight: 800;
            cursor: pointer;
            box-shadow: 0 16px 28px rgba(55, 48, 163, .25);
            transition: transform .18s ease, background .18s ease;
        }

        .register-primary:hover {
            background: #312e81;
            transform: translateY(-1px);
        }

        .signin-line {
            margin-top: 22px;
            text-align: center;
            color: #111827;
            font-size: 15px;
        }

        @media (max-width: 960px) {
            .upsi-register {
                align-items: flex-start;
                padding: 18px 12px 28px;
            }

            .register-shell {
                grid-template-columns: 1fr;
                border-radius: 18px;
            }

            .register-brand {
                padding: 24px;
                gap: 18px;
            }

            .register-brand h1,
            .quote-card {
                display: none;
            }

            .register-brand p {
                margin-top: 10px;
                font-size: 15px;
            }

            .register-panel {
                padding: 28px 20px 24px;
            }

            .top-link {
                justify-content: flex-start;
            }

            .register-panel h2 {
                font-size: 32px;
            }

            .register-panel .intro {
                font-size: 15px;
            }

            .form-grid {
                grid-template-columns: 1fr;
                gap: 0;
            }

            .form-field {
                margin-bottom: 18px;
            }

            .form-field input[type="text"],
            .form-field input[type="email"],
            .form-field input[type="tel"],
            .form-field input[type="password"] {
                height: 52px;
                font-size: 15px;
            }

            .role-tab {
                font-size: 14px;
                padding: 12px 8px;
            }

            .register-primary {
                height: 56px;
                font-size: 18px;
            }
        }
    </style>
</head>

<body>
    <main class="upsi-register">
        <video autoplay muted loop playsinline>
            <source src="{{ asset('videos/background-myupsi-small.mp4') }}" type="video/mp4">
            Your browser does not support the video tag.
        </video>

        <section class="register-shell">
            <aside class="register-brand">
                <a href="{{ url('/') }}" class="brand-lockup">
                    <span class="brand-icon">
                        <img src="{{ upsi2u_platform_favicon_url() }}" alt="{{ upsi2u_platform_name() }} Logo">
                    </span>
                    <span class="brand-title">{{ upsi2u_platform_tagline() }}</span>
                </a>

                <div>
                    <h1>Start your journey with us.</h1>
                    <p>Access student services, trusted campus support, and community help in one unified platform.</p>
                </div>

                <div class="quote-card">
                    <div class="stars">★★★★★</div>
                    <blockquote>"{{ upsi2u_platform_name() }} makes it easier to find student help and support the UPSI community."</blockquote>
                    <cite>UPSI2u Community</cite>
                </div>
            </aside>

            <section class="register-panel">
                <div class="top-link">
                    <a href="{{ url('/') }}">← Back to Home</a>
                </div>

                <div class="eyebrow">{{ upsi2u_platform_tagline() }}</div>
                <h2>Create an account</h2>
                <p class="intro">Enter your details below to get started.</p>

                <form method="POST" action="{{ route('register') }}" class="register-form" id="registerForm">
                    @csrf

                    @if($errors->has('registration'))
                        <div class="role-section" style="background:#fff1f2;color:#be123c;border:1px solid #fecdd3;">
                            <strong style="color:#be123c;">Registration failed</strong>
                            {{ $errors->first('registration') }}
                        </div>
                    @endif

                    <div class="form-field">
                        <span class="form-label">Account type</span>
                        <div class="role-tabs">
                            <label class="role-tab {{ $registerUi['is_student_selected'] ? 'is-active' : '' }}"
                                data-role-card="student"
                                data-active-class="is-active"
                                data-inactive-class="">
                                <input type="radio" name="role" value="student" class="hidden" {{ $registerUi['is_student_selected'] ? 'checked' : '' }}>
                                Student
                                <span data-role-check="student" class="role-check {{ $registerUi['is_student_selected'] ? '' : 'hidden' }}"></span>
                            </label>

                            <label class="role-tab {{ $registerUi['is_community_selected'] ? 'is-active' : '' }}"
                                data-role-card="community"
                                data-active-class="is-active"
                                data-inactive-class="">
                                <input type="radio" name="role" value="community" class="hidden" {{ $registerUi['is_community_selected'] ? 'checked' : '' }}>
                                Community Member
                                <span data-role-check="community" class="role-check {{ $registerUi['is_community_selected'] ? '' : 'hidden' }}"></span>
                            </label>
                        </div>
                        <x-input-error :messages="$errors->get('role')" class="mt-2 text-xs text-red-500" />
                    </div>

                    <div class="form-field">
                        <label for="name">Full name</label>
                        <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" placeholder="Enter your full name">
                        <x-input-error :messages="$errors->get('name')" class="mt-2 text-xs text-red-500" />
                    </div>

                    <div class="form-field">
                        <label for="email">Email address</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" placeholder="you@student.upsi.edu.my">
                        <x-input-error :messages="$errors->get('email')" class="mt-2 text-xs text-red-500" />
                    </div>

                    <div data-role-section="student" class="role-section {{ $registerUi['show_student_section'] ? '' : 'hidden' }}">
                        <strong>Student verification</strong>
                        <div class="form-field" style="margin-bottom:12px;">
                            <label for="student_id">Student ID</label>
                            <input id="student_id" type="text" name="student_id" value="{{ old('student_id') }}" placeholder="e.g. D20231234567">
                            <x-input-error :messages="$errors->get('student_id')" class="mt-2 text-xs text-red-500" />
                        </div>
                        Use your <b>@siswa.upsi.edu.my</b> email for faster student processing.
                    </div>

                    <div class="form-field">
                        <label for="phone">Phone number</label>
                        <input id="phone" type="tel" name="phone" value="{{ old('phone') }}" required placeholder="0123456789">
                        <x-input-error :messages="$errors->get('phone')" class="mt-2 text-xs text-red-500" />
                    </div>

                    <div data-role-section="community" class="role-section {{ $registerUi['show_community_section'] ? '' : 'hidden' }}" style="background:#fef9c3;color:#854d0e;">
                        <strong style="color:#854d0e;">Community details</strong>
                        <div class="community-options">
                            <label>
                                <input type="radio" name="community_type" value="public" {{ $registerUi['initial_community_type'] === 'public' ? 'checked' : '' }}>
                                <span>Public user</span>
                            </label>
                            <label>
                                <input type="radio" name="community_type" value="staff" {{ $registerUi['initial_community_type'] === 'staff' ? 'checked' : '' }}>
                                <span>UPSI staff</span>
                            </label>
                        </div>
                        <span data-community-message="staff" class="{{ $registerUi['initial_community_type'] === 'staff' ? '' : 'hidden' }}">Staff: Use your <b>@upsi.edu.my</b> email above for auto-verification.</span>
                        <span data-community-message="public" class="{{ $registerUi['initial_community_type'] === 'public' ? '' : 'hidden' }}">Public: Upload your verification document after registration so you can request services.</span>
                    </div>

                    <div class="form-grid">
                        <div class="form-field">
                            <label for="password">Password</label>
                            <input id="password" type="password" name="password" required autocomplete="new-password" placeholder="Enter password">
                            <x-input-error :messages="$errors->get('password')" class="mt-2 text-xs text-red-500" />
                        </div>
                        <div class="form-field">
                            <label for="password_confirmation">Confirm password</label>
                            <input id="password_confirmation" type="password" name="password_confirmation" required placeholder="Re-enter password">
                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-xs text-red-500" />
                        </div>
                    </div>

                    <label for="terms" class="terms-row">
                        <input id="terms" type="checkbox" required>
                        <span>I agree to the <a href="{{ route('terms') }}">Terms</a> and <a href="{{ route('privacy') }}">Privacy Policy</a>.</span>
                    </label>

                    <button type="submit" class="register-primary">Create account</button>

                    <p class="signin-line">Already have an account? <a href="{{ route('login') }}">Sign in</a></p>
                </form>
            </section>
        </section>
    </main>

    <div id="registerConfig"
        data-initial-role="{{ $registerUi['initial_role'] }}"
        data-initial-community-type="{{ $registerUi['initial_community_type'] }}"></div>
    <script src="{{ asset('js/nonadmin-auth-register.js') }}"></script>
</body>

</html>
