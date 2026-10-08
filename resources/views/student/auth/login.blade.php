<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Little Learner</title>
    {{-- PWA Setup --}}
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#2563EB">
    <link rel="apple-touch-icon" href="{{ asset('icons/icon-192x192.png') }}">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Bubblegum+Sans&family=Quicksand:wght@500;700;900&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Quicksand', sans-serif;
            background-color: #FFF9E5;
            background-image: url('{{ asset("uploads/images/background.png") }}');
            background-size: 100% 100%;
            background-position: center;
            background-attachment: fixed;
            background-repeat: no-repeat;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 24px 16px 80px;
            color: #5E4D3B;
        }

        /* ── Mascot ── */
        .mascot-wrap {
            width: 110px; height: 110px;
            border-radius: 50%;
            background: linear-gradient(180deg, #8BDDFF 50%, #FFB37C 50%);
            padding: 8px;
            margin: 0 auto 16px;
            box-shadow: 0 8px 0 rgba(0,0,0,0.08), 0 12px 24px rgba(0,0,0,0.1);
        }
        .mascot-inner {
            width: 100%; height: 100%;
            border-radius: 50%;
            background: #FFF;
            display: flex; align-items: center; justify-content: center;
        }
        .mascot-inner img { width: 72px; height: 72px; object-fit: contain; }

        .page-title {
            font-family: 'Bubblegum Sans', cursive;
            font-size: clamp(24px, 6vw, 36px);
            color: #D89839;
            text-align: center;
            margin-bottom: 6px;
            text-shadow: 0 2px 4px rgba(216,152,57,0.2);
        }
        .page-subtitle {
            font-size: 14px;
            font-weight: 700;
            color: #8D7E6A;
            text-align: center;
            margin-bottom: 28px;
        }

        /* ── Card ── */
        .card {
            width: 100%;
            max-width: 420px;
            background: rgba(255,255,255,0.85);
            backdrop-filter: blur(8px);
            border-radius: 24px;
            padding: 28px 24px;
            box-shadow: 0 10px 0 rgba(210,170,60,0.25), 0 14px 32px rgba(0,0,0,0.1), inset 0 1px 0 rgba(255,255,255,0.9);
        }

        /* ── Tabs ── */
        .tab-row {
            display: flex;
            background: #FFF3CC;
            border-radius: 999px;
            padding: 4px;
            margin-bottom: 24px;
            box-shadow: inset 0 2px 6px rgba(0,0,0,0.06);
        }
        .tab {
            flex: 1;
            text-align: center;
            padding: 9px;
            border-radius: 999px;
            font-weight: 900;
            font-size: 14px;
            color: #8D7E6A;
            text-decoration: none;
            transition: all .2s;
        }
        .tab.active {
            background: linear-gradient(135deg, #FFD561, #FFB37C);
            color: #5E4D3B;
            box-shadow: 0 4px 0 #CC7A00, 0 6px 12px rgba(200,120,0,0.25);
            transform: translateY(-2px);
        }

        /* ── Fields ── */
        .field { margin-bottom: 16px; }
        label {
            display: block;
            font-size: 12px;
            font-weight: 900;
            color: #8D7E6A;
            margin-bottom: 6px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        input {
            width: 100%;
            padding: 12px 16px;
            background: #FFF9E5;
            border: 2px solid #E8D8A0;
            border-radius: 14px;
            color: #5E4D3B;
            font-family: 'Quicksand', sans-serif;
            font-size: 15px;
            font-weight: 700;
            outline: none;
            transition: border-color .2s, box-shadow .2s;
        }
        input:focus {
            border-color: #FFB37C;
            box-shadow: 0 0 0 3px rgba(255,179,124,0.2);
        }
        input::placeholder { color: #C4B08A; font-weight: 600; }

        /* ── Error ── */
        .error-box {
            background: rgba(255,100,100,0.1);
            border: 2px solid rgba(255,100,100,0.3);
            border-radius: 14px;
            padding: 10px 14px;
            margin-bottom: 16px;
            font-size: 13px;
            font-weight: 700;
            color: #CC3333;
        }

        /* ── Button ── */
        .btn {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #FFD561, #FFB37C);
            color: #5E4D3B;
            border: none;
            border-radius: 999px;
            font-family: 'Bubblegum Sans', cursive;
            font-size: 18px;
            cursor: pointer;
            margin-top: 8px;
            box-shadow: 0 8px 0 #CC7A00, 0 10px 20px rgba(200,120,0,0.3);
            transform: translateY(-3px);
            transition: all .15s;
        }
        .btn:hover { transform: translateY(-5px); box-shadow: 0 11px 0 #CC7A00, 0 14px 24px rgba(200,120,0,0.3); }
        .btn:active { transform: translateY(0); box-shadow: 0 2px 0 #CC7A00; }

        /* ── Back link ── */
        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #8D7E6A;
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
        }
        .back-link:hover { color: #5E4D3B; }
    </style>
</head>
<body>

    <div class="page-title">Welcome Back! 🎒</div>
    <div class="page-subtitle">Log in to continue your adventure</div>

    <!-- Card -->
    <div class="card">
        <h3 id="form-title" style="text-align: center; font-weight: 900; color: #5E4D3B; margin-bottom: 24px; font-size: 22px;">🔓 Login</h3>
        
        <div class="tab-row">
            <a href="#" class="tab active" id="tab-email">Email</a>
            <a href="#" class="tab" id="tab-whatsapp">WhatsApp</a>
        </div>

        @if($errors->any())
        <div class="error-box" id="global-error">⚠️ {{ $errors->first() }}</div>
        @endif
        
        <div id="error-message" class="error-box" style="display:none;"></div>

        <!-- Email Form -->
        <form id="form-email" method="POST" action="{{ route('student.login.submit') }}">
            @csrf
            <div class="field">
                <label> Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="your@email.com" required>
            </div>
            <div class="field" style="position: relative;">
                <label> Password</label>
                <input type="password" name="password" id="login-password" data-is-password="true" placeholder="Enter your password" required style="padding-right: 40px;">
                <span class="password-toggle" onmousedown="event.preventDefault()" onclick="togglePassword()" style="position: absolute; right: 14px; top: 38px; cursor: pointer; color: #8D7E6A; transition: color 0.2s;" onmouseover="this.style.color='#5E4D3B'" onmouseout="this.style.color='#8D7E6A'">
                    <svg id="eye-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                </span>
            </div>
            <button type="submit" class="btn">Let's Go! </button>
        </form>

        <!-- WhatsApp Forms -->
        <!-- Step 1: Phone -->
        <form id="form-wa-phone" style="display: none;" onsubmit="sendOtp(event)">
            @csrf
            <div class="field">
                <label>📱 WhatsApp Number</label>
                <input type="text" id="wa-phone" name="phone" placeholder="e.g. 9876543210" required>
            </div>
            <button type="submit" class="btn" id="btn-send-otp">Send OTP 💬</button>
        </form>

        <!-- Step 2: OTP -->
        <form id="form-wa-otp" style="display: none;" onsubmit="verifyOtp(event)">
            @csrf
            <div class="field">
                <label>🔢 Enter OTP</label>
                <input type="text" id="wa-otp" name="otp" placeholder="6-digit code" required maxlength="6">
            </div>
            <button type="submit" class="btn" id="btn-verify-otp">Verify OTP ✅</button>
        </form>

        <!-- Step 3: Name (New User) -->
        <form id="form-wa-name" style="display: none;" onsubmit="registerName(event)">
            @csrf
            <div class="field">
                <label>👤 What's your name?</label>
                <input type="text" id="wa-name" name="name" placeholder="Enter your full name" required>
            </div>
            <button type="submit" class="btn" id="btn-register-name">Complete Login ✨</button>
        </form>
    </div>

    <a href="{{ route('student.register') }}" class="back-link">New User? Register here ✨</a>
    <a href="{{ route('admin.login') }}" class="back-link" style="margin-top:5px; color:#1a4f66;">⚙️ Admin Login</a>

    {{-- PWA Service Worker Registration --}}
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/sw.js').then(function(registration) {
                    console.log('ServiceWorker registration successful with scope: ', registration.scope);
                }, function(err) {
                    console.log('ServiceWorker registration failed: ', err);
                });
            });
        }
        
        // WhatsApp Login UI Logic
        const tabEmail = document.getElementById('tab-email');
        const tabWa = document.getElementById('tab-whatsapp');
        const formEmail = document.getElementById('form-email');
        const formWaPhone = document.getElementById('form-wa-phone');
        const formWaOtp = document.getElementById('form-wa-otp');
        const formWaName = document.getElementById('form-wa-name');
        const errorBox = document.getElementById('error-message');
        const globalError = document.getElementById('global-error');

        tabEmail.addEventListener('click', (e) => {
            e.preventDefault();
            tabEmail.classList.add('active');
            tabWa.classList.remove('active');
            formEmail.style.display = 'block';
            formWaPhone.style.display = 'none';
            formWaOtp.style.display = 'none';
            formWaName.style.display = 'none';
            errorBox.style.display = 'none';
            if(globalError) globalError.style.display = 'block';
        });

        tabWa.addEventListener('click', (e) => {
            e.preventDefault();
            tabWa.classList.add('active');
            tabEmail.classList.remove('active');
            formEmail.style.display = 'none';
            formWaPhone.style.display = 'block';
            formWaOtp.style.display = 'none';
            formWaName.style.display = 'none';
            errorBox.style.display = 'none';
            if(globalError) globalError.style.display = 'none';
        });

        function showError(msg) {
            errorBox.textContent = '⚠️ ' + msg;
            errorBox.style.display = 'block';
        }

        async function sendOtp(e) {
            e.preventDefault();
            errorBox.style.display = 'none';
            const btn = document.getElementById('btn-send-otp');
            const phone = document.getElementById('wa-phone').value;
            btn.disabled = true;
            btn.textContent = 'Sending...';

            try {
                let formData = new FormData();
                formData.append('phone', phone);
                formData.append('_token', '{{ csrf_token() }}');

                let res = await fetch('{{ route("student.whatsapp.send_otp") }}', {
                    method: 'POST',
                    body: formData
                });
                let data = await res.json();
                
                if (data.success) {
                    formWaPhone.style.display = 'none';
                    formWaOtp.style.display = 'block';
                } else {
                    showError(data.message || 'Failed to send OTP');
                }
            } catch (err) {
                showError('Network error occurred.');
            }
            btn.disabled = false;
            btn.textContent = 'Send OTP 💬';
        }

        async function verifyOtp(e) {
            e.preventDefault();
            errorBox.style.display = 'none';
            const btn = document.getElementById('btn-verify-otp');
            const phone = document.getElementById('wa-phone').value;
            const otp = document.getElementById('wa-otp').value;
            btn.disabled = true;
            btn.textContent = 'Verifying...';

            try {
                let formData = new FormData();
                formData.append('phone', phone);
                formData.append('otp', otp);
                formData.append('_token', '{{ csrf_token() }}');

                let res = await fetch('{{ route("student.whatsapp.verify_otp") }}', {
                    method: 'POST',
                    body: formData
                });
                let data = await res.json();
                
                if (data.success) {
                    if (data.is_new_user) {
                        formWaOtp.style.display = 'none';
                        formWaName.style.display = 'block';
                    } else {
                        window.location.href = data.redirect;
                    }
                } else {
                    showError(data.message || 'Invalid OTP');
                }
            } catch (err) {
                showError('Network error occurred.');
            }
            btn.disabled = false;
            btn.textContent = 'Verify OTP ✅';
        }

        async function registerName(e) {
            e.preventDefault();
            errorBox.style.display = 'none';
            const btn = document.getElementById('btn-register-name');
            const name = document.getElementById('wa-name').value;
            btn.disabled = true;
            btn.textContent = 'Completing...';

            try {
                let formData = new FormData();
                formData.append('name', name);
                formData.append('_token', '{{ csrf_token() }}');

                let res = await fetch('{{ route("student.whatsapp.register") }}', {
                    method: 'POST',
                    body: formData
                });
                let data = await res.json();
                
                if (data.success) {
                    window.location.href = data.redirect;
                } else {
                    showError(data.message || 'Registration failed');
                }
            } catch (err) {
                showError('Network error occurred.');
            }
            btn.disabled = false;
            btn.textContent = 'Complete Login ✨';
        }
    </script>
    <script src="{{ asset('js/mascot-engine.js') }}"></script>
    @include('partials.pwa_popup')
    <script>
        function togglePassword() {
            const input = document.getElementById('login-password');
            const icon = document.getElementById('eye-icon');
            if (input.type === 'password') {
                input.type = 'text';
                // Eye-off icon
                icon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>';
                input.focus();
                
                // Explicitly tell the engine to peek, because focus never actually dropped!
                if (document.activeElement === input && window.togglePrivacyGuard) {
                    window.togglePrivacyGuard('peek');
                }
            } else {
                input.type = 'password';
                // Eye icon
                icon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>';
                input.focus();
                
                // Explicitly tell the engine to hide
                if (document.activeElement === input && window.togglePrivacyGuard) {
                    window.togglePrivacyGuard('hide');
                }
            }
        }
    </script>
</body>
</html>
