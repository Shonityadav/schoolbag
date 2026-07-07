@extends('layouts.student')
@section('title', 'Terms & Conditions')

@push('styles')
<style>
html, body {
    background-color: #FFF9E5;
    background-image: none;
    font-family: 'Quicksand', sans-serif;
    color: #1E1E35;
    overflow: hidden;
    height: 100%;
    margin: 0;
    padding: 0;
}

/* Hide layout navigation */
.sidebar, .topbar { display: none !important; }
.main { padding-bottom: 0 !important; overflow: hidden; height: 100vh; display: flex; flex-direction: column; }
.content{ padding: 0px;}
.tc-container {
    height: 100vh;
    width: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
    position: relative;
    z-index: 1;
    padding: 1rem;
    overflow: hidden;
}

.wave-top {
    position: absolute;
    top: -10%;
    left: -10%;
    width: 400px;
    max-width: 50vw;
    z-index: 0;
    pointer-events: none;
}
.wave-bottom {
    position: absolute;
    bottom: -5%;
    right: -5%;
    width: 400px;
    max-width: 50vw;
    z-index: 0;
    transform: rotate(180deg);
    pointer-events: none;
}

.tc-card {
    background: #FFF2D1;
    border: 4px solid #FFEAC2;
    border-radius: 20px;
    width: 100%;
    max-width: 600px;
    height: 85vh;
    max-height: 800px;
    display: flex;
    flex-direction: column;
    padding: 3rem 1rem 1rem 1.5rem;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    position: relative;
    z-index: 2;
}


.tc-content {
    font-size: 14px;
    line-height: 1.6;
    color: #333;
    overflow-y: auto;
    flex: 1;
    padding-right: 15px;
}

.tc-content::-webkit-scrollbar {
    width: 6px;
}
.tc-content::-webkit-scrollbar-track {
    background: #FFEAC2;
    border-radius: 10px;
}
.tc-content::-webkit-scrollbar-thumb {
    background: #E6B952;
    border-radius: 10px;
}

.tc-content p {
    margin-bottom: 12px;
}

.tc-content h3 {
    font-size: 15px;
    font-weight: 900;
    margin-top: 22px;
    margin-bottom: 8px;
    color: #1E1E35;
}

.tc-content ul {
    margin-left: 0;
    padding-left: 20px;
    margin-bottom: 12px;
}

.tc-content li {
    margin-bottom: 4px;
}

/* ── Tablet / iPad responsive ── */
@media (min-width: 769px) {
    .tc-card {
        max-width: 680px;
        height: 80vh;
        max-height: 900px;
        padding: 3.5rem 1.5rem 1.5rem 2rem;
    }
    .tc-content {
        font-size: 16px;
    }
    .tc-content h3 {
        font-size: 18px;
    }
}

</style>
@endpush

@section('content')
<div class="tc-container">
    <img src="{{ asset('uploads/images/banners/shapes.png') }}" class="wave-top" alt="Wave Top" fetchpriority="high" loading="eager" decoding="async">
    <img src="{{ asset('uploads/images/banners/shapes.png') }}" class="wave-bottom" alt="Wave Bottom" fetchpriority="high" loading="eager" decoding="async">

    <div class="tc-card mx-auto">
        <div style="position: absolute; top: -25px; left: 50%; transform: translateX(-50%); width: 85%; max-width: 320px; text-align: center; z-index: 5;">
            <img src="{{ asset('uploads/images/stage1/banner-2.png') }}" alt="Banner" style="width: 100%; height: auto; drop-shadow: 0 4px 6px rgba(0,0,0,0.1);" fetchpriority="high" loading="eager" decoding="async">
            <h2 style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); font-family: 'Quicksand', sans-serif; font-weight: 900; font-size: clamp(18px, 5vw, 24px); color: #1E1E35; margin: 0; white-space: nowrap;">Term & Condition</h2>
        </div>

        <div class="tc-content">
            <p>Welcome to School Bag!</p>
            <p>School Bag is a fun educational learning platform designed for students from Class 1 to Class 5. By using our app and website, you agree to follow these Terms & Conditions.</p>

            <h3>1. Use of the App</h3>
            <p>School Bag is created for educational and learning purposes only. Users should use the app respectfully and responsibly.</p>
            <p>You agree not to:</p>
            <ul>
                <li>misuse the platform</li>
                <li>copy educational content</li>
                <li>attempt to hack or damage the app</li>
                <li>use offensive language or harmful behavior</li>
            </ul>

            <h3>2. User Accounts</h3>
            <p>Some features may require account creation. Users are responsible for maintaining the security of their accounts.<br>
            Parents or guardians may supervise children while using the platform.</p>

            <h3>3. Educational Content</h3>
            <p>All lessons, activities, illustrations, designs, and educational materials inside School Bag belong to the platform and are protected by copyright laws.</p>
            <p>Users may not:</p>
            <ul>
                <li>copy</li>
                <li>resell</li>
                <li>distribute</li>
                <li>reproduce content without permission</li>
            </ul>

            <h3>4. Rewards & Progress</h3>
            <p>The app may include:</p>
            <ul>
                <li>stars</li>
                <li>rewards</li>
                <li>badges</li>
                <li>levels</li>
                <li>XP systems</li>
            </ul>
            <p>These are designed only for learning motivation and have no real-world monetary value.</p>

            <h3>5. Safety & Child-Friendly Environment</h3>
            <p>We aim to provide a safe and positive learning experience for children. Any misuse of the platform may result in restricted access.</p>

            <h3>6. Changes & Updates</h3>
            <p>School Bag may update lessons, features, or design elements at any time to improve the learning experience.</p>

            <h3>7. Limitation of Liability</h3>
            <p>While we try to provide accurate educational content, we cannot guarantee uninterrupted or error-free service at all times.</p>

            <h3>8. Contact Us</h3>
            <p>If you have any questions regarding these Terms & Conditions, please contact us through our official support page.</p>
        </div>
    </div>
</div>
@endsection
