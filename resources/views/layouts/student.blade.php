<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'School Bag') — School Bag</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Bubblegum+Sans&family=Quicksand:wght@500;700;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg:        #FFF9E5;
            --bg2:       #FFFFFF;
            --card:      #FFFFFF;
            --border:    rgba(94, 77, 59, 0.1);
            --accent:    #8BDDFF;
            --accent2:   #FFB37C;
            --gold:      #FFD561;
            --green:     #9DE182;
            --text:      #5E4D3B;
            --muted:     #8D7E6A;
            --radius:    24px;
            --shadow:    0 8px 24px rgba(94, 77, 59, 0.08);
        }

        body {
            font-family: 'Quicksand', sans-serif;
            background-color: var(--bg);
            background-image: url('{{ asset("uploads/images/background.png") }}');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            background-repeat: no-repeat;
            color: var(--text);
            min-height: 100vh;
            display: flex;
        }

        /* ── Typography ── */
        h1, h2, h3, h4, .brand, .page-title, .sc-val {
            font-family: 'Bubblegum Sans', cursive;
            letter-spacing: 0.5px;
        }

        /* ── Sidebar (Dock) ── */
        .sidebar {
            width: 500px;
            max-width: calc(100% - 32px);
            height: 80px;
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
            position: fixed;
            bottom: 20px; left: 50%;
            transform: translateX(-50%);
            z-index: 1000;
            border-radius: 40px;
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.08);
            gap: 4px;
        }
        .sidebar .logo { display: none; }
        .nav-item {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            gap: 4px;
            padding: 6px 10px;
            height: 100%;
            color: #1a4f66;
            text-decoration: none;
            transition: all .2s;
            font-weight: 900;
            border-radius: 14px;
        }
        .nav-item:hover {
            transform: translateY(-3px);
            background: rgba(255,255,255,0.35);
        }
        /* Active: transparent white rounded-rect wrapping icon + label */
        .nav-item.active {
            
            backdrop-filter: blur(6px);
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            color: #1a4f66;
            transform: translateY(-2px);
            height: auto;            /* shrink to content, not full dock height */
            align-self: center;
            padding: 8px 10px;
        }
        .nav-item.active .icon {
            background: transparent;
            box-shadow: none;
            transform: scale(1.08);
        }
        .nav-spacer { display: none; }
        .nav-logout-form { margin: 0; padding: 0; height: 100%; }
        .nav-item .icon { 
            font-size: 20px; 
            width: 36px; 
            height: 36px; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            border-radius: 10px;   /* rounded-rect, not circle */
            transition: all .2s;
        }
        .nav-item .label { font-size: 11px; font-family: 'Quicksand', sans-serif; }

        /* ── Main ── */
        .main {
            margin-left: 0;
            flex: 1;
            min-width: 0;          /* critical: prevents flex child from overflowing */
            width: 0;              /* forces it to shrink to available space */
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            overflow-x: hidden;
            padding-bottom: 120px;
        }

        /* ── Top bar ── */
        .topbar {
            background: var(--bg2);
            border-bottom: 1px solid var(--border);
            padding: 16px 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            box-shadow: 0 4px 24px rgba(94, 77, 59, 0.03);
        }
        .mobile-logo { display: none; width: 64px; height: 64px; object-fit: contain; }
        .page-title { font-size: 24px; color: var(--text); }

        /* XP bar */
        .xp-section {
            display: flex;
            align-items: center;
            gap: 12px;
            flex: 1;
            max-width: 380px;
            margin: 0 auto;
        }
        .xp-label { font-size: 12px; color: var(--muted); white-space: nowrap; }
        .xp-bar-wrap {
            flex: 1;
            background: var(--border);
            border-radius: 999px;
            height: 10px;
            overflow: hidden;
        }
        .xp-bar-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--accent), var(--accent2));
            border-radius: 999px;
            transition: width 1s ease;
        }
        .xp-val { font-size: 12px; font-weight: 800; color: var(--accent); white-space: nowrap; }

        /* Streak */
        .streak-badge {
            display: flex; align-items: center; gap: 6px;
            background: rgba(255,213,0,.12);
            border: 1px solid rgba(255,213,0,.3);
            border-radius: 999px;
            padding: 5px 14px;
            font-size: 14px; font-weight: 800; color: var(--gold);
        }

        /* ── Content ── */
        .content {
            flex: 1;
            padding: 28px;
            width: 100%;
            min-width: 0;
            overflow-x: hidden;
            box-sizing: border-box;
        }

        /* ── Cards ── */
        .card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 20px;
            box-shadow: var(--shadow);
        }

        /* ── Alerts ── */
        .alert {
            padding: 12px 18px;
            border-radius: var(--radius);
            margin-bottom: 16px;
            font-weight: 600;
            font-size: 14px;
        }
        .alert-success { background: rgba(0,212,170,.12); border: 1px solid rgba(0,212,170,.3); color: var(--green); }
        .alert-error   { background: rgba(255,101,132,.12); border: 1px solid rgba(255,101,132,.3); color: var(--accent2); }
        .alert-info    { background: rgba(108,99,255,.12); border: 1px solid rgba(108,99,255,.3); color: var(--accent); }

        /* ── Btn ── */
        .btn {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 10px 22px;
            border-radius: 999px;
            font-weight: 800; font-size: 14px;
            border: none; cursor: pointer;
            transition: all .2s; text-decoration: none;
        }
        .btn-primary {
            background: linear-gradient(135deg, var(--accent), #5A51FF);
            color: #fff;
            box-shadow: 0 4px 16px rgba(108,99,255,.4);
        }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(108,99,255,.5); }
        .btn-ghost { background: transparent; border: 1px solid var(--border); color: var(--muted); }
        .btn-ghost:hover { border-color: var(--accent); color: var(--accent); }
        .btn-danger { background: rgba(255,101,132,.15); border: 1px solid rgba(255,101,132,.3); color: var(--accent2); }

        /* ── Grid ── */
        .grid-2 { display: grid; grid-template-columns: repeat(auto-fill, minmax(240px,1fr)); gap: 18px; }
        .grid-3 { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px,1fr)); gap: 16px; }

        /* ── Progress ring ── */
        .ring-wrap { position: relative; width: 60px; height: 60px; }
        .ring-wrap svg { transform: rotate(-90deg); }
        .ring-center { position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 900; }

        /* ── Mobile Responsive ── */
        @media (max-width: 768px) {
            .grid-2, .grid-3 { grid-template-columns: 1fr; }
            .sidebar {
                position: fixed;
                width: calc(100% - 32px); max-width: 480px; height: 80px; 
                display: flex; flex-direction: row; justify-content: center; align-items: center;
                top: auto; bottom: 16px; left: 50%; transform: translateX(-50%);
                border-right: none; border-top: none; padding: 0 60px; z-index: 1000;
                background-color: #FFF9E5;
                background-image: url('/uploads/images/pencil.png');
                background-size: 100% 100%;
                background-position: center;
                background-repeat: no-repeat;
                border-radius: 40px;
                box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
                gap: 4px;
            }
            .sidebar::-webkit-scrollbar { display: none; }
            .sidebar:hover { width: calc(100% - 32px); transform: translateX(-50%); }
            .sidebar .logo { display: none; }
            .nav-item { 
                flex: 0 0 60px !important; width: 60px !important; max-width: 60px !important;
                display: flex; flex-direction: column; justify-content: center; align-items: center; gap: 4px; padding: 6px 4px; margin: 0; 
                height: 100%; border-radius: 12px;
                border: none !important; background: transparent; box-shadow: none !important;
            }
            .nav-item.active {
                background: rgba(255,255,255,0.55) !important;
                backdrop-filter: blur(6px);
                box-shadow: 0 2px 8px rgba(0,0,0,0.08) !important;
                transform: translateY(-2px);
                height: auto !important;    /* wrap content, not full dock height */
                align-self: center;
                padding: 6px 6px !important;
            }
            .nav-item.active .icon { background: transparent !important; box-shadow: none !important; transform: scale(1.08); }
            .nav-spacer { display: none; }
            .nav-logout-form { flex: 0 0 60px !important; width: 60px !important; height: 100%; display: flex; align-items: center; justify-content: center; margin: 0; padding: 0; }
            
            .nav-item .icon { font-size: 20px; margin: 0; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; border-radius: 50%; }
            .nav-item .label { display: block !important; opacity: 1; font-size: 9px; font-weight: 900; color: #1a4f66; font-family: 'Quicksand', sans-serif; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 100%; padding: 0 1px; text-align: center; }
            .nav-item.active .icon { background: #FFFFFF; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
            .nav-item.active { background: transparent !important; box-shadow: none !important; color: #1a4f66 !important; transform: none; border: none !important; }
            .sidebar form button { width: 100%; height: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 0; margin: 0; border: none; background: transparent; gap: 4px; }
            .main { margin-left: 0; padding-bottom: 120px; background: transparent; width: 100%; overflow-x: hidden; }
            
            .content { padding: 0px; width: 100%; box-sizing: border-box; overflow-x: hidden; }
        }

        /* ── Tablet / iPad sidebar ── */
        @media (min-width: 769px) and (max-width: 1200px) {
            .sidebar {
                position: fixed;
                width: calc(100% - 48px);
                max-width: 680px;
                height: 110px;
                display: flex; flex-direction: row; justify-content: center; align-items: center;
                top: auto; bottom: 20px; left: 50%; transform: translateX(-50%);
                border-right: none; border-top: none; padding: 0 80px; z-index: 1000;
                background-color: #FFF9E5;
                background-image: url('/uploads/images/pencil.png');
                background-size: 100% 100%;
                background-position: center;
                background-repeat: no-repeat;
                border-radius: 55px;
                box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
                gap: 8px;
            }
            .sidebar .logo { display: none; }
            .nav-item {
                flex: 0 0 90px !important; width: 90px !important; max-width: 90px !important;
                display: flex; flex-direction: column; justify-content: center; align-items: center; gap: 6px; padding: 8px 4px; margin: 0;
                height: 100%; border-radius: 16px;
                border: none !important; background: transparent; box-shadow: none !important;
            }
            .nav-item.active {
                background: rgba(255,255,255,0.55) !important;
                backdrop-filter: blur(6px);
                box-shadow: 0 2px 8px rgba(0,0,0,0.08) !important;
                align-self: center;
                padding: 8px 8px !important;
            }
            .nav-item .icon { font-size: 28px; margin: 0; width: 52px; height: 52px; display: flex; align-items: center; justify-content: center; border-radius: 50%; }
            .nav-item .icon img { width: 44px !important; height: 46px !important; }
            .nav-item .label { display: block !important; opacity: 1; font-size: 13px; font-weight: 900; color: #1a4f66; font-family: 'Quicksand', sans-serif; white-space: nowrap; text-align: center; }
            .nav-item.active .icon { background: #FFFFFF; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
            .nav-item.active { background: transparent !important; box-shadow: none !important; color: #1a4f66 !important; border: none !important; }
            .nav-spacer { display: none; }
            .sidebar form button { width: 100%; height: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 0; margin: 0; border: none; background: transparent; gap: 6px; }
            .main { margin-left: 0; padding-bottom: 160px; background: transparent; width: 100%; overflow-x: hidden; }
            .content { padding: 0px; width: 100%; box-sizing: border-box; overflow-x: hidden; }
        }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Global Page Loader -->
    <div id="global-page-loader" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: #FDF6E9; z-index: 99999; display: flex; justify-content: center; align-items: center; transition: opacity 0.3s ease;">
        <div style="text-align: center;">
            <div class="spinner-border" role="status" style="width: 3rem; height: 3rem; color: #FFC145;">
                <span class="visually-hidden">Loading...</span>
            </div>
            <div style="margin-top: 15px; font-weight: 800; color: #1E1E35;">Loading...</div>
        </div>
    </div>
    <!-- Sidebar -->
    <nav class="sidebar">
        <img src="{{ asset('images/logo.png') }}" alt="School Bag" class="logo">
        <a href="{{ route('student.dashboard') }}"   class="nav-item @yield('nav_dashboard', '')">
            <span class="icon"><img src="{{ asset('uploads/images/buttons/home button.png') }}" alt="Dashboard" style="width:30px;height:32px;object-fit:contain;"></span><span class="label">Dashboard</span>
        </a>
        <a href="{{ route('student.assigned_ebooks.index') }}" class="nav-item @yield('nav_courses', '')">
            <span class="icon"><img src="{{ asset('uploads/images/icons/game.png') }}" alt="Subjects" style="width:30px;height:32px;object-fit:contain;"></span><span class="label">Subjects</span>
        </a>
        <a href="{{ route('student.ebooks') }}"  class="nav-item @yield('nav_worksheets', '')">
            <span class="icon"><img src="{{ asset('uploads/images/icons/ebook.png') }}" alt="Ebooks" style="width:30px;height:32px;object-fit:contain;"></span><span class="label">Ebooks</span>
        </a>
        <a href="{{ route('student.profile') }}"     class="nav-item @yield('nav_profile', '')">
            <span class="icon"><img src="{{ asset('uploads/images/icons/profile.png') }}" alt="Profile" style="width:30px;height:32px;object-fit:contain;"></span><span class="label">Profile</span>
        </a>
    </nav>

    <div class="main">
        <!-- Topbar -->
        <div class="topbar bg-transparent border-0 shadow-none p-3 px-md-4 py-md-4 d-flex justify-content-center justify-content-md-between align-items-center w-100">
            <div class="topbar-logo text-center">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="img-fluid" style="height: 56px; max-height: 8vh; object-fit: contain;">
            </div>
            
            <div class="topbar-actions d-none d-md-flex align-items-center gap-2 gap-md-3 position-relative">
                <div id="notification-bell" class="tb-btn shadow-sm position-relative" style="width: 44px; height: 44px; background: #FFFFFF; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 20px; cursor: pointer;">
                    🔔
                    <div class="position-absolute" style="top: 10px; right: 10px; width: 10px; height: 10px; background: #FF4B4B; border-radius: 50%; border: 2px solid #FFF;"></div>
                </div>
                
                <!-- Notification Panel -->
                <div id="notification-panel" style="display: none; position: absolute; top: 56px; right: 0; width: 300px; max-width: 90vw; background: #FFFFFF; border-radius: 24px; box-shadow: 0 12px 48px rgba(0,0,0,0.12); z-index: 1000; overflow: hidden; border: 1px solid var(--border);">
                    <div style="padding: 16px 20px; background: var(--bg2); border-bottom: 1px solid var(--border); font-weight: 900; font-size: 16px; color: #1a4f66; display: flex; justify-content: space-between; align-items: center;">
                        <span>Notifications</span>
                        <span style="background: #FF4B4B; color: #FFF; font-size: 11px; padding: 2px 8px; border-radius: 999px;">2 New</span>
                    </div>
                    <div style="max-height: 320px; overflow-y: auto;">
                        <div style="padding: 16px 20px; border-bottom: 1px solid var(--border); display: flex; gap: 12px; align-items: flex-start; background: rgba(0,212,170,0.05); transition: background 0.2s; cursor: pointer;" onmouseover="this.style.background='rgba(0,212,170,0.1)'" onmouseout="this.style.background='rgba(0,212,170,0.05)'">
                            <div style="font-size: 24px; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1));">🎉</div>
                            <div>
                                <div style="font-weight: 800; font-size: 14px; margin-bottom: 4px; color: #1a4f66;">Welcome to Little Learner!</div>
                                <div style="font-size: 12px; color: var(--muted); font-weight: 600; line-height: 1.4;">Start your first quest to earn XP.</div>
                                <div style="font-size: 10px; color: #A0AAB2; font-weight: 700; margin-top: 8px;">Just now</div>
                            </div>
                        </div>
                        <div style="padding: 16px 20px; border-bottom: 1px solid var(--border); display: flex; gap: 12px; align-items: flex-start; transition: background 0.2s; cursor: pointer;" onmouseover="this.style.background='#F9FBFC'" onmouseout="this.style.background='transparent'">
                            <div style="font-size: 24px; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1));">⭐</div>
                            <div>
                                <div style="font-weight: 800; font-size: 14px; margin-bottom: 4px; color: #1a4f66;">Daily Streak Unlocked</div>
                                <div style="font-size: 12px; color: var(--muted); font-weight: 600; line-height: 1.4;">You earned 10 XP for logging in today.</div>
                                <div style="font-size: 10px; color: #A0AAB2; font-weight: 700; margin-top: 8px;">2 hours ago</div>
                            </div>
                        </div>
                    </div>
                    <div style="padding: 12px; text-align: center; border-top: 1px solid var(--border); font-size: 13px; font-weight: 800; color: var(--accent); cursor: pointer; background: #FAFAFA; transition: background 0.2s;" onmouseover="this.style.background='#F1F1F1'" onmouseout="this.style.background='#FAFAFA'">
                        Mark all as read
                    </div>
                </div>

                <a href="{{ route('student.profile') }}" class="tb-btn shadow-sm" style="width: 50px; height: 50px; background: #FFD561; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 28px; text-decoration: none; border: 3px solid #FFF; z-index: 10;">
                    👦
                </a>
            </div>
        </div>

        <div class="content">
            @if(session('success'))
                <div class="alert alert-success">🎉 {{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-error">⚠️ {{ session('error') }}</div>
            @endif
            @if(session('info'))
                <div class="alert alert-info">ℹ️ {{ session('info') }}</div>
            @endif

            @yield('content')
        </div>
    </div>

    @stack('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const bell = document.getElementById('notification-bell');
            const panel = document.getElementById('notification-panel');
            
            if (bell && panel) {
                bell.addEventListener('click', function(e) {
                    e.stopPropagation();
                    if (panel.style.display === 'none' || panel.style.display === '') {
                        panel.style.display = 'block';
                        // Add a subtle bounce animation when opening
                        panel.animate([
                            { transform: 'translateY(-10px) scale(0.98)', opacity: 0 },
                            { transform: 'translateY(0) scale(1)', opacity: 1 }
                        ], { duration: 200, easing: 'cubic-bezier(0.175, 0.885, 0.32, 1.275)' });
                    } else {
                        panel.style.display = 'none';
                    }
                });
                
                document.addEventListener('click', function(e) {
                    if (!panel.contains(e.target) && !bell.contains(e.target)) {
                        panel.style.display = 'none';
                    }
                });
            }

            // Auto-dismiss alerts (like toastr) after 3 seconds
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
                    alert.style.opacity = '0';
                    alert.style.transform = 'translateY(-20px)';
                    setTimeout(() => alert.remove(), 600);
                }, 3000);
            });
        });

        // Hide loader when page is fully loaded
        window.addEventListener('load', function() {
            const loader = document.getElementById('global-page-loader');
            if (loader) {
                loader.style.opacity = '0';
                setTimeout(() => {
                    loader.style.display = 'none';
                }, 300);
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
