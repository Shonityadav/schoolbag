<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'School Bag') — School Bag</title>

    {{-- PWA Setup --}}
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#2563EB">
    <link rel="apple-touch-icon" href="{{ asset('app-icons/icon-192x192-v2.png') }}">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

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
            overscroll-behavior-y: none;
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
                width: calc(100% - 16px); max-width: 480px; height: 80px; 
                display: flex; flex-direction: row; justify-content: space-evenly; align-items: center;
                top: auto; bottom: 32px; left: 50%; transform: translateX(-50%);
                border-right: none; border-top: none; padding: 0 45px; z-index: 1000;
                background-color: #FFF9E5;
                background-image: url('/uploads/images/pencil.png');
                background-size: 100% 100%;
                background-position: center;
                background-repeat: no-repeat;
                border-radius: 40px;
                box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
                overflow-x: hidden;
                gap: 2px;
            }
            
            @keyframes ringBell {
                0%, 100% { transform: rotate(0deg); }
                20% { transform: rotate(15deg); }
                40% { transform: rotate(-10deg); }
                60% { transform: rotate(5deg); }
                80% { transform: rotate(-5deg); }
            }
            .bell-icon { transition: transform 0.3s; }
            #notification-bell:hover .bell-icon {
                animation: ringBell 0.5s ease-in-out;
            }
            #notification-bell { transition: all 0.2s ease; }
            #notification-bell:hover { background: #F8FAFC !important; transform: translateY(-2px); box-shadow: 0 8px 16px rgba(94, 77, 59, 0.1) !important; border-color: var(--accent) !important; }
            
            .sidebar::-webkit-scrollbar { display: none; }
            .sidebar:hover { width: calc(100% - 32px); transform: translateX(-50%); }
            .sidebar .logo { display: none; }
            .nav-item { 
                flex: 1 1 0; min-width: 45px; max-width: 65px;
                display: flex; flex-direction: column; justify-content: center; align-items: center; gap: 2px; padding: 4px 2px; margin: 0; 
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
            .nav-item .label { display: block !important; opacity: 1; font-size: 9px; font-weight: 900; color: #1a4f66; font-family: 'Quicksand', sans-serif; white-space: nowrap; overflow: hidden; text-overflow: clip; max-width: 100%; padding: 0; text-align: center; }
            .nav-item.active .icon { background: #FFFFFF; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
            .nav-item.active { background: transparent !important; box-shadow: none !important; color: #1a4f66 !important; transform: none; border: none !important; }
            .sidebar form button { width: 100%; height: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 0; margin: 0; border: none; background: transparent; gap: 4px; }
            .main { margin-left: 0; padding-bottom: 140px; background: transparent; width: 100%; overflow-x: hidden; }
            
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
    <style>
        .global-loader-bag { animation: gLoaderBag 0.7s ease-in-out infinite alternate; }
        @keyframes gLoaderBag {
            0% { transform: translateY(0) scale(1); filter: drop-shadow(0 10px 15px rgba(0,0,0,0.1)); }
            100% { transform: translateY(-15px) scale(1.05); filter: drop-shadow(0 20px 25px rgba(0,0,0,0.15)); }
        }
        @keyframes typingDots1 { 0%, 20% { opacity: 0; } 21%, 100% { opacity: 1; } }
        @keyframes typingDots2 { 0%, 40% { opacity: 0; } 41%, 100% { opacity: 1; } }
        @keyframes typingDots3 { 0%, 60% { opacity: 0; } 61%, 100% { opacity: 1; } }
        .dot-1 { animation: typingDots1 1.5s infinite; }
        .dot-2 { animation: typingDots2 1.5s infinite; }
        .dot-3 { animation: typingDots3 1.5s infinite; }
    </style>
    <div id="global-page-loader" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(255, 253, 245, 0.95); backdrop-filter: blur(5px); z-index: 99999; display: flex; flex-direction: column; justify-content: center; align-items: center; transition: opacity 0.3s ease;">
        <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('images/logo.png'))) }}" class="global-loader-bag" style="width: 110px; height: 110px; object-fit: contain;" alt="Loading...">
        <div class="global-loader-text" style="font-family: 'Bubblegum Sans', cursive; font-size: 24px; color: #5D1A1A; margin-top: 20px;">
            Loading your adventure<span class="dot-1">.</span><span class="dot-2">.</span><span class="dot-3">.</span>
        </div>
    </div>
    <!-- Sidebar -->
    <nav class="sidebar">
        <img src="{{ asset('images/logo.png') }}" alt="School Bag" class="logo">
        <a href="{{ route('student.dashboard') }}"   class="nav-item @yield('nav_dashboard', '')">
            <span class="icon"><img src="{{ asset('uploads/images/buttons/home button.png') }}" alt="Dashboard" style="width:30px;height:32px;object-fit:contain;"></span><span class="label">Dashboard</span>
        </a>
        <a href="{{ route('student.ebooks') }}"  class="nav-item @yield('nav_worksheets', '')">
            <span class="icon"><img src="{{ asset('uploads/images/icons/ebook.png') }}" alt="Ebooks" style="width:30px;height:32px;object-fit:contain;"></span><span class="label">Ebooks</span>
        </a>
        <a href="{{ route('student.workspace') }}"   class="nav-item @yield('nav_workspace', '')">
            <span class="icon"><img src="{{ asset('uploads/images/workspace/Preset.png') }}" alt="Workspace" style="width:30px;height:32px;object-fit:contain;"></span><span class="label">Workspace</span>
        </a>
        
        <a href="{{ route('student.profile') }}"     class="nav-item @yield('nav_profile', '')">
            <span class="icon"><img src="{{ asset('uploads/images/icons/profile.png') }}" alt="Profile" style="width:30px;height:32px;object-fit:contain;"></span><span class="label">Profile</span>
        </a>
    </nav>

    <div class="main">
        <!-- Topbar -->
        <div class="topbar bg-transparent border-0 shadow-none p-3 px-md-4 py-md-4 d-flex justify-content-between align-items-center w-100">
            <!-- Mobile spacer to center logo -->
            <div class="d-md-none" style="width: 48px;"></div>
            
            <div class="topbar-logo text-center">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="img-fluid" style="height: 56px; max-height: 8vh; object-fit: contain;">
            </div>
            
            <div class="topbar-actions d-flex align-items-center gap-2 gap-md-3 position-relative">
                <div id="notification-bell" class="tb-btn shadow-sm position-relative" style="width: 44px; height: 44px; background: #FFFFFF; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; border: 2px solid var(--border);">
                    <svg class="bell-icon" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#FFB37C" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                        <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                    </svg>
                    @if(auth()->check() && auth()->user()->unreadNotifications->count() > 0)
                        <div class="position-absolute d-flex align-items-center justify-content-center" style="top: -5px; right: -5px; min-width: 20px; height: 20px; padding: 0 4px; background: #FF4B4B; border-radius: 10px; border: 2px solid #FFF; color: white; font-size: 11px; font-weight: bold;">
                            {{ auth()->user()->unreadNotifications->count() }}
                        </div>
                    @endif
                </div>
                
                <!-- Notification Panel -->
                <div id="notification-panel" style="display: none; position: absolute; top: 56px; right: 0; width: 320px; max-width: 90vw; background: #FFFFFF; border-radius: 24px; box-shadow: 0 12px 48px rgba(0,0,0,0.12); z-index: 1000; overflow: hidden; border: 1px solid var(--border);">
                    <div style="padding: 16px 20px; background: var(--bg2); border-bottom: 1px solid var(--border); font-weight: 900; font-size: 16px; color: #1a4f66; display: flex; justify-content: space-between; align-items: center;">
                        <span>Notifications</span>
                        @if(auth()->check() && auth()->user()->unreadNotifications->count() > 0)
                            <span style="background: #FF4B4B; color: #FFF; font-size: 11px; padding: 2px 8px; border-radius: 999px;">{{ auth()->user()->unreadNotifications->count() }} New</span>
                        @endif
                    </div>
                    <div style="max-height: 320px; overflow-y: auto;">
                        @if(auth()->check() && auth()->user()->notifications->count() > 0)
                            @foreach(auth()->user()->unreadNotifications as $notification)
                                <a href="{{ route('student.notifications.read', $notification->id) }}" style="text-decoration: none; display: block; padding: 16px 20px; border-bottom: 1px solid var(--border); display: flex; gap: 12px; align-items: flex-start; background: rgba(0,212,170,0.05); transition: background 0.2s; cursor: pointer;" onmouseover="this.style.background='rgba(0,212,170,0.1)'" onmouseout="this.style.background='rgba(0,212,170,0.05)'">
                                    <div style="font-size: 24px; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1));">{{ $notification->data['icon'] ?? '🔔' }}</div>
                                    <div>
                                        <div style="font-weight: 800; font-size: 14px; margin-bottom: 4px; color: #1a4f66;">{{ $notification->data['title'] ?? 'Notification' }}</div>
                                        <div style="font-size: 12px; color: var(--muted); font-weight: 600; line-height: 1.4;">{{ $notification->data['message'] ?? 'You have a new message.' }}</div>
                                        <div style="font-size: 10px; color: #A0AAB2; font-weight: 700; margin-top: 8px;">{{ $notification->created_at->diffForHumans() }}</div>
                                    </div>
                                </a>
                            @endforeach
                            @foreach(auth()->user()->readNotifications->take(5) as $notification)
                                <a href="{{ $notification->data['link'] ?? '#' }}" style="text-decoration: none; display: block; padding: 16px 20px; border-bottom: 1px solid var(--border); display: flex; gap: 12px; align-items: flex-start; transition: background 0.2s; cursor: pointer;" onmouseover="this.style.background='#F9FBFC'" onmouseout="this.style.background='transparent'">
                                    <div style="font-size: 24px; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1)); opacity: 0.7;">{{ $notification->data['icon'] ?? '🔔' }}</div>
                                    <div style="opacity: 0.7;">
                                        <div style="font-weight: 800; font-size: 14px; margin-bottom: 4px; color: #1a4f66;">{{ $notification->data['title'] ?? 'Notification' }}</div>
                                        <div style="font-size: 12px; color: var(--muted); font-weight: 600; line-height: 1.4;">{{ $notification->data['message'] ?? 'You have a new message.' }}</div>
                                        <div style="font-size: 10px; color: #A0AAB2; font-weight: 700; margin-top: 8px;">{{ $notification->created_at->diffForHumans() }}</div>
                                    </div>
                                </a>
                            @endforeach
                        @else
                            <div style="padding: 30px 20px; text-align: center; color: var(--muted); font-weight: 600; font-size: 14px;">
                                No notifications yet.
                            </div>
                        @endif
                    </div>
                    @if(auth()->check() && auth()->user()->unreadNotifications->count() > 0)
                        <form id="markAllReadForm" action="{{ route('student.notifications.markAllRead') ?? '#' }}" method="POST" style="margin: 0;" onsubmit="handleMarkAllRead(event)">
                            @csrf
                            <button type="submit" style="width: 100%; border: none; padding: 12px; text-align: center; border-top: 1px solid var(--border); font-size: 13px; font-weight: 800; color: var(--accent); cursor: pointer; background: #FAFAFA; transition: background 0.2s;" onmouseover="this.style.background='#F1F1F1'" onmouseout="this.style.background='#FAFAFA'">
                                Mark all as read
                            </button>
                        </form>
                    @endif
                </div>

                <a href="{{ route('student.profile') }}" class="tb-btn shadow-sm d-none d-md-flex" style="width: 50px; height: 50px; background: #FFD561; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 28px; text-decoration: none; border: 3px solid #FFF; z-index: 10;">
                    👦
                </a>
            </div>
        </div>

        <div class="content">
            @if(session('stage_complete'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                showStageCompleteToast(@json(session('stage_complete')));
            });
        </script>
    @elseif(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                showGlobalToast("{{ session('success') }}", 'success');
            });
        </script>
    @endif
            @if(session('error'))
                <script>document.addEventListener('DOMContentLoaded', () => showGlobalToast("{{ session('error') }}", 'error'));</script>
            @endif
            @if(session('info'))
                <script>document.addEventListener('DOMContentLoaded', () => showGlobalToast("{{ session('info') }}", 'info'));</script>
            @endif

            @yield('content')
        </div>
    </div>

    @stack('scripts')
    <script>
        function showGlobalToast(message, type = 'success') {
            const toast = document.createElement('div');
            
            let iconSvg = '';
            if (type === 'success') {
                iconSvg = `<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>`;
            } else if (type === 'error') {
                iconSvg = `<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>`;
            } else {
                iconSvg = `<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>`;
            }
            
            toast.innerHTML = `<span style="display: flex; align-items: center; justify-content: center; margin-right: 12px; filter: drop-shadow(0 2px 2px rgba(0,0,0,0.2));">${iconSvg}</span> <span>${message}</span>`;
            toast.style.position = 'fixed';
            toast.style.top = '30px';
            toast.style.left = '50%';
            toast.style.transform = 'translateX(-50%) scale(0.8)';
            toast.style.display = 'flex';
            toast.style.alignItems = 'center';
            toast.style.justifyContent = 'center';
            
            if (type === 'error') {
                toast.style.background = 'linear-gradient(180deg, #FF9999, #FF6B6B)';
                toast.style.border = '3px solid #FFF';
                toast.style.boxShadow = '0 0 0 3px #FF4B4B, 0 6px 0 3px #D32F2F';
                toast.style.color = '#FFF';
            } else if (type === 'info') {
                toast.style.background = 'linear-gradient(180deg, #8BDDFF, #4FC3F7)';
                toast.style.border = '3px solid #FFF';
                toast.style.boxShadow = '0 0 0 3px #29B6F6, 0 6px 0 3px #0288D1';
                toast.style.color = '#FFF';
            } else { // success
                toast.style.background = 'linear-gradient(180deg, #9DE182, #68CC45)';
                toast.style.border = '3px solid #FFF';
                toast.style.boxShadow = '0 0 0 3px #3AAA5B, 0 6px 0 3px #2E7D32';
                toast.style.color = '#FFF';
            }
            
            toast.querySelector('span:last-child').style.textShadow = '0 2px 4px rgba(0,0,0,0.3)';
            
            toast.style.padding = '10px 28px';
            toast.style.borderRadius = '8px';
            toast.style.fontFamily = "'Quicksand', sans-serif";
            toast.style.fontWeight = '900';
            toast.style.fontSize = '18px';
            toast.style.zIndex = '99999';
            toast.style.opacity = '0';
            toast.style.transition = 'all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275)';
            
            document.body.appendChild(toast);

            setTimeout(() => {
                toast.style.opacity = '1';
                toast.style.transform = 'translateX(-50%) scale(1)';
            }, 10);

            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateX(-50%) scale(0.8)';
                setTimeout(() => toast.remove(), 500);
            }, 3500);
        }

        function showStageCompleteToast(data) {
            const toast = document.createElement('div');
            const currentStage = data.stage || 1;
            const nextStage = data.next_stage || null;
            const xp = data.xp || 0;
            const isLevelComplete = data.is_level_complete || false;
            const levelNumber = data.level_number || 1;

            const titleText = isLevelComplete ? `Level ${levelNumber} Complete!` : `Stage ${currentStage} Complete!`;

            toast.style.position = 'fixed';
            toast.style.top = '70px'; // moved down to avoid back button
            toast.style.left = '50%';
            toast.style.width = '92%';
            toast.style.maxWidth = '420px';
            toast.style.transform = 'translateX(-50%) translateY(-20px) scale(0.9)';
            toast.style.opacity = '0';
            toast.style.zIndex = '99999';
            toast.style.transition = 'all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275)';

            toast.innerHTML = `
                <div style="background: linear-gradient(90deg, #FFFFFF, #F5F0FF); border-radius: 8px; padding: 10px 14px; display: flex; align-items: center; gap: 10px; box-shadow: 0 12px 32px rgba(138, 90, 255, 0.15), inset 0 3px 0 rgba(255,255,255,1); border: 2px solid #D6C3FF; position: relative; width: 100%; box-sizing: border-box;">
                    
                    <div onclick="this.parentElement.parentElement.remove()" style="position: absolute; top: -6px; right: -6px; width: 24px; height: 24px; background: #C4A1FF; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; border: 2px solid #FFF; box-shadow: 0 4px 8px rgba(0,0,0,0.1); transition: transform 0.2s; z-index: 10;" onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#FFF" stroke-width="4" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </div>

                    <div style="position: relative; width: 54px; height: 54px; flex-shrink: 0; filter: drop-shadow(0 6px 8px rgba(124, 77, 255, 0.35)); margin-left: -4px;">
                        <svg viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg" style="position: absolute; inset: 0;">
                            <path d="M12 45 Q 8 60 5 70 Q 18 65 25 70 Z" fill="#6C83E5"/>
                            <path d="M68 45 Q 72 60 75 70 Q 62 65 55 70 Z" fill="#6C83E5"/>
                            <path d="M40 5 L 12 18 V 40 C 12 55 25 70 40 78 C 55 70 68 55 68 40 V 18 Z" fill="#FFD54F"/>
                            <path d="M40 12 L 18 22 V 40 C 18 52 27 63 40 70 C 53 63 62 52 62 40 V 22 Z" fill="#8C54FF"/>
                            <path d="M28 40 L 36 48 L 52 28" stroke="#FFF" stroke-width="7" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <div style="position: absolute; top: 8px; left: -8px;"><svg width="10" height="10" viewBox="0 0 24 24" fill="#FFCA28"><path d="M12 2l2.4 7.6H22l-6.2 4.5 2.4 7.6L12 17.2l-6.2 4.5 2.4-7.6L2 9.6h7.6z"/></svg></div>
                        <div style="position: absolute; bottom: 0px; left: -2px;"><svg width="6" height="6" viewBox="0 0 24 24" fill="#FFCA28"><path d="M12 2l2.4 7.6H22l-6.2 4.5 2.4 7.6L12 17.2l-6.2 4.5 2.4-7.6L2 9.6h7.6z"/></svg></div>
                        <div style="position: absolute; top: 20px; right: -10px;"><svg width="12" height="12" viewBox="0 0 24 24" fill="#FFCA28"><path d="M12 2l2.4 7.6H22l-6.2 4.5 2.4 7.6L12 17.2l-6.2 4.5 2.4-7.6L2 9.6h7.6z"/></svg></div>
                    </div>

                    <div style="flex: 1; display: flex; flex-direction: column; justify-content: center;">
                        <h2 style="font-family: 'Quicksand', sans-serif; font-size: 16px; font-weight: 900; color: #2D1A54; margin: 0 0 1px 0; letter-spacing: -0.2px;">${titleText}</h2>
                        <div style="font-family: 'Quicksand', sans-serif; font-size: 11px; font-weight: 800; color: #5B4F81; display: flex; align-items: center; flex-wrap: wrap; gap: 2px;">
                            ${xp > 0 ? `
                                Great job! You earned 
                                <span style="display: inline-flex; align-items: center;">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="#FFC107" style="margin-left: 2px; filter: drop-shadow(0 2px 2px rgba(255,193,7,0.4));"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg> 
                                    <span style="color: #7C4DFF; font-weight: 900; font-size: 12px; margin-left: 2px;">+${xp} XP</span>
                                </span>
                            ` : `Great job!`}
                        </div>
                    </div>

                    <div style="width: 2px; height: 38px; border-left: 2px dashed #D6C3FF; margin: 0 2px;"></div>

                    <div style="display: flex; align-items: center; justify-content: center; position: relative; padding-right: 4px; margin-left: 4px; flex-shrink: 0;">
                        <div style="position: relative; z-index: 2;">
                            <div style="width: 36px; height: 36px; background: #9E6CFF; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-family: 'Quicksand', sans-serif; font-weight: 900; font-size: 16px; color: #FFF; box-shadow: inset 0 -2px 0 rgba(0,0,0,0.15), 0 3px 6px rgba(158, 108, 255, 0.4);">
                                ${currentStage}
                            </div>
                            <div style="position: absolute; top: -2px; right: -2px; width: 14px; height: 14px; background: #26C281; border-radius: 50%; border: 2px solid #FFF; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                <svg width="8" height="8" viewBox="0 0 24 24" fill="none" stroke="#FFF" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            </div>
                        </div>

                        <div style="width: 18px; height: 3px; background: #C4A1FF; margin: 0 -2px; z-index: 1;"></div>

                        <div style="position: relative; z-index: 2;">
                            <div style="width: 36px; height: 36px; background: #E8DDFF; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-family: 'Quicksand', sans-serif; font-weight: 900; font-size: 16px; color: #A57DFF; box-shadow: inset 0 -2px 0 rgba(255,255,255,0.7);">
                                ${nextStage || '✔'}
                            </div>
                            ${nextStage ? `
                            <div style="position: absolute; top: -2px; right: -2px; width: 14px; height: 14px; background: #B388FF; border-radius: 50%; border: 2px solid #FFF; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                <svg width="6" height="8" viewBox="0 0 24 24" fill="none" stroke="#FFF" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                            </div>
                            ` : ''}
                        </div>
                    </div>

                </div>
            `;
            
            document.body.appendChild(toast);

            setTimeout(() => {
                toast.style.opacity = '1';
                toast.style.transform = 'translateX(-50%) translateY(0) scale(1)';
            }, 10);

            setTimeout(() => {
                if (toast) {
                    toast.style.opacity = '0';
                    toast.style.transform = 'translateX(-50%) translateY(-20px) scale(0.9)';
                    setTimeout(() => toast.remove(), 500);
                }
            }, 6000); 
        }

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

        // Show global loader programmatically
        function showGlobalLoader(event, url = null) {
            if (event) event.preventDefault();
            const loader = document.getElementById('global-page-loader');
            if (loader) {
                loader.style.display = 'flex';
                // trigger reflow
                void loader.offsetWidth;
                loader.style.opacity = '1';
            }
            if (url) {
                setTimeout(() => {
                    window.location.href = url;
                }, 50);
            }
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

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
    @include('partials.pwa_popup')
    <script>
        async function handleMarkAllRead(e) {
            e.preventDefault();
            const form = e.target;
            try {
                const res = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value,
                        'Accept': 'application/json'
                    }
                });
                if (res.ok) {
                    const badge = document.querySelector('#notification-bell .position-absolute');
                    if (badge) badge.style.display = 'none';

                    const newLabel = document.querySelector('#notification-panel span[style*="background: #FF4B4B"]');
                    if (newLabel) newLabel.style.display = 'none';

                    const unreadLinks = document.querySelectorAll('#notification-panel a[style*="rgba(0,212,170,0.05)"]');
                    unreadLinks.forEach(a => {
                        a.style.background = 'transparent';
                        a.onmouseout = function() { this.style.background='transparent' };
                        a.onmouseover = function() { this.style.background='#F9FBFC' };
                    });

                    form.style.display = 'none';
            const currentStage = data.stage || 1;
            const nextStage = data.next_stage || null;
            const xp = data.xp || 0;
            const isLevelComplete = data.is_level_complete || false;
            const levelNumber = data.level_number || 1;

            const titleText = isLevelComplete ? `Level ${levelNumber} Complete!` : `Stage ${currentStage} Complete!`;

            toast.style.position = 'fixed';
            toast.style.top = '70px'; // moved down to avoid back button
            toast.style.left = '50%';
            toast.style.width = '92%';
            toast.style.maxWidth = '420px';
            toast.style.transform = 'translateX(-50%) translateY(-20px) scale(0.9)';
            toast.style.opacity = '0';
            toast.style.zIndex = '99999';
            toast.style.transition = 'all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275)';

            toast.innerHTML = `
                <div style="background: linear-gradient(90deg, #FFFFFF, #F5F0FF); border-radius: 8px; padding: 10px 14px; display: flex; align-items: center; gap: 10px; box-shadow: 0 12px 32px rgba(138, 90, 255, 0.15), inset 0 3px 0 rgba(255,255,255,1); border: 2px solid #D6C3FF; position: relative; width: 100%; box-sizing: border-box;">
                    
                    <div onclick="this.parentElement.parentElement.remove()" style="position: absolute; top: -6px; right: -6px; width: 24px; height: 24px; background: #C4A1FF; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; border: 2px solid #FFF; box-shadow: 0 4px 8px rgba(0,0,0,0.1); transition: transform 0.2s; z-index: 10;" onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#FFF" stroke-width="4" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </div>

                    <div style="position: relative; width: 54px; height: 54px; flex-shrink: 0; filter: drop-shadow(0 6px 8px rgba(124, 77, 255, 0.35)); margin-left: -4px;">
                        <svg viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg" style="position: absolute; inset: 0;">
                            <path d="M12 45 Q 8 60 5 70 Q 18 65 25 70 Z" fill="#6C83E5"/>
                            <path d="M68 45 Q 72 60 75 70 Q 62 65 55 70 Z" fill="#6C83E5"/>
                            <path d="M40 5 L 12 18 V 40 C 12 55 25 70 40 78 C 55 70 68 55 68 40 V 18 Z" fill="#FFD54F"/>
                            <path d="M40 12 L 18 22 V 40 C 18 52 27 63 40 70 C 53 63 62 52 62 40 V 22 Z" fill="#8C54FF"/>
                            <path d="M28 40 L 36 48 L 52 28" stroke="#FFF" stroke-width="7" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <div style="position: absolute; top: 8px; left: -8px;"><svg width="10" height="10" viewBox="0 0 24 24" fill="#FFCA28"><path d="M12 2l2.4 7.6H22l-6.2 4.5 2.4 7.6L12 17.2l-6.2 4.5 2.4-7.6L2 9.6h7.6z"/></svg></div>
                        <div style="position: absolute; bottom: 0px; left: -2px;"><svg width="6" height="6" viewBox="0 0 24 24" fill="#FFCA28"><path d="M12 2l2.4 7.6H22l-6.2 4.5 2.4 7.6L12 17.2l-6.2 4.5 2.4-7.6L2 9.6h7.6z"/></svg></div>
                        <div style="position: absolute; top: 20px; right: -10px;"><svg width="12" height="12" viewBox="0 0 24 24" fill="#FFCA28"><path d="M12 2l2.4 7.6H22l-6.2 4.5 2.4 7.6L12 17.2l-6.2 4.5 2.4-7.6L2 9.6h7.6z"/></svg></div>
                    </div>

                    <div style="flex: 1; display: flex; flex-direction: column; justify-content: center;">
                        <h2 style="font-family: 'Quicksand', sans-serif; font-size: 16px; font-weight: 900; color: #2D1A54; margin: 0 0 1px 0; letter-spacing: -0.2px;">${titleText}</h2>
                        <div style="font-family: 'Quicksand', sans-serif; font-size: 11px; font-weight: 800; color: #5B4F81; display: flex; align-items: center; flex-wrap: wrap; gap: 2px;">
                            ${xp > 0 ? `
                                Great job! You earned 
                                <span style="display: inline-flex; align-items: center;">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="#FFC107" style="margin-left: 2px; filter: drop-shadow(0 2px 2px rgba(255,193,7,0.4));"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg> 
                                    <span style="color: #7C4DFF; font-weight: 900; font-size: 12px; margin-left: 2px;">+${xp} XP</span>
                                </span>
                            ` : `Great job!`}
                        </div>
                    </div>

                    <div style="width: 2px; height: 38px; border-left: 2px dashed #D6C3FF; margin: 0 2px;"></div>

                    <div style="display: flex; align-items: center; justify-content: center; position: relative; padding-right: 4px; margin-left: 4px; flex-shrink: 0;">
                        <div style="position: relative; z-index: 2;">
                            <div style="width: 36px; height: 36px; background: #9E6CFF; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-family: 'Quicksand', sans-serif; font-weight: 900; font-size: 16px; color: #FFF; box-shadow: inset 0 -2px 0 rgba(0,0,0,0.15), 0 3px 6px rgba(158, 108, 255, 0.4);">
                                ${currentStage}
                            </div>
                            <div style="position: absolute; top: -2px; right: -2px; width: 14px; height: 14px; background: #26C281; border-radius: 50%; border: 2px solid #FFF; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                <svg width="8" height="8" viewBox="0 0 24 24" fill="none" stroke="#FFF" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            </div>
                        </div>

                        <div style="width: 18px; height: 3px; background: #C4A1FF; margin: 0 -2px; z-index: 1;"></div>

                        <div style="position: relative; z-index: 2;">
                            <div style="width: 36px; height: 36px; background: #E8DDFF; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-family: 'Quicksand', sans-serif; font-weight: 900; font-size: 16px; color: #A57DFF; box-shadow: inset 0 -2px 0 rgba(255,255,255,0.7);">
                                ${nextStage || '✔'}
                            </div>
                            ${nextStage ? `
                            <div style="position: absolute; top: -2px; right: -2px; width: 14px; height: 14px; background: #B388FF; border-radius: 50%; border: 2px solid #FFF; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                <svg width="6" height="8" viewBox="0 0 24 24" fill="none" stroke="#FFF" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                            </div>
                            ` : ''}
                        </div>
                    </div>

                </div>
            `;
            
            document.body.appendChild(toast);

            setTimeout(() => {
                toast.style.opacity = '1';
                toast.style.transform = 'translateX(-50%) translateY(0) scale(1)';
            }, 10);

            setTimeout(() => {
                if (toast) {
                    toast.style.opacity = '0';
                    toast.style.transform = 'translateX(-50%) translateY(-20px) scale(0.9)';
                    setTimeout(() => toast.remove(), 500);
                }
            }, 6000); 
        }

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

        // Show global loader programmatically
        function showGlobalLoader(event, url = null) {
            if (event) event.preventDefault();
            const loader = document.getElementById('global-page-loader');
            if (loader) {
                loader.style.display = 'flex';
                // trigger reflow
                void loader.offsetWidth;
                loader.style.opacity = '1';
            }
            if (url) {
                setTimeout(() => {
                    window.location.href = url;
                }, 50);
            }
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

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
    @include('partials.pwa_popup')
    <script>
        async function handleMarkAllRead(e) {
            e.preventDefault();
            const form = e.target;
            try {
                const res = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value,
                        'Accept': 'application/json'
                    }
                });
                if (res.ok) {
                    const badge = document.querySelector('#notification-bell .position-absolute');
                    if (badge) badge.style.display = 'none';

                    const newLabel = document.querySelector('#notification-panel span[style*="background: #FF4B4B"]');
                    if (newLabel) newLabel.style.display = 'none';

                    const unreadLinks = document.querySelectorAll('#notification-panel a[style*="rgba(0,212,170,0.05)"]');
                    unreadLinks.forEach(a => {
                        a.style.background = 'transparent';
                        a.onmouseout = function() { this.style.background='transparent' };
                        a.onmouseover = function() { this.style.background='#F9FBFC' };
                    });

                    form.style.display = 'none';
                }
            } catch(err) {
                console.error('Error marking notifications as read:', err);
            }
        }
    </script>
    <script src="{{ asset('js/mascot-engine.js') }}?v={{ time() }}"></script>
</body>
</html>
