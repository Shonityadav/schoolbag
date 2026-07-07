@extends('layouts.student')

@section('title', 'Profile - I Card')
@section('nav_workspace', 'active')

@push('styles')
<style>
    body {
        background-color: #F0F4F8 !important;
        background-image: none !important;
        margin: 0;
        padding: 0;
        font-family: 'Quicksand', sans-serif;
    }

    .topbar { display: none !important; }

    .header-curve {
        background-color: #472C25;
        height: 220px;
        width: 100%;
        border-bottom-left-radius: 50% 40px;
        border-bottom-right-radius: 50% 40px;
        position: relative;
        z-index: 1;
        transition: background-color 0.3s;
    }

    .back-btn-container {
        position: absolute;
        top: 30px;
        left: 20px;
    }

    .main-content {
        position: relative;
        z-index: 2;
        margin-top: -80px;
        padding: 0 20px;
        max-width: 440px;
        margin-left: auto;
        margin-right: auto;
    }

    .tabs-container {
        display: flex;
        align-items: flex-end;
        padding-left: 10px;
        padding-right: 10px;
        margin-bottom: 0;
    }

    .tab {
        color: #fff;
        font-weight: 900;
        text-align: center;
        border-radius: 12px 12px 0 0;
        padding: 8px 16px;
        margin-right: 4px;
        font-size: 13px;
        box-shadow: inset 0 2px 4px rgba(255,255,255,0.2);
        text-decoration: none;
        width: 25%;
        cursor: pointer;
        transition: width 0.3s, background-color 0.3s, padding 0.3s;
    }

    .tab-active {
        width: 50%;
        padding: 12px 24px;
        font-size: 16px;
        z-index: 3;
        box-shadow: 0 -4px 10px rgba(0,0,0,0.1), inset 0 2px 4px rgba(255,255,255,0.2);
    }
    
    .tab-icard { background-color: #8A4032; }
    .tab-attendance { background-color: #4CAF50; }
    .tab-fees { background-color: #1E88E5; }

    .card-body-main {
        background-color: #FFF9D2;
        border-radius: 20px 20px 20px 20px;
        padding: 24px 20px;
        box-shadow: 0 8px 24px rgba(0,0,0,0.1);
        position: relative;
        z-index: 2;
        transition: background-color 0.3s;
    }

    /* Tab Content Wrappers */
    .tab-content { display: none; }
    .tab-content.active { display: block; animation: fadeIn 0.3s; }
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

    /* I-Card Specific Styles */
    .title-text {
        font-size: 16px;
        font-weight: 900;
        color: #000;
        text-align: center;
        margin-bottom: 20px;
    }

    .id-card-replica {
        background-color: #E6E8E6;
        border-radius: 12px;
        padding: 16px;
        box-shadow: inset 0 0 0 2px rgba(255,255,255,0.5), 0 4px 8px rgba(0,0,0,0.15);
        display: flex;
        flex-direction: column;
        position: relative;
        margin-bottom: 24px;
        border: 1px solid #C4C4C4;
    }

    .id-card-header {
        display: flex;
        align-items: center;
        margin-bottom: 12px;
        border-bottom: 1px solid #CCC;
        padding-bottom: 8px;
    }

    .id-card-logo {
        width: 24px;
        height: 24px;
        background: #3B7FB6;
        color: white;
        font-size: 10px;
        font-weight: bold;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 4px;
        margin-right: 8px;
    }

    .id-card-school-name {
        font-size: 14px;
        font-weight: 900;
        color: #333;
        letter-spacing: 0.5px;
    }

    .id-card-content { display: flex; gap: 12px; }

    .id-card-photo {
        width: 80px;
        height: 100px;
        background: #CCC;
        border: 2px solid #FFF;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        object-fit: cover;
    }

    .id-card-details {
        flex: 1;
        font-size: 10px;
        color: #222;
        line-height: 1.4;
        font-weight: 700;
    }

    .id-card-name {
        font-size: 14px;
        font-weight: 900;
        text-transform: uppercase;
        margin-bottom: 4px;
    }

    .id-card-footer {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        margin-top: 12px;
    }

    .id-card-barcode {
        width: 120px;
        height: 24px;
        background: repeating-linear-gradient(90deg, #000, #000 2px, #fff 2px, #fff 4px, #000 4px, #000 5px, #fff 5px, #fff 8px);
    }

    .id-card-seal {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        border: 2px dashed #999;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 8px;
        color: #999;
        font-weight: bold;
    }

    .guidelines-title { font-size: 14px; font-weight: 900; color: #000; margin-bottom: 8px; }
    .guidelines-list { list-style-type: disc; padding-left: 20px; margin: 0; font-size: 12px; font-weight: 700; color: #000; line-height: 1.6; }

    /* Attendance Specific Styles */
    .att-header-card {
        background: #FFF8D6;
        border-radius: 16px;
        padding: 12px 16px;
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 16px;
    }
    .att-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        border: 2px solid #000;
        background: #FFF;
        object-fit: cover;
    }
    .att-avatar-svg {
        width: 50px; height: 50px; border-radius: 50%; border: 2px solid #000; background: #FFF; fill: #000; padding: 4px;
    }
    .att-user-info { line-height: 1.2; }
    .att-name { font-size: 18px; font-weight: 900; color: #000; margin: 0; }
    .att-class { font-size: 12px; font-weight: 800; color: #333; margin: 0; }
    .att-school { font-size: 11px; font-weight: 700; color: #666; margin: 0; }

    .att-middle-section {
        display: flex;
        gap: 12px;
        margin-bottom: 16px;
    }
    .att-overall-box {
        background: #62B868;
        border-radius: 12px;
        width: 110px;
        flex-shrink: 0;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    .att-overall-header {
        background: #38843D;
        color: white;
        font-size: 12px;
        font-weight: 900;
        width: 100%;
        text-align: center;
        padding: 4px 0;
        border-radius: 12px 12px 0 0;
    }
    .att-progress-ring {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: conic-gradient(#1A5E20 75%, #A5D6A7 0);
        margin: 12px 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .att-progress-inner {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background: #62B868;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 900;
        font-size: 14px;
    }

    .att-summary-box {
        flex: 1;
        display: flex;
        flex-direction: column;
    }
    .att-summary-title {
        font-size: 12px;
        font-weight: 900;
        color: #000;
        text-align: center;
        margin-bottom: 8px;
    }
    .att-summary-cards {
        display: flex;
        gap: 8px;
        justify-content: space-between;
    }
    .att-sum-card {
        background: #FFF;
        border-radius: 12px;
        padding: 6px;
        flex: 1;
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
        border: 1px solid #CCC;
    }
    .att-sum-card.green { border-color: #4CAF50; }
    .att-sum-card.red { border-color: #F44336; }
    .att-sum-card.yellow { border-color: #FFC107; }
    .att-sum-card img { width: 32px; height: 32px; object-fit: contain; margin-bottom: 4px; }
    .att-sum-val { font-size: 10px; font-weight: 900; color: #000; line-height: 1.1; }

    .att-calendar-box {
        background: #E8F8EA;
        border: 2px solid #A8D0A6;
        border-radius: 16px;
        padding: 12px;
        margin-bottom: 16px;
    }
    .calendar-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
    }
    .calendar-header .nav-btn {
        width: 24px; height: 24px; background: #62B868; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; cursor: pointer; font-weight: bold; font-size: 14px; box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }
    .calendar-header h5 { margin: 0; font-size: 14px; font-weight: 900; color: #000; }
    .calendar-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 4px;
        text-align: center;
    }
    .cal-day-name { font-size: 11px; font-weight: 900; color: #333; margin-bottom: 4px; }
    .cal-day-name.sun { color: #F44336; }
    .cal-date {
        font-size: 12px;
        font-weight: 800;
        color: #555;
        width: 24px;
        height: 24px;
        line-height: 24px;
        margin: 0 auto;
        border-radius: 50%;
    }
    .cal-date.sun-date { color: #F44336; }
    .cal-date.present { background: #82CB88; color: #000; }
    .cal-date.absent { background: #FF6B6B; color: #fff; }
    .cal-date.holiday { background: #FFD166; color: #000; }
    
    .cal-legend {
        display: flex;
        justify-content: center;
        gap: 16px;
        margin-top: 12px;
        font-size: 10px;
        font-weight: 900;
        color: #000;
    }
    .legend-item { display: flex; align-items: center; gap: 4px; }
    .legend-dot { width: 10px; height: 10px; border-radius: 50%; }

    .att-important-box {
        background: #E0DFDF;
        border: 1px solid #CCC;
        border-radius: 12px;
        padding: 16px 12px 12px;
        position: relative;
        text-align: center;
        font-size: 11px;
        font-weight: 800;
        color: #554433;
        margin-top: 24px;
    }
    .important-badge {
        position: absolute;
        top: -12px;
        left: -5px;
        background: #6A5ACD;
        color: white;
        font-size: 11px;
        font-weight: 900;
        padding: 4px 12px;
        border-radius: 8px;
        transform: rotate(-3deg);
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }
    .important-badge::before {
        content: '!';
        display: inline-block;
        background: white;
        color: #6A5ACD;
        width: 14px; height: 14px;
        border-radius: 50%;
        text-align: center;
        line-height: 14px;
        margin-right: 4px;
    }

    /* Fees Specific Styles */
    .fees-middle-section {
        display: flex;
        gap: 12px;
        margin-bottom: 16px;
    }
    .fees-overall-box {
        background: #9BD0FF;
        border-radius: 12px;
        width: 110px;
        flex-shrink: 0;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        align-items: center;
        border: 2px solid #E8F4FF;
    }
    .fees-overall-header {
        background: #6FAEFF;
        color: white;
        font-size: 12px;
        font-weight: 900;
        width: 100%;
        text-align: center;
        padding: 4px 0;
        border-radius: 12px 12px 0 0;
    }
    .fees-progress-ring {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: conic-gradient(#1E88E5 75%, #D0E8FF 0);
        margin: 12px 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .fees-progress-inner {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background: #9BD0FF;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #1E88E5;
        font-weight: 900;
        font-size: 16px;
    }

    .fees-summary-box {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    .fees-sum-card {
        background: #FFF;
        border-radius: 12px;
        padding: 6px 12px;
        display: flex;
        align-items: center;
        gap: 12px;
        border: 1px solid #CCC;
    }
    .fees-sum-card.blue { background: #EAF4FF; border-color: #2196F3; }
    .fees-sum-card.green { background: #F2FAEC; border-color: #4CAF50; }
    .fees-sum-card.red { background: #FEF4F4; border-color: #F44336; }
    .fees-sum-card img { width: 30px; height: 30px; object-fit: contain; }
    .fees-sum-text { flex: 1; line-height: 1.1; }
    .fees-sum-title { font-size: 10px; font-weight: 800; color: #555; }
    .fees-sum-val { font-size: 14px; font-weight: 900; color: #000; }

    .fees-timeline-box {
        background: #FFF;
        border-radius: 16px;
        border: 2px solid #E0E0E0;
        padding: 24px 16px 16px;
        position: relative;
        margin-bottom: 24px;
    }
    .fees-timeline-badge {
        position: absolute;
        top: -12px;
        left: 12px;
        background: #2196F3;
        color: white;
        font-size: 12px;
        font-weight: 900;
        padding: 4px 12px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        gap: 6px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }
    .fees-timeline-badge img { width: 16px; height: 16px; }
    
    .timeline {
        position: relative;
        padding-left: 20px;
        margin-top: 12px;
    }
    .timeline::before {
        content: '';
        position: absolute;
        top: 0; left: 4px;
        width: 2px;
        height: 100%;
        background: #C4C4C4;
    }
    .timeline-item {
        position: relative;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .timeline-item:last-child { margin-bottom: 0; }
    .timeline-dot {
        position: absolute;
        left: -21px;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: #FFF;
        border: 2px solid #82CB88;
    }
    .timeline-item.due .timeline-dot { border-color: #F44336; }
    .timeline-item.pending .timeline-dot { border-color: #999; }
    
    .timeline-date {
        font-size: 10px;
        font-weight: 800;
        color: #555;
        width: 40px;
        flex-shrink: 0;
    }
    .timeline-card {
        flex: 1;
        border-radius: 20px;
        padding: 6px 12px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border: 1px solid #CCC;
    }
    .timeline-item.paid .timeline-card { border-color: #82CB88; }
    .timeline-item.due .timeline-card { border-color: #F44336; }
    .timeline-item.pending .timeline-card { border-color: #999; }
    
    .timeline-term { font-size: 11px; font-weight: 800; color: #333; }
    .timeline-term span { font-weight: 600; color: #777; }
    .timeline-status {
        font-size: 10px;
        font-weight: 900;
        color: #FFF;
        padding: 2px 10px;
        border-radius: 12px;
    }
    .timeline-item.paid .timeline-status { background: #82CB88; }
    .timeline-item.due .timeline-status { background: #F44336; }
    .timeline-item.pending .timeline-status { background: #999; color: transparent; }

    .fees-footer-msg {
        background: #E0DFDF;
        border: 1px solid #CCC;
        border-radius: 12px;
        padding: 12px 24px;
        position: relative;
        text-align: center;
        font-size: 11px;
        font-weight: 800;
        color: #554433;
        margin-top: 16px;
    }
    .star-icon {
        position: absolute;
        width: 24px; height: 24px;
    }
    .star-left { bottom: -8px; left: -8px; transform: rotate(-15deg); }
    .star-right { top: -12px; right: -8px; transform: rotate(15deg); }
</style>
@endpush

@section('content')
<div class="header-curve" id="mainHeader">
    <div class="back-btn-container">
        <a href="{{ route('student.workspace') }}" style="display: inline-block; transition: transform 0.1s;">
            <img src="{{ asset('uploads/images/buttons/Previous button.png') }}" alt="Back" style="height: 48px; object-fit: contain;" fetchpriority="high" loading="eager" decoding="async">
        </a>
    </div>
</div>

<div class="main-content mb-5">
    <div class="tabs-container">
        <div class="tab tab-active tab-icard" id="tab-icard" onclick="switchTab('icard')">ID card</div>
        <div class="tab tab-attendance" id="tab-attendance" onclick="switchTab('attendance')">Attendance</div>
        <div class="tab tab-fees" id="tab-fees" onclick="switchTab('fees')">Fees</div>
    </div>

    <div class="card-body-main" id="mainCardBody">
        
        <!-- I-Card Content -->
        <div id="content-icard" class="tab-content active">
            <div class="title-text">This is your official Student I-Card.</div>

            <div class="id-card-replica">
                <div class="id-card-header">
                    <div class="id-card-logo">AC</div>
                    <div class="id-card-school-name">{{ auth()->user()->institute->name ?? 'ACETECH SCHOOL' }}</div>
                </div>
                
                <div class="id-card-content">
                    @if(isset($user->avatar))
                        <img src="{{ asset($user->avatar) }}" class="id-card-photo" alt="Student Photo">
                    @else
                        <div class="id-card-photo d-flex align-items-center justify-content-center" style="background: #fff;">
                            <svg viewBox="0 0 16 16" style="width: 50px; height: 50px; fill: #CCC;">
                                <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>
                                <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1z"/>
                            </svg>
                        </div>
                    @endif
                    
                    <div class="id-card-details">
                        <div class="id-card-name">{{ $user->name }}</div>
                        <div>STUDENT ID: {{ $user->student->admission_no ?? 'STU-'.rand(1000,9999) }}</div>
                        <div>DOB: {{ $user->student->dob ? \Carbon\Carbon::parse($user->student->dob)->format('d M Y') : 'N/A' }}</div>
                        <div>CLASS: {{ $user->student->class->name ?? 'N/A' }}</div>
                        <div>BLOOD GROUP: {{ $user->student->blood_group ?? 'O+' }}</div>
                        <div>CONTACT: {{ $user->phone ?? 'N/A' }}</div>
                    </div>
                </div>

                <div class="id-card-footer">
                    <div class="id-card-barcode"></div>
                    <div class="id-card-seal">SEAL</div>
                </div>
            </div>

            <div class="guidelines-title">ID Card Guidelines</div>
            <ul class="guidelines-list">
                <li>Wear your ID Card every day at school.</li>
                <li>Keep your ID Card clean and safe.</li>
                <li>Do not share your ID Card with others.</li>
                <li>Inform your class teacher if your ID Card is lost or damaged.</li>
                <li>Show your ID Card when requested by school staff.</li>
            </ul>
        </div>

        <!-- Attendance Content -->
        <div id="content-attendance" class="tab-content">
            <div class="att-header-card">
                @if(isset($user->avatar))
                    <img src="{{ asset($user->avatar) }}" class="att-avatar" alt="Avatar">
                @else
                    <svg class="att-avatar-svg" viewBox="0 0 16 16">
                        <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>
                        <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1z"/>
                    </svg>
                @endif
                <div class="att-user-info">
                    <p class="att-name">Hi, {{ explode(' ', $user->name)[0] }}</p>
                    <p class="att-class">{{ $user->studentClass->standard ?? '3-A' }}</p>
                    <p class="att-school">{{ auth()->user()->institute->name ?? 'Delhi Public School' }}</p>
                </div>
            </div>

            <div class="att-middle-section">
                <div class="att-overall-box">
                    <div class="att-overall-header">Overall</div>
                    <div class="att-progress-ring" id="attProgressRing">
                        <div class="att-progress-inner" id="attProgressText">0%</div>
                    </div>
                </div>
                <div class="att-summary-box">
                    <div class="att-summary-title">This Month summary</div>
                    <div class="att-summary-cards">
                        <div class="att-sum-card green">
                            <img src="{{ asset('uploads/images/workspace/Preset.png') }}" alt="Preset" fetchpriority="high" loading="eager" decoding="async">
                            <div class="att-sum-val">Preset<br><span id="sumPreset">0 Days</span></div>
                        </div>
                        <div class="att-sum-card red">
                            <img src="{{ asset('uploads/images/workspace/Absent.png') }}" alt="Absent" fetchpriority="high" loading="eager" decoding="async">
                            <div class="att-sum-val">Absent<br><span id="sumAbsent">0 Days</span></div>
                        </div>
                        <div class="att-sum-card yellow">
                            <img src="{{ asset('uploads/images/workspace/Holiday.png') }}" alt="Holiday" fetchpriority="high" loading="eager" decoding="async">
                            <div class="att-sum-val">Holiday<br><span id="sumHoliday">0 Days</span></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="att-calendar-box">
                <div class="calendar-header">
                    <div class="nav-btn" onclick="prevMonth()">&#10094;</div>
                    <h5 id="calendarMonthYear">June 2026</h5>
                    <div class="nav-btn" onclick="nextMonth()">&#10095;</div>
                </div>
                <div class="calendar-grid">
                    <div class="cal-day-name sun">Su</div>
                    <div class="cal-day-name">Mo</div>
                    <div class="cal-day-name">Tu</div>
                    <div class="cal-day-name">We</div>
                    <div class="cal-day-name">Th</div>
                    <div class="cal-day-name">Fr</div>
                    <div class="cal-day-name">Sa</div>
                </div>
                <div class="calendar-grid" id="calendarDays">
                    <!-- Days injected via JS -->
                </div>
                <div class="cal-legend">
                    <div class="legend-item"><div class="legend-dot" style="background: #82CB88;"></div> Preset</div>
                    <div class="legend-item"><div class="legend-dot" style="background: #FF6B6B;"></div> Absent</div>
                    <div class="legend-item"><div class="legend-dot" style="background: #FFD166;"></div> Holiday</div>
                </div>
            </div>

            <div class="att-important-box">
                <div class="important-badge">IMPORTANT</div>
                Regular attendance helps you learn better and achieve more!
            </div>
        </div>

        <!-- Fees Content -->
        <div id="content-fees" class="tab-content">
            <div class="att-header-card">
                @if(isset($user->avatar))
                    <img src="{{ asset($user->avatar) }}" class="att-avatar" alt="Avatar">
                @else
                    <svg class="att-avatar-svg" viewBox="0 0 16 16">
                        <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>
                        <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1z"/>
                    </svg>
                @endif
                <div class="att-user-info">
                    <p class="att-name">Hi, {{ explode(' ', $user->name)[0] }}</p>
                    <p class="att-class">{{ $user->studentClass->standard ?? '3-A' }}</p>
                    <p class="att-school">{{ auth()->user()->institute->name ?? 'Delhi Public School' }}</p>
                </div>
            </div>

            <div class="fees-middle-section">
                <div class="fees-overall-box">
                    <div class="fees-overall-header">Overall</div>
                    <div class="fees-progress-ring">
                        <div class="fees-progress-inner">75%</div>
                    </div>
                </div>
                <div class="fees-summary-box">
                    <div class="fees-sum-card blue">
                        <img src="{{ asset('uploads/images/workspace/total amount.png') }}" alt="Total" fetchpriority="high" loading="eager" decoding="async">
                        <div class="fees-sum-text">
                            <div class="fees-sum-title">Total Fees</div>
                            <div class="fees-sum-val">20,000</div>
                        </div>
                    </div>
                    <div class="fees-sum-card green">
                        <img src="{{ asset('uploads/images/workspace/pay.png') }}" alt="Paid" fetchpriority="high" loading="eager" decoding="async">
                        <div class="fees-sum-text">
                            <div class="fees-sum-title">Paid Amount</div>
                            <div class="fees-sum-val">15,000</div>
                        </div>
                    </div>
                    <div class="fees-sum-card red">
                        <img src="{{ asset('uploads/images/workspace/due.png') }}" alt="Due" fetchpriority="high" loading="eager" decoding="async">
                        <div class="fees-sum-text">
                            <div class="fees-sum-title">Due Amount</div>
                            <div class="fees-sum-val">5,000</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="fees-timeline-box">
                <div class="fees-timeline-badge">
                    🗓️ Fees Payment
                </div>
                <div class="timeline">
                    <div class="timeline-item paid">
                        <div class="timeline-dot"></div>
                        <div class="timeline-date">10 Apr</div>
                        <div class="timeline-card">
                            <div class="timeline-term">Term-1 <span>(April to June)</span></div>
                            <div class="timeline-status">Paid</div>
                        </div>
                    </div>
                    <div class="timeline-item paid">
                        <div class="timeline-dot"></div>
                        <div class="timeline-date">10 Jul</div>
                        <div class="timeline-card">
                            <div class="timeline-term">Term-2 <span>(July to Sept)</span></div>
                            <div class="timeline-status">Paid</div>
                        </div>
                    </div>
                    <div class="timeline-item due">
                        <div class="timeline-dot"></div>
                        <div class="timeline-date">10 Oct</div>
                        <div class="timeline-card">
                            <div class="timeline-term">Term-3 <span>(Oct to Dec)</span></div>
                            <div class="timeline-status">Due</div>
                        </div>
                    </div>
                    <div class="timeline-item pending">
                        <div class="timeline-dot"></div>
                        <div class="timeline-date">10 Jan</div>
                        <div class="timeline-card">
                            <div class="timeline-term" style="color:#999;">Term-4 <span>(Jan to Mar)</span></div>
                            <div class="timeline-status">--</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="fees-footer-msg">
                <svg class="star-icon star-left" viewBox="0 0 24 24" fill="#FFC107"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                Thank you! Your timely payments help us maintain quality education.
                <svg class="star-icon star-right" viewBox="0 0 24 24" fill="#FFC107"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
            </div>
        </div>

    </div>
</div>

<script>
    // Tab Switching Logic
    function switchTab(tabName) {
        // Reset all tabs
        document.querySelectorAll('.tab').forEach(t => t.classList.remove('tab-active'));
        document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
        
        // Set active tab
        document.getElementById('tab-' + tabName).classList.add('tab-active');
        document.getElementById('content-' + tabName).classList.add('active');

        const mainHeader = document.getElementById('mainHeader');
        const mainCardBody = document.getElementById('mainCardBody');

        if(tabName === 'icard') {
            mainHeader.style.backgroundColor = '#472C25'; // Brown
            mainCardBody.style.backgroundColor = '#FFF9D2'; // Yellow
            mainCardBody.style.border = 'none';
        } else if(tabName === 'attendance') {
            mainHeader.style.backgroundColor = '#386034'; // Dark Green
            mainCardBody.style.backgroundColor = '#8EBD87'; // Sage Green
            mainCardBody.style.border = 'none';
        } else if(tabName === 'fees') {
            mainHeader.style.backgroundColor = '#1C5AC7'; // Blue
            mainCardBody.style.backgroundColor = '#69B3F4'; // Light Blue
            mainCardBody.style.border = 'none';
        }
    }

    // Attendance Calendar Logic
    const attendanceData = @json($attendanceData ?? []);
    let currentDate = new Date();

    function renderCalendar() {
        const monthYear = document.getElementById('calendarMonthYear');
        const daysContainer = document.getElementById('calendarDays');
        
        const year = currentDate.getFullYear();
        const month = currentDate.getMonth();
        
        const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
        monthYear.innerText = `${monthNames[month]} ${year}`;
        
        daysContainer.innerHTML = '';
        
        const firstDay = new Date(year, month, 1).getDay();
        const daysInMonth = new Date(year, month + 1, 0).getDate();
        
        const today = new Date();
        const isCurrentMonth = (today.getFullYear() === year && today.getMonth() === month);
        const currentDayStr = today.getDate();

        let presetCount = 0;
        let absentCount = 0;
        let holidayCount = 0;
        let totalWorkingDays = 0;
        
        for (let i = 0; i < firstDay; i++) {
            daysContainer.innerHTML += `<div></div>`;
        }
        
        for (let i = 1; i <= daysInMonth; i++) {
            const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(i).padStart(2, '0')}`;
            const dateObj = new Date(year, month, i);
            const isSunday = dateObj.getDay() === 0;
            const isPastOrToday = dateObj <= today;
            
            let statusClass = '';
            let isHoliday = false;

            if (attendanceData[dateStr] === 'Present') {
                statusClass = 'present';
                presetCount++;
            } else if (attendanceData[dateStr] === 'Absent') {
                statusClass = 'absent';
                absentCount++;
            } else if (isSunday) {
                // Sunday is a holiday
                statusClass = 'holiday';
                isHoliday = true;
                holidayCount++;
            } else if (isPastOrToday) {
                // Past weekdays without attendance marked are considered absent
                statusClass = 'absent';
                absentCount++;
            }

            if(!isSunday && isPastOrToday) totalWorkingDays++;
            
            const textColorClass = isSunday ? 'sun-date' : '';

            daysContainer.innerHTML += `
                <div class="cal-date ${statusClass} ${textColorClass}">${i}</div>
            `;
        }

        // Update Summary Counts
        document.getElementById('sumPreset').innerText = presetCount + ' Days';
        document.getElementById('sumAbsent').innerText = absentCount + ' Days';
        document.getElementById('sumHoliday').innerText = holidayCount + ' Days';

        // Calculate Overall %
        let percentage = 0;
        if(totalWorkingDays > 0) {
            percentage = Math.round((presetCount / totalWorkingDays) * 100);
        }
        document.getElementById('attProgressText').innerText = percentage + '%';
        const ring = document.getElementById('attProgressRing');
        ring.style.background = `conic-gradient(#1A5E20 ${percentage}%, #A5D6A7 0)`;
    }

    function prevMonth() {
        currentDate.setMonth(currentDate.getMonth() - 1);
        renderCalendar();
    }

    function nextMonth() {
        currentDate.setMonth(currentDate.getMonth() + 1);
        renderCalendar();
    }

    // Initialize calendar on load
    document.addEventListener('DOMContentLoaded', () => {
        renderCalendar();
        // Initialize view
        switchTab('icard');
    });
</script>

@endsection
