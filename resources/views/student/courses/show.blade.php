@extends('layouts.student')
@section('title', $course->title)
@section('nav_courses', 'active')

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;600;700&family=Quicksand:wght@500;700;900&display=swap" rel="stylesheet">
<style>
/* ── Layout Overrides (can't avoid these — Bootstrap doesn't override parent layout) ── */
.sidebar { display: none !important; }
.topbar  { display: none !important; }
.main    { padding-bottom: 0 !important; margin: 0 !important; width: 100% !important; background: transparent !important; }
.content { padding: 0 !important; background: transparent !important; }
body {
    font-family: 'Quicksand', sans-serif;
    background: radial-gradient(ellipse at 60% 20%, #3a5fa8 0%, #1a2d6b 35%, #0d1a42 65%, #060c22 100%) !important;
    background-attachment: fixed !important;
    min-height: 100vh;
    overflow-x: hidden !important;
}

/* ── Page Loader ── */
.page-loader {
    position: fixed;
    inset: 0;
    z-index: 9999;
    background: radial-gradient(ellipse at 60% 20%, #3a5fa8 0%, #1a2d6b 35%, #0d1a42 65%, #060c22 100%);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 18px;
    transition: opacity 0.5s ease, visibility 0.5s ease;
    opacity: 1;
    visibility: visible;
}
.page-loader.hidden {
    opacity: 0;
    visibility: hidden;
    pointer-events: none;
}
.loader-bag {
    width: 100px;
    height: 100px;
    object-fit: contain;
    animation: loaderBag 0.7s ease-in-out infinite alternate;
    filter: drop-shadow(0 0 14px rgba(255,213,97,0.5));
}
.loader-text {
    font-family: 'Bubblegum Sans', cursive;
    font-size: 20px;
    color: #FFD561;
    letter-spacing: 0.5px;
    animation: loaderPulse 1.5s ease-in-out infinite;
}
.loader-dots span {
    display: inline-block;
    width: 10px; height: 10px;
    border-radius: 50%;
    background: #FFD561;
    margin: 0 4px;
    animation: loaderDot 1.2s ease-in-out infinite;
}
.loader-dots span:nth-child(2) { animation-delay: 0.2s; }
.loader-dots span:nth-child(3) { animation-delay: 0.4s; }
@@keyframes loaderBag {
    0%   { transform: scale(1);    filter: drop-shadow(0 0 10px rgba(255,213,97,0.35)); }
    100% { transform: scale(1.28); filter: drop-shadow(0 0 30px rgba(255,213,97,0.9)); }
}
@@keyframes loaderPulse {
    0%, 100% { opacity: 1; }
    50%       { opacity: 0.5; }
}
@@keyframes loaderDot {
    0%, 80%, 100% { transform: scale(0.6); opacity: 0.4; }
    40%           { transform: scale(1.2); opacity: 1; }
}

/* ── Map Container — max-width not in Bootstrap 5 as 450px ── */
.map-container { max-width: 450px; }

/* ── Crown Cap Badge — decorative image, must stay custom ── */
.cap-badge {
    position: absolute;
    top: 50px; left: 50%;
    transform: translateX(-50%);
    width: 160px; height: auto;
    z-index: 50;
    pointer-events: none;
    filter: drop-shadow(0 6px 18px rgba(0,0,0,0.4));
}

/* ── Book Button — green leaf pseudo-elements ── */
.btn-book {
    position: absolute;
    top: 20px;
    width: 52px; height: 52px;
    background: #FFFFFF;
    border: 3px solid #E8F5FF;
    border-radius: 18px;
    box-shadow: 0 5px 0 #A8D1EE, 0 8px 16px rgba(0,0,0,0.1);
    transition: all 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    z-index: 100;
    color: #3B9EE8;
}
.btn-book { right: 10px; }
.btn-book:hover {
    transform: translateY(-2px);
    box-shadow: 0 7px 0 #A8D1EE, 0 12px 20px rgba(0,0,0,0.15);
    color: #1A6BAA;
}
.btn-book:active {
    transform: translateY(2px);
    box-shadow: 0 1px 0 #A8D1EE, 0 2px 4px rgba(0,0,0,0.1);
}
.btn-book::before {
    content: ''; position: absolute; top: -5px; left: -5px;
    width: 16px; height: 16px;
    background: #8BD85F; border: 2px solid #5E4D3B;
    border-radius: 0 100% 0 100%; transform: rotate(-15deg); z-index: -1;
}
.btn-book::after {
    content: ''; position: absolute; bottom: -5px; right: -5px;
    width: 16px; height: 16px;
    background: #8BD85F; border: 2px solid #5E4D3B;
    border-radius: 0 100% 0 100%; transform: rotate(45deg); z-index: -1;
}
.btn-book svg { width: 26px; height: 26px; }

/* ── Header Scroll — background image must stay custom ── */
.header-scroll {
    background-image: url('{{ asset("uploads/images/stage/banner 2.png") }}');
    background-size: 100% 100%;
    background-repeat: no-repeat;
    width: 70%; height: 110px;
    margin-top: 30px;
    top: 65px;
    z-index: 10;
}
.header-chapter { font-size: 13px; font-weight: 900; color: #5E4D3B; letter-spacing: 0.5px; }
.header-title {
    font-family: 'Bubblegum Sans', cursive;
    font-size: 21px; font-weight: bold; color: #8B4F1D;
    overflow: hidden; text-overflow: ellipsis;
    display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical;
}

/* ── Map Board — background image must stay custom ── */
.map-board {
    background-image: url('{{ asset("uploads/images/stage/banner 1.png") }}');
    background-size: 100% 100%;
    background-repeat: no-repeat;
    padding: 30px 24px 20px 24px;
    margin-top: -15px;
    z-index: 5;
}

/* ── Progress Card — multi-layer shadow + gradient can't be done in Bootstrap ── */
.progress-card {
    background: linear-gradient(135deg, #FFF8E6 0%, #FFF2CC 60%, #FFE89A 100%);
    border: 2.5px solid #F5C842 !important;
    box-shadow: 0 2px 0 #C9A300, 0 6px 0 rgba(201,163,0,0.25),
                0 10px 24px rgba(0,0,0,0.18), inset 0 1px 0 rgba(255,255,255,0.7) !important;
    top: 50px;
    z-index: 20;
}

/* ── Progress track — custom colors ── */
.progress-track {
    height: 20px;
    background: #B6E5FF;
    border: 3px solid #FFFFFF;
    box-shadow: inset 0 2px 4px rgba(0,0,0,0.1), 0 2px 4px rgba(0,0,0,0.05);
    overflow: hidden;
}
.progress-fill {
    height: 100%;
    background: linear-gradient(180deg, #8BDDFF 0%, #3B9EE8 100%);
    transition: width 0.5s ease;
}

/* ── Star badge — overlaps progress bar edge, needs absolute + custom color ── */
.progress-star-badge {
    position: absolute; left: -15px; top: 50%;
    transform: translateY(-50%); z-index: 25;
}
.star-icon { filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2)); animation: bounce 2s infinite ease-in-out; display: flex; justify-content: center; }
.star-icon img { width: 36px; height: 36px; object-fit: contain; }
.star-label {
    font-size: 9px; font-weight: 900; color: #FFF;
    background: #3B9EE8; border: 2px solid #FFF;
    border-radius: 8px; margin-top: -6px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.15);
    min-width: 32px; text-align: center; line-height: 1.1;
    padding: 1px 6px;
}

/* ── Progress pill — custom color ── */
.progress-value-pill {
    position: absolute; right: -15px; top: 50%;
    transform: translateY(-50%); z-index: 25;
    background: #3B9EE8; color: #FFF;
    font-size: 12px; font-weight: 900;
    border: 2px solid #FFF;
    box-shadow: 0 2px 4px rgba(0,0,0,0.15);
}

/* ── Treasure chest — floats above card corner ── */
.treasure-box-img {
    position: absolute; top: -18px; right: -10px;
    width: 72px; height: auto; z-index: 30;
    filter: drop-shadow(0 6px 12px rgba(0,0,0,0.25));
    animation: pulse-chest 3s infinite ease-in-out;
    cursor: pointer;
}

/* ── Animations — always custom ── */
@@keyframes bounce { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-4px); } }
@@keyframes pulse-chest { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.05); } }
@@keyframes wiggle { 0%, 100% { transform: rotate(0deg); } 25% { transform: rotate(-2deg); } 75% { transform: rotate(2deg); } }
@@keyframes happy-dance {
    /* Wobble left-right */
    0%  { transform: translateY(0px) rotate(0deg) scale(1, 1); }
    7%  { transform: translateY(-4px) rotate(-15deg) scale(1, 1); }
    14% { transform: translateY(0px) rotate(0deg) scale(1, 1); }
    21% { transform: translateY(-4px) rotate(15deg) scale(1, 1); }
    
    42% { transform: translateY(0px) rotate(0deg) scale(1, 1); }


    /* Jump */
    62% { transform: translateY(0px) rotate(0deg) scale(1.15, 0.85); } /* squish down */
    75% { transform: translateY(-16px) rotate(0deg) scale(0.9, 1.1); } /* stretch up */
    88% { transform: translateY(0px) rotate(0deg) scale(1.1, 0.9); }   /* land squish */
    94% { transform: translateY(-5px) rotate(0deg) scale(0.98, 1.02); }/* mini bounce */
    100%{ transform: translateY(0px) rotate(0deg) scale(1, 1); }
}

/* Apply happy dance animation to the star on the current active stage */
.current-stage .sc-star-img {
    animation: happy-dance 2.5s infinite ease-in-out;
    transform-origin: bottom center;
}
/* 🌟 Path Map Area - fixed height needed for absolute card positions 🌟 */
.path-map-area {
    position: relative; width: 100%;
    height: 600px; margin-top: 70px; margin-bottom: -70px;
}
.path-svg { position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 1; pointer-events: none; }

/* ── Stage card wrappers — absolute grid positions can't be Bootstrap ── */
.stage-card-wrapper { position: absolute; z-index: 10; }
.wrapper-1 { top: 2%;  left: 0%;  width: 75%; }
.wrapper-2 { top: 23%; right: 0%; width: 75%; }
.wrapper-3 { top: 44%; left: 0%;  width: 75%; }
.wrapper-4 { top: 65%; right: 0%; width: 75%; }
.wrapper-5 { top: 84%; left: 0%;  width: 75%; }

.stage-card { display: block; position: relative; width: 100%; text-decoration: none; transition: transform 0.25s cubic-bezier(0.175, 0.885, 0.32, 1.275), filter 0.25s; cursor: pointer; }
.stage-card:hover          { transform: scale(1.04) translateY(-2px); filter: drop-shadow(0 10px 20px rgba(0,0,0,0.15)); }
.stage-card:active         { transform: scale(0.98) translateY(0); }
.stage-card.locked         { cursor: not-allowed; filter: opacity(0.85); }
.stage-card.locked:hover   { transform: none; animation: wiggle 0.4s ease; }
.stage-card.disabled-completed       { cursor: default; }
.stage-card.disabled-completed:hover { transform: none; filter: none; }

/* ── Dynamic Stage Card Layout ── */
.sc-card {
    display: flex;
    align-items: center;
    background: #FFFFFF;
    border-radius: 20px;
    border: 3px solid;
    padding: 10px 16px 10px 0;
    gap: 8px;
    box-shadow: 0 4px 0 rgba(0,0,0,0.12), 0 8px 20px rgba(0,0,0,0.10);
    position: relative;
    overflow: visible;
    min-height: 72px;
    width: 85%;
    right: -30px;
}
.sc-icon-wrap {
    position: relative;
    flex-shrink: 0;
    width: 68px; height: 68px;
    margin-left: -26px;
}
.sc-icon-img {
    width: 68px; height: 68px;
    object-fit: contain;
    filter: drop-shadow(0 3px 8px rgba(0,0,0,0.18));
}
.sc-status {
    position: absolute; bottom: 0px; left: 2px;
    width: 24px; height: 24px;
    object-fit: contain;
    filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));
    z-index: 5;
}
.sc-body {
    flex: 1;
    min-width: 0;
}
.sc-title {
    font-family: 'Quicksand', sans-serif;
    font-size: 13px; font-weight: 900;
    margin: 0 0 2px 0;
    line-height: 1.2;
}
.sc-desc {
    font-size: 10px; font-weight: 600;
    color: #8D7E6A;
    margin: 0;
    line-height: 1.3;
}
.sc-right {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 2px;
    flex-shrink: 0;
    background: #F8FBFF;
    border: 2px solid #E2E8F0;
    border-radius: 12px;
    padding: 6px 12px;
    margin-right: 4px;
    box-shadow: inset 0 2px 4px rgba(255,255,255,0.8), 0 2px 4px rgba(0,0,0,0.05);
}
.sc-star-img {
    width: 28px; height: 28px;
    object-fit: contain;
    filter: drop-shadow(0 2px 4px rgba(0,0,0,0.15));
}
.sc-star-count {
    font-size: 11px; font-weight: 900;
    color: #1E3A8A;
    line-height: 1;
}
.sc-arrow {
    position: absolute;
    right: -14px; top: 50%;
    transform: translateY(-50%);
    width: 24px; height: auto;
    filter: drop-shadow(0 2px 4px rgba(0,0,0,0.15));
    z-index: 5;
}
/* Per-stage color themes */
.sc-stage-1 { border-color: #6BB8FF; }
.sc-stage-1 .sc-title { color: #1A5FAA; }
.sc-stage-1 .sc-arrow { filter: drop-shadow(0 2px 4px rgba(0,0,0,0.15)) hue-rotate(-46deg); }

.sc-stage-2 { border-color: #5CB85C; }
.sc-stage-2 .sc-title { color: #276027; }
.sc-stage-2 .sc-arrow { filter: drop-shadow(0 2px 4px rgba(0,0,0,0.15)) hue-rotate(-135deg); }

.sc-stage-3 { border-color: #FF9043; }
.sc-stage-3 .sc-title { color: #C05010; }
.sc-stage-3 .sc-arrow { filter: drop-shadow(0 2px 4px rgba(0,0,0,0.15)) hue-rotate(130deg); }

.sc-stage-4 { border-color: #B19DFF; }
.sc-stage-4 .sc-title { color: #5B45B0; }
/* stage 4 arrow is already purple, no rotation needed */


.stage-card.locked .sc-card { filter: saturate(0.6) opacity(0.85); }

/* ── Flower Decorations — absolute positions, custom ── */
.deco { position: absolute; height: auto; z-index: 2; pointer-events: none; filter: drop-shadow(0 4px 8px rgba(0,0,0,0.1)); }
.deco-1 { top: 11%; right: 8%;  width: 50px; }
.deco-2 { top: 28%; left: 8%;   width: 55px; }
.deco-3 { top: 48%; right: 10%; width: 50px; }
.deco-4 { top: 66%; left: 8%;   width: 55px; }
.deco-5 { top: 82%; right: 8%;  width: 55px; }

/* ── Footer Scroll — background image ── */
.scroll-banner {
    background-image: url('{{ asset("uploads/images/stage/treasure map.png") }}');
    background-size: 100% 100%; background-repeat: no-repeat;
    width: 105%; height: 75px;
}
.scroll-text  { font-family: 'Fredoka', sans-serif; color: #D35400; font-weight: 900; font-size: 13px; line-height: 1.2; }
.scroll-icon  { position: absolute; right: 28px; top: 50%; transform: translateY(-50%); width: 32px; height: 32px; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1)); }

/* ── Chapter Modal — custom colors + transitions ── */
.ch-overlay { position: fixed; inset: 0; background: rgba(15,23,42,0.75); backdrop-filter: blur(8px); z-index: 2000; opacity: 1; transition: opacity 0.3s ease; }
.ch-overlay.hidden { opacity: 0; pointer-events: none; }
.ch-overlay.hidden .ch-modal { transform: scale(0.9); }
.ch-modal { background: #FFF9E5; border: 4px solid #FFE8AC !important; box-shadow: 0 24px 48px rgba(0,0,0,0.25); transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275); }

.ch-title { font-family: 'Bubblegum Sans', cursive; font-size: 26px; color: #5E4D3B; }
.ch-list::-webkit-scrollbar { width: 6px; }
.ch-list::-webkit-scrollbar-track { background: transparent; }
.ch-list::-webkit-scrollbar-thumb { background: rgba(94,77,59,0.2); border-radius: 999px; }
.ch-item { display: flex; align-items: center; justify-content: space-between; background: #FFF; border: 2px solid #E8E0D0 !important; text-decoration: none; color: inherit; transition: all 0.2s; }
.ch-item:hover { border-color: #8BDDFF !important; transform: translateY(-2px); box-shadow: 0 6px 16px rgba(94,77,59,0.06); }
.ch-item.active { border-color: #FFB37C !important; background: #FFFDF8; }
.ch-item.locked { opacity: 0.65; cursor: not-allowed; }
.ch-item.locked:hover { transform: none; box-shadow: none; border-color: #E8E0D0 !important; }
.ch-info h4 { margin: 0 0 2px 0; font-size: 15px; font-weight: 800; color: #5E4D3B; }
.ch-info p  { margin: 0; font-size: 11px; font-weight: 600; color: #8D7E6A; }
.ch-badge { font-size: 11px; font-weight: 900; }
.ch-badge.completed { background: rgba(157,225,130,0.15); color: #5CAA44; }
.ch-badge.unlocked  { background: rgba(139,221,255,0.15); color: #1A6BAA; }
.ch-badge.locked    { background: rgba(141,126,106,0.15); color: #8D7E6A; }

/* ── Locked Toast — fixed + custom color ── */
.locked-toast {
    position: fixed; bottom: 100px; left: 50%;
    transform: translateX(-50%) translateY(20px);
    background: rgba(94,77,59,0.95); color: #FFF;
    font-weight: 800; font-size: 14px; z-index: 9999;
    box-shadow: 0 8px 24px rgba(0,0,0,0.2); border: 2px solid #FFE8AC;
    opacity: 0; transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}
.locked-toast.show { opacity: 1; transform: translateX(-50%) translateY(0); }

/* ── Treasure Chest Unlock Overlay ── */
.chest-unlock-overlay {
    position: fixed; inset: 0; background: rgba(10, 15, 35, 0.85); backdrop-filter: blur(5px);
    z-index: 3000; display: flex; flex-direction: column; align-items: center; justify-content: center;
    opacity: 0; pointer-events: none; transition: opacity 0.5s ease;
}
.chest-unlock-overlay.show { opacity: 1; pointer-events: auto; }

.chest-container {
    position: relative; width: 300px; height: 300px;
    transform: scale(0); transition: transform 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}
.chest-unlock-overlay.show .chest-container { transform: scale(1); animation: chest-wiggle 2.5s infinite 1s; }
.chest-container.opening { animation: none !important; }

.chest-lower { position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: contain; z-index: 2; }
.chest-upper { position: absolute; top: 0; left: 0; width: 100%; height: 48%; object-fit: contain; z-index: 3; transition: opacity 1.2s ease; }
.chest-upper.opened-lid { opacity: 0; height: 48%;
    width: 120%;
    top: -15%; }

.chest-star {
    position: absolute; top: 40px; left: 50%; width: 120px; height: 120px;
    margin-left: -60px; z-index: 4; opacity: 0; transform: scale(0.2) translateY(60px);
    filter: drop-shadow(0 0 25px rgba(255, 215, 0, 0.9));
    transition: all 1.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

/* Opening sequence */
.chest-container.opening .closed-lid { opacity: 0; }
.chest-container.opening .opened-lid { opacity: 1; }
.chest-container.opening .chest-star { opacity: 1; transform: scale(2.2) translateY(-50px) rotate(360deg); transition-delay: 0.5s; }

.chest-wrapper {
    display: flex; flex-direction: column; align-items: center; cursor: pointer; margin-top: 45%;
}
.chest-btn-img {
    position: absolute; top: 14%; left: 19%; transform: translate(-50%, -10px);
    z-index: 5; width: 100px;
    animation: pulse-chest 5s infinite; filter: drop-shadow(0 4px 10px rgba(0,0,0,0.4));
    transition: top 0.6s cubic-bezier(0.55, 0.085, 0.68, 0.53), opacity 0.5s ease-in;
}
.tap-text {
    color: white; font-weight: bold; font-size: 45px; margin-top: 20px;
    font-family: 'Fredoka', sans-serif; text-shadow: 0 2px 4px rgba(0,0,0,0.8); white-space: nowrap;
    transition: opacity 0.3s;
}
.chest-container.opening .chest-btn-img { top: 100% !important; opacity: 0; pointer-events: none; }
.chest-wrapper.opening .tap-text { opacity: 0; pointer-events: none; }

.success-message-wrap {
    margin-top: 40px; opacity: 0; pointer-events: none; transform: translateY(20px);
    transition: all 0.5s ease 1.2s; /* Appears after star animation finishes */
    display: flex; flex-direction: column; align-items: center;
}
.chest-wrapper.opening ~ .success-message-wrap { opacity: 1; pointer-events: auto; transform: translateY(0); }

.yay-text {
    font-family: 'Bubblegum Sans', cursive; font-size: 38px; color: #FFD700;
    text-shadow: 0 4px 10px rgba(0,0,0,0.6); margin-bottom: 20px;
    animation: bounce 2s infinite; text-align: center; line-height: 1.1;
}



@@keyframes chest-wiggle {
    0%, 100% { transform: scale(1) rotate(0); }
    5%, 15% { transform: scale(1.05) rotate(-5deg); }
    10%, 20% { transform: scale(1.05) rotate(5deg); }
    25% { transform: scale(1) rotate(0); }
}
</style>
@endpush

@section('content')

@php
    $requestedChapterId = request()->query('chapter_id');
    $currentChapterData = null;
    if ($requestedChapterId) {
        foreach ($chaptersData as $data) {
            if ($data['chapter']->id == $requestedChapterId) { $currentChapterData = $data; break; }
        }
    }
    if (!$currentChapterData) {
        foreach ($chaptersData as $data) {
            if ($data['unlocked'] && !$data['completed']) { $currentChapterData = $data; break; }
        }
    }
    if (!$currentChapterData && count($chaptersData) > 0) $currentChapterData = $chaptersData[0];

    $activeChapter   = $currentChapterData ? $currentChapterData['chapter'] : null;
    $chapterUnlocked = $currentChapterData ? $currentChapterData['unlocked'] : false;
    $chapterCompleted= $currentChapterData ? $currentChapterData['completed'] : false;
    $lessons = $activeChapter ? $activeChapter->lessons : collect();


    $stage1 = $lessons->get(0); $stage2 = $lessons->get(1);
    $stage3 = $lessons->get(2); $stage4 = $lessons->get(3);

    $stage1Completed = $stage1 ? $stage1->isCompletedBy($user) : false;
    $stage2Completed = $stage2 ? $stage2->isCompletedBy($user) : false;
    $stage3Completed = $stage3 ? $stage3->isCompletedBy($user) : false;
    $stage4Completed = $stage4 ? $stage4->isCompletedBy($user) : false;

    $s1Unlocked = $chapterUnlocked;
    $s2Unlocked = $chapterUnlocked && ($stage1 ? $stage1Completed : true);
    $s3Unlocked = $s2Unlocked && ($stage2 ? $stage2Completed : true);
    $s4Unlocked = $s3Unlocked && ($stage3 ? $stage3Completed : true);

    $stage2EarnedStars = 0;
    $stage3EarnedStars = 0;
    $stage4EarnedStars = 0;
    if ($stage2 || $stage3 || $stage4) {
        $ebChap = \App\Models\EbookChapter::where('ebook_id', $course->ebook_id ?? 2)
            ->where('chapter_number', $activeChapter->order + 1)->first();
        if ($ebChap) {
            $stage2Progress = \App\Models\LessonProgress::where('user_id', $user->id)
                ->where('chapter_id', $ebChap->id)
                ->where('stage_number', 2)
                ->first();
            if ($stage2Progress) {
                $stage2Score = $stage2Progress->score ?? 0;
                if ($stage2Score == 10) $stage2EarnedStars = 3;
                elseif ($stage2Score >= 8) $stage2EarnedStars = 2;
                elseif ($stage2Score >= 4) $stage2EarnedStars = 1;
            }

            $stage3Progress = \App\Models\LessonProgress::where('user_id', $user->id)
                ->where('chapter_id', $ebChap->id)
                ->where('stage_number', 3)
                ->first();
            if ($stage3Progress) {
                $stage3Score = $stage3Progress->score ?? 0;
                if ($stage3Score == 10) $stage3EarnedStars = 3;
                elseif ($stage3Score >= 8) $stage3EarnedStars = 2;
                elseif ($stage3Score >= 4) $stage3EarnedStars = 1;
            }

            $stage4Progress = \App\Models\LessonProgress::where('user_id', $user->id)
                ->where('chapter_id', $ebChap->id)
                ->where('stage_number', 4)
                ->first();
            if ($stage4Progress) {
                $stage4Score = $stage4Progress->score ?? 0;
                if ($stage4Score == 10) $stage4EarnedStars = 4;
                elseif ($stage4Score >= 8) $stage4EarnedStars = 3;
                elseif ($stage4Score >= 5) $stage4EarnedStars = 2;
                elseif ($stage4Score >= 2) $stage4EarnedStars = 1;
            }
        }
    }

    $earnedStars = 0; $totalStars = 0;
    if ($stage1) { $totalStars += 3; if ($stage1Completed) $earnedStars += 3; }
    if ($stage2) { $totalStars += 3; if ($stage2Completed) $earnedStars += $stage2EarnedStars; }
    if ($stage3) { $totalStars += 3; if ($stage3Completed) $earnedStars += $stage3EarnedStars; }
    if ($stage4) { $totalStars += 4; if ($stage4Completed) $earnedStars += $stage4EarnedStars; }
    if ($totalStars === 0) $totalStars = 13;
    $progressPercent = $totalStars > 0 ? round(($earnedStars / $totalStars) * 100) : 0;
@endphp

@if($activeChapter)

{{-- ── CHAPTER JOURNEY MAP (shows first on load, dismisses to reveal stage) ── --}}
@if(count($chaptersData) > 0)
@include('student.partials.chapter_map')
@endif

{{-- ── PAGE LOADER (shown while images are loading) ── --}}
<div id="page-loader" class="page-loader">
    <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('images/logo.png'))) }}" class="loader-bag" alt="Loading..." fetchpriority="high" loading="eager" decoding="async">
    <div class="loader-text">Loading your adventure…</div>
    <div class="loader-dots">
        <span></span><span></span><span></span>
    </div>
</div>

{{-- ── MAP CONTAINER ── --}}
<div class="map-container w-100 mx-auto position-relative d-flex flex-column align-items-center">

    {{-- Crown cap badge --}}
    <img src="{{ asset('uploads/images/stage/banner 3.png') }}" class="cap-badge" alt="Chapter Crown" fetchpriority="high" loading="eager" decoding="async">

    {{-- Navigation Buttons --}}
    @if(count($chaptersData) > 0)
    <button onclick="openCjm()" style="position: absolute; top: 20px; left: 10px; background: transparent; border: none; padding: 0; cursor: pointer; z-index: 100; transition: transform 0.15s;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'" title="Back to Map">
        <img src="{{ asset('uploads/images/buttons/Previous button.png') }}" alt="Back" style="height: 52px; object-fit: contain;" fetchpriority="high" loading="eager" decoding="async">
    </button>
    @else
    <a href="{{ route('student.courses.index') }}" style="position: absolute; top: 20px; left: 10px; z-index: 100; transition: transform 0.15s;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'" title="Back to Subjects">
        <img src="{{ asset('uploads/images/buttons/Previous button.png') }}" alt="Back" style="height: 52px; object-fit: contain;" fetchpriority="high" loading="eager" decoding="async">
    </a>
    @endif
    <button class="btn-book d-flex align-items-center justify-content-center border-0" onclick="openChapterList()" title="Select Chapter">
        <svg viewBox="0 0 24 24"><path fill="currentColor" d="M19,2L14,7H9L4,2V14H9L14,19H19V2M17,16H15.5V14.5H14V13H15.5V11.5H17V13H18.5V14.5H17V16Z"/></svg>
    </button>

    {{-- Header Scroll Banner --}}
    <div class="header-scroll position-relative d-flex flex-column align-items-center justify-content-center mx-auto text-center pt-1 mb-1">
        <div class="header-chapter text-uppercase mb-1">Chapter-{{ $activeChapter->order + 1 }}</div>
        <h2 class="header-title mb-0">{{ $activeChapter->title }}</h2>
    </div>

    {{-- Map Board --}}
    <div class="map-board w-100 d-flex flex-column position-relative">

        {{-- Progress Card --}}
        <div class="progress-card position-relative rounded-4 p-3 mx-auto" style="width:90%;">
            <div class="position-relative d-flex align-items-center px-2">
                {{-- Progress Container --}}
                <div class="position-relative flex-grow-1 d-flex align-items-center" style="margin-left:28px; margin-right:60px; height:42px;">
                    {{-- Star badge --}}
                    <div class="progress-star-badge d-flex flex-column align-items-center">
                        <div class="star-icon"><img src="{{ asset('uploads/images/stage/star.png') }}" fetchpriority="high" loading="eager" decoding="async"></div>
                        <div class="star-label px-1">x{{ $earnedStars }}</div>
                    </div>
                    {{-- Track --}}
                    <div class="progress-track w-100 rounded-pill">
                        <div class="progress-fill rounded-pill" style="width: {{ $progressPercent }}%;"></div>
                    </div>
                    {{-- Value pill --}}
                    <div class="progress-value-pill px-3 py-0 rounded-pill">
                        {{ $earnedStars }}/{{ $totalStars }}
                    </div>
                </div>
                {{-- Treasure Chest --}}
                @if($chapterCompleted)
                    <div class="treasure-box-img" style="height: 72px; animation: none; cursor: default;" title="Reward Claimed!">
                        <img src="{{ asset('uploads/images/stage/star.png') }}" style="position: absolute; top: -15px; left: 50%; width: 50px; margin-left: -25px; z-index: 1; filter: drop-shadow(0 0 10px rgba(255, 215, 0, 0.9));" alt="Star" fetchpriority="high" loading="eager" decoding="async">
                        <img src="{{ asset('uploads/images/stage/lower_chest.png') }}" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: contain; z-index: 2;" alt="Opened Chest" fetchpriority="high" loading="eager" decoding="async">
                        <img src="{{ asset('uploads/images/stage/upper_chest_opened.png') }}" style="position: absolute; top: -19%; left: -5%; width: 120%; height: 50%; object-fit: contain; z-index: 3;" alt="Lid" fetchpriority="high" loading="eager" decoding="async">
                    </div>
                @else
                    <img src="{{ asset('uploads/images/stage/treasure box.png') }}" class="treasure-box-img" alt="Treasure Chest" fetchpriority="high" loading="eager" decoding="async">
                @endif
            </div>
        </div>

        {{-- Path Map Area --}}
        <div class="path-map-area">
            {{-- Curved dashed path lines --}}
            <svg class="path-svg" viewBox="0 0 100 100" preserveAspectRatio="none">
                {{-- 1 to 2: Blue — left card right-edge (~x=37, y=10) → right card left-edge (~x=25, y=31) --}}
                <path d="M 37 11  C 55 11,  55 31,  63 31" fill="none" stroke="#6BB8FF" stroke-width="4" stroke-linecap="round" stroke-dasharray="10 14" vector-effect="non-scaling-stroke"/>
                {{-- 2 to 3: Green — right card left-edge (~x=25, y=31) → left card right-edge (~x=37, y=52) --}}
                <path d="M 63 31  C 45 31,  45 52,  37 52" fill="none" stroke="#5CB85C" stroke-width="4" stroke-linecap="round" stroke-dasharray="10 14" vector-effect="non-scaling-stroke"/>
                {{-- 3 to 4: Orange — left card right-edge (~x=37, y=52) → right card left-edge (~x=25, y=73) --}}
                <path d="M 37 52  C 55 52,  55 73,  63 73" fill="none" stroke="#FF9043" stroke-width="4" stroke-linecap="round" stroke-dasharray="10 14" vector-effect="non-scaling-stroke"/>
            </svg>

            {{-- Flower Decorations --}}
            <img src="{{ asset('uploads/images/stage/flower-1.png') }}" class="deco deco-1" alt="" fetchpriority="high" loading="eager" decoding="async">
            <img src="{{ asset('uploads/images/stage/flower-1.png') }}" class="deco deco-2" alt="" fetchpriority="high" loading="eager" decoding="async">
            <img src="{{ asset('uploads/images/stage/flower-2.png') }}" class="deco deco-3" alt="" fetchpriority="high" loading="eager" decoding="async">
            <img src="{{ asset('uploads/images/stage/flower-2.png') }}" class="deco deco-4" alt="" fetchpriority="high" loading="eager" decoding="async">
            <img src="{{ asset('uploads/images/stage/flower-1.png') }}" class="deco deco-5" alt="" fetchpriority="high" loading="eager" decoding="async">

            {{-- ── Stage Cards (generated from $stageMap array) ── --}}
            @php
                $stageMap = [
                    [
                        'model'      => $stage1,
                        'unlocked'   => $s1Unlocked,
                        'completed'  => $stage1Completed,
                        'is_current' => $s1Unlocked && !$stage1Completed,
                        'route'      => 'student.courses.stage',
                        'route_params' => ['id' => $course->id, 'stage' => 1, 'chapter_id' => $activeChapter->id],
                        'wrapper'    => 'wrapper-1',
                        'theme'      => 'sc-stage-1',
                        'icon'       => 'reading.png',
                        'title'      => '1. Reading Mission',
                        'desc'       => 'Read the story and answer the questions.',
                        'stars'      => 3,
                        'earned_stars' => $stage1Completed ? 3 : 0,
                    ],
                    [
                        'model'      => $stage2,
                        'unlocked'   => $s2Unlocked,
                        'completed'  => $stage2Completed,
                        'is_current' => $s2Unlocked && !$stage2Completed,
                        'route'      => 'student.courses.stage',
                        'route_params' => ['id' => $course->id, 'stage' => 2, 'chapter_id' => $activeChapter->id],
                        'wrapper'    => 'wrapper-2',
                        'theme'      => 'sc-stage-2',
                        'icon'       => 'hardwords.png',
                        'title'      => '2. Hard Words',
                        'desc'       => 'Learn new words and their meanings.',
                        'stars'      => 3,
                        'earned_stars' => $stage2EarnedStars,
                    ],
                    [
                        'model'      => $stage3,
                        'unlocked'   => $s3Unlocked,
                        'completed'  => $stage3Completed,
                        'is_current' => $s3Unlocked && !$stage3Completed,
                        'route'      => 'student.courses.stage',
                        'route_params' => ['id' => $course->id, 'stage' => 3, 'chapter_id' => $activeChapter->id],
                        'wrapper'    => 'wrapper-3',
                        'theme'      => 'sc-stage-3',
                        'icon'       => 'games.png',
                        'title'      => '3. Activity Mission',
                        'desc'       => 'Play fun activities to understand better.',
                        'stars'      => 3,
                        'earned_stars' => $stage3EarnedStars,
                    ],
                    [
                        'model'      => $stage4,
                        'unlocked'   => $s4Unlocked,
                        'completed'  => $stage4Completed,
                        'is_current' => $s4Unlocked && !$stage4Completed,
                        'route'      => 'student.courses.stage',
                        'route_params' => ['id' => $course->id, 'stage' => 4, 'chapter_id' => $activeChapter->id],
                        'wrapper'    => 'wrapper-4',
                        'theme'      => 'sc-stage-4',
                        'icon'       => 'exercise.png',
                        'title'      => '4. Exercise Mission',
                        'desc'       => 'Complete the exercises to build your skills.',
                        'stars'      => 4,
                        'earned_stars' => $stage4EarnedStars,
                    ],
                ];
            @endphp

            @foreach($stageMap as $s)
            <div class="stage-card-wrapper {{ $s['wrapper'] }} {{ $s['is_current'] ? 'current-stage' : '' }}">
                @if($s['model'] && $s['unlocked'])
                    <a href="{{ isset($s['route_params']) ? route($s['route'], $s['route_params']) : route($s['route'], $s['model']->id) }}" class="stage-card">
                @else
                    <div class="stage-card {{ !$s['model'] ? 'disabled-completed' : 'locked' }}"
                         @if($s['model']) onclick="playLockedSound()" @endif>
                @endif
                    <div class="sc-card {{ $s['theme'] }}">
                        <div class="sc-icon-wrap">
                            <img src="{{ asset('uploads/images/stage/' . $s['icon']) }}"
                                 class="sc-icon-img" alt="{{ $s['title'] }}" fetchpriority="high" loading="eager" decoding="async">
                            @if($s['completed'])
                                <img src="{{ asset('uploads/images/stage/tick icon.png') }}" class="sc-status" alt="Done" fetchpriority="high" loading="eager" decoding="async">
                            @elseif(!$s['unlocked'])
                                <img src="{{ asset('uploads/images/buttons/lock button.png') }}" class="sc-status" alt="Locked" fetchpriority="high" loading="eager" decoding="async">
                            @endif
                        </div>
                        <div class="sc-body">
                            <p class="sc-title">{{ $s['title'] }}</p>
                            <p class="sc-desc">{{ $s['desc'] }}</p>
                        </div>
                        <div class="sc-right">
                            <img src="{{ asset('uploads/images/stage/star.png') }}" class="sc-star-img" alt="Star" fetchpriority="high" loading="eager" decoding="async">
                            <span class="sc-star-count">
                                @if($s['completed'] && isset($s['earned_stars']))
                                    {{ $s['earned_stars'] }}/{{ $s['stars'] }}
                                @else
                                    x{{ $s['stars'] }}
                                @endif
                            </span>
                        </div>
                        <img src="{{ asset('uploads/images/stage/arrow.png') }}" class="sc-arrow" alt="" fetchpriority="high" loading="eager" decoding="async">
                    </div>
                @if($s['model'] && $s['unlocked']) </a> @else </div> @endif
            </div>
            @endforeach

        </div>

        {{-- Bottom Scroll Banner --}}
        <div class="w-100 mt-3 d-flex justify-content-center position-relative" style="z-index:15;">
            <div class="scroll-banner d-flex align-items-center justify-content-center position-relative" style="padding: 0 45px;">
                @if($chapterCompleted)
                    <span class="scroll-text text-center pe-3" style="color:#27AE60;">Chapter completed! Great job! 🎉</span>
                    <img src="{{ asset('uploads/images/stage/tick icon.png') }}" class="scroll-icon" alt="Tick" fetchpriority="high" loading="eager" decoding="async">
                @else
                    <span class="scroll-text text-center pe-3">Complete all missions to unlock the next chapter!</span>
                    <img src="{{ asset('uploads/images/buttons/lock button.png') }}" class="scroll-icon" alt="Lock" fetchpriority="high" loading="eager" decoding="async">
                @endif
            </div>
        </div>

    </div>{{-- /map-board --}}
</div>{{-- /map-container --}}

{{-- Chapter Selector Modal --}}
<div id="ch-selector-modal" class="ch-overlay d-flex justify-content-center align-items-center hidden" onclick="if(event.target===this) closeChapterList()">
    <div class="ch-modal position-relative rounded-4 p-4" style="width:90%; max-width:400px;">
        <button onclick="closeChapterList()" style="position: absolute; top: -14px; right: -14px; background: transparent; border: none; padding: 0; cursor: pointer; transition: transform 0.15s;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
            <img src="{{ asset('uploads/images/buttons/cross button.png') }}" alt="Close" style="width: 38px; height: 38px; object-fit: contain;" fetchpriority="high" loading="eager" decoding="async">
        </button>
        <h3 class="ch-title text-center mb-4">Select Chapter</h3>
        <div class="ch-list d-flex flex-column gap-3 pe-1" style="max-height:350px; overflow-y:auto;">
            @foreach($chaptersData as $data)
                @php
                    $ch = $data['chapter'];
                    $isCurrent   = $activeChapter && $ch->id == $activeChapter->id;
                    $chUnlocked  = $data['unlocked'];
                    $chCompleted = $data['completed'];
                    $badgeText   = $chCompleted ? 'Done' : ($chUnlocked ? 'Unlocked' : 'Locked');
                    $badgeClass  = $chCompleted ? 'completed' : ($chUnlocked ? 'unlocked' : 'locked');
                @endphp
                @if($chUnlocked)
                    <a href="?chapter_id={{ $ch->id }}" class="ch-item rounded-4 p-3 {{ $isCurrent ? 'active' : '' }}">
                @else
                    <div class="ch-item rounded-4 p-3 locked" onclick="playLockedSound()">
                @endif
                    <div class="ch-info">
                        <h4>Chapter {{ $ch->order + 1 }}</h4>
                        <p>{{ $ch->title }}</p>
                    </div>
                    <span class="ch-badge px-2 py-1 rounded-pill {{ $badgeClass }}">{{ $badgeText }}</span>
                @if($chUnlocked) </a> @else </div> @endif
            @endforeach
        </div>
    </div>
</div>

@else
<div class="text-center py-5" style="color: #FFF9E5;">
    <div style="font-size: 64px;">📭</div>
    <h3 style="font-family: 'Bubblegum Sans', cursive; font-size: 28px; margin-top: 15px;">No chapters found</h3>
    <a href="{{ route('student.courses.index') }}" class="btn btn-primary mt-3">Back to Subjects</a>
</div>
@endif

{{-- ── Treasure Chest Unlock Overlay ── --}}
<div id="chest-unlock-overlay" class="chest-unlock-overlay">
    <div class="chest-wrapper" id="chest-wrapper" onclick="openTreasureChest()">
        <div class="chest-container" id="chest-container">
            <img src="{{ asset('uploads/images/stage/star.png') }}" class="chest-star" alt="Star" fetchpriority="high" loading="eager" decoding="async">
            <img src="{{ asset('uploads/images/stage/lower_chest.png') }}" class="chest-lower" alt="Chest Base" fetchpriority="high" loading="eager" decoding="async">
            <img src="{{ asset('uploads/images/stage/upper_chest.png') }}" class="chest-upper closed-lid" alt="Chest Lid Closed" fetchpriority="high" loading="eager" decoding="async">
            <img src="{{ asset('uploads/images/stage/upper_chest_opened.png') }}" class="chest-upper opened-lid" alt="Chest Lid Opened" fetchpriority="high" loading="eager" decoding="async">
            <img src="{{ asset('uploads/images/stage/chest_button.png') }}" class="chest-btn-img" alt="Tap to Open" fetchpriority="high" loading="eager" decoding="async">
        </div>
        <div class="tap-text">Tap to open</div>
    </div>
    <div class="success-message-wrap">
        <h2 class="yay-text">Yay! You got a star!</h2>
        <button onclick="closeTreasureChest(); openChapterList();" style="background: transparent; border: none; cursor: pointer; padding: 0; transition: transform 0.2s; margin-top: 20px;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
            <img src="{{ asset('uploads/images/buttons/continue buttons.png') }}" alt="Continue" style="height: 60px; object-fit: contain;" fetchpriority="high" loading="eager" decoding="async">
        </button>
    </div>
</div>

@push('scripts')
<script>
    // ── Page Loader Logic ──
    (function() {
        const loader = document.getElementById('page-loader');
        if (!loader) return;

        function hideLoader() {
            loader.classList.add('hidden');
            setTimeout(() => loader.remove(), 500);
        }

        const imgs = Array.from(document.querySelectorAll('img'));
        if (imgs.length === 0) { hideLoader(); return; }

        let loaded = 0;
        const total = imgs.length;

        function onLoad() {
            loaded++;
            if (loaded >= total) hideLoader();
        }

        imgs.forEach(img => {
            if (img.complete && img.naturalWidth > 0) {
                onLoad();
            } else {
                img.addEventListener('load',  onLoad, { once: true });
                img.addEventListener('error', onLoad, { once: true });
            }
        });

        setTimeout(hideLoader, 4000);
    })();

    function openChapterList() {
        document.getElementById('ch-selector-modal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    function closeChapterList() {
        document.getElementById('ch-selector-modal').classList.add('hidden');
        document.body.style.overflow = '';
    }
    function playLockedSound() {
        document.querySelectorAll('.locked-toast').forEach(t => t.remove());
        const toast = document.createElement('div');
        toast.className = 'locked-toast px-4 py-2 rounded-4';
        toast.innerText = '🔒 Complete the previous mission first!';
        document.body.appendChild(toast);
        setTimeout(() => { toast.classList.add('show'); }, 10);
        setTimeout(() => { toast.classList.remove('show'); setTimeout(() => toast.remove(), 300); }, 2200);
    }

    // ── Treasure Chest Logic ──
    function openTreasureChest() {
        document.getElementById('chest-container').classList.add('opening');
        document.getElementById('chest-wrapper').classList.add('opening');
    }
    function closeTreasureChest() {
        document.getElementById('chest-unlock-overlay').classList.remove('show');
    }

    @if($progressPercent == 100)
    window.addEventListener('DOMContentLoaded', () => {
        const chapterId = "{{ $activeChapter->id ?? 'unknown' }}";
        const storageKey = 'chest_opened_chapter_' + chapterId;
        
        if (!localStorage.getItem(storageKey)) {
            setTimeout(() => {
                document.getElementById('chest-unlock-overlay').classList.add('show');
                localStorage.setItem(storageKey, 'true');
            }, 1500); 
        }
    });
    @endif
</script>
@endpush
@endsection
