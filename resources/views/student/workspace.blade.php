@extends('layouts.student')

@section('title', 'Workspace')
@section('nav_workspace', 'active')

@push('styles')
<style>
    /* Prevent scrolling if we want it to feel like an app, but let's just style correctly */
    body {
        background-image: none !important;
        background-color: #FDFDFD !important;
        margin: 0;
        padding: 0;
        font-family: 'Quicksand', sans-serif;
    }

    .topbar { display: none !important; }

    /* Background Blobs */
    .blob-bg {
        position: fixed;
        z-index: -1;
    }
    .blob-top-right {
        top: -100px;
        right: -80px;
        width: 380px;
        height: 380px;
        background: #D0E7FC;
        border-radius: 40% 60% 70% 30% / 40% 50% 60% 50%;
        transform: rotate(25deg);
        opacity: 0.9;
    }
    .blob-bottom-left {
        bottom: -50px;
        left: -120px;
        width: 400px;
        height: 400px;
        background: #D0E7FC;
        border-radius: 50% 50% 50% 50% / 60% 60% 40% 40%;
        transform: rotate(-15deg);
        opacity: 0.9;
    }

    /* Header */
    .avatar-circle {
        width: 65px;
        height: 65px;
        border-radius: 50%;
        background: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }
    .avatar-icon {
        width: 70px;
        height: 65px;
        fill: #000;
        margin-top: 0px; /* Push down to align properly */
    }
    
    .bell-circle {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: #FFC54E;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 10px rgba(255, 197, 78, 0.4);
    }
    .bell-icon {
        width: 24px;
        height: 24px;
        fill: #fff;
    }

    /* Cards */
    .tab-card {
        border-radius: 20px;
        overflow: hidden;
        color: #fff;
        text-align: center;
        display: flex;
        flex-direction: column;
        height: 140px;
        box-shadow: 0 8px 16px rgba(0,0,0,0.06);
    }
    .tab-header {
        padding: 8px 0;
        font-weight: 900;
        font-size: 14px;
        letter-spacing: 0.5px;
        border-radius: 20px 20px 0 0;
        box-shadow: inset 0 4px 6px rgba(255,255,255,0.4), 0 2px 4px rgba(0,0,0,0.05);
        margin-bottom: 2px;
    }
    .tab-body {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding-bottom: 12px;
    }
    
    /* Attendance Card */
    .card-attendance { background: #82CB88; border: 2px solid #82CB88; }
    .card-attendance .tab-header { background: #62B868; }
    .card-attendance .date-val {
        font-size: 52px;
        font-weight: 900;
        line-height: 1;
        margin-top: 5px;
    }
    .card-attendance .date-month {
        font-size: 15px;
        font-weight: 800;
    }

    /* Fees Card */
    .card-fees { background: #96C6F7; border: 2px solid #96C6F7; }
    .card-fees .tab-header { background: #81BAF3; }
    .fees-ring {
        position: relative;
        width: 84px;
        height: 84px;
        margin-top: 5px;
    }
    .fees-ring svg {
        width: 100%;
        height: 100%;
        transform: rotate(-90deg);
    }
    .fees-ring-bg {
        fill: none;
        stroke: #7AAFF0;
        stroke-width: 10;
    }
    .fees-ring-progress {
        fill: none;
        stroke: #fff;
        stroke-width: 10;
        stroke-dasharray: 220; 
        stroke-dashoffset: 165; 
    }
    .fees-ring-text {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        font-weight: 900;
        letter-spacing: -1px;
    }

    /* Typography */
    .section-title {
        font-family: 'Quicksand', sans-serif;
        font-size: 26px;
        font-weight: 900;
        color: #000;
        margin-bottom: 4px;
    }
    .section-subtitle {
        font-size: 13px;
        font-weight: 800;
        color: #000;
        margin-bottom: 24px;
    }

    /* Notice Board */
    .notice-board-container {
        position: relative;
        width: 100%;
        max-width: 380px;
        margin: 0 auto;
    }
    .notice-board-bg {
        width: 100%;
        display: block;
        filter: drop-shadow(0 12px 24px rgba(0,0,0,0.1));
    }
    .notice-board-content {
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        padding: 30px 40px;
        display: flex;
        flex-direction: column;
    }
    .notice-date {
        text-align: center;
        font-size: 18px;
        font-weight: 900;
        color: #000;
        margin-top: 15px;
        margin-bottom: 20px;
        font-family: 'Quicksand', sans-serif;
    }
    .notice-list {
        list-style-type: none;
        padding-left: 0;
        margin: 0;
    }
    .notice-list li {
        position: relative;
        padding-left: 20px;
        font-size: 16px;
        font-weight: 800;
        color: #000;
        margin-bottom: 16px;
        line-height: 1.4;
    }
    .notice-list li::before {
        content: '';
        position: absolute;
        left: 0;
        top: 8px;
        width: 8px;
        height: 8px;
        background: #000;
        border-radius: 50%;
    }

</style>
@endpush

@section('content')
<!-- Background Blobs -->
<div class="blob-bg blob-top-right"></div>
<div class="blob-bg blob-bottom-left"></div>

<div class="container pb-5" style="max-width: 440px; margin: 0 auto; padding-top: 30px;">
    
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-5 workspace-header">
        <div class="d-flex gap-3 align-items-center">
            <a href="{{ route('student.workspace.profile') }}" style="text-decoration: none;">
                <div class="avatar-circle">
                    @if(isset($user->avatar))
                        <img src="{{ asset($user->avatar) }}" alt="Avatar" style="width:100%;height:100%;object-fit:cover;">
                    @else
                        <svg class="avatar-icon" viewBox="0 0 16 16">
                            <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>
                            <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1z"/>
                        </svg>
                    @endif
                </div>
            </a>
            <div>
                <h4 class="mb-0 fw-bold text-dark" style="font-family:'Quicksand', sans-serif; font-size: 22px;">Hi, {{ explode(' ', $user->name)[0] }}</h4>
                <div style="font-size:11px;font-weight:800;color:#000;line-height:1.2;">let's complete<br>homework</div>
            </div>
        </div>
        <div class="bell-circle">
            <svg class="bell-icon" viewBox="0 0 16 16">
                <path d="M8 16a2 2 0 0 0 2-2H6a2 2 0 0 0 2 2zM8 1.918l-.797.161A4.002 4.002 0 0 0 4 6c0 .628-.134 2.197-.459 3.742-.16.767-.376 1.566-.663 2.258h10.244c-.287-.692-.502-1.49-.663-2.258C12.134 8.197 12 6.628 12 6a4.002 4.002 0 0 0-3.203-3.92L8 1.917zM14.22 12c.223.447.481.801.78 1H1c.299-.199.557-.553.78-1C2.68 10.2 3 6.88 3 6c0-2.42 1.72-4.44 4.005-4.901a1 1 0 1 1 1.99 0A5.002 5.002 0 0 1 13 6c0 .88.32 4.2 1.22 6z"/>
            </svg>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <!-- Attendance -->
        <div class="col-6">
            <div class="tab-card card-attendance">
                <div class="tab-header">Attendance</div>
                <div class="tab-body">
                    <div class="date-val">{{ date('d') }}</div>
                    <div class="date-month">{{ date('F') }}</div>
                </div>
            </div>
        </div>
        <!-- Fees -->
        <div class="col-6">
            <div class="tab-card card-fees">
                <div class="tab-header">Fees</div>
                <div class="tab-body">
                    <div class="fees-ring">
                        <svg viewBox="0 0 100 100">
                            <circle class="fees-ring-bg" cx="50" cy="50" r="40"></circle>
                            <circle class="fees-ring-progress" cx="50" cy="50" r="40"></circle>
                        </svg>
                        <div class="fees-ring-text">25%</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Assignment Section -->
    <div class="mt-5 pt-2">
        <h2 class="section-title">Today's Assignment</h2>
        <div class="section-subtitle">Work assigned by teachers</div>
        
        <div class="notice-board-container mt-4">
            <img src="{{ asset('uploads/images/workspace/notice board.png') }}" alt="Notice Board" class="notice-board-bg">
            
            <div class="notice-board-content">
                <div class="notice-date">{{ date('d F Y') }}</div>
                <ul class="notice-list">
                    <li>English - Read Chapter-2</li>
                    <li>Read Chapter-2</li>
                </ul>
            </div>
        </div>
    </div>

@endsection
