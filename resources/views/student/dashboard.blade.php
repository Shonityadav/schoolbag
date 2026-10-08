@extends('layouts.student')
@section('title', 'Dashboard')
@section('nav_dashboard', 'active')

@push('styles')
<style>
body {
    padding: 2%;
}

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

/* Thought Bubble */
.thought-bubble {
    position: absolute;
    top: -30px;
    left: 45%;
    background: #fff;
    padding: 8px 16px;
    border-radius: 20px;
    font-family: 'Bubblegum Sans', cursive;
    font-size: 16px;
    color: #5E4D3B;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    z-index: 10;
    opacity: 0;
    pointer-events: none;
    white-space: nowrap;
    transform: translateX(-10%) translateY(10px) scale(0.9);
    animation: popIn 0.5s forwards 0.5s, floatBubble 3s ease-in-out infinite 1s;
}
.thought-bubble::after {
    content: '';
    position: absolute;
    bottom: -10px;
    left: 20px;
    width: 12px;
    height: 12px;
    background: #fff;
    border-radius: 50%;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}
.thought-bubble::before {
    content: '';
    position: absolute;
    bottom: -18px;
    left: 10px;
    width: 8px;
    height: 8px;
    background: #fff;
    border-radius: 50%;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}
@keyframes popIn {
    to { opacity: 1; transform: translateX(-10%) translateY(0) scale(1); }
}
@keyframes floatBubble {
    0%, 100% { transform: translateX(-10%) translateY(0); }
    50% { transform: translateX(-10%) translateY(-5px); }
}
@keyframes mascotBreathe {
    0%, 100% { transform: scale(1) translateY(0); }
    50% { transform: scale(1.02) translateY(-3px); }
}
@keyframes mascotWiggle {
    0%, 100% { transform: rotate(0deg) scale(1.1); }
    25% { transform: rotate(-8deg) scale(1.1); }
    50% { transform: rotate(8deg) scale(1.1); }
    75% { transform: rotate(-8deg) scale(1.1); }
}
.mascot-breathing {
    animation: mascotBreathe 3s ease-in-out infinite;
}
.mascot-wiggling {
    animation: mascotWiggle 0.4s ease-in-out;
}
.particle {
    position: fixed;
    width: 8px; height: 8px;
    border-radius: 50%;
    pointer-events: none;
    z-index: 9999;
    opacity: 1;
    animation: particleFade 0.6s ease-out forwards;
}
@keyframes particleFade {
    100% { transform: translate(var(--tx), var(--ty)) scale(0); opacity: 0; }
}

/* ── Memory Match Minigame ── */
.memory-game-wrap {
    background: linear-gradient(160deg, #FFFFFF 0%, #F5FAFF 100%);
    border-radius: 20px;
    padding: 20px;
    box-shadow: 0 10px 0 #D4E1F9, 0 14px 28px rgba(0,0,0,0.05), inset 0 1px 0 #FFF;
    transform: translateY(-4px);
    margin-bottom: 24px;
}
.memory-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 12px;
    perspective: 1000px;
}
.m-card {
    aspect-ratio: 3/4;
    position: relative;
    transform-style: preserve-3d;
    transition: transform 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    cursor: pointer;
    border-radius: 12px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
.m-card.flip { transform: rotateY(180deg); }
.m-card-face {
    position: absolute;
    width: 100%; height: 100%;
    backface-visibility: hidden;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
}
.m-card-front {
    background: linear-gradient(135deg, #FFB37C, #FFD561);
    color: #fff; font-size: clamp(20px, 4vw, 32px); font-weight: 900;
}
.m-card-back {
    background: #fff;
    transform: rotateY(180deg);
    border: 3px solid #E2E8F0;
}
.m-card-back svg { width: 60%; height: 60%; }
.m-card.matched .m-card-back { border-color: #9DE182; background: #EAFFEA; }
.win-msg {
    display: none;
    text-align: center; font-family: 'Bubblegum Sans', cursive;
    font-size: clamp(20px, 4vw, 28px); color: #4AADCC; margin-top: 16px;
    animation: popIn 0.5s ease-out;
}

/* ── Word Scramble Minigame ── */
.scramble-game-wrap {
    background: linear-gradient(160deg, #FFFFFF 0%, #F5FAFF 100%);
    border-radius: 20px;
    padding: 20px;
    box-shadow: 0 10px 0 #D4E1F9, 0 14px 28px rgba(0,0,0,0.05), inset 0 1px 0 #FFF;
    transform: translateY(-4px);
    margin-bottom: 24px;
}
.scramble-title-area {
    display: flex; align-items: center; justify-content: center; gap: 8px;
    font-family: 'Bubblegum Sans', cursive; font-size: clamp(20px, 5vw, 28px);
    color: #1a4f66; margin-bottom: 16px;
}
.scramble-icon { width: 32px; height: 32px; }
.scramble-answer-row {
    display: flex; gap: 8px; justify-content: center; margin-bottom: 20px;
    min-height: 50px;
}
.scramble-slot {
    width: clamp(40px, 10vw, 50px);
    height: clamp(40px, 10vw, 50px);
    border: 2px dashed #B5C9DF;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
}
.scramble-letters-row {
    display: flex; gap: 8px; justify-content: center; flex-wrap: wrap;
}
.s-tile {
    width: clamp(40px, 10vw, 50px);
    height: clamp(40px, 10vw, 50px);
    background: linear-gradient(135deg, #FFB37C, #FFD561);
    color: #fff; font-size: clamp(20px, 5vw, 26px); font-weight: 900;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    box-shadow: 0 4px 0 #D89839, 0 4px 8px rgba(0,0,0,0.1);
    cursor: pointer;
    transition: transform 0.1s;
    user-select: none;
}
.s-tile:active { transform: translateY(4px); box-shadow: 0 0px 0 #D89839; }
.s-tile.in-answer {
    background: linear-gradient(135deg, #8BDDFF, #5CC3FF);
    box-shadow: 0 4px 0 #4A9CCC, 0 4px 8px rgba(0,0,0,0.1);
}
.s-tile.in-answer:active { box-shadow: 0 0px 0 #4A9CCC; }
</style>
@endpush

@section('content')

@include('student.partials.splash')

<div class="container-fluid pt-4 px-0 pb-5 mb-5" style="overflow: visible;">
    <div class="row g-3 g-md-4 align-items-start">
        
        <!-- Left Column: Mascot -->
        <div class="col-12 col-lg-4 col-xl-3">
            <div class="mascot-col text-center">
                <div class="mascot-greeting" style="font-size: 32px; font-weight: 900; color: #D89839; text-shadow: 0 2px 4px rgba(216,152,57,0.2); margin-bottom: 16px; font-family: 'Bubblegum Sans', cursive;">Hi, {{ explode(' ', $user->name)[0] }}!</div>
                
                <!-- The static ring where the mascot sits initially -->
                <div id="dashboard-mascot-ring" class="mx-auto" style="position: relative; width: 140px; height: 140px; border-radius: 50%; background: linear-gradient(180deg, #8BDDFF 50%, #FFB37C 50%); padding: 8px; box-shadow: 0 8px 24px rgba(0,0,0,0.05); margin-bottom: 16px;">
                    <div style="width: 100%; height: 100%; border-radius: 50%; background: #FFF;"></div>
                </div>
                
                <div class="mascot-level" style="font-size: 18px; font-weight: 900; color: #5E4D3B; margin-bottom: 8px; font-family: 'Bubblegum Sans', cursive;">Level {{ $user->level }}: Super Learner!</div>
                
                <div class="mascot-xp-pill mx-auto" style="background: linear-gradient(90deg, #FFD561, #FFF3CC); padding: 4px 16px; border-radius: 999px; display: inline-block; font-size: 14px; font-weight: 800; color: #5E4D3B; box-shadow: 0 4px 12px rgba(255,213,97,0.3);">
                    ⭐ {{ number_format($user->total_xp) }}/500 XP
                </div>
            </div>
        </div>
        
        <!-- Right Column: Content -->
        <div class="col-12 col-lg-8 col-xl-9 mt-4 mt-lg-3" style="overflow: visible;">
            
        <!-- The 3 Div Cards - Side by Side on Mobile -->
            <div class="row g-2 g-md-3 mb-4" style="overflow: visible; padding-top: 8px;">
                <div class="col-4">
                    <a href="{{ route('student.assigned_ebooks.index', ['subject' => 'Math']) }}" class="s-card h-100 d-flex flex-column align-items-center text-center p-2 p-md-3" style="background: linear-gradient(160deg, #A8E8FF 0%, #8BDDFF 100%); border-radius: 20px; text-decoration: none; box-shadow: 0 10px 0 #4AADCC, 0 14px 28px rgba(70,160,200,0.3), inset 0 1px 0 rgba(255,255,255,0.5);">
                        <img src="{{ asset('uploads/images/owl teacher.png') }}" alt="Math Adventure" class="img-fluid mb-2" style="max-height: 80px; object-fit: contain; filter: drop-shadow(0 6px 10px rgba(0,0,0,0.2));" fetchpriority="high" loading="eager" decoding="async">
                        <div class="s-card-title text-white" style="font-family: 'Bubblegum Sans', cursive; font-size: clamp(14px, 4vw, 20px); font-weight: 900; line-height: 1.1; margin-bottom: 4px; text-shadow: 0 2px 4px rgba(0,0,0,0.2);">Math Adventure</div>
                        <div class="s-card-desc text-white" style="font-size: clamp(9px, 2.5vw, 13px); line-height: 1.2; opacity: 0.9;">Solve equations and unlock treasure!</div>
                    </a>
                </div>
                <div class="col-4">
                    <a href="{{ route('student.assigned_ebooks.index', ['subject' => 'Science']) }}" class="s-card h-100 d-flex flex-column align-items-center text-center p-2 p-md-3" style="background: linear-gradient(160deg, #BAEDAA 0%, #9DE182 100%); border-radius: 20px; text-decoration: none; box-shadow: 0 10px 0 #5CAA44, 0 14px 28px rgba(80,160,60,0.3), inset 0 1px 0 rgba(255,255,255,0.5);">
                        <img src="{{ asset('uploads/images/robot.png') }}" alt="Science Explorer" class="img-fluid mb-2" style="max-height: 80px; object-fit: contain; filter: drop-shadow(0 6px 10px rgba(0,0,0,0.2));" fetchpriority="high" loading="eager" decoding="async">
                        <div class="s-card-title text-white" style="font-family: 'Bubblegum Sans', cursive; font-size: clamp(14px, 4vw, 20px); font-weight: 900; line-height: 1.1; margin-bottom: 4px; text-shadow: 0 2px 4px rgba(0,0,0,0.2);">Science Explorer</div>
                        <div class="s-card-desc text-white" style="font-size: clamp(9px, 2.5vw, 13px); line-height: 1.2; opacity: 0.9;">Discover the world with experiments!</div>
                    </a>
                </div>
                <div class="col-4">
                    <a href="{{ route('student.assigned_ebooks.index', ['subject' => 'English']) }}" class="s-card h-100 d-flex flex-column align-items-center text-center p-2 p-md-3" style="background: linear-gradient(160deg, #FFCC9E 0%, #FFB37C 100%); border-radius: 20px; text-decoration: none; box-shadow: 0 10px 0 #CC7A3C, 0 14px 28px rgba(200,120,60,0.3), inset 0 1px 0 rgba(255,255,255,0.5);">
                        <img src="{{ asset('uploads/images/test paper.png') }}" alt="English Storytime" class="img-fluid mb-2" style="max-height: 80px; object-fit: contain; filter: drop-shadow(0 6px 10px rgba(0,0,0,0.2));" fetchpriority="high" loading="eager" decoding="async">
                        <div class="s-card-title text-white" style="font-family: 'Bubblegum Sans', cursive; font-size: clamp(14px, 4vw, 20px); font-weight: 900; line-height: 1.1; margin-bottom: 4px; text-shadow: 0 2px 4px rgba(0,0,0,0.2);">English Storytime</div>
                        <div class="s-card-desc text-white" style="font-size: clamp(9px, 2.5vw, 13px); line-height: 1.2; opacity: 0.9;">Read tales and grow your vocabulary!</div>
                    </a>
                </div>
            </div>
            
            <!-- Scan QR Card -->
            <a href="{{ route('student.ebooks') }}?scan=true" class="daily-quest-flat d-flex align-items-stretch gap-3 p-3 p-md-4 mb-4" style="text-decoration: none; background: linear-gradient(160deg, #FFFFFF 0%, #F5F9FF 100%); border-radius: 20px; box-shadow: 0 10px 0 #E5EDF7, 0 14px 28px rgba(0,0,0,0.05), inset 0 1px 0 rgba(255,255,255,0.8); margin-top: 30px; transition: transform 0.15s, box-shadow 0.15s;"
                onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 14px 0 #E5EDF7, 0 20px 32px rgba(0,0,0,0.08), inset 0 1px 0 rgba(255,255,255,0.8)';"
                onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 10px 0 #E5EDF7, 0 14px 28px rgba(0,0,0,0.05), inset 0 1px 0 rgba(255,255,255,0.8)';"
                onmousedown="this.style.transform='translateY(4px)'; this.style.boxShadow='0 4px 0 #E5EDF7, 0 8px 16px rgba(0,0,0,0.04), inset 0 1px 0 rgba(255,255,255,0.8)';">
                <!-- Left Icon Column -->
                <div class="dq-left flex-shrink-0" style="width: 30%; max-width: 100px; background: linear-gradient(135deg, #FF9A9E, #FECFEF); border: 2px dashed #FF889B; flex: 0 0 30%;">
                    <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#FFF" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="filter: drop-shadow(0 2px 4px rgba(255,100,100,0.4));">
                        <path d="M4 7V4h3"></path>
                        <path d="M17 4h3v3"></path>
                        <path d="M20 17v3h-3"></path>
                        <path d="M7 20H4v-3"></path>
                        <rect x="8" y="8" width="8" height="8" rx="2"></rect>
                    </svg>
                </div>
                
                <!-- Right Text Column -->
                <div class="dq-right w-100 d-flex flex-column justify-content-center">
                    <div class="dq-title-text" style="font-family: 'Bubblegum Sans', cursive; font-size: clamp(18px, 5vw, 24px); font-weight: 900; color: #5E4D3B; line-height: 1.1; margin-bottom: 4px;">Unlock Ebook!</div>
                    <div class="dq-subtitle mb-2" style="font-size: clamp(10px, 2.8vw, 13px); color: #8D7E6A; line-height: 1.2;">Got a physical book? Scan its QR code here.</div>
                    
                    <div style="font-size: clamp(12px, 3.5vw, 15px); font-weight: 800; color: #4CBF88; margin-top: 2px; display: flex; align-items: center; gap: 4px;">
                        Click to scan
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M5 12h14M12 5l7 7-7 7"/>
                        </svg>
                    </div>
                </div>
            </a>
            
            <!-- Daily Quest Box -->
            <div class="daily-quest-flat d-flex align-items-stretch gap-3 p-3 p-md-4" style="background: linear-gradient(160deg, #FFF8DD 0%, #FFF3CC 100%); border-radius: 20px; box-shadow: 0 10px 0 #D4A017, 0 14px 28px rgba(200,160,20,0.25), inset 0 1px 0 rgba(255,255,255,0.8); margin-top: 30px;">
                <div class="dq-left flex-shrink-0" style="width: 35%; max-width: 140px; position: relative;">
                    <img src="{{ asset('uploads/images/treasuremap.png') }}" alt="Treasure Map" style="width: 100%; height: 100%; object-fit: cover; border-radius: 12px;" fetchpriority="high" loading="eager" decoding="async">
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

            <!-- Memory Match Minigame -->
            <div class="memory-game-wrap mt-5">
                <div class="section-title text-center mb-3 d-flex justify-content-center align-items-center gap-2" style="font-family:'Bubblegum Sans',cursive; font-size:24px; color:#5E4D3B;">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#5E4D3B" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="2" y="6" width="20" height="12" rx="2"></rect>
                        <path d="M6 12h4"></path>
                        <path d="M8 10v4"></path>
                        <path d="M15 13h.01"></path>
                        <path d="M18 11h.01"></path>
                    </svg>
                    Minigame: Memory Match
                </div>
                <div style="font-family:'Bubblegum Sans',cursive; font-size:clamp(16px,4vw,20px); color:#5E4D3B; text-align:center; margin-bottom:16px;">Flip the cards and find all the matching pairs!</div>
                <div class="memory-grid" id="memory-board">
                    <!-- Cards injected via JS -->
                </div>
                <div class="win-msg" id="win-msg">🎉 You matched them all! Awesome job! 🎉</div>
                <div class="text-center mt-4">
                    <button class="btn" style="background:#FFD561; color:#5E4D3B; font-weight:900; border-radius:999px; box-shadow:0 4px 0 #C9A300; padding: 8px 24px;" onclick="initMemoryGame()">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" style="margin-right:4px; margin-top:-2px;"><polyline points="1 4 1 10 7 10"></polyline><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path></svg>
                        Play Again
                    </button>
                </div>
            </div>

            <!-- Word Scramble Minigame -->
            <div class="scramble-game-wrap mt-5">
                <div class="scramble-title-area text-center mb-3">
                    <svg class="scramble-icon" viewBox="0 0 24 24" fill="none" stroke="#FF7C7C" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"></path>
                        <line x1="4" y1="22" x2="4" y2="15"></line>
                    </svg>
                    <span style="font-family:'Bubblegum Sans',cursive; font-size:24px; color:#5E4D3B;">Minigame: Word Scramble</span>
                </div>
                <div style="font-family:'Bubblegum Sans',cursive; font-size:clamp(16px,4vw,20px); color:#5E4D3B; text-align:center; margin-bottom:16px;" id="scramble-hint">Unscramble the letters to make a word!</div>
                
                <div class="scramble-answer-row" id="scramble-answer"></div>
                <div class="scramble-letters-row" id="scramble-letters"></div>
                
                <div class="win-msg" id="scramble-win-msg">🎉 Correct! Amazing! 🎉</div>
                <div class="text-center mt-4">
                    <button class="btn" id="scramble-next-btn" style="display:none; background:#FFD561; color:#5E4D3B; font-weight:900; border-radius:999px; box-shadow:0 4px 0 #C9A300; padding: 8px 24px; margin:0 auto;" onclick="initScrambleGame()">
                        Next Word
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" style="margin-left:4px; margin-top:-2px;"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                    </button>
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
                    ✅ Present Today
                </span>
            @elseif($alreadyRequested ?? false)
                <span style="background:#FFE0B2; color:#E65100; border-radius:999px; padding:8px 20px; font-size:13px; font-weight:900; font-family:'Quicksand',sans-serif; box-shadow:0 4px 0 #F57C00; display:inline-flex; align-items:center; gap:6px;">
                    ⏳ Requested
                </span>
            @else
                <form method="POST" action="{{ route('student.attendance.mark') }}">
                    @csrf
                    <button type="submit" style="background:linear-gradient(135deg,#9DE182,#5CAA44); color:#fff; border:none; border-radius:999px; padding:10px 22px; font-size:13px; font-weight:900; font-family:'Quicksand',sans-serif; cursor:pointer; box-shadow:0 6px 0 #3A7A28, 0 8px 16px rgba(60,120,40,0.25); transform:translateY(-2px); transition:all 0.15s; display:inline-flex; align-items:center; gap:6px;"
                        onmouseover="this.style.transform='translateY(-4px)'"
                        onmouseout="this.style.transform='translateY(-2px)'"
                        onmousedown="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 0 #3A7A28'">
                        ✋ Ask for Attendance
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

<!-- Memory Match Script -->
<script>
    const svgs = [
        '<svg viewBox="0 0 24 24" fill="#FFD561"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>',
        '<svg viewBox="0 0 24 24" fill="#FF7C7C"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>',
        '<svg viewBox="0 0 24 24" fill="#9DE182"><path d="M17 8C8 10 5.9 16.17 3.82 21.34l1.89.66l.95-2.3c.48.17 1.04.3 1.71.3 4.7 0 11.53-2.9 14.67-11.08C23.59 7.6 23.36 4.78 22 2c-3.17 1.4-4.8 4.67-5 6z"/></svg>',
        '<svg viewBox="0 0 24 24" fill="#8BDDFF"><path d="M7 2v11h3v9l7-12h-4l4-8z"/></svg>'
    ];
    
    let hasFlippedCard = false;
    let lockBoard = false;
    let firstCard, secondCard;
    let matches = 0;

    function initMemoryGame() {
        const board = document.getElementById('memory-board');
        const winMsg = document.getElementById('win-msg');
        if (!board) return;
        board.innerHTML = '';
        winMsg.style.display = 'none';
        matches = 0;
        hasFlippedCard = false;
        lockBoard = false;
        firstCard = null;
        secondCard = null;

        let cards = [...svgs, ...svgs];
        cards.sort(() => Math.random() - 0.5);

        cards.forEach((svg, index) => {
            const card = document.createElement('div');
            card.classList.add('m-card');
            card.dataset.val = svg;

            card.innerHTML = `
                <div class="m-card-face m-card-front">?</div>
                <div class="m-card-face m-card-back">${svg}</div>
            `;

            card.addEventListener('click', flipCard);
            board.appendChild(card);
        });
    }

    function flipCard() {
        if (lockBoard) return;
        if (this === firstCard) return;

        this.classList.add('flip');

        if (!hasFlippedCard) {
            hasFlippedCard = true;
            firstCard = this;
            return;
        }

        secondCard = this;
        checkForMatch();
    }

    function checkForMatch() {
        let isMatch = firstCard.dataset.val === secondCard.dataset.val;

        if (isMatch) {
            disableCards();
        } else {
            unflipCards();
        }
    }

    function disableCards() {
        firstCard.removeEventListener('click', flipCard);
        secondCard.removeEventListener('click', flipCard);
        firstCard.classList.add('matched');
        secondCard.classList.add('matched');
        resetBoard();
        
        matches++;
        if(matches === svgs.length) {
            setTimeout(() => {
                document.getElementById('win-msg').style.display = 'block';
            }, 500);
        }
    }

    function unflipCards() {
        lockBoard = true;
        setTimeout(() => {
            firstCard.classList.remove('flip');
            secondCard.classList.remove('flip');
            resetBoard();
        }, 1000);
    }

    function resetBoard() {
        [hasFlippedCard, lockBoard] = [false, false];
        [firstCard, secondCard] = [null, null];
    }

    document.addEventListener('DOMContentLoaded', initMemoryGame);
</script>

<!-- Word Scramble Script -->
<script>
    const scrambleWords = [
    { word: 'APPLE', hint: 'A red or green sweet fruit' },
    { word: 'TIGER', hint: 'A big wild cat with orange and black stripes' },
    { word: 'HOUSE', hint: 'A building where a family lives' },
    { word: 'WATER', hint: 'A clear liquid we drink when thirsty' },
    { word: 'HAPPY', hint: 'How you feel when you smile and laugh' },
    { word: 'LEARN', hint: 'To gain new knowledge or skill' },
    { word: 'SCHOOL', hint: 'A place where children go to study' },
    { word: 'FRIEND', hint: 'Someone you like to play and spend time with' },
    { word: 'BOOK', hint: 'You read stories and learn from this' },
    { word: 'SUN', hint: 'The bright star that gives us light during the day' },
    { word: 'MOON', hint: 'You can see this shining in the night sky' },
    { word: 'CLOUD', hint: 'A white or gray shape floating in the sky' },
    { word: 'FLOWER', hint: 'A colorful part of a plant that often smells nice' },
    { word: 'GARDEN', hint: 'A place where flowers and plants grow' },
    { word: 'BIRD', hint: 'An animal with feathers and wings' },
    { word: 'HORSE', hint: 'A large animal that people can ride' },
    { word: 'SMILE', hint: 'What you do when you are happy' },
    { word: 'CHAIR', hint: 'Something you sit on' },
    { word: 'PENCIL', hint: 'You use this to write or draw' },
    { word: 'RAINBOW', hint: 'A colorful arc that can appear after rain' }
];
    
    let currentWordObj;
    let answerTiles = [];
    let scrambledLetters = [];

    function initScrambleGame() {
        if (!document.getElementById('scramble-win-msg')) return;
        document.getElementById('scramble-win-msg').style.display = 'none';
        document.getElementById('scramble-next-btn').style.display = 'none';
        
        currentWordObj = scrambleWords[Math.floor(Math.random() * scrambleWords.length)];
        document.getElementById('scramble-hint').innerText = "Hint: " + currentWordObj.hint;
        
        let letters = currentWordObj.word.split('');
        // Ensure it is actually scrambled
        let scrambled = [...letters];
        while (scrambled.join('') === currentWordObj.word) {
            scrambled.sort(() => Math.random() - 0.5);
        }
        
        scrambledLetters = scrambled.map((char, index) => ({ id: index, char: char }));
        answerTiles = new Array(currentWordObj.word.length).fill(null);
        
        renderScramble();
    }

    function renderScramble() {
        const answerRow = document.getElementById('scramble-answer');
        const lettersRow = document.getElementById('scramble-letters');
        
        answerRow.innerHTML = '';
        lettersRow.innerHTML = '';
        
        // Render answer slots
        answerTiles.forEach((tileObj, slotIndex) => {
            const slot = document.createElement('div');
            slot.className = 'scramble-slot';
            if (tileObj) {
                const tile = createTile(tileObj, true, slotIndex);
                slot.appendChild(tile);
            }
            answerRow.appendChild(slot);
        });
        
        // Render available letters
        scrambledLetters.forEach(tileObj => {
            const tile = createTile(tileObj, false);
            lettersRow.appendChild(tile);
        });
        
        checkScrambleWin();
    }
    
    function createTile(tileObj, inAnswer, slotIndex = null) {
        const tile = document.createElement('div');
        tile.className = 's-tile' + (inAnswer ? ' in-answer' : '');
        tile.innerText = tileObj.char;
        
        tile.addEventListener('click', () => {
            if (inAnswer) {
                // Move back to letters row
                scrambledLetters.push(tileObj);
                answerTiles[slotIndex] = null;
                renderScramble();
            } else {
                // Move to first empty slot
                const emptyIndex = answerTiles.indexOf(null);
                if (emptyIndex !== -1) {
                    scrambledLetters = scrambledLetters.filter(t => t.id !== tileObj.id);
                    answerTiles[emptyIndex] = tileObj;
                    renderScramble();
                }
            }
        });
        return tile;
    }
    
    function checkScrambleWin() {
        if (answerTiles.includes(null)) return; // not full
        
        const currentWord = answerTiles.map(t => t.char).join('');
        if (currentWord === currentWordObj.word) {
            document.getElementById('scramble-win-msg').style.display = 'block';
            document.getElementById('scramble-next-btn').style.display = 'block';
        }
    }
    
    document.addEventListener('DOMContentLoaded', initScrambleGame);
</script>

@endsection
