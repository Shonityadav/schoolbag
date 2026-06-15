<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register — Little Learner</title>
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
            justify-content: flex-start;
            padding: 28px 16px 80px;
            color: #5E4D3B;
        }

        /* ── Mascot ── */
        .mascot-wrap {
            width: 100px; height: 100px;
            border-radius: 50%;
            background: linear-gradient(180deg, #8BDDFF 50%, #FFB37C 50%);
            padding: 7px;
            margin: 0 auto 14px;
            box-shadow: 0 8px 0 rgba(0,0,0,0.08), 0 12px 24px rgba(0,0,0,0.1);
        }
        .mascot-inner {
            width: 100%; height: 100%;
            border-radius: 50%;
            background: #FFF;
            display: flex; align-items: center; justify-content: center;
        }
        .mascot-inner img { width: 64px; height: 64px; object-fit: contain; }

        .page-title {
            font-family: 'Bubblegum Sans', cursive;
            font-size: clamp(22px, 6vw, 34px);
            color: #D89839;
            text-align: center;
            margin-bottom: 4px;
            text-shadow: 0 2px 4px rgba(216,152,57,0.2);
        }
        .page-subtitle {
            font-size: 14px;
            font-weight: 700;
            color: #8D7E6A;
            text-align: center;
            margin-bottom: 22px;
        }

        /* ── Card ── */
        .card {
            width: 100%;
            max-width: 460px;
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
            margin-bottom: 22px;
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
        .field { margin-bottom: 14px; }
        label {
            display: block;
            font-size: 11px;
            font-weight: 900;
            color: #8D7E6A;
            margin-bottom: 5px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        input[type="text"],
        input[type="email"],
        input[type="tel"],
        input[type="password"] {
            width: 100%;
            padding: 11px 15px;
            background: #FFF9E5;
            border: 2px solid #E8D8A0;
            border-radius: 14px;
            color: #5E4D3B;
            font-family: 'Quicksand', sans-serif;
            font-size: 14px;
            font-weight: 700;
            outline: none;
            transition: border-color .2s, box-shadow .2s;
        }
        input:focus {
            border-color: #FFB37C;
            box-shadow: 0 0 0 3px rgba(255,179,124,0.2);
        }
        input::placeholder { color: #C4B08A; font-weight: 600; }

        /* ── Class Picker ── */
        .class-label {
            font-size: 11px;
            font-weight: 900;
            color: #8D7E6A;
            margin-bottom: 8px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .class-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(64px, 1fr));
            gap: 8px;
        }
        .class-opt { position: relative; }
        .class-opt input[type=radio] { position: absolute; opacity: 0; width: 0; height: 0; }
        .class-opt label {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            aspect-ratio: 1;
            background: #FFF9E5;
            border: 2px solid #E8D8A0;
            border-radius: 14px;
            cursor: pointer;
            transition: all .2s;
            font-size: 11px;
            font-weight: 900;
            gap: 4px;
            text-transform: none;
            letter-spacing: 0;
            color: #5E4D3B;
        }
        .class-opt input[type=radio]:checked + label {
            border-color: #FFB37C;
            background: linear-gradient(135deg, #FFE5C0, #FFD580);
            box-shadow: 0 4px 0 #CC7A00;
            transform: translateY(-2px);
            color: #5E4D3B;
        }
        .class-opt label:hover { border-color: #FFB37C; transform: translateY(-2px); }

        /* ── Error ── */
        .error-box {
            background: rgba(255,100,100,0.1);
            border: 2px solid rgba(255,100,100,0.3);
            border-radius: 14px;
            padding: 10px 14px;
            margin-bottom: 14px;
            font-size: 13px;
            font-weight: 700;
            color: #CC3333;
        }

        /* ── Button ── */
        .btn {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #9DE182, #5CAA44);
            color: #fff;
            border: none;
            border-radius: 999px;
            font-family: 'Bubblegum Sans', cursive;
            font-size: 18px;
            cursor: pointer;
            margin-top: 10px;
            box-shadow: 0 8px 0 #3A7A28, 0 10px 20px rgba(60,120,40,0.3);
            transform: translateY(-3px);
            transition: all .15s;
            text-shadow: 0 1px 2px rgba(0,0,0,0.15);
        }
        .btn:hover { transform: translateY(-5px); box-shadow: 0 11px 0 #3A7A28, 0 14px 24px rgba(60,120,40,0.3); }
        .btn:active { transform: translateY(0); box-shadow: 0 2px 0 #3A7A28; }

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
            <img src="{{ asset('uploads/images/lion.png') }}" alt="Little Learner Lion">
        </div>
    </div>
    <div class="page-title">Join the Adventure! 🌟</div>
    <div class="page-subtitle">Create your free account and start learning</div>

    <!-- Card -->
    <div class="card">
        <!-- Tabs -->
        <div class="tab-row">
            <a href="{{ route('student.login') }}" class="tab">🔓 Login</a>
            <a href="{{ route('student.register') }}" class="tab active">✨ Register</a>
        </div>

        @if($errors->any())
        <div class="error-box">⚠️ {{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('student.register.submit') }}">
            @csrf
            <div class="field">
                <label>👤 Full Name</label>
                <input type="text" name="name" value="{{ old('name') }}" placeholder="Your name" required>
            </div>
            <div class="field">
                <label>📧 Email</label>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="your@email.com" required>
            </div>
            <div class="field">
                <label>📱 Phone (optional)</label>
                <input type="tel" name="phone" value="{{ old('phone') }}" placeholder="Mobile number">
            </div>
            <div class="field">
                <label>🔑 Password</label>
                <input type="password" name="password" placeholder="Choose a password" required>
            </div>
            <div class="field">
                <label>🔒 Confirm Password</label>
                <input type="password" name="password_confirmation" placeholder="Repeat password" required>
            </div>
            <div style="margin-bottom: 18px;">
                <div class="class-label">🎓 I am in...</div>
                <div class="class-grid">
                    @foreach($classes as $cls)
                    <div class="class-opt">
                        <input type="radio" name="class_id" id="cls{{ $cls->id }}" value="{{ $cls->id }}" {{ old('class_id') == $cls->id ? 'checked' : '' }} required>
                        <label for="cls{{ $cls->id }}">
                            <span style="font-size:20px">📚</span>
                            <span>Class {{ $cls->standard }} {{ $cls->section }}</span>
                        </label>
                    </div>
                    @endforeach
                </div>
            </div>
            <button type="submit" class="btn">Join Now! 🚀</button>
        </form>
    </div>

    <a href="{{ route('student.welcome') }}" class="back-link">← Back to home</a>

</body>
</html>
