<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login – BMS Beacon Engineering</title>
    @vite(['resources/css/app.css'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; }

        .login-wrapper {
            display: flex;
            min-height: 100vh;
        }

        /* ── Left Panel ── */
        .left-panel {
            flex: 0 0 65%;
            background: #ffffff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 60px 64px;
            position: relative;
        }
        .left-panel .tagline {
            font-size: 44px;
            font-weight: 800;
            font-style: italic;
            color: #B40404;
            line-height: 1.25;
            margin-bottom: 8px;
            text-align: left;
            padding-left: 40px;
        }
        .left-panel .subtitle {
            font-size: 24px;
            color: #020202ff;
            margin-bottom: 20px;
            text-align: left;
            padding-left: 40px;
        }
        .left-panel .illustration {
            width: 100%;
            max-width: 590px;
            display: block;
            margin: 0 auto;
        }

        /* ── Right Panel ── */
        .right-panel {
            flex: 0 0 35%;
            background: #f3f4f6;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 60px 56px 36px;
        }
        .form-wrap {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .form-title {
            font-size: 32px;
            font-weight: 700;
            font-style: bold;
            color: #000000ff;
            margin-bottom: 0px;
        }
        .form-subtitle {
            font-size: 16px;
            color: #070707ff;
            margin-bottom: 24px;
        }
        .field-label {
            font-size: 13px;
            font-weight: 600;
            color: #000000ff;
            margin-bottom: 6px;
            display: block;
        }
        .field-input {
            width: 100%;
            padding: 10px 14px;
            border: 1.5px solid #d1d5db;
            border-radius: 6px;
            font-size: 13.5px;
            font-family: 'Inter', sans-serif;
            color: #111827;
            background: #ffffff;
            outline: none;
            transition: border-color .15s;
        }
        .field-input:focus { border-color: #B40404; }
        .field-input::placeholder { color: #9ca3af; }
        .field-group { margin-bottom: 5px; }
        .password-wrap { position: relative; }
        .password-wrap .field-input { padding-right: 44px; }
        .toggle-pw {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #9ca3af;
            background: none;
            border: none;
            padding: 0;
            display: flex;
            align-items: center;
        }
        .toggle-pw:hover { color: #6b7280; }

        .row-remember {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 28px;
        }
        .remember-label {
            display: flex;
            align-items: center;
            gap: 7px;
            font-size: 13px;
            color: #374151;
            cursor: pointer;
        }
        .remember-label input[type="checkbox"] {
            appearance: none;
            -webkit-appearance: none;
            width: 15px;
            height: 15px;
            border: 1.5px solid #d1d5db;
            border-radius: 3px;
            background: white;
            cursor: pointer;
            display: grid;
            place-content: center;
            flex-shrink: 0;
            margin: 0;
            outline: none;
            transition: background .15s, border-color .15s;
        }
        .remember-label input[type="checkbox"]:focus {
            outline: none;
            box-shadow: none;
        }
        .remember-label input[type="checkbox"]:checked {
            background: #CC0000;
            border-color: #CC0000;
        }
        .remember-label input[type="checkbox"]:checked::before {
            content: '';
            width: 8px;
            height: 8px;
            clip-path: polygon(14% 44%, 0 65%, 50% 100%, 100% 16%, 80% 0%, 43% 62%);
            background: white;
        }
        .forgot-link {
            font-size: 13px;
            color: #CC0000;
            text-decoration: none;
            font-weight: 500;
        }
        .forgot-link:hover { text-decoration: underline; }

        .btn-login {
            width: 100%;
            padding: 11px;
            background: #B40404;
            color: #ffffff;
            font-size: 14px;
            font-weight: 600;
            font-family: 'Inter', sans-serif;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: background .15s;
        }
        .btn-login:hover { background: #b30000; }

        .error-msg {
            font-size: 12px;
            color: #dc2626;
            margin-top: 5px;
        }

        /* ── Footer Logos ── */
        .partner-logos {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }
        .logo-row {
            display: flex;
            align-items: center;
            gap: 16px;
        }
        .logo-row img {
            height: 38px;
            object-fit: contain;
        }
        .copyright {
            font-size: 11px;
            color: #9ca3af;
            text-align: center;
        }

        @media (max-width: 768px) {
            .login-wrapper { flex-direction: column; }
            .left-panel { flex: none; padding: 40px 28px 32px; }
            .right-panel { flex: none; padding: 40px 28px 32px; }
            .partner-logos { align-items: center; }
            .copyright { text-align: center; }
        }
    </style>
</head>
<body>
<div class="login-wrapper">

    {{-- ── Left Panel ── --}}
    <div class="left-panel">
        <p class="tagline">Smarter control for better buildings.</p>
        <p class="subtitle">Pahami kondisi gedung lebih cepat, kelola dengan lebih tepat</p>
        <img src="{{ asset('images/login_img.svg') }}" alt="BMS Illustration" class="illustration">
    </div>

    {{-- ── Right Panel ── --}}
    <div class="right-panel">

        <div class="form-wrap">
            <h1 class="form-title">Masuk ke Akun Anda</h1>
            <p class="form-subtitle">Masukkan detail akun Anda untuk melanjutkan.</p>

            {{-- Session Status --}}
            @if(session('status'))
                <div style="background:#fef2f2;border:1px solid #fca5a5;color:#b91c1c;padding:10px 14px;border-radius:6px;font-size:13px;margin-bottom:18px;">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                {{-- Nama Pengguna --}}
                <div class="field-group">
                    <label class="field-label" for="email">Nama Pengguna</label>
                    <input id="email" class="field-input" type="text" name="email"
                           value="{{ old('email') }}"
                           placeholder="Masukkan nama pengguna"
                           required autofocus autocomplete="username">
                    @error('email')
                        <p class="error-msg">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Kata Sandi --}}
                <div class="field-group">
                    <label class="field-label" for="password">Kata Sandi</label>
                    <div class="password-wrap">
                        <input id="password" class="field-input" type="password" name="password"
                               placeholder="Masukkan kata sandi"
                               required autocomplete="current-password">
                        <button type="button" class="toggle-pw" onclick="togglePassword()" id="toggleBtn" title="Tampilkan/sembunyikan">
                            {{-- Eye open --}}
                            <svg id="eye-open" xmlns="http://www.w3.org/2000/svg" width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                            {{-- Eye closed --}}
                            <svg id="eye-closed" xmlns="http://www.w3.org/2000/svg" width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="display:none">
                                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>
                                <line x1="1" y1="1" x2="23" y2="23"/>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="error-msg">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Remember + Forgot --}}
                <div class="row-remember">
                    <label class="remember-label">
                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                        Ingat Saya
                    </label>
                    @if(Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="forgot-link">Lupa Kata Sandi?</a>
                    @endif
                </div>

                <button type="submit" class="btn-login">Masuk</button>
            </form>
        </div>

        {{-- Partner Logos --}}
        <div class="partner-logos">
            <div class="logo-row">
                <img src="{{ asset('images/logo_be.svg') }}" alt="Beacon Engineering">
                <img src="{{ asset('images/logostesy.svg') }}" alt="Stesy">
                <img src="{{ asset('images/belimo.svg') }}" alt="Belimo">
            </div>
            <p class="copyright">© Beacon Engineering {{ date('Y') }}</p>
        </div>
    </div>

</div>

<script>
function togglePassword() {
    const input = document.getElementById('password');
    const eyeOpen   = document.getElementById('eye-open');
    const eyeClosed = document.getElementById('eye-closed');
    if (input.type === 'password') {
        input.type = 'text';
        eyeOpen.style.display   = 'none';
        eyeClosed.style.display = 'block';
    } else {
        input.type = 'password';
        eyeOpen.style.display   = 'block';
        eyeClosed.style.display = 'none';
    }
}
</script>
</body>
</html>
