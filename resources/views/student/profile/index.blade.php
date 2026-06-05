@extends('layouts.student')
@section('title', 'My Profile')
@section('nav_profile', 'active')

@push('styles')
<style>
/* Override default layout body background if possible, or just cover it */
.profile-container {
    position: relative;
    width: 100%;
    min-height: 100vh;
    background-color: #FDF6E9; /* Cream background */
    z-index: 1;
    overflow-x: hidden;
    padding-bottom: 120px;
}

/* Wavy Top Background */
.profile-top-wave {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 300px;
    background: linear-gradient(180deg, #FFCE63 0%, #FFB642 100%);
    border-bottom-left-radius: 50% 20%;
    border-bottom-right-radius: 50% 20%;
    z-index: -1;
}
.profile-top-wave::after {
    content: '';
    position: absolute;
    bottom: -30px;
    left: -10%;
    width: 120%;
    height: 150px;
    background: rgba(255, 206, 99, 0.4);
    border-top-left-radius: 50% 100%;
    border-top-right-radius: 50% 100%;
    border-bottom-left-radius: 50% 20%;
    border-bottom-right-radius: 50% 20%;
    z-index: -1;
    transform: rotate(-3deg);
}

/* Decorative Background Image (Shapes) */
.profile-bg-shapes {
    position: absolute;
    top: 50px;
    left: 0;
    width: 100%;
    height: 100%;
    background-image: url('{{ asset("uploads/images/banners/shapes.png") }}');
    background-size: cover;
    background-position: top center;
    background-repeat: no-repeat;
    opacity: 0.6;
    z-index: -2;
    pointer-events: none;
}

/* Header & Back Button */
.profile-header-bar {
    padding: 20px;
    display: flex;
    align-items: center;
}
.btn-back {
    width: 48px;
    height: 48px;
    background: linear-gradient(135deg, #40C4FF, #0081CB);
    border: 3px solid #FFD54F;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 24px;
    box-shadow: 0 4px 0 #005B8F;
    cursor: pointer;
    text-decoration: none;
    transition: transform 0.1s;
}
.btn-back:active {
    transform: translateY(4px);
    box-shadow: 0 0 0 #005B8F;
}

/* Avatar Section */
.profile-avatar-wrapper {
    position: relative;
    width: 180px;
    height: 180px;
    margin: 0 auto 10px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.profile-avatar-frame {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 2;
}
.profile-avatar-img {
    width: 130px;
    height: 130px;
    border-radius: 50%;
    object-fit: cover;
    z-index: 1;
}
.edit-pencil {
    position: absolute;
    bottom: 15px;
    right: 25px;
    width: 32px;
    height: 32px;
    background: white;
    border: 1px solid #CCC;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 3;
    font-size: 16px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    cursor: pointer;
}

/* User Info */
.profile-info {
    text-align: center;
    margin-bottom: 30px;
    position: relative;
    z-index: 2;
}
.profile-name {
    font-size: 28px;
    font-weight: 900;
    color: #332211;
    margin-bottom: 0;
}
.profile-email {
    font-size: 14px;
    color: #554433;
    margin-bottom: 8px;
    font-weight: 600;
}
.profile-class-level {
    font-size: 16px;
    font-weight: 700;
    color: #554433;
}

/* Stats Cards */
.stats-container {
    display: flex;
    justify-content: center;
    gap: 16px;
    margin-bottom: 40px;
    position: relative;
    z-index: 2;
    padding: 0 20px;
}
.stat-card {
    background: #FFEAC2;
    border: 2px solid #5B3B24;
    border-radius: 12px;
    width: 100px;
    height: 110px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    position: relative;
    box-shadow: 0 4px 0 #D4B688;
}
/* Calendar pins */
.stat-card::before, .stat-card::after {
    content: '';
    position: absolute;
    top: -8px;
    width: 10px;
    height: 16px;
    background: #5B3B24;
    border-radius: 4px;
}
.stat-card::before { left: 16px; }
.stat-card::after { right: 16px; }
.stat-icon {
    font-size: 32px;
    margin-bottom: 4px;
    margin-top: 8px;
}
.stat-icon img {
    width: 36px;
    height: 36px;
    object-fit: contain;
}
.stat-value {
    font-size: 14px;
    font-weight: 800;
    color: #5B3B24;
}

/* Quiz Stats Section */
.quiz-stats-section {
    padding: 0 30px;
    max-width: 500px;
    margin: 0 auto;
    position: relative;
    z-index: 2;
}
.quiz-stats-title {
    font-size: 20px;
    font-weight: 900;
    color: #5B3B24;
    margin-bottom: 16px;
    text-transform: uppercase;
}
.quiz-stats-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
}
.quiz-stat-box {
    background: #FFEAC2;
    border: 2px solid #5B3B24;
    border-radius: 12px;
    height: 90px;
}

/* Ensure the layout sidebar is above */
.sidebar {
    z-index: 1000 !important;
}
</style>
@endpush

@section('content')
<div class="profile-container">
    <div class="profile-top-wave"></div>
    <div class="profile-bg-shapes"></div>

    <div class="profile-header-bar">
        <a href="{{ route('student.dashboard') }}" class="btn-back">
            <i class="bi bi-arrow-left-short" style="font-size:32px; font-weight:bold;-webkit-text-stroke:1px;"></i>
        </a>
    </div>

    <div class="profile-avatar-wrapper">
        <img src="{{ asset('uploads/images/banners/badges/Gold.png') }}" alt="Gold Frame" class="profile-avatar-frame">
        <img src="{{ asset('uploads/images/banners/Avatar/iron male.png') }}" alt="Avatar" class="profile-avatar-img">
        <div class="edit-pencil">
            ✏️
        </div>
    </div>

    <div class="profile-info">
        <h2 class="profile-name">{{ $user->name }}</h2>
        <div class="profile-email">{{ $user->email }}</div>
        <div class="profile-class-level">{{ $user->studentClass->name ?? 'Class 4' }} &bull; Level {{ $user->level ?? 1 }}</div>
    </div>

    <div class="stats-container">
        <div class="stat-card">
            <div class="stat-icon" style="color: #FFB300;">⭐</div>
            <div class="stat-value">{{ number_format($user->total_xp ?? 320) }} XP</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="color: #FF5722;">🔥</div>
            <div class="stat-value">{{ $user->streak_count ?? 2 }} Streak</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <img src="{{ asset('uploads/images/banners/badges/Gold.png') }}" alt="Gold Tier">
            </div>
            <div class="stat-value">Gold Tier</div>
        </div>
    </div>

    <div class="quiz-stats-section">
        <div class="quiz-stats-title">Quiz Stats</div>
        <div class="quiz-stats-grid">
            <div class="quiz-stat-box"></div>
            <div class="quiz-stat-box"></div>
            <div class="quiz-stat-box"></div>
            <div class="quiz-stat-box"></div>
        </div>
        
        <!-- Logout Button at bottom -->
        <div style="margin-top: 40px; text-align: center;">
            <form method="POST" action="{{ route('student.logout') }}">
                @csrf
                <button type="submit" style="background: #FF6584; color: #FFF; border: 2px solid #C23351; padding: 12px 30px; border-radius: 999px; font-size: 16px; font-weight: 900; cursor: pointer; box-shadow: 0 4px 0 #C23351; transition: transform 0.1s;">
                    Logout
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
