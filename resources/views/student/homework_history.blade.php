@extends('layouts.student')

@section('title', 'Homework History')
@section('nav_workspace', 'active')

@push('styles')
<style>
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
        margin-bottom: 15px;
        font-family: 'Quicksand', sans-serif;
    }
    
    /* Scrollbar for notice board */
    .notice-scroll-wrapper::-webkit-scrollbar {
        width: 5px;
    }
    .notice-scroll-wrapper::-webkit-scrollbar-track {
        background: transparent;
    }
    .notice-scroll-wrapper::-webkit-scrollbar-thumb {
        background: rgba(0,0,0,0.15);
        border-radius: 10px;
    }
    .notice-scroll-wrapper::-webkit-scrollbar-thumb:hover {
        background: rgba(0,0,0,0.25);
    }
    
    .hw-history-item {
        margin-bottom: 15px;
        padding-bottom: 15px;
        border-bottom: 1px dashed rgba(0,0,0,0.1);
    }
    .hw-history-item:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }
</style>
@endpush

@section('content')
<!-- Background Blobs -->
<div class="blob-bg blob-top-right"></div>
<div class="blob-bg blob-bottom-left"></div>

<div class="container pb-5" style="padding-top: 30px; max-width: 480px; margin: 0 auto; min-height: 100vh;">
    
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('student.workspace') }}" class="me-3" style="display: inline-block; transition: transform 0.1s;">
            <img src="{{ asset('uploads/images/buttons/Previous button.png') }}" alt="Back" style="height: 48px; object-fit: contain;">
        </a>
        <h2 class="section-title mb-0" style="font-size: 24px;">Homework History</h2>
    </div>

    <!-- Assignment Section -->
    @forelse($homeworks as $hw)
        <div class="notice-board-container mt-4 mb-5">
            <img src="{{ asset('uploads/images/workspace/notice board.png') }}" alt="Notice Board" class="notice-board-bg">
            
            <div class="notice-board-content">
                <div class="notice-date">{{ \Carbon\Carbon::parse($hw->for_date)->format('d F Y') }}</div>
                
                <div class="notice-scroll-wrapper" style="max-height: 140px; overflow-y: auto; padding-right: 6px;">
                    <div class="notice-text" style="font-size: 13px; font-weight: 700; color: #1E1E35; line-height: 1.6; margin-top: 5px;">
                        {!! nl2br(e($hw->content)) !!}
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="notice-board-container mt-4">
            <img src="{{ asset('uploads/images/workspace/notice board.png') }}" alt="Notice Board" class="notice-board-bg">
            
            <div class="notice-board-content">
                <div class="notice-date">History</div>
                <div class="notice-text text-center mt-3" style="font-size: 14px; font-weight: 700; color: #64748B;">
                    No homework history found!
                </div>
            </div>
        </div>
    @endforelse
</div>
@endsection
