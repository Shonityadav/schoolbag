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

    <!-- Mascot & Title -->
    <div class="mascot-wrap">
        <div class="mascot-inner">
            <img src="{{ asset('uploads/images/lion.png') }}" alt="Little Learner Lion" fetchpriority="high" loading="eager" decoding="async">
        </div>
    </div>
    <div class="page-title">Welcome Back! 🎒</div>
    <div class="page-subtitle">Log in to continue your adventure</div>

    <!-- Card -->
    <div class="card">
        <!-- Tabs -->
        <div class="tab-row">
            <a href="{{ route('student.login') }}" class="tab active">🔓 Login</a>
            <a href="{{ route('student.register') }}" class="tab">✨ Register</a>
        </div>

        @if($errors->any())
        <div class="error-box">⚠️ {{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('student.login.submit') }}">
            @csrf
            <div class="field">
                <label>📧 Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="your@email.com" required>
            </div>
            <div class="field">
                <label>🔑 Password</label>
                <input type="password" name="password" placeholder="Enter your password" required>
            </div>
            <button type="submit" class="btn">Let's Go! 🚀</button>
        </form>
    </div>

    <a href="{{ route('student.welcome') }}" class="back-link">← Back to home</a>

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
    </script>
</body>
</html>
