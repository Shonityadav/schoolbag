@extends('layouts.student')
@section('title', 'My Profile')
@section('nav_profile', 'active')

@push('styles')
<style>
.profile-header{background:linear-gradient(135deg,rgba(108,99,255,.15),rgba(255,101,132,.08));border:1px solid rgba(108,99,255,.2);border-radius:18px;padding:28px;margin-bottom:24px;display:flex;align-items:center;gap:20px}
.avatar-circle{width:80px;height:80px;border-radius:50%;background:linear-gradient(135deg,#6C63FF,#FF6584);display:flex;align-items:center;justify-content:center;font-size:36px;flex-shrink:0}
.user-name{font-size:22px;font-weight:900}
.user-class{color:#8888BB;font-size:14px;margin-bottom:10px}
.stat-row{display:flex;gap:14px;flex-wrap:wrap}
.stat-pill{background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.1);border-radius:999px;padding:5px 16px;font-size:13px;font-weight:800}
.badges-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(140px,1fr));gap:12px;margin-bottom:24px}
.badge-card{background:#1E1E35;border:1px solid #2A2A4A;border-radius:14px;padding:16px;text-align:center}
.badge-icon{font-size:36px;margin-bottom:8px}
.badge-name{font-size:13px;font-weight:800;margin-bottom:4px}
.badge-desc{font-size:11px;color:#8888BB}
.xp-row{display:flex;align-items:center;justify-content:space-between;padding:10px 0;border-bottom:1px solid #2A2A4A}
.xp-row:last-child{border-bottom:none}
.heatmap{display:flex;flex-wrap:wrap;gap:4px}
.h-cell{width:14px;height:14px;border-radius:3px;background:#2A2A4A}
.h-cell.active{background:#6C63FF}
.sec-title{font-size:16px;font-weight:900;margin:22px 0 12px;display:flex;align-items:center;gap:8px}

@media (max-width: 600px) {
    .profile-header { flex-direction: column; text-align: center; }
    .stat-row { justify-content: center; }

}
</style>
@endpush

@section('content')
<div class="profile-header">
    <div class="avatar-circle">{{ $user->avatar ?? '🧑‍🎓' }}</div>
    <div>
        <div class="user-name">{{ $user->name }}</div>
        <div class="user-class">{{ $user->studentClass->name ?? 'School Bag' }} • Level {{ $user->level }}</div>
        <div class="stat-row">
            <span class="stat-pill">⚡ {{ number_format($user->total_xp) }} XP</span>
            <span class="stat-pill">🔥 {{ $user->streak_count }} Streak</span>
            <span class="stat-pill">🏅 {{ $badges->count() }} Badges</span>
            <span class="stat-pill">📧 {{ $user->email }}</span>
        </div>
    </div>
</div>



<!-- Badges -->
<div class="sec-title">🏅 My Badges</div>
@if($badges->count())
<div class="badges-grid">
    @foreach($badges as $badge)
    <div class="badge-card">
        <div class="badge-icon">{{ $badge->icon }}</div>
        <div class="badge-name">{{ $badge->name }}</div>
        <div class="badge-desc">{{ $badge->description }}</div>
    </div>
    @endforeach
</div>
@else
<div style="color:#8888BB;font-size:14px;padding:20px 0">Complete lessons and keep streaks to earn badges!</div>
@endif

<!-- Attendance heatmap -->
<div class="sec-title">📅 Attendance (last 12 weeks)</div>
<div class="card" style="margin-bottom:24px">
    <div class="heatmap">
        @php $start = now()->subWeeks(12)->startOfWeek(); @endphp
        @for($day = clone $start; $day->lte(now()); $day->addDay())
            <div class="h-cell {{ in_array($day->toDateString(), $attendances) ? 'active' : '' }}" title="{{ $day->format('d M Y') }}"></div>
        @endfor
    </div>
    <div style="font-size:12px;color:#8888BB;margin-top:10px">🟣 = Present · ⬛ = Absent</div>
</div>

<!-- XP History -->
<div class="sec-title">⭐ XP History</div>
<div class="card">
    @forelse($xpHistory as $tx)
    <div class="xp-row">
        <div>
            <div style="font-size:14px;font-weight:700">{{ $tx->description ?? ucfirst($tx->source_type) }}</div>
            <div style="font-size:12px;color:#8888BB">{{ $tx->created_at->format('d M Y, H:i') }}</div>
        </div>
        <div style="font-weight:900;color:#00D4AA;font-size:14px">+{{ $tx->amount }}</div>
    </div>
    @empty
    <div style="color:#8888BB;font-size:13px;padding:10px 0">No XP earned yet. Start learning!</div>
    @endforelse
</div>

<!-- Logout Button -->
<div style="margin-top: 32px; margin-bottom: 24px; text-align: center;">
    <form method="POST" action="{{ route('student.logout') }}">
        @csrf
        <button type="submit" style="background: #FF6584; color: #FFF; border: none; padding: 14px 40px; border-radius: 999px; font-size: 16px; font-weight: 900; font-family: 'Quicksand', sans-serif; cursor: pointer; box-shadow: 0 6px 16px rgba(255,101,132,0.3); transition: transform 0.2s;">
            <span style="margin-right: 8px;">👋</span> Logout from {{ config('app.name') }}
        </button>
    </form>
</div>
@endsection
