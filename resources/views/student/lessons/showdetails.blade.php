@extends('layouts.student')
@section('title', $lesson->title)

@push('styles')
<style>
/* Hide student layout sidebar and navbars to keep it clean */
.sidebar, .navbar, .top-nav, .logo, .topbar, .mobile-logo {
    display: none !important;
}

/* Remove bottom padding from the main layout container */
.main {
    padding-bottom: 0 !important;
}

.min-vh-100 {
    min-height: 97vh !important;
}

/* Base styling to make the layout fit the screen using background image */
body {
    background-color: #FEF8E6;
    position: relative;
}

body::before {
    content: "";
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-image: url('{{ asset('uploads/images/stage1/bg.png') }}');
    background-size: cover;
    background-position: center;
    background-attachment: fixed;
    filter: blur(6px);
    z-index: -1;
}

/* Container for the reading stage, making it responsive */
.reading-stage-container {
    max-width: 600px;
    margin: 0 auto;
    font-family: 'Quicksand', sans-serif;
}

/* Top bar styles */
.top-bar-custom {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    margin-bottom: 20px;
}

.back-btn-square {
    background: linear-gradient(180deg, #64B5F6, #1E88E5);
    border: 3px solid #FFF;
    border-radius: 14px;
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 0 0 3px #FFD54F, 0 6px 0 3px #F57F17;
    color: white;
}

.timer-pill {
    background: #FFF3E0;
    color: #4E342E;
    font-weight: 900;
    font-size: 20px;
    padding: 8px 20px;
    border-radius: 20px;
    border: 3px solid #FFD54F;
    box-shadow: 0 4px 0 #FFCA28;
}

/* Banner images positioning */
.ribbon-wrapper {
    position: relative;
    z-index: 10;
    text-align: center;
    margin-bottom: -50px; /* Pull the note card up */
}

.purple-banner-img {
    width: 80%;
    max-width: 350px;
    position: relative;
    z-index: 11;
}

.beige-banner-img {
    width: 60%;
    max-width: 250px;
    margin-top: -30px;
    position: relative;
    z-index: 10;
}

.lesson-title-overlay {
    position: absolute;
    top: 0%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-family: 'Bubblegum Sans', cursive;
    color: #8D6E63;
    font-size: 1.1rem;
    z-index: 12;
    width: 80%;
    text-align: center;
    line-height: 1.2;
}

/* The Note container */
.note-container {
    position: relative;
    width: 100%;
}

.note-bg-img {
    width: 100%;
    height: auto;
    display: block;
}

/* Viewport for pagination */
.note-viewport {
    position: absolute;
    top: 60px;
    bottom: 90px;
    left: 40px;
    right: 40px;
    perspective: 2000px; /* 3D effect for flip */
}

/* Content wrapper */
.note-content-overlay {
    position: relative;
    width: 100%;
    height: 100%;
    transform-style: preserve-3d;
}

/* Individual pages */
.page-image-wrapper {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    transform-origin: left center;
    transition: transform 0.6s cubic-bezier(0.645, 0.045, 0.355, 1);
    backface-visibility: hidden;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 10px;
    background-color: transparent;
}

.page-image-wrapper.flipped {
    transform: rotateY(-180deg);
}

/* Buttons positioning */
.nav-buttons-container {
    position: absolute;
    bottom: -20px;
    left: -15px;
    right: -15px;
    display: flex;
    justify-content: space-between;
    z-index: 20;
    pointer-events: none;
}

.nav-btn-wrapper {
    position: relative;
    pointer-events: auto;
    width: 45%;
    max-width: 160px;
    transition: transform 0.1s;
    cursor: pointer;
    text-decoration: none;
}

.nav-btn-wrapper:active {
    transform: translateY(4px);
}

.nav-btn-img {
    width: 100%;
    height: auto;
    display: block;
}

.nav-btn-text {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: white;
    font-family: 'Quicksand', sans-serif;
    font-weight: 900;
    font-size: 1.1rem;
    pointer-events: none;
    white-space: nowrap;
    text-transform: uppercase;
}

.text-prev { padding-left: 15px; }
.text-next { padding-right: 15px; }

.submit-container {
    text-align: center;
    margin-top: 30px;
    padding-bottom: 50px;
}

.submit-btn-img {
    width: 60%;
    max-width: 250px;
    transition: transform 0.1s;
}

.submit-btn-img:active {
    transform: translateY(4px);
}

/* Custom scrollbar for the note content */
.note-content-overlay::-webkit-scrollbar {
    width: 8px;
}
.note-content-overlay::-webkit-scrollbar-track {
    background: transparent;
}
.note-content-overlay::-webkit-scrollbar-thumb {
    background: #D7CCC8;
    border-radius: 4px;
}

/* ===== MATCH THE FOLLOWING ===== */
.match-game-wrapper {
    width: 100%;
    height: 100%;
    display: flex;
    flex-direction: column;
    padding: 0;
    box-sizing: border-box;
    background: transparent;
    position: relative;
}
.match-outer-card {
    background: linear-gradient(180deg, #F4813A, #E06828);
    border-radius: 18px;
    padding: 16px 12px 18px;
    display: flex;
    flex-direction: column;
    gap: 14px;
    position: relative;
    width: 100%;
    box-sizing: border-box;
    box-shadow: 0 6px 0 #A84A10;
    flex: 1;
    overflow: hidden;
}
.match-heading-bar h2 {
    font-family: 'Quicksand', sans-serif;
    font-weight: 900;
    font-size: 1.2rem;
    color: #FFF;
    margin: 0;
    text-align: center;
    text-shadow: 0 2px 6px rgba(0,0,0,0.25);
    letter-spacing: 0.3px;
}
.match-scroll-wrapper {
    flex: 1;
    overflow-y: auto;
    overflow-x: hidden;
    margin: 0 -12px;
    padding: 0 12px;
}
.match-scroll-wrapper::-webkit-scrollbar {
    width: 6px;
}
.match-scroll-wrapper::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.2);
    border-radius: 6px;
}
.match-scroll-wrapper::-webkit-scrollbar-thumb {
    background: #5D1A1A;
    border-radius: 6px;
}
.match-inner-columns {
    display: flex;
    gap: 0;
    position: relative;
}
.match-col {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 14px;
    background: #FEF4E0;
    border-radius: 14px;
    padding: 14px 10px;
}
.match-col.left-col {
    border-radius: 14px 0 0 14px;
    border-right: 2px solid #E8C07A;
}
.match-col.right-col {
    border-radius: 0 14px 14px 0;
    border-left: 2px solid #E8C07A;
}
.match-item {
    border: none;
    border-radius: 12px;
    padding: 12px 8px;
    font-family: 'Quicksand', sans-serif;
    font-weight: 800;
    font-size: 0.95rem;
    color: #5D1A1A;
    text-align: center;
    cursor: pointer;
    user-select: none;
    box-shadow: 0 4px 0 #D97070;
    z-index: 2;
    display: flex;
    align-items: center;
    justify-content: center;
    flex: 1 1 0px;
    min-height: 65px;
    height: auto;
    transition: background 0.15s, transform 0.1s;
}
.match-item:active { transform: scale(0.96); }
.match-item.selected {
    background: #FFD580;
    box-shadow: 0 4px 0 #C89000;
    transform: scale(1.04);
}
.match-item.matched {
    background: #B8F5C8;
    box-shadow: 0 4px 0 #3AAA5B;
    pointer-events: none;
}
.match-item.wrong {
    background: #FFB3B3;
    box-shadow: 0 4px 0 #E04444;
    animation: shake 0.35s ease;
}
@keyframes shake {
    0%,100% { transform: translateX(0); }
    25% { transform: translateX(-6px); }
    75% { transform: translateX(6px); }
}
#match-svg {
    position: absolute;
    top: 0; left: 0;
    width: 100%; height: 100%;
    pointer-events: none;
    overflow: visible;
    z-index: 5;
}

.mcq-scroll::-webkit-scrollbar {
    width: 6px;
}
.mcq-scroll::-webkit-scrollbar-track {
    background: transparent;
}
.mcq-scroll::-webkit-scrollbar-thumb {
    background: #68A984;
    border-radius: 3px;
}
</style>
@endpush

@section('content')
<!-- Page Loader -->
<div id="page-loader" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: #FEF8E6; z-index: 9999; display: flex; justify-content: center; align-items: center; flex-direction: column; transition: opacity 0.4s ease;">
    <div class="spinner-border text-warning" style="width: 4rem; height: 4rem;" role="status"></div>
    <h3 class="mt-4" style="font-family: 'Bubblegum Sans', cursive; color: #F57C00; font-size: 2rem; letter-spacing: 2px;">Loading...</h3>
</div>

<div class="container-fluid min-vh-100 p-0">
    <div class="reading-stage-container">
        
        <!-- TOP BAR -->
        <div class="top-bar-custom">
            <button onclick="showExitPopup()" class="back-btn-square" id="back-btn" style="border:none; cursor:pointer;">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 12H5M12 19l-7-7 7-7"/>
                </svg>
            </button>
            <div class="timer-pill" id="lesson-timer">00:00</div>
        </div>

        <!-- RIBBONS -->
        <div class="ribbon-wrapper">
            @php
                $hueRotate = '0deg';
                $stageName = '';
                if ($lesson->order == 0) { $hueRotate = '-46deg'; $stageName = '1. Reading Mission'; }
                elseif ($lesson->order == 1) { $hueRotate = '-135deg'; $stageName = '2. Hard Words'; }
                elseif ($lesson->order == 2) { $hueRotate = '130deg'; $stageName = '3. Activity Mission'; }
                elseif ($lesson->order == 3) { $hueRotate = '0deg'; $stageName = '4. Exercise Mission'; }
                elseif ($lesson->order >= 4) { $hueRotate = '144deg'; $stageName = '5. Challenge Mission'; }
            @endphp
            <div style="position: relative; display: inline-block; width: 100%;">
                <img src="{{ asset('uploads/images/stage1/banner.png') }}" class="purple-banner-img" style="filter: hue-rotate({{ $hueRotate }});" alt="Purple Banner">
                <div style="position: absolute; top: 40%; left: 50%; transform: translate(-50%, -50%); font-family: 'Bubblegum Sans', cursive; color: #FFF; font-size: 1.5rem; font-weight: bold; z-index: 12; width: 70%; text-align: center; text-shadow: 0 2px 4px rgba(0,0,0,0.3);">
                    {{ $stageName }}
                </div>
            </div>
            <div style="position: relative; display: inline-block; width: 100%;">
                <img src="{{ asset('uploads/images/stage1/banner-2.png') }}" class="beige-banner-img" alt="Beige Banner">
                <div class="lesson-title-overlay" style="top: 25%;">{{ $lesson->chapter->title }}</div>
            </div>
        </div>

        <!-- NOTE CARD -->
        <div class="note-container">
            <img src="{{ asset('uploads/images/stage1/note.png') }}" class="note-bg-img" alt="Note Card">
            
            <div class="note-viewport" id="note-viewport">
                <div class="note-content-overlay" id="note-content">
                    @if(isset($mcqs) && count($mcqs) > 0)
                        <div class="page-image-wrapper" id="mcq-container" style="z-index: 10; padding: 10px 20px; box-sizing: border-box; display: flex; flex-direction: column; align-items: stretch; justify-content: flex-start; overflow-y: hidden; background-color: #FFFDF5;" data-mcqs="{{ json_encode($mcqs) }}">
                            <div style="font-family: 'Quicksand', sans-serif; font-weight: 800; font-size: 0.85rem; text-align: left; margin-bottom: 10px; color: #000; line-height: 1.2;">
                                Tick(✓) the correct option to answer the following questions:
                            </div>
                            <div class="mcq-scroll" style="background-color: #81D2A4; border-radius: 12px; padding: 15px 15px; margin-bottom: 15px; display: flex; align-items: flex-start; justify-content: center; min-height: 80px; max-height: 110px; overflow-y: auto; box-shadow: inset 0 -3px 0 rgba(0,0,0,0.1);">
                                <h3 id="mcq-question-text" style="font-family: 'Quicksand', sans-serif; color: #FFF; font-weight: 800; font-size: 1.25rem; text-align: center; margin: auto; line-height: 1.2; text-shadow: 0px 2px 3px rgba(0,0,0,0.2); width: 100%;">
                                    {{ $mcqs[0]['question'] }}
                                </h3>
                            </div>
                            <div class="options-container" id="mcq-options-container" style="display: flex; flex-direction: column; gap: 8px;">
                                @foreach($mcqs[0]['options'] as $optIndex => $option)
                                    <div class="mcq-option" onclick="selectOption(this, {{ $optIndex }})" style="background-color: #B2F0D1; border-radius: 10px; padding: 10px 15px; font-family: 'Quicksand', sans-serif; font-weight: 800; font-size: 0.95rem; color: #000; cursor: pointer; transition: transform 0.1s, background-color 0.2s; box-shadow: inset 0 -2px 0 rgba(0,0,0,0.05);">
                                        {{ $option }}
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @elseif(isset($matchPairs) && count($matchPairs) > 0)
                        @php
                            $shuffledLeft  = collect($matchPairs)->pluck('left')->shuffle()->values();
                            $shuffledRight = collect($matchPairs)->pluck('right')->shuffle()->values();
                        @endphp
                        <div class="match-game-wrapper" id="match-game-wrapper">
                            <div class="match-outer-card">
                                <!-- Heading -->
                                <div class="match-heading-bar">
                                    <h2>Match the Following</h2>
                                </div>
                                <!-- Scroll Wrapper -->
                                <div class="match-scroll-wrapper">
                                    <!-- Two independent columns -->
                                    <div class="match-inner-columns" id="match-columns" data-pairs="{{ json_encode($matchPairs) }}">
                                        <!-- SVG overlay for lines -->
                                        <svg id="match-svg"></svg>

                                        <!-- LEFT column (all left items stacked) -->
                                        <div class="match-col left-col" id="left-col">
                                            @foreach($shuffledLeft as $i => $lWord)
                                                <div class="match-item" id="left-{{ $i }}" data-side="left" data-word="{{ $lWord }}">
                                                    <div style="margin: auto; width: 100%;">{{ $lWord }}</div>
                                                </div>
                                            @endforeach
                                        </div>

                                        <!-- RIGHT column (all right items stacked independently) -->
                                        <div class="match-col right-col" id="right-col">
                                            @foreach($shuffledRight as $j => $rWord)
                                                <div class="match-item" id="right-{{ $j }}" data-side="right" data-word="{{ $rWord }}">
                                                    <div style="margin: auto; width: 100%;">{{ $rWord }}</div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @elseif(isset($ebookPages) && count($ebookPages) > 0)
                        @foreach($ebookPages as $index => $page)
                            <div class="page-image-wrapper" id="page-{{ $index }}" style="z-index: {{ count($ebookPages) - $index }}; background-color: #FFFDF5;">
                                <img src="{{ asset($page->url . '/' . $page->title) }}" style="max-width: 100%; max-height: 100%; object-fit: contain; border-radius: 8px;" alt="Page {{ $loop->iteration }}">
                            </div>
                        @endforeach
                    @else
                        <!-- Fallback if no images -->
                        <div class="page-image-wrapper" style="z-index: 1;">
                            <h3 style="color: #8D7E6A; font-family: 'Quicksand', sans-serif;">No content available.</h3>
                        </div>
                    @endif
                </div>
            </div>
            <!-- PREVIOUS & NEXT BUTTONS (Inside Note Container at Corners) -->
            <div class="nav-buttons-container">
                <a href="javascript:void(0)" onclick="prevPage()" id="prev-btn" class="nav-btn-wrapper">
                    <img src="{{ asset('uploads/images/stage1/previous.png') }}" class="nav-btn-img" alt="Previous">
                    <span class="nav-btn-text text-prev" style="text-transform: uppercase;">PREVIOUS</span>
                </a>
                
                <a href="javascript:void(0)" onclick="nextPage()" id="next-btn" class="nav-btn-wrapper">
                    <img src="{{ asset('uploads/images/stage1/next.png') }}" class="nav-btn-img" alt="Next Page">
                    <span class="nav-btn-text text-next" style="text-transform: uppercase; margin-right: 15px;">NEXT PAGE</span>
                </a>
            </div>
        </div>

        <!-- SUBMIT + RETRY BUTTONS (last page only) -->
        <div class="submit-container" id="submit-container" style="display: none; position: relative; margin-top: 20px;">
            <div style="display: flex; align-items: center; justify-content: center; gap: 24px;">
                @if(isset($matchPairs) && count($matchPairs) > 0)
                    {{-- Retry button for match game --}}
                    <a href="javascript:void(0)" onclick="retryMatchGame()" id="retry-btn" class="action-btn" style="text-decoration: none; position: relative; display: inline-block; width: 140px; transition: transform 0.1s;">
                        <img src="{{ asset('uploads/images/stage1/previous.png') }}" alt="Retry" style="width: 100%; height: auto; display: block;">
                        <span class="nav-btn-text text-prev" style="font-size: 1.1rem; text-transform: uppercase;">RETRY</span>
                    </a>
                @endif

                <form id="complete-form" method="POST" action="{{ route('student.lessons.complete', $lesson->id) }}" class="m-0" style="display: inline-block; width: 140px;">
                    @csrf
                    <input type="hidden" name="answers" id="answers-input" value="{}">
                    <input type="hidden" name="time_taken" id="time_taken_input" value="0">
                    @if(isset($course))
                        <input type="hidden" name="course_id" value="{{ $course->id }}">
                    @endif
                    @if(isset($stage))
                        <input type="hidden" name="stage" value="{{ $stage }}">
                    @endif
                    @if(isset($chapter_id))
                        <input type="hidden" name="chapter_id" value="{{ $chapter_id }}">
                    @endif
                    
                    <a href="javascript:void(0)" onclick="submitLesson()" class="action-btn" style="text-decoration: none; position: relative; display: inline-block; width: 100%; transition: transform 0.1s;">
                        <img src="{{ asset('uploads/images/stage1/submit.png') }}" alt="Submit" style="width: 100%; height: auto; display: block;">
                        <span class="nav-btn-text" style="top: 45%; font-size: 1.25rem;">SUBMIT</span>
                    </a>
                </form>
            </div>
        </div>
        
        {{-- Level Completed Modal --}}
        <div id="level-complete-modal" class="d-flex justify-content-center align-items-center" style="position: fixed; inset: 0; background: rgba(15,23,42,0.85); backdrop-filter: blur(5px); z-index: 9999; opacity: 0; pointer-events: none; transition: opacity 0.4s ease;">
            <div class="modal-content-wrapper position-relative" style="width: 90%; max-width: 420px; text-align: center; transform: scale(0.8); transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);">
                
                <!-- Main Board (banner 1) -->
                <img src="{{ asset('uploads/images/stagecomplete/banner 1.png') }}" style="width: 100%; height: auto; position: relative; z-index: 1;" alt="Board">
                
                <div style="position: absolute; top: 0; left: 0; width: 100%; height: 110%; z-index: 2; display: flex; flex-direction: column; align-items: center;">
                    
                    <!-- Top Congrats Banner (banner 3) -->
                    <div style="position: relative; margin-top: 17%; width: 85%;">
                        <img src="{{ asset('uploads/images/stagecomplete/banner 3.png') }}" style="width: 100%; height: auto; padding-top: 15%;" alt="Congrats">
                        <div style="position: absolute; top: 60%; left: 55%; transform: translate(-50%, -50%); width: 70%; text-align: center;">
                            <h2 style="font-family: 'Quicksand', sans-serif; font-weight: 900; font-size: 1.3rem; color: #FF5A5F; margin: 0; letter-spacing: 0.5px;">Congratulations</h2>
                            <p style="font-family: 'Quicksand', sans-serif; font-weight: 700; font-size: 0.8rem; color: #8D7E6A; margin: 0; height: 10px;">You Completed this level</p>
                        </div>
                    </div>

                    <!-- Inner Stars Board (banner 2) -->
                    <div style="position: relative; margin-top: 4%; width: 72%;">
                        <img src="{{ asset('uploads/images/stagecomplete/banner 2.png') }}" style="width: 100%; height: auto;" alt="Stars Board">
                        
                        <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; padding-top: 8px;">
                            <h3 style="font-family: 'Quicksand', sans-serif; color: #FF6B6B; font-weight: 800; font-size: 1.2rem; margin-bottom: 15px;">You Got {{ $lesson->xp_reward ?? 3 }} XP</h3>
                            
                            <div id="modal-stars-container" class="d-flex justify-content-center gap-2">
                                <!-- Three Stars -->
                                <img src="{{ asset('uploads/images/stage/star.png') }}" style="width: 50px; height: 50px; object-fit: contain; filter: grayscale(100%) opacity(0.4) drop-shadow(0 4px 6px rgba(0,0,0,0.15));" alt="Star">
                                <img src="{{ asset('uploads/images/stage/star.png') }}" style="width: 60px; height: 60px; object-fit: contain; filter: grayscale(100%) opacity(0.4) drop-shadow(0 4px 6px rgba(0,0,0,0.15)); transform: translateY(-10px);" alt="Star">
                                <img src="{{ asset('uploads/images/stage/star.png') }}" style="width: 50px; height: 50px; object-fit: contain; filter: grayscale(100%) opacity(0.4) drop-shadow(0 4px 6px rgba(0,0,0,0.15));" alt="Star">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Continue Button -->
                    <div style="position: absolute; bottom: -8%; left: 50%; transform: translateX(-50%); width: 70%; cursor: pointer; z-index: 10;" onclick="finalSubmit()">
                        <img src="{{ asset('uploads/images/stagecomplete/continue buttons.png') }}" style="width: 100%; filter: drop-shadow(0 6px 12px rgba(0,0,0,0.25)); transition: transform 0.1s;" onmousedown="this.style.transform='scale(0.95)'" onmouseup="this.style.transform='scale(1)'" alt="Continue">
                    </div>

                </div>
            </div>
        </div>
        
    </div>
</div>
@push('scripts')
<script>
// Hide loader when all images and assets are fully loaded
window.addEventListener('load', function() {
    const loader = document.getElementById('page-loader');
    if (loader) {
        loader.style.opacity = '0';
        setTimeout(() => {
            loader.style.display = 'none';
        }, 400); // match transition duration
    }
});

document.addEventListener('DOMContentLoaded', function() {
    const viewport = document.getElementById('note-viewport');
    const content = document.getElementById('note-content');
    const prevBtn = document.getElementById('prev-btn');
    const nextBtn = document.getElementById('next-btn');
    const submitContainer = document.getElementById('submit-container');
    
    let currentPage = 0;
    let totalPages = 1;
    let colWidth = 0;
    let isSubmitting = false;
    
    const storageKey = 'lesson_answers_' + {{ $lesson->id }} + '_stage_' + {{ $stage ?? 0 }};
    
    // Check if this was a page reload
    let isReload = false;
    if (window.performance) {
        const navEntries = performance.getEntriesByType("navigation");
        if (navEntries.length > 0 && navEntries[0].type === "reload") {
            isReload = true;
        } else if (performance.navigation && performance.navigation.type === 1) {
            isReload = true;
        }
    }

    // If it's a fresh navigation (not a reload), or if they already completed it, start fresh
    const isAlreadyCompleted = {{ $isCompleted ? 'true' : 'false' }};
    if (!isReload || isAlreadyCompleted) {
        localStorage.removeItem(storageKey);
    }

    let userAnswers = JSON.parse(localStorage.getItem(storageKey) || '{}');

    function initPagination() {
        colWidth = viewport.offsetWidth;
        
        document.fonts.ready.then(() => {
            setTimeout(() => {
                totalPages = {{ (isset($mcqs) && count($mcqs) > 0) ? count($mcqs) : (isset($ebookPages) && count($ebookPages) > 0 ? count($ebookPages) : 1) }};
                updateButtons();
            }, 100);
        });
    }

    function updateButtons() {
        // Hide previous button if on the first page
        if (currentPage === 0) {
            prevBtn.style.visibility = 'hidden';
        } else {
            prevBtn.style.visibility = 'visible';
        }
        
        // Hide next button if on the last page
        if (currentPage >= totalPages - 1) {
            nextBtn.style.visibility = 'hidden';
            submitContainer.style.display = 'block';
        } else {
            nextBtn.style.visibility = 'visible';
            submitContainer.style.display = 'none';
        }
    }

    const mcqContainer = document.getElementById('mcq-container');
    let mcqData = null;
    if (mcqContainer) {
        mcqData = JSON.parse(mcqContainer.getAttribute('data-mcqs'));
    }

    function renderMCQ(index) {
        if (!mcqData || !mcqData[index]) return;
        
        const qText = document.getElementById('mcq-question-text');
        const optsContainer = document.getElementById('mcq-options-container');
        
        qText.innerText = mcqData[index].question;
        
        optsContainer.innerHTML = '';
        mcqData[index].options.forEach((opt, optIndex) => {
            const isSelected = userAnswers[index] === optIndex;
            const bgClass = isSelected ? '#79D1A3' : '#B2F0D1';
            const colorClass = isSelected ? '#FFF' : '#000';
            const textContent = isSelected ? '✓ ' + opt : opt;
            
            optsContainer.innerHTML += `
                <div class="mcq-option" onclick="selectOption(this, ${optIndex})" style="background-color: ${bgClass}; border-radius: 10px; padding: 10px 15px; font-family: 'Quicksand', sans-serif; font-weight: 800; font-size: 0.95rem; color: ${colorClass}; cursor: pointer; transition: transform 0.1s, background-color 0.2s; box-shadow: inset 0 -2px 0 rgba(0,0,0,0.05);">
                    ${textContent}
                </div>
            `;
        });
    }

    window.nextPage = function() {
        if (currentPage < totalPages - 1) {
            if (mcqData) {
                currentPage++;
                renderMCQ(currentPage);
            } else {
                const currentPgElement = document.getElementById('page-' + currentPage);
                if (currentPgElement) {
                    currentPgElement.classList.add('flipped');
                }
                currentPage++;
            }
            updateButtons();
        }
    };

    window.prevPage = function() {
        if (currentPage > 0) {
            currentPage--;
            if (mcqData) {
                renderMCQ(currentPage);
            } else {
                const prevPgElement = document.getElementById('page-' + currentPage);
                if (prevPgElement) {
                    prevPgElement.classList.remove('flipped');
                }
            }
            updateButtons();
        }
    };
    
    window.finalSubmit = function() {
        isSubmitting = true;
        if (typeof timerInterval !== 'undefined') {
            clearInterval(timerInterval);
        }
        localStorage.removeItem(storageKey);
        sessionStorage.removeItem(storageKey + '_timer');
        document.getElementById('complete-form').submit();
    };

    window.submitLesson = function() {
        document.getElementById('answers-input').value = JSON.stringify(userAnswers);
        document.getElementById('time_taken_input').value = totalSeconds;
        
        let score = 0;
        const matchColumns = document.getElementById('match-columns');
        if (typeof mcqData !== 'undefined' && mcqData && mcqData.length > 0) {
            mcqData.forEach((mcq, idx) => {
                if (userAnswers[idx] == mcq.correct) score++;
            });
        } else if (matchColumns) {
            const pairsData = JSON.parse(matchColumns.getAttribute('data-pairs'));
            let correctMatches = 0;
            if (userAnswers['match']) {
                pairsData.forEach(p => {
                    if (userAnswers['match'][p.left] === p.right) correctMatches++;
                });
            }
            score = pairsData.length > 0 ? Math.round((correctMatches / pairsData.length) * 10) : 10;
        } else {
            // For stages without MCQs or Matching like Reading Mission, automatically award full score (10 points = 3 stars)
            score = 10;
        }
        
        // Calculate stars
        let earnedStars = 0;
        if (score === 10) earnedStars = 3;
        else if (score >= 8) earnedStars = 2;
        else if (score >= 4) earnedStars = 1;

        // Update modal stars
        const starsContainer = document.getElementById('modal-stars-container');
        if (starsContainer) {
            const starImgs = starsContainer.querySelectorAll('img');
            starImgs.forEach((img, idx) => {
                if (idx < earnedStars) {
                    if (img.style.transform.includes('translateY')) {
                        img.style.filter = 'drop-shadow(0 4px 6px rgba(0,0,0,0.15))';
                    } else {
                        img.style.filter = 'drop-shadow(0 4px 6px rgba(0,0,0,0.15))';
                    }
                } else {
                    img.style.filter = 'grayscale(100%) opacity(0.4) drop-shadow(0 4px 6px rgba(0,0,0,0.15))';
                }
            });
        }

        
        const modal = document.getElementById('level-complete-modal');
        if(modal) {
            modal.style.opacity = '1';
            modal.style.pointerEvents = 'auto';
            modal.querySelector('.modal-content-wrapper').style.transform = 'scale(1)';
        } else {
            finalSubmit();
        }
    };

    // Initialize layout
    initPagination();

    // Re-calculate on resize
    window.addEventListener('resize', () => {
        currentPage = 0;
        content.style.transform = `translateX(0px)`;
        initPagination();
    });

    // Timer Logic
    const timerElement = document.getElementById('lesson-timer');
    const timerStorageKey = storageKey + '_timer';
    
    if (!isReload) {
        sessionStorage.removeItem(timerStorageKey);
    }

    let totalSeconds = sessionStorage.getItem(timerStorageKey) ? parseInt(sessionStorage.getItem(timerStorageKey)) : 0;
    
    // Initial display
    let m = Math.floor(totalSeconds / 60);
    let s = totalSeconds % 60;
    timerElement.textContent = String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
    
    let timerInterval = setInterval(() => {
        if (isSubmitting) return;
        
        totalSeconds++;
        const minutes = Math.floor(totalSeconds / 60);
        const seconds = totalSeconds % 60;
        timerElement.textContent = 
            String(minutes).padStart(2, '0') + ':' + 
            String(seconds).padStart(2, '0');
            
        // Save to sessionStorage every 5 seconds to reduce write overhead
        if (totalSeconds % 5 === 0) {
            sessionStorage.setItem(timerStorageKey, totalSeconds);
            // Clean up any lingering timer in localStorage to prevent confusion
            if (userAnswers['timer']) {
                delete userAnswers['timer'];
                localStorage.setItem(storageKey, JSON.stringify(userAnswers));
            }
        }
    }, 1000);
    window.selectOption = function(element, optIndex) {
        // Reset all options in this question
        const container = element.closest('.options-container');
        const allOptions = container.querySelectorAll('.mcq-option');
        allOptions.forEach(opt => {
            opt.style.backgroundColor = '#B2F0D1'; // reset color
            opt.style.color = '#000';
            opt.innerHTML = opt.textContent.replace('✓ ', ''); // remove checkmark
        });
        
        // Mark the selected one
        element.style.backgroundColor = '#79D1A3';
        element.style.color = '#FFF';
        element.innerHTML = '✓ ' + element.innerHTML.trim();
        
        // Save to localStorage
        userAnswers[currentPage] = optIndex;
        localStorage.setItem(storageKey, JSON.stringify(userAnswers));
    };

    // ===== MATCH THE FOLLOWING GAME =====
    const matchColumns = document.getElementById('match-columns');
    if (matchColumns) {
        const pairsData = JSON.parse(matchColumns.getAttribute('data-pairs'));
        // Build answer key: left word -> correct right word
        const answerKey = {};
        pairsData.forEach(p => { answerKey[p.left] = p.right; });

        const svg = document.getElementById('match-svg');
        let selectedLeft = null;
        let drawnPaths = {};  // leftWord -> svgPath element
        let matchAnswers = {}; // leftWord -> rightWord (final, locked)
        let usedRight = new Set(); // right words already locked

        // Handle maintaining the same order on reload
        if (!userAnswers['matchOrder']) {
            userAnswers['matchOrder'] = {
                left: Array.from(document.querySelectorAll('#left-col .match-item')).map(el => el.dataset.word),
                right: Array.from(document.querySelectorAll('#right-col .match-item')).map(el => el.dataset.word)
            };
            localStorage.setItem(storageKey, JSON.stringify(userAnswers));
        } else {
            // Reorder the DOM to match userAnswers['matchOrder']
            const leftCol = document.getElementById('left-col');
            const rightCol = document.getElementById('right-col');
            
            userAnswers['matchOrder'].left.forEach(word => {
                const el = document.querySelector(`.match-item[data-side="left"][data-word="${word}"]`);
                if (el) leftCol.appendChild(el);
            });
            
            userAnswers['matchOrder'].right.forEach(word => {
                const el = document.querySelector(`.match-item[data-side="right"][data-word="${word}"]`);
                if (el) rightCol.appendChild(el);
            });
        }

        function getCenter(el) {
            const svgRect = matchColumns.getBoundingClientRect();
            const rect = el.getBoundingClientRect();
            return {
                x: rect.left - svgRect.left + rect.width / 2,
                y: rect.top  - svgRect.top  + rect.height / 2,
            };
        }

        function drawPath(x1, y1, x2, y2, color) {
            const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
            const cx = (x1 + x2) / 2;
            path.setAttribute('d', `M ${x1} ${y1} C ${cx} ${y1}, ${cx} ${y2}, ${x2} ${y2}`);
            path.setAttribute('stroke', color);
            path.setAttribute('stroke-width', '3.5');
            path.setAttribute('fill', 'none');
            path.setAttribute('stroke-linecap', 'round');
            path.setAttribute('opacity', '0.9');
            svg.appendChild(path);
            return path;
        }

        // Restore previous matches
        if (userAnswers['match']) {
            matchAnswers = userAnswers['match'];
            for (const lw in matchAnswers) {
                const rw = matchAnswers[lw];
                usedRight.add(rw);
                const isCorrect = answerKey[lw] === rw;
                const color     = isCorrect ? '#3AAA5B' : '#E04444';
                
                // Delay drawing slightly so layout can calculate centers
                setTimeout(() => {
                    const lItem = document.querySelector(`.match-item[data-side="left"][data-word="${lw}"]`);
                    const rItem = document.querySelector(`.match-item[data-side="right"][data-word="${rw}"]`);
                    if (lItem && rItem) {
                        const c1 = getCenter(lItem);
                        const c2 = getCenter(rItem);
                        drawnPaths[lw] = drawPath(c1.x, c1.y, c2.x, c2.y, color);
                        lItem.classList.add(isCorrect ? 'matched' : 'wrong');
                        rItem.classList.add(isCorrect ? 'matched' : 'wrong');
                    }
                }, 100);
            }
        }


        // --- Handle clicks (left items) ---
        document.querySelectorAll('.match-item[data-side="left"]').forEach(el => {
            el.addEventListener('click', function () {
                // If already locked (correct or wrong), ignore
                if (this.classList.contains('matched') || this.classList.contains('wrong')) return;

                // Deselect previously selected
                document.querySelectorAll('.match-item[data-side="left"].selected')
                        .forEach(s => s.classList.remove('selected'));

                this.classList.add('selected');
                selectedLeft = this;
            });
        });

        // --- Handle clicks (right items) ---
        document.querySelectorAll('.match-item[data-side="right"]').forEach(el => {
            el.addEventListener('click', function () {
                if (!selectedLeft) return;
                // Block if right item is already locked
                if (this.classList.contains('matched') || this.classList.contains('wrong')) return;
                // Block if right word is already used
                if (usedRight.has(this.dataset.word)) return;

                const leftWord  = selectedLeft.dataset.word;
                const rightWord = this.dataset.word;
                const isCorrect = answerKey[leftWord] === rightWord;
                const color     = isCorrect ? '#3AAA5B' : '#E04444';

                // Draw bezier line (permanent)
                const c1 = getCenter(selectedLeft);
                const c2 = getCenter(this);
                drawnPaths[leftWord] = drawPath(c1.x, c1.y, c2.x, c2.y, color);
                matchAnswers[leftWord] = rightWord;
                usedRight.add(rightWord);

                // Lock BOTH items permanently (correct = green, wrong = red)
                selectedLeft.classList.remove('selected');
                if (isCorrect) {
                    selectedLeft.classList.add('matched');
                    this.classList.add('matched');
                } else {
                    selectedLeft.classList.add('wrong');
                    this.classList.add('wrong');
                }

                selectedLeft = null;

                // Save to userAnswers immediately
                userAnswers['match'] = matchAnswers;
                localStorage.setItem(storageKey, JSON.stringify(userAnswers));
            });
        });

        // --- Retry: reset all match game state ---
        window.retryMatchGame = function() {
            // Remove from userAnswers
            delete userAnswers['match'];
            delete userAnswers['matchOrder'];
            localStorage.setItem(storageKey, JSON.stringify(userAnswers));
            
            // Reload page to get a fresh shuffle from server
            window.location.reload();
        };
    }
    // ===== END MATCH GAME =====

});
</script>

{{-- ===== EXIT CONFIRMATION POPUP ===== --}}
<style>
.exit-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.55);
    backdrop-filter: blur(6px);
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.25s ease;
}
.exit-overlay.show {
    opacity: 1;
    pointer-events: all;
}
.exit-popup {
    position: relative;
    width: 300px;
    max-width: 90vw;
    display: flex;
    flex-direction: column;
    align-items: center;
    transform: scale(0.7);
    transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.4);
}
.exit-overlay.show .exit-popup {
    transform: scale(1);
}
.exit-frame-img {
    width: 100%;
    display: block;
    pointer-events: none;
    user-select: none;
}
.exit-popup-inner {
    position: absolute;
    top: 12%;
    left: 10%;
    right: 10%;
    bottom: 16%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 18px;
}
.exit-bag-img {
    width: 90px;
    height: auto;
    animation: exit-bounce 1.2s infinite ease-in-out;
}
@keyframes exit-bounce {
    0%,100% { transform: translateY(0); }
    50% { transform: translateY(-8px); }
}
.exit-question {
    font-family: 'Quicksand', sans-serif;
    font-weight: 900;
    font-size: 1.05rem;
    color: #5D1A1A;
    text-align: center;
    background: #fff8e8;
    border-radius: 18px;
    padding: 8px 18px;
    box-shadow: 0 3px 0 #e0c070;
    border: 2px solid #f0d88a;
    line-height: 1.35;
}
.exit-btns {
    display: flex;
    gap: 14px;
    justify-content: center;
}
.exit-btn {
    font-family: 'Quicksand', sans-serif;
    font-weight: 900;
    font-size: 1.1rem;
    border: none;
    border-radius: 30px;
    padding: 10px 34px;
    cursor: pointer;
    box-shadow: 0 5px 0 rgba(0,0,0,0.18);
    transition: transform 0.1s, box-shadow 0.1s;
    letter-spacing: 0.5px;
}
.exit-btn:active {
    transform: translateY(3px);
    box-shadow: 0 2px 0 rgba(0,0,0,0.18);
}
.exit-btn-no {
    background: linear-gradient(180deg, #6fcf6f, #3aaa3a);
    color: #fff;
    text-shadow: 0 1px 3px rgba(0,0,0,0.25);
}
.exit-btn-yes {
    background: linear-gradient(180deg, #ff6b6b, #e03030);
    color: #fff;
    text-shadow: 0 1px 3px rgba(0,0,0,0.25);
}
</style>

<div class="exit-overlay" id="exit-overlay">
    <div class="exit-popup">
        <img src="{{ asset('uploads/images/stagecomplete/banner 1.png') }}" class="exit-frame-img" alt="">
        <div class="exit-popup-inner">
            <img src="{{ asset('uploads/images/splash/bag.png') }}" class="exit-bag-img" alt="">
            <div class="exit-question">Do you really<br>want to exit ?</div>
            <div class="exit-btns">
                <button class="exit-btn exit-btn-no" onclick="hideExitPopup()">No</button>
                <button class="exit-btn exit-btn-yes" id="exit-yes-btn">Yes</button>
            </div>
        </div>
    </div>
</div>

<script>
    const exitBackUrl = "{{ route('student.courses.show', $lesson->chapter->course_id) }}?chapter_id={{ $lesson->chapter_id }}";
    function showExitPopup() {
        document.getElementById('exit-overlay').classList.add('show');
    }
    function hideExitPopup() {
        document.getElementById('exit-overlay').classList.remove('show');
    }
    document.getElementById('exit-yes-btn').addEventListener('click', function() {
        window.location.href = exitBackUrl;
    });
    // Close on backdrop click
    document.getElementById('exit-overlay').addEventListener('click', function(e) {
        if (e.target === this) hideExitPopup();
    });
</script>
@endpush
@endsection
