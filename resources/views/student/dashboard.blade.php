@extends('layouts.student')
@section('title', 'Dashboard')
@section('nav_dashboard', 'active')

@push('styles')
<style>
.dashboard-layout-wrapper {
    display: grid;
    grid-template-columns: 320px 1fr;
    gap: 40px;
    align-items: start;
    padding-top: 20px;
}

/* Left Column */
.mascot-col {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
}
.mascot-greeting {
    font-size: 36px;
    font-weight: 900;
    color: #CA8A04;
    font-family: 'Bubblegum Sans', cursive;
    margin-bottom: 32px;
    text-shadow: 1px 1px 0 #FFF;
}
.mascot-ring {
    width: 220px;
    height: 220px;
    border-radius: 50%;
    background: #FFFFFF;
    border: 14px solid transparent;
    background-image: linear-gradient(#FFFFFF, #FFFFFF), linear-gradient(180deg, #8BDDFF 50%, #FFB37C 50%);
    background-origin: border-box;
    background-clip: padding-box, border-box;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 24px;
    box-shadow: 0 8px 0 rgba(0,0,0,0.08), 0 16px 32px rgba(0,0,0,0.12);
    font-size: 100px;
}
.mascot-level {
    font-size: 24px;
    font-weight: 900;
    color: #5E4D3B;
    margin-bottom: 12px;
    font-family: 'Bubblegum Sans', cursive;
}
.mascot-xp-pill {
    background: #FFD561;
    border-radius: 999px;
    padding: 8px 32px;
    font-weight: 900;
    font-size: 16px;
    color: #5E4D3B;
    position: relative;
    overflow: hidden;
    box-shadow: 0 6px 0 #C9A300, 0 8px 16px rgba(255, 213, 97, 0.4);
}

/* Right Column: 3 Cards */
.static-cards-row {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
    margin-bottom: 32px;
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
.s-card:hover {
    transform: translateY(-8px);
}
.s-card:active {
    transform: translateY(0px);
}
.s-card img {
    width: 100px;
    height: 100px;
    object-fit: contain;
    margin-bottom: 16px;
    filter: drop-shadow(0 4px 8px rgba(0,0,0,0.15));
}
.s-card-title {
    font-size: 22px;
    font-weight: 900;
    font-family: 'Bubblegum Sans', cursive;
    margin-bottom: 8px;
    line-height: 1.1;
    text-shadow: 1px 1px 0 rgba(0,0,0,0.1);
}
.s-card-desc {
    font-size: 13px;
    font-weight: 700;
    opacity: 0.95;
    line-height: 1.3;
}

/* Right Column: Daily Quest */
.daily-quest-flat {
    background: #FFF3CC;
    border-radius: 24px;
    padding: 24px;
    display: flex;
    gap: 24px;
    box-shadow: 0 8px 0 rgba(210, 170, 60, 0.3), 0 12px 28px rgba(0,0,0,0.08);
}
.dq-left {
    flex: 0 0 200px;
    background: #FFE899;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 64px;
    border: 2px dashed #E6C86A;
    overflow: hidden;
}
.dq-left img { width: 100%; height: 100%; object-fit: cover; }
.dq-right { flex: 1; }
.dq-title-text {
    font-size: 26px;
    font-family: 'Bubblegum Sans', cursive;
    color: #5E4D3B;
    margin-bottom: 4px;
}
.dq-subtitle {
    font-size: 14px;
    font-weight: 700;
    color: #8D7E6A;
    margin-bottom: 16px;
}
.dq-tasks-flex {
    display: flex;
    gap: 16px;
}
.dq-task { flex: 1; }
.dq-t-name { font-size: 13px; font-weight: 800; color: #5E4D3B; margin-bottom: 6px; }
.dq-t-bar {
    height: 8px; background: #FFFFFF; border-radius: 999px; overflow: hidden;
}
.dq-t-fill { height: 100%; border-radius: 999px; }
</style>
@endpush

@section('content')

@include('student.partials.splash')

<div class="container-fluid pt-2 px-0 pb-5 mb-5">
    <div class="row g-3 g-md-4 align-items-start">
        
        <!-- Left Column: Mascot -->
        <div class="col-12 col-lg-4 col-xl-3">
            <div class="mascot-col text-center">
                <div class="mascot-greeting" style="font-size: 32px; font-weight: 900; color: #D89839; text-shadow: 0 2px 4px rgba(216,152,57,0.2); margin-bottom: 16px; font-family: 'Bubblegum Sans', cursive;">Hi, {{ explode(' ', $user->name)[0] }}!</div>
                
                <div class="mascot-ring-wrapper mx-auto" style="width: 140px; height: 140px; border-radius: 50%; background: linear-gradient(180deg, #8BDDFF 50%, #FFB37C 50%); padding: 8px; box-shadow: 0 8px 24px rgba(0,0,0,0.05); margin-bottom: 16px;">
                    <div class="mascot-ring-inner" style="width: 100%; height: 100%; border-radius: 50%; background: #FFF; display: flex; justify-content: center; align-items: center;">
                        <img src="{{ asset('uploads/images/lion.png') }}" alt="Lion Badge" style="width: 90px; height: 90px; object-fit: contain;">
                    </div>
                </div>
                
                <div class="mascot-level" style="font-size: 18px; font-weight: 900; color: #5E4D3B; margin-bottom: 8px; font-family: 'Bubblegum Sans', cursive;">Level {{ $user->level }}: Super Learner!</div>
                
                <div class="mascot-xp-pill mx-auto" style="background: linear-gradient(90deg, #FFD561, #FFF3CC); padding: 4px 16px; border-radius: 999px; display: inline-block; font-size: 14px; font-weight: 800; color: #5E4D3B; box-shadow: 0 4px 12px rgba(255,213,97,0.3);">
                    ⭐ {{ number_format($user->total_xp) }}/500 XP
                </div>
            </div>
        </div>
        
        <!-- Right Column: Content -->
        <div class="col-12 col-lg-8 col-xl-9 mt-4 mt-lg-0">
            
            <!-- The 3 Div Cards - Side by Side on Mobile -->
            <div class="row g-2 g-md-3 mb-4">
                <div class="col-4">
                    <a href="{{ route('student.courses.index') }}" class="s-card h-100 d-flex flex-column align-items-center text-center p-2 p-md-3" style="background: linear-gradient(160deg, #A8E8FF 0%, #8BDDFF 100%); border-radius: 20px; text-decoration: none; box-shadow: 0 10px 0 #4AADCC, 0 14px 28px rgba(70,160,200,0.3), inset 0 1px 0 rgba(255,255,255,0.5);">
                        <img src="{{ asset('uploads/images/owl teacher.png') }}" alt="Math Adventure" class="img-fluid mb-2" style="max-height: 80px; object-fit: contain; filter: drop-shadow(0 6px 10px rgba(0,0,0,0.2));">
                        <div class="s-card-title text-white" style="font-family: 'Bubblegum Sans', cursive; font-size: clamp(14px, 4vw, 20px); font-weight: 900; line-height: 1.1; margin-bottom: 4px; text-shadow: 0 2px 4px rgba(0,0,0,0.2);">Math Adventure</div>
                        <div class="s-card-desc text-white" style="font-size: clamp(9px, 2.5vw, 13px); line-height: 1.2; opacity: 0.9;">Solve equations and unlock treasure!</div>
                    </a>
                </div>
                <div class="col-4">
                    <a href="{{ route('student.courses.index') }}" class="s-card h-100 d-flex flex-column align-items-center text-center p-2 p-md-3" style="background: linear-gradient(160deg, #BAEDAA 0%, #9DE182 100%); border-radius: 20px; text-decoration: none; box-shadow: 0 10px 0 #5CAA44, 0 14px 28px rgba(80,160,60,0.3), inset 0 1px 0 rgba(255,255,255,0.5);">
                        <img src="{{ asset('uploads/images/robot.png') }}" alt="Science Explorer" class="img-fluid mb-2" style="max-height: 80px; object-fit: contain; filter: drop-shadow(0 6px 10px rgba(0,0,0,0.2));">
                        <div class="s-card-title text-white" style="font-family: 'Bubblegum Sans', cursive; font-size: clamp(14px, 4vw, 20px); font-weight: 900; line-height: 1.1; margin-bottom: 4px; text-shadow: 0 2px 4px rgba(0,0,0,0.2);">Science Explorer</div>
                        <div class="s-card-desc text-white" style="font-size: clamp(9px, 2.5vw, 13px); line-height: 1.2; opacity: 0.9;">Discover the world with experiments!</div>
                    </a>
                </div>
                <div class="col-4">
                    <a href="{{ route('student.courses.index') }}" class="s-card h-100 d-flex flex-column align-items-center text-center p-2 p-md-3" style="background: linear-gradient(160deg, #FFCC9E 0%, #FFB37C 100%); border-radius: 20px; text-decoration: none; box-shadow: 0 10px 0 #CC7A3C, 0 14px 28px rgba(200,120,60,0.3), inset 0 1px 0 rgba(255,255,255,0.5);">
                        <img src="{{ asset('uploads/images/test paper.png') }}" alt="English Storytime" class="img-fluid mb-2" style="max-height: 80px; object-fit: contain; filter: drop-shadow(0 6px 10px rgba(0,0,0,0.2));">
                        <div class="s-card-title text-white" style="font-family: 'Bubblegum Sans', cursive; font-size: clamp(14px, 4vw, 20px); font-weight: 900; line-height: 1.1; margin-bottom: 4px; text-shadow: 0 2px 4px rgba(0,0,0,0.2);">English Storytime</div>
                        <div class="s-card-desc text-white" style="font-size: clamp(9px, 2.5vw, 13px); line-height: 1.2; opacity: 0.9;">Read tales and grow your vocabulary!</div>
                    </a>
                </div>
            </div>
            
            <!-- Daily Quest Box -->
            <div class="daily-quest-flat d-flex align-items-stretch gap-3 p-3 p-md-4" style="background: linear-gradient(160deg, #FFF8DD 0%, #FFF3CC 100%); border-radius: 20px; box-shadow: 0 10px 0 #D4A017, 0 14px 28px rgba(200,160,20,0.25), inset 0 1px 0 rgba(255,255,255,0.8);">
                <div class="dq-left flex-shrink-0" style="width: 35%; max-width: 140px; position: relative;">
                    <img src="{{ asset('uploads/images/treasuremap.png') }}" alt="Treasure Map" style="width: 100%; height: 100%; object-fit: cover; border-radius: 12px;">
                </div>
                <div class="dq-right w-100 d-flex flex-column justify-content-center">
                    <div class="dq-title-text" style="font-family: 'Bubblegum Sans', cursive; font-size: clamp(18px, 5vw, 24px); font-weight: 900; color: #5E4D3B; line-height: 1.2;">Daily Quest</div>
                    <div class="dq-subtitle mb-2" style="font-size: clamp(10px, 3vw, 13px); color: #8D7E6A;">Complete 3 Activities to Find the Treasure!</div>
                    
                    <!-- Progress Bars -->
                    <div class="dq-tasks-container mt-1">
                        <!-- Full width bar -->
                        <div class="dq-task mb-2">
                            <div class="dq-t-name" style="font-size: clamp(9px, 2.5vw, 12px); font-weight: 800; color: #5E4D3B; margin-bottom: 2px;">Read a Story</div>
                            <div class="dq-t-bar" style="height: 6px; background: rgba(255,255,255,0.6); border-radius: 999px; overflow: hidden;">
                                <div class="dq-t-fill" style="width: 70%; height: 100%; background: #FFB37C; border-radius: 999px;"></div>
                            </div>
                        </div>
                        <!-- Split bars -->
                        <div class="row g-2">
                            <div class="col-6">
                                <div class="dq-t-name" style="font-size: clamp(9px, 2.5vw, 12px); font-weight: 800; color: #5E4D3B; margin-bottom: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Solve a Math Puzzle</div>
                                <div class="dq-t-bar" style="height: 6px; background: rgba(255,255,255,0.6); border-radius: 999px; overflow: hidden;">
                                    <div class="dq-t-fill" style="width: 40%; height: 100%; background: #9DE182; border-radius: 999px;"></div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="dq-t-name" style="font-size: clamp(9px, 2.5vw, 12px); font-weight: 800; color: #5E4D3B; margin-bottom: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Learn a Science Fact</div>
                                <div class="dq-t-bar" style="height: 6px; background: rgba(255,255,255,0.6); border-radius: 999px; overflow: hidden;">
                                    <div class="dq-t-fill" style="width: 20%; height: 100%; background: #8BDDFF; border-radius: 999px;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- ── Attendance Calendar ── -->
<div class="container-fluid px-3 px-md-4 pb-5 mb-4" style="max-width: 860px; margin: 0 auto;">
    @push('styles')
    <style>
    .attendance-card {
        background: rgba(255,255,255,0.82);
        border-radius: 24px;
        padding: 24px;
        box-shadow: 0 10px 0 rgba(157,225,130,0.25), 0 14px 32px rgba(0,0,0,0.07), inset 0 1px 0 rgba(255,255,255,0.9);
        margin-top: 8px;
    }
    .att-heading {
        font-family: 'Bubblegum Sans', cursive;
        font-size: clamp(20px, 5vw, 28px);
        color: #5E4D3B;
        margin-bottom: 4px;
    }
    .att-month-label {
        font-size: 13px;
        font-weight: 700;
        color: #8D7E6A;
        margin-bottom: 18px;
    }
    .cal-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 4px;
    }
    .cal-day-name {
        text-align: center;
        font-size: 11px;
        font-weight: 900;
        color: #8D7E6A;
        padding-bottom: 4px;
        letter-spacing: 0.3px;
    }
    .cal-day {
        aspect-ratio: 1;
        max-height: 64px;       /* cap on large screens so cells don't become huge */
        border-radius: 10px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        font-size: clamp(11px, 2vw, 13px);
        font-weight: 800;
        position: relative;
        background: #FFF9E5;
        border: 2px solid #F0E4C0;
        color: #5E4D3B;
        transition: transform 0.1s;
    }
    .cal-day.empty {
        background: transparent;
        border-color: transparent;
    }
    .cal-day.present {
        background: linear-gradient(135deg, #BAEDB0, #9DE182);
        border-color: #5CAA44;
        box-shadow: 0 4px 0 #3A7A28, 0 6px 12px rgba(60,120,40,0.2);
        transform: translateY(-2px);
        color: #2A5A18;
    }
    .cal-day.absent {
        background: #E8E0D0;
        border-color: #C8BCA0;
        color: #A89880;
    }
    .cal-day.today {
        border-color: #FFB37C;
        background: #FFF3E0;
        box-shadow: 0 0 0 3px rgba(255,179,124,0.3);
    }
    /* When today IS also present, keep green bg but add orange border */
    .cal-day.present.today {
        background: linear-gradient(135deg, #BAEDB0, #9DE182);
        border-color: #FFB37C;
        box-shadow: 0 4px 0 #3A7A28, 0 0 0 3px rgba(255,179,124,0.4);
        color: #2A5A18;
    }
    .cal-day.future {
        background: rgba(255,249,229,0.5);
        border-color: #F0E4C0;
        color: #C4B08A;
    }
    .att-legend {
        display: flex;
        gap: 16px;
        flex-wrap: wrap;
        margin-top: 16px;
        font-size: 12px;
        font-weight: 700;
        color: #8D7E6A;
    }
    .att-legend span { display: flex; align-items: center; gap: 5px; }
    .legend-dot {
        width: 14px; height: 14px;
        border-radius: 4px;
        flex-shrink: 0;
    }
    </style>
    @endpush

    <div class="attendance-card">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-1">
            <div class="att-heading">📅 Attendance</div>
            @php $markedToday = in_array(now()->toDateString(), $attendanceDates); @endphp
            @if($markedToday)
                <span style="background:#9DE182; color:#2A5A18; border-radius:999px; padding:8px 20px; font-size:13px; font-weight:900; font-family:'Quicksand',sans-serif; box-shadow:0 4px 0 #3A7A28; display:inline-flex; align-items:center; gap:6px;">
                    ✓ Marked for Today
                </span>
            @else
                <form method="POST" action="{{ route('student.attendance.mark') }}">
                    @csrf
                    <button type="submit" style="background:linear-gradient(135deg,#9DE182,#5CAA44); color:#fff; border:none; border-radius:999px; padding:10px 22px; font-size:13px; font-weight:900; font-family:'Quicksand',sans-serif; cursor:pointer; box-shadow:0 6px 0 #3A7A28, 0 8px 16px rgba(60,120,40,0.25); transform:translateY(-2px); transition:all 0.15s; display:inline-flex; align-items:center; gap:6px;"
                        onmouseover="this.style.transform='translateY(-4px)'"
                        onmouseout="this.style.transform='translateY(-2px)'"
                        onmousedown="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 0 #3A7A28'">
                        📋 Mark Today's Attendance
                    </button>
                </form>
            @endif
        </div>
        <div class="att-month-label">{{ now()->format('F Y') }}</div>

        @php
            $today       = now()->toDateString();
            $monthStart  = now()->startOfMonth();
            $daysInMonth = now()->daysInMonth;
            $startDow    = (int) $monthStart->dayOfWeek; // 0=Sun
            $dayNames    = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];
        @endphp

        <div class="cal-grid">
            {{-- Day name headers --}}
            @foreach($dayNames as $dn)
                <div class="cal-day-name">{{ $dn }}</div>
            @endforeach

            {{-- Leading empty cells --}}
            @for($e = 0; $e < $startDow; $e++)
                <div class="cal-day empty"></div>
            @endfor

            {{-- Day cells --}}
            @for($d = 1; $d <= $daysInMonth; $d++)
                @php
                    $dateStr = now()->startOfMonth()->addDays($d - 1)->toDateString();
                    $isToday   = $dateStr === $today;
                    $isFuture  = $dateStr > $today;
                    $isPresent = in_array($dateStr, $attendanceDates);
                    $isAbsent  = !$isFuture && !$isPresent && $dateStr < $today;

                    $cls = 'cal-day';
                    if ($isPresent) $cls .= ' present';
                    elseif ($isAbsent) $cls .= ' absent';
                    elseif ($isFuture) $cls .= ' future';
                    if ($isToday) $cls .= ' today';
                @endphp
                <div class="{{ $cls }}" title="{{ $dateStr }}">{{ $d }}</div>
            @endfor
        </div>

        {{-- Legend --}}
        <div class="att-legend">
            <span><span class="legend-dot" style="background:#9DE182; border:2px solid #5CAA44;"></span> Present</span>
            <span><span class="legend-dot" style="background:#E8E0D0; border:2px solid #C8BCA0;"></span> Absent</span>
            <span><span class="legend-dot" style="background:#FFF9E5; border:2px solid #F0E4C0;"></span> Upcoming</span>
        </div>
    </div>
</div>

@endsection
