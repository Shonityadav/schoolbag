@extends('layouts.student')
@section('title', 'Feedback')

@push('styles')
<style>
/* Hide standard layout elements */
.sidebar, .topbar { display: none !important; }

.cp-container {
    min-height: 100vh;
    background: transparent;
    position: relative;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    align-items: center;
}

/* Yellow Waves */
.wave-top {
    position: absolute;
    top: -31%;
    left: -80px;
    width: 160%;
    z-index: 3;
    object-fit: cover;
}
.wave-bottom {
    position: absolute;
    bottom: -24%;
    left: -86px;
    width: 155%;
    transform: rotate(90deg);
    z-index: 1;
    object-fit: cover;
}

.cp-content {
    position: relative;
    display: flex;
    flex-direction: column;
    align-items: center;
    width: 100%;
    max-width: 500px;
    padding-top: 20px;
}

.cp-back-btn {
    position: absolute;
    top: 20px;
    left: 20px;
    z-index: 10;
}
.cp-back-btn img {
    height: 48px;
    object-fit: contain;
    transition: transform 0.1s;
}
.cp-back-btn:hover img {
    transform: scale(1.05);
}

.cp-bag-img {
    height: 110px;
    object-fit: contain;
    margin-top: 60px;
    margin-bottom: 16px;
    position: relative;
    z-index: 4;
}

.cp-pill {
    background: #FFDE99;
    color: #1E1E35;
    font-weight: 900;
    font-size: 15px;
    padding: 10px 48px;
    border-radius: 999px;
    margin-bottom: 40px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    position: relative;
    z-index: 6;
}

.cp-card {
    background: #FFF2D1;
    border: 3px solid #FFEAC2;
    border-radius: 20px;
    width: 85%;
    max-width: 400px;
    min-height: 360px;
    padding: 30px 20px 40px;
    text-align: center;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    position: relative;
    z-index: 5;
}

.cp-card-title {
    font-weight: 800;
    font-size: 14px;
    color: #1E1E35;
    margin-bottom: 20px;
    text-align: left;
    padding-left: 10px;
}

.cp-input-group {
    text-align: left;
    margin-bottom: 20px;
    padding: 0 10px;
}
.cp-input-label {
    font-size: 13px;
    font-weight: 800;
    color: #554433;
    margin-bottom: 6px;
    display: block;
}
.cp-textarea {
    width: 100%;
    background: #FFFFFF;
    border: none;
    border-radius: 8px;
    padding: 12px 16px;
    font-size: 14px;
    font-weight: 700;
    color: #1E1E35;
    min-height: 150px;
    resize: none;
}
.cp-textarea:focus {
    outline: 2px solid #FFC145;
}

.cp-save-btn {
    background: #6EE49F;
    color: white;
    font-weight: 900;
    font-size: 16px;
    border: none;
    border-radius: 999px;
    padding: 12px 48px;
    box-shadow: 0 4px 0 #57C082;
    transition: transform 0.1s, box-shadow 0.1s;
    cursor: pointer;
    display: inline-block;
    margin-top: 10px;
}
.cp-save-btn:active {
    transform: translateY(4px);
    box-shadow: 0 0 0 #57C082;
}
</style>
@endpush

@section('content')
<div class="cp-container">
    <img src="{{ asset('uploads/images/banners/shapes.png') }}" class="wave-top" alt="Wave Top" fetchpriority="high" loading="eager" decoding="async">
    <img src="{{ asset('uploads/images/banners/shapes.png') }}" class="wave-bottom" alt="Wave Bottom" fetchpriority="high" loading="eager" decoding="async">

    <div class="cp-content">
        <a href="{{ route('student.profile') }}" class="cp-back-btn">
            <img src="{{ asset('uploads/images/buttons/Previous button.png') }}" alt="Back" fetchpriority="high" loading="eager" decoding="async">
        </a>

        <img src="{{ asset('uploads/images/splash/bag3.png') }}" class="cp-bag-img" alt="Bag" fetchpriority="high" loading="eager" decoding="async">
        
        <div class="cp-pill">Feedback</div>

        <div class="cp-card">
            @if(session('success'))
            <div style="padding: 12px; border-radius: 8px; margin-bottom: 15px; font-weight: bold; font-size: 13px; background-color: #E8F5E9; color: #2E7D32; border: 1px solid #C8E6C9;">
                {{ session('success') }}
            </div>
            @endif

            <div class="cp-card-title" style="font-size: 18px; margin-bottom: 30px;">We value your feedback</div>
            
            <form action="{{ route('student.profile.store_feedback') }}" method="POST">
                @csrf
                <div class="cp-input-group">
                    
                    <textarea name="details" class="cp-textarea" placeholder="Enter your feedback here..." required></textarea>
                </div>

                <button type="submit" class="cp-save-btn">Submit</button>
            </form>
        </div>
    </div> 
</div>
@endsection
