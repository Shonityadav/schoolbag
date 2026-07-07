<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome — Little Learner</title>
    {{-- PWA Setup --}}
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#2563EB">
    <link rel="apple-touch-icon" href="{{ asset('icons/icon-192x192.png') }}">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Bubblegum+Sans&family=Quicksand:wght@500;700;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg: #FFF9E5;
            --text: #5E4D3B;
            --muted: #8D7E6A;
        }

        body {
            font-family: 'Quicksand', sans-serif;
            background-color: var(--bg);
            background-image: url('{{ asset("uploads/images/background.png") }}');
            background-size: 100% 100%;
            background-position: center;
            background-attachment: fixed;
            background-repeat: no-repeat;
            color: var(--text);
            min-height: 100vh;
        }

        /* ── Top Bar ── */
        .guest-topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 24px;
        }
        .guest-topbar img.logo { height: 52px; object-fit: contain; }
        .auth-btns { display: flex; gap: 12px; }
        .btn-login {
            background: transparent;
            border: 2px solid #5E4D3B;
            color: #5E4D3B;
            border-radius: 999px;
            padding: 8px 24px;
            font-weight: 900;
            font-size: 14px;
            font-family: 'Quicksand', sans-serif;
            text-decoration: none;
            transition: all 0.2s;
        }
        .btn-login:hover { background: #5E4D3B; color: #FFF9E5; }
        .btn-signup {
            background: #FFD561;
            border: 2px solid #C9A300;
            color: #5E4D3B;
            border-radius: 999px;
            padding: 8px 24px;
            font-weight: 900;
            font-size: 14px;
            font-family: 'Quicksand', sans-serif;
            text-decoration: none;
            box-shadow: 0 6px 0 #C9A300, 0 8px 16px rgba(255,213,97,0.4);
            transition: all 0.15s;
            transform: translateY(-3px);
        }
        .btn-signup:hover { transform: translateY(-5px); box-shadow: 0 9px 0 #C9A300, 0 12px 20px rgba(255,213,97,0.4); }
        .btn-signup:active { transform: translateY(0); box-shadow: 0 2px 0 #C9A300; }

        /* ── Hero ── */
        .hero-section {
            text-align: center;
            padding: 32px 24px 24px;
        }
        .hero-mascot-wrapper {
            width: 150px; height: 150px;
            border-radius: 50%;
            background: linear-gradient(180deg, #8BDDFF 50%, #FFB37C 50%);
            padding: 10px;
            margin: 0 auto 20px;
            box-shadow: 0 8px 0 rgba(0,0,0,0.08), 0 16px 32px rgba(0,0,0,0.1);
        }
        .hero-mascot-inner {
            width: 100%; height: 100%;
            border-radius: 50%;
            background: #FFF;
            display: flex; align-items: center; justify-content: center;
        }
        .hero-mascot-inner img { width: 100px; height: 100px; object-fit: contain; }
        .hero-title {
            font-family: 'Bubblegum Sans', cursive;
            font-size: clamp(28px, 7vw, 48px);
            color: #D89839;
            text-shadow: 0 2px 4px rgba(216,152,57,0.2);
            margin-bottom: 10px;
        }
        .hero-subtitle {
            font-size: clamp(14px, 3.5vw, 18px);
            font-weight: 700;
            color: var(--muted);
            max-width: 500px;
            margin: 0 auto 28px;
        }
        .hero-cta {
            display: inline-block;
            background: linear-gradient(135deg, #FFD561, #FFB37C);
            color: #5E4D3B;
            font-family: 'Bubblegum Sans', cursive;
            font-size: clamp(16px, 4vw, 22px);
            border-radius: 999px;
            padding: 14px 40px;
            text-decoration: none;
            box-shadow: 0 10px 0 #CC7A00, 0 14px 28px rgba(200,120,0,0.3);
            transform: translateY(-4px);
            transition: all 0.15s;
            border: none;
        }
        .hero-cta:hover { transform: translateY(-7px); box-shadow: 0 13px 0 #CC7A00, 0 18px 32px rgba(200,120,0,0.3); color: #5E4D3B; }
        .hero-cta:active { transform: translateY(0); box-shadow: 0 3px 0 #CC7A00; }

        /* ── Preview Cards ── */
        .section-title {
            font-family: 'Bubblegum Sans', cursive;
            font-size: clamp(20px, 5vw, 28px);
            color: var(--text);
            text-align: center;
            margin-bottom: 20px;
        }
        .s-card {
            border-radius: 20px;
            padding: 24px 16px 20px;
            color: #FFFFFF;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            text-decoration: none;
            transform: translateY(-4px);
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }
        .s-card:hover { transform: translateY(-8px); color: #FFF; }
        .s-card:active { transform: translateY(0px); }
        .s-card img { max-height: 90px; object-fit: contain; margin-bottom: 14px; filter: drop-shadow(0 6px 10px rgba(0,0,0,0.2)); }
        .s-card-title { font-family: 'Bubblegum Sans', cursive; font-size: clamp(15px, 4vw, 22px); margin-bottom: 6px; text-shadow: 0 2px 4px rgba(0,0,0,0.2); }
        .s-card-desc { font-size: clamp(10px, 2.5vw, 13px); opacity: 0.9; line-height: 1.3; }

        /* ── Locked Overlay ── */
        .locked-overlay {
            position: absolute; inset: 0;
            border-radius: 20px;
            background: rgba(94,77,59,0.45);
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            gap: 8px;
            text-align: center;
            padding: 16px;
            backdrop-filter: blur(2px);
        }
        .locked-overlay .lock-icon { font-size: 36px; }
        .locked-overlay .lock-text { font-family: 'Bubblegum Sans', cursive; font-size: 15px; color: #FFF; text-shadow: 0 1px 4px rgba(0,0,0,0.3); }
        .locked-overlay .lock-link {
            background: #FFD561; color: #5E4D3B;
            border-radius: 999px; padding: 6px 18px;
            font-size: 12px; font-weight: 900;
            text-decoration: none; margin-top: 4px;
            box-shadow: 0 4px 0 #C9A300;
            transform: translateY(-2px);
            display: inline-block;
            transition: all 0.15s;
        }
        .locked-overlay .lock-link:hover { transform: translateY(-4px); box-shadow: 0 6px 0 #C9A300; }

        /* ── Daily Quest Preview ── */
        .quest-preview {
            background: linear-gradient(160deg, #FFF8DD 0%, #FFF3CC 100%);
            border-radius: 20px;
            padding: 20px;
            box-shadow: 0 10px 0 #D4A017, 0 14px 28px rgba(200,160,20,0.2), inset 0 1px 0 rgba(255,255,255,0.8);
            transform: translateY(-4px);
        }
        .dq-bar-wrap { height: 8px; background: rgba(0,0,0,0.08); border-radius: 999px; overflow: hidden; margin-top: 6px; }
        .dq-bar-fill { height: 100%; border-radius: 999px; }

        /* ── Features ── */
        .feature-pill {
            display: flex; align-items: center; gap: 12px;
            background: rgba(255,255,255,0.7);
            border-radius: 16px;
            padding: 14px 20px;
            box-shadow: 0 4px 0 rgba(0,0,0,0.05), 0 6px 16px rgba(0,0,0,0.05);
            margin-bottom: 12px;
            font-weight: 700;
            font-size: 15px;
        }
        .feature-pill .fp-icon { font-size: 28px; flex-shrink: 0; }

        /* ── Guest Bottom Dock ── */
        .guest-dock {
            position: fixed;
            width: calc(100% - 32px);
            max-width: 480px;
            height: 80px;
            bottom: 16px; left: 50%;
            transform: translateX(-50%);
            background-color: #FFF9E5;
            background-image: url('/uploads/images/pencil.png');
            background-size: 100% 100%;
            background-position: center;
            background-repeat: no-repeat;
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: center;
            padding: 0 60px;
            border-radius: 40px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.08);
            z-index: 999;
            gap: 4px;
        }
        .dock-item {
            flex: 0 0 70px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 3px;
            text-decoration: none;
            color: #1a4f66;
            font-family: 'Quicksand', sans-serif;
            font-size: 9px;
            font-weight: 900;
            padding: 4px 0;
        }
        .dock-item .d-icon {
            font-size: 22px;
            width: 36px; height: 36px;
            display: flex; align-items: center; justify-content: center;
            border-radius: 50%;
        }
        .dock-item.active .d-icon {
            background: #FFFFFF;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
        .dock-item:hover { color: #1a4f66; opacity: 0.8; }

        @media (max-width: 600px) {
            .auth-btns .btn-login { display: none; }
        }
    </style>
</head>
<body>

    <!-- Top Bar -->
    <div class="guest-topbar">
        <img src="{{ asset('images/logo.png') }}" alt="Little Learner Logo" class="logo" fetchpriority="high" loading="eager" decoding="async">
        <div class="auth-btns">
            <a href="{{ route('student.login') }}" class="btn-login">Login</a>
            <a href="{{ route('student.register') }}" class="btn-signup">🚀 Sign Up Free</a>
        </div>
    </div>

    <!-- Hero -->
    <div class="hero-section">
        <div class="hero-mascot-wrapper">
            <div class="hero-mascot-inner">
                <img src="{{ asset('uploads/images/lion.png') }}" alt="Little Learner Lion" fetchpriority="high" loading="eager" decoding="async">
            </div>
        </div>
        <div class="hero-title">Hi, Little Learner! 🎒</div>
        <div class="hero-subtitle">Join thousands of students on the most fun learning adventure ever!</div>
        <a href="{{ route('student.register') }}" class="hero-cta">✨ Start Learning for Free!</a>
    </div>

    <!-- Main Content -->
    <div class="container-fluid px-3 px-md-5 pb-5" style="max-width: 1000px;">

        <!-- Subject Cards Preview -->
        <div class="section-title mt-4">🗺️ Explore Subjects</div>
        <div class="row g-3 mb-4">
            <!-- Math — visible preview -->
            <div class="col-4">
                <div class="s-card" style="background: linear-gradient(160deg, #A8E8FF 0%, #8BDDFF 100%); box-shadow: 0 10px 0 #4AADCC, 0 14px 28px rgba(70,160,200,0.3), inset 0 1px 0 rgba(255,255,255,0.5);">
                    <img src="{{ asset('uploads/images/owl teacher.png') }}" alt="Math Adventure" class="img-fluid" fetchpriority="high" loading="eager" decoding="async">
                    <div class="s-card-title">Math Adventure</div>
                    <div class="s-card-desc">Solve equations and unlock treasure!</div>
                </div>
            </div>
            <!-- Science — locked -->
            <div class="col-4">
                <div class="position-relative">
                    <div class="s-card" style="background: linear-gradient(160deg, #BAEDAA 0%, #9DE182 100%); box-shadow: 0 10px 0 #5CAA44, 0 14px 28px rgba(80,160,60,0.3), inset 0 1px 0 rgba(255,255,255,0.5);">
                        <img src="{{ asset('uploads/images/robot.png') }}" alt="Science Explorer" class="img-fluid" fetchpriority="high" loading="eager" decoding="async">
                        <div class="s-card-title">Science Explorer</div>
                        <div class="s-card-desc">Discover the world with experiments!</div>
                    </div>
                    <div class="locked-overlay">
                        <div class="lock-icon">🔒</div>
                        <div class="lock-text">Login to unlock!</div>
                        <a href="{{ route('student.login') }}" class="lock-link">Login →</a>
                    </div>
                </div>
            </div>
            <!-- English — locked -->
            <div class="col-4">
                <div class="position-relative">
                    <div class="s-card" style="background: linear-gradient(160deg, #FFCC9E 0%, #FFB37C 100%); box-shadow: 0 10px 0 #CC7A3C, 0 14px 28px rgba(200,120,60,0.3), inset 0 1px 0 rgba(255,255,255,0.5);">
                        <img src="{{ asset('uploads/images/test paper.png') }}" alt="English Storytime" class="img-fluid" fetchpriority="high" loading="eager" decoding="async">
                        <div class="s-card-title">English Storytime</div>
                        <div class="s-card-desc">Read tales and grow your vocabulary!</div>
                    </div>
                    <div class="locked-overlay">
                        <div class="lock-icon">🔒</div>
                        <div class="lock-text">Login to unlock!</div>
                        <a href="{{ route('student.login') }}" class="lock-link">Login →</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Daily Quest Preview -->
        <div class="section-title mt-2">🗺️ Daily Quest</div>
        <div class="quest-preview d-flex align-items-center gap-3 mb-4">
            <div class="flex-shrink-0" style="width: 35%; max-width: 130px;">
                <img src="{{ asset('uploads/images/treasuremap.png') }}" alt="Treasure Map" style="width:100%; border-radius:12px;" fetchpriority="high" loading="eager" decoding="async">
            </div>
            <div class="flex-grow-1">
                <div style="font-family:'Bubblegum Sans',cursive; font-size:clamp(16px,5vw,22px); color:#5E4D3B; margin-bottom:4px;">Daily Quest</div>
                <div style="font-size:clamp(10px,3vw,13px); color:#8D7E6A; margin-bottom:10px;">Complete 3 Activities to Find the Treasure!</div>
                <div style="font-size:11px; font-weight:800; color:#5E4D3B; margin-bottom:4px;">Read a Story</div>
                <div class="dq-bar-wrap mb-2"><div class="dq-bar-fill" style="width:0%; background:#FFB37C;"></div></div>
                <div class="row g-2">
                    <div class="col-6">
                        <div style="font-size:10px; font-weight:800; color:#5E4D3B;">Solve a Math Puzzle</div>
                        <div class="dq-bar-wrap"><div class="dq-bar-fill" style="width:0%; background:#9DE182;"></div></div>
                    </div>
                    <div class="col-6">
                        <div style="font-size:10px; font-weight:800; color:#5E4D3B;">Learn a Science Fact</div>
                        <div class="dq-bar-wrap"><div class="dq-bar-fill" style="width:0%; background:#8BDDFF;"></div></div>
                    </div>
                </div>
                <a href="{{ route('student.register') }}" style="display:inline-block; margin-top:10px; background:#FFD561; color:#5E4D3B; border-radius:999px; padding:6px 18px; font-size:12px; font-weight:900; text-decoration:none; box-shadow:0 4px 0 #C9A300;">🔓 Sign up to start quests!</a>
            </div>
        </div>

        <!-- Why Join -->
        <div class="section-title mt-2">⭐ Why join Little Learner?</div>
        <div class="feature-pill">
            <span class="fp-icon">🏆</span>
            <div><strong>Earn XP & Level Up</strong> — Every lesson and activity earns you experience points!</div>
        </div>
        <div class="feature-pill">
            <span class="fp-icon">🎖️</span>
            <div><strong>Collect Badges</strong> — Unlock cool badges as you hit milestones on your learning journey.</div>
        </div>
        <div class="feature-pill">
            <span class="fp-icon">🔥</span>
            <div><strong>Daily Streaks</strong> — Learn every day and build your streak to become a Super Learner!</div>
        </div>
        <div class="feature-pill">
            <span class="fp-icon">📝</span>
            <div><strong>Worksheets</strong> — Practice what you learn with fun, interactive activities.</div>
        </div>

        <!-- Extra bottom spacer for fixed CTA -->
        <div style="height: 80px;"></div>
    </div>

    <!-- Guest Bottom Dock -->
    <nav class="guest-dock">
        <a href="{{ route('student.welcome') }}" class="dock-item active">
            <span class="d-icon">🏠</span>
            <span>Home</span>
        </a>
        <a href="{{ route('student.login') }}" class="dock-item">
            <span class="d-icon">📚</span>
            <span>Ebooks</span>
        </a>
        <a href="{{ route('student.login') }}" class="dock-item">
            <span class="d-icon">👤</span>
            <span>Profile</span>
        </a>
    </nav>

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
