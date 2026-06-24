<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ upsi2u_platform_title('Complete Google Sign In') }}</title>
    <link rel="icon" type="image/png" href="{{ upsi2u_platform_favicon_url() }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .google-complete {
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 24px 16px;
            background: #eef6ff;
            color: #0f172a;
        }

        .complete-card {
            width: min(100%, 520px);
            padding: 34px;
            border-radius: 20px;
            background: #fff;
            box-shadow: 0 24px 70px rgba(15, 23, 42, .15);
        }

        .brand-row {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 24px;
        }

        .brand-row img {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            object-fit: contain;
            box-shadow: 0 10px 24px rgba(15, 23, 42, .08);
        }

        .brand-row strong {
            display: block;
            font-size: 18px;
            font-weight: 800;
        }

        .brand-row span {
            display: block;
            color: #64748b;
            font-size: 13px;
        }

        h1 {
            margin: 0;
            font-size: 30px;
            line-height: 1.1;
            font-weight: 800;
        }

        .intro {
            margin: 12px 0 26px;
            color: #475569;
            line-height: 1.55;
        }

        .google-account {
            margin-bottom: 22px;
            padding: 14px 16px;
            border-radius: 14px;
            background: #f8fafc;
            color: #334155;
            font-size: 14px;
            word-break: break-word;
        }

        .field {
            margin-bottom: 18px;
        }

        .field label,
        .terms-row {
            display: block;
            margin-bottom: 10px;
            color: #111827;
            font-size: 14px;
            font-weight: 700;
        }

        .field input {
            width: 100%;
            height: 52px;
            border: 1px solid #c7c9dd;
            border-radius: 14px;
            padding: 0 18px;
            color: #111827;
            outline: none;
        }

        .field input:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 4px rgba(79, 70, 229, .12);
        }

        .terms-row {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            color: #475569;
            font-weight: 500;
            line-height: 1.45;
        }

        .terms-row input {
            margin-top: 2px;
            width: 18px;
            height: 18px;
        }

        .terms-row a {
            color: #1d1fd8;
            font-weight: 700;
            text-decoration: none;
        }

        .complete-primary {
            width: 100%;
            height: 54px;
            margin-top: 8px;
            border: 0;
            border-radius: 14px;
            background: #4f46e5;
            color: #fff;
            font-size: 18px;
            font-weight: 800;
            cursor: pointer;
            box-shadow: 0 14px 24px rgba(79, 70, 229, .24);
        }

        .back-link {
            display: block;
            margin-top: 18px;
            text-align: center;
            color: #475569;
            text-decoration: none;
            font-weight: 700;
        }

        @media (max-width: 560px) {
            .complete-card {
                padding: 26px 20px;
                border-radius: 18px;
            }

            h1 {
                font-size: 26px;
            }
        }
    </style>
</head>

<body>
    <main class="google-complete">
        <section class="complete-card">
            <div class="brand-row">
                <img src="{{ upsi2u_platform_favicon_url() }}" alt="{{ upsi2u_platform_name() }} Logo">
                <span>
                    <strong>{{ upsi2u_platform_name() }}</strong>
                    <span>{{ upsi2u_platform_tagline() }}</span>
                </span>
            </div>

            <h1>Complete your account</h1>
            <p class="intro">
                @if ($isStudentGoogleAccount)
                    Your Google email is verified. Your official student name will be obtained from UPSI.
                @else
                    Your Google email is verified. Confirm your identity details to continue.
                @endif
            </p>

            <div class="google-account">
                Signed in as <strong>{{ $googleUser['email'] }}</strong>
            </div>

            @if($errors->has('google'))
                <div class="google-account" style="background:#fff1f2;color:#be123c;">
                    {{ $errors->first('google') }}
                </div>
            @endif

            <form method="POST" action="{{ route('auth.google.complete') }}">
                @csrf

                @unless ($isStudentGoogleAccount)
                    <div class="field">
                        <label for="full_name">Full name as shown on your ID</label>
                        <input id="full_name" type="text" name="full_name"
                            value="{{ old('full_name', $googleUser['name'] ?? '') }}" required maxlength="255"
                            autocomplete="name" placeholder="Enter your full legal name">
                        <p style="margin:0.45rem 0 0;color:#64748b;font-size:0.78rem;line-height:1.45;">
                            All community members must use the name matching their identity document.
                        </p>
                        <x-input-error :messages="$errors->get('full_name')" class="mt-2 text-xs text-red-500" />
                    </div>
                @endunless

                <div class="field">
                    <label for="phone">Phone number</label>
                    <input id="phone" type="tel" name="phone" value="{{ old('phone') }}" required placeholder="0123456789">
                    <x-input-error :messages="$errors->get('phone')" class="mt-2 text-xs text-red-500" />
                </div>

                <label for="terms" class="terms-row">
                    <input id="terms" type="checkbox" name="terms" value="1" required>
                    <span>I agree to the <a href="{{ route('terms') }}">Terms</a> and <a href="{{ route('privacy') }}">Privacy Policy</a>.</span>
                </label>
                <x-input-error :messages="$errors->get('terms')" class="mb-3 text-xs text-red-500" />

                <button type="submit" class="complete-primary">Continue</button>
            </form>

            <a href="{{ route('login') }}" class="back-link">Use email and password instead</a>
        </section>
    </main>
</body>

</html>
