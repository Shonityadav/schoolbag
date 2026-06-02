<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') | SchoolBag Admin</title>

    {{-- Bootstrap 5 --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    {{-- Inter Font --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --sb-sidebar-bg:    #0F172A;
            --sb-sidebar-w:     256px;
            --sb-accent:        #2563EB;
            --sb-accent-hover:  #1D4ED8;
            --sb-bg:            #F8FAFC;
            --sb-card:          #FFFFFF;
            --sb-border:        #E2E8F0;
            --sb-text:          #0F172A;
            --sb-muted:         #64748B;
            --sb-green:         #059669;
            --sb-orange:        #D97706;
            --sb-red:           #DC2626;
            --sb-purple:        #7C3AED;
            --sb-topbar-h:      64px;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--sb-bg);
            color: var(--sb-text);
            font-size: 14px;
            line-height: 1.5;
        }

        /* ════════════════════════════
           SIDEBAR
        ════════════════════════════ */
        .sb-sidebar {
            position: fixed;
            top: 0; left: 0; bottom: 0;
            width: var(--sb-sidebar-w);
            background: var(--sb-sidebar-bg);
            display: flex;
            flex-direction: column;
            z-index: 300;
            overflow-y: auto;
            overflow-x: hidden;
            transition: transform 0.3s ease;
        }
        .sb-sidebar::-webkit-scrollbar { width: 0; }

        /* Logo */
        .sb-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 20px 20px 18px;
            border-bottom: 1px solid rgba(255,255,255,0.06);
            text-decoration: none;
        }
        .sb-logo-icon {
            width: 34px; height: 34px;
            background: var(--sb-accent);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .sb-logo-icon i { font-size: 18px; color: #fff; }
        .sb-logo-text { color: #fff; font-size: 15px; font-weight: 600; letter-spacing: -0.2px; }
        .sb-logo-sub  { color: rgba(255,255,255,0.35); font-size: 11px; font-weight: 400; margin-top: 1px; text-transform: uppercase; letter-spacing: 0.5px; }

        /* Nav */
        .sb-nav { padding: 12px 12px; flex: 1; }
        .sb-nav-group { margin-bottom: 20px; }
        .sb-nav-label {
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: rgba(255,255,255,0.25);
            padding: 0 8px 6px;
        }
        .sb-nav-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 10px;
            border-radius: 7px;
            color: rgba(255,255,255,0.55);
            text-decoration: none;
            font-size: 13.5px;
            font-weight: 450;
            transition: background 0.15s, color 0.15s;
            margin-bottom: 1px;
        }
        .sb-nav-link i { font-size: 15px; width: 18px; text-align: center; flex-shrink: 0; }
        .sb-nav-link:hover { background: rgba(255,255,255,0.07); color: rgba(255,255,255,0.9); }
        .sb-nav-link.active { background: var(--sb-accent); color: #fff; font-weight: 500; }
        .sb-nav-badge {
            margin-left: auto;
            background: rgba(255,255,255,0.1);
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
            padding: 1px 6px;
            color: rgba(255,255,255,0.65);
        }
        .sb-nav-link.active .sb-nav-badge { background: rgba(255,255,255,0.2); color: #fff; }

        /* Sidebar Footer */
        .sb-sidebar-footer {
            padding: 14px 16px;
            border-top: 1px solid rgba(255,255,255,0.06);
        }
        .sb-user {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .sb-user-avatar {
            width: 34px; height: 34px;
            background: rgba(37,99,235,0.3);
            border: 1px solid rgba(37,99,235,0.5);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .sb-user-avatar i { font-size: 17px; color: #93C5FD; }
        .sb-user-name { font-size: 13px; font-weight: 600; color: #fff; }
        .sb-user-role { font-size: 11px; color: rgba(255,255,255,0.35); }

        /* ════════════════════════════
           TOPBAR
        ════════════════════════════ */
        .sb-topbar {
            position: fixed;
            top: 0;
            left: var(--sb-sidebar-w);
            right: 0;
            height: var(--sb-topbar-h);
            background: var(--sb-card);
            border-bottom: 1px solid var(--sb-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 28px;
            z-index: 200;
        }
        .sb-page-title { font-size: 16px; font-weight: 600; color: var(--sb-text); }
        .sb-breadcrumb { display: flex; align-items: center; gap: 6px; font-size: 12px; color: var(--sb-muted); margin-top: 1px; }
        .sb-breadcrumb i { font-size: 10px; }

        .sb-topbar-right { display: flex; align-items: center; gap: 8px; }
        .sb-icon-btn {
            width: 36px; height: 36px;
            border-radius: 7px;
            border: 1px solid var(--sb-border);
            background: transparent;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer;
            color: var(--sb-muted);
            font-size: 16px;
            transition: all 0.15s;
            text-decoration: none;
            position: relative;
        }
        .sb-icon-btn:hover { background: var(--sb-bg); color: var(--sb-text); border-color: #CBD5E1; }
        .sb-notif-dot {
            position: absolute; top: 6px; right: 6px;
            width: 7px; height: 7px;
            background: var(--sb-red);
            border-radius: 50%;
            border: 1.5px solid #fff;
        }
        .sb-divider-v { width: 1px; height: 24px; background: var(--sb-border); margin: 0 4px; }
        .sb-topbar-avatar {
            width: 34px; height: 34px;
            background: #EEF2FF;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer;
            font-size: 16px;
            color: var(--sb-accent);
            border: 2px solid var(--sb-border);
        }
        .sb-search {
            display: flex;
            align-items: center;
            gap: 8px;
            background: var(--sb-bg);
            border: 1px solid var(--sb-border);
            border-radius: 7px;
            padding: 0 12px;
            height: 36px;
            width: 220px;
        }
        .sb-search i { color: var(--sb-muted); font-size: 14px; }
        .sb-search input {
            border: none;
            background: transparent;
            font-size: 13px;
            font-family: 'Inter', sans-serif;
            color: var(--sb-text);
            outline: none;
            width: 100%;
        }
        .sb-search input::placeholder { color: var(--sb-muted); }

        /* ════════════════════════════
           MAIN CONTENT
        ════════════════════════════ */
        .sb-main {
            margin-left: var(--sb-sidebar-w);
            padding-top: var(--sb-topbar-h);
            min-height: 100vh;
        }
        .sb-content { padding: 28px; }

        /* ════════════════════════════
           STAT CARDS
        ════════════════════════════ */
        .sb-stat-card {
            background: var(--sb-card);
            border: 1px solid var(--sb-border);
            border-radius: 10px;
            padding: 22px 22px 18px;
            transition: box-shadow 0.2s, transform 0.2s;
        }
        .sb-stat-card:hover { box-shadow: 0 4px 20px rgba(0,0,0,0.07); transform: translateY(-1px); }
        .sb-stat-icon {
            width: 40px; height: 40px;
            border-radius: 9px;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }
        .sb-stat-icon.blue   { background: #EFF6FF; color: var(--sb-accent); }
        .sb-stat-icon.green  { background: #ECFDF5; color: var(--sb-green); }
        .sb-stat-icon.orange { background: #FFFBEB; color: var(--sb-orange); }
        .sb-stat-icon.purple { background: #F5F3FF; color: var(--sb-purple); }
        .sb-stat-icon.red    { background: #FEF2F2; color: var(--sb-red); }

        .sb-stat-value { font-size: 26px; font-weight: 700; color: var(--sb-text); letter-spacing: -0.5px; line-height: 1.1; }
        .sb-stat-label { font-size: 12.5px; color: var(--sb-muted); font-weight: 450; margin-top: 3px; }
        .sb-stat-change { font-size: 12px; font-weight: 600; padding: 2px 7px; border-radius: 4px; }
        .sb-stat-change.up   { background: #ECFDF5; color: var(--sb-green); }
        .sb-stat-change.down { background: #FEF2F2; color: var(--sb-red); }
        .sb-stat-change.neutral { background: #F1F5F9; color: var(--sb-muted); }

        /* ════════════════════════════
           PANELS
        ════════════════════════════ */
        .sb-panel {
            background: var(--sb-card);
            border: 1px solid var(--sb-border);
            border-radius: 10px;
            overflow: hidden;
        }
        .sb-panel-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 22px;
            border-bottom: 1px solid var(--sb-border);
        }
        .sb-panel-title { font-size: 14px; font-weight: 600; color: var(--sb-text); }
        .sb-panel-action {
            font-size: 12.5px;
            font-weight: 500;
            color: var(--sb-accent);
            text-decoration: none;
        }
        .sb-panel-action:hover { text-decoration: underline; }

        /* ════════════════════════════
           TABLE
        ════════════════════════════ */
        .sb-table { width: 100%; border-collapse: collapse; }
        .sb-table th {
            padding: 10px 20px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.7px;
            color: var(--sb-muted);
            background: #F8FAFC;
            border-bottom: 1px solid var(--sb-border);
            text-align: left;
            white-space: nowrap;
        }
        .sb-table td {
            padding: 13px 20px;
            font-size: 13px;
            color: var(--sb-text);
            border-bottom: 1px solid var(--sb-border);
            vertical-align: middle;
        }
        .sb-table tbody tr:last-child td { border-bottom: none; }
        .sb-table tbody tr:hover td { background: #FAFBFD; }

        .sb-avatar-sm {
            width: 30px; height: 30px;
            border-radius: 50%;
            background: #EEF2FF;
            color: var(--sb-accent);
            display: flex; align-items: center; justify-content: center;
            font-size: 12px;
            font-weight: 600;
            flex-shrink: 0;
        }

        /* ════════════════════════════
           STATUS BADGES
        ════════════════════════════ */
        .sb-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 3px 9px;
            border-radius: 4px;
            font-size: 11.5px;
            font-weight: 600;
        }
        .sb-badge::before {
            content: '';
            width: 5px; height: 5px;
            border-radius: 50%;
            background: currentColor;
        }
        .sb-badge.paid    { background: #ECFDF5; color: var(--sb-green); }
        .sb-badge.pending { background: #FFFBEB; color: var(--sb-orange); }
        .sb-badge.overdue { background: #FEF2F2; color: var(--sb-red); }

        /* ════════════════════════════
           PROGRESS BAR
        ════════════════════════════ */
        .sb-progress { height: 5px; background: #E2E8F0; border-radius: 999px; overflow: hidden; width: 90px; }
        .sb-progress-fill { height: 100%; background: var(--sb-accent); border-radius: 999px; }

        /* ════════════════════════════
           REVENUE PANEL
        ════════════════════════════ */
        .sb-revenue-num { font-size: 30px; font-weight: 700; letter-spacing: -1px; color: var(--sb-text); }
        .sb-revenue-sub { font-size: 12px; color: var(--sb-muted); margin-top: 2px; }
        .sb-kv { display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid var(--sb-border); }
        .sb-kv:last-child { border-bottom: none; }
        .sb-kv-key { font-size: 13px; color: var(--sb-muted); }
        .sb-kv-val { font-size: 13px; font-weight: 600; color: var(--sb-text); }

        /* ════════════════════════════
           LIST ITEMS
        ════════════════════════════ */
        .sb-list-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 13px 22px;
            border-bottom: 1px solid var(--sb-border);
            transition: background 0.12s;
        }
        .sb-list-item:last-child { border-bottom: none; }
        .sb-list-item:hover { background: #FAFBFD; }
        .sb-list-avatar {
            width: 34px; height: 34px;
            border-radius: 50%;
            background: #EEF2FF;
            color: var(--sb-accent);
            display: flex; align-items: center; justify-content: center;
            font-size: 15px;
            flex-shrink: 0;
            font-weight: 600;
        }
        .sb-list-title { font-size: 13.5px; font-weight: 600; color: var(--sb-text); }
        .sb-list-sub   { font-size: 12px; color: var(--sb-muted); margin-top: 1px; }
        .sb-list-meta  { font-size: 12px; color: var(--sb-muted); margin-left: auto; white-space: nowrap; }

        /* ════════════════════════════
           MOBILE TOGGLE
        ════════════════════════════ */
        .sb-mobile-toggle {
            display: none;
            align-items: center;
            justify-content: center;
            width: 36px; height: 36px;
            border: 1px solid var(--sb-border);
            border-radius: 7px;
            background: transparent;
            cursor: pointer;
            color: var(--sb-muted);
            font-size: 18px;
        }

        /* ════════════════════════════
           RESPONSIVE
        ════════════════════════════ */
        @media (max-width: 992px) {
            :root { --sb-sidebar-w: 0px; }
            .sb-sidebar { transform: translateX(-256px); width: 256px; }
            .sb-sidebar.open { transform: translateX(0); }
            .sb-topbar { left: 0; }
            .sb-main { margin-left: 0; }
            .sb-mobile-toggle { display: flex; }
            .sb-search { display: none; }
        }
        @media (max-width: 576px) {
            .sb-content { padding: 16px; }
            .sb-topbar { padding: 0 16px; }
        }
    </style>
    @stack('admin-styles')
</head>
<body>

{{-- ══════════════════════════════════════
     SIDEBAR
══════════════════════════════════════ --}}
<aside class="sb-sidebar" id="sb-sidebar">
    <a href="{{ route('admin.dashboard') }}" class="sb-logo">
        <div class="sb-logo-icon"><i class="bi bi-grid-1x2-fill"></i></div>
        <div>
            <div class="sb-logo-text">SchoolBag</div>
            <div class="sb-logo-sub">Admin Console</div>
        </div>
    </a>

    <nav class="sb-nav">
        <div class="sb-nav-group">
            <div class="sb-nav-label">Main</div>
            <a href="{{ route('admin.dashboard') }}" class="sb-nav-link @yield('admin_nav_dashboard', '')">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
        </div>

        <div class="sb-nav-group">
            <div class="sb-nav-label">People</div>
            <a href="{{ route('admin.students.index') }}" class="sb-nav-link @yield('admin_nav_students', '')">
                <i class="bi bi-people"></i> Students
                <span class="sb-nav-badge">{{ \App\Models\User::where('role','student')->count() }}</span>
            </a>
            <a href="{{ route('admin.staff.index') }}" class="sb-nav-link @yield('admin_nav_staff', '')">
                <i class="bi bi-person-badge"></i> Staff
                <span class="sb-nav-badge">{{ \App\Models\User::where('role','admin')->count() }}</span>
            </a>
            <a href="{{ route('admin.classes.index') }}" class="sb-nav-link @yield('admin_nav_classes', '')">
                <i class="bi bi-building"></i> Classes
                <span class="sb-nav-badge">{{ \App\Models\StudentClass::count() }}</span>
            </a>
        </div>

        <div class="sb-nav-group">
            <div class="sb-nav-label">Academic</div>
            <a href="#" class="sb-nav-link @yield('admin_nav_courses', '')">
                <i class="bi bi-journal-bookmark"></i> Courses
            </a>
            <a href="#" class="sb-nav-link">
                <i class="bi bi-calendar3"></i> Attendance
            </a>
            <a href="#" class="sb-nav-link">
                <i class="bi bi-clipboard-data"></i> Results
            </a>
        </div>

        <div class="sb-nav-group">
            <div class="sb-nav-label">Finance</div>
            <a href="#" class="sb-nav-link @yield('admin_nav_fees', '')">
                <i class="bi bi-credit-card"></i> Fee Collection
            </a>
            <a href="#" class="sb-nav-link @yield('admin_nav_transactions', '')">
                <i class="bi bi-receipt"></i> Transactions
            </a>
            <a href="#" class="sb-nav-link @yield('admin_nav_reports', '')">
                <i class="bi bi-bar-chart-line"></i> Reports
            </a>
        </div>

        <div class="sb-nav-group">
            <div class="sb-nav-label">System</div>
            <a href="#" class="sb-nav-link">
                <i class="bi bi-gear"></i> Settings
            </a>
            <a href="#" onclick="event.preventDefault(); document.getElementById('admin-logout-form').submit();" class="sb-nav-link" style="color:rgba(220,38,38,0.6);">
                <i class="bi bi-box-arrow-left"></i> Sign Out
            </a>
            <form id="admin-logout-form" action="{{ route('admin.logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </div>
    </nav>

    <div class="sb-sidebar-footer">
        <div class="sb-user">
            <div class="sb-user-avatar"><i class="bi bi-person-fill"></i></div>
            <div>
                <div class="sb-user-name">{{ Auth::user() ? Auth::user()->name : 'Administrator' }}</div>
                <div class="sb-user-role">{{ Auth::user() && Auth::user()->user_type == 1 ? 'Institute Admin' : 'Admin' }}</div>
            </div>
        </div>
    </div>
</aside>


{{-- ══════════════════════════════════════
     TOPBAR
══════════════════════════════════════ --}}
<div class="sb-topbar">
    <div class="d-flex align-items-center gap-3">
        <button class="sb-mobile-toggle" id="sb-toggle" onclick="document.getElementById('sb-sidebar').classList.toggle('open')">
            <i class="bi bi-list"></i>
        </button>
        <div>
            <div class="sb-page-title">@yield('admin_page_title', 'Dashboard')</div>
            <div class="sb-breadcrumb">
                <span>Admin</span>
                <i class="bi bi-chevron-right"></i>
                <span>@yield('admin_page_title', 'Dashboard')</span>
            </div>
        </div>
    </div>

    <div class="sb-topbar-right">
        <div class="sb-search d-none d-lg-flex">
            <i class="bi bi-search"></i>
            <input type="text" placeholder="Search students, classes…">
        </div>
        <div class="sb-divider-v d-none d-md-block"></div>
        <button class="sb-icon-btn" title="Notifications">
            <i class="bi bi-bell"></i>
            <div class="sb-notif-dot"></div>
        </button>
        <a href="#" class="sb-icon-btn" title="Help">
            <i class="bi bi-question-circle"></i>
        </a>
        <div class="sb-divider-v"></div>
        <div class="sb-topbar-avatar">
            <i class="bi bi-person-fill"></i>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════
     MAIN
══════════════════════════════════════ --}}
<div class="sb-main">
    <div class="sb-content">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-4" role="alert" style="border-radius:8px;font-size:13.5px;">
                <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 mb-4" role="alert" style="border-radius:8px;font-size:13.5px;">
                <i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('admin_content')
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('admin-scripts')
</body>
</html>
