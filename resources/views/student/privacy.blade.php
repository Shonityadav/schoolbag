<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Policy - School Bag</title>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <style>
html, body {
    background-color: #FFF9E5;
    font-family: 'Quicksand', sans-serif;
    color: #1E1E35;
    margin: 0;
    padding: 0;
}
.tc-container {
    min-height: 100vh;
    width: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
    position: relative;
    padding: 1rem;
    overflow-x: hidden;
    overflow-y: auto;
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
</head>
<body>
<div class="tc-container">
    <a href="{{ route('student.profile') }}" style="position: absolute; top: 20px; left: 20px; z-index: 10; transition: transform 0.2s;">
        <img src="{{ asset('uploads/images/buttons/Previous button.png') }}" alt="Back" style="width: 45px; height: auto; filter: drop-shadow(0 4px 6px rgba(0,0,0,0.2));">
    </a>
    <img src="{{ asset('uploads/images/banners/shapes.png') }}" class="wave-top" alt="Wave Top" fetchpriority="high" loading="eager" decoding="async">
    <img src="{{ asset('uploads/images/banners/shapes.png') }}" class="wave-bottom" alt="Wave Bottom" fetchpriority="high" loading="eager" decoding="async">

    <div class="tc-card mx-auto">
        <div style="position: absolute; top: -25px; left: 50%; transform: translateX(-50%); width: 85%; max-width: 320px; text-align: center; z-index: 5;">
            <img src="{{ asset('uploads/images/stage1/banner-2.png') }}" alt="Banner" style="width: 100%; height: auto; drop-shadow: 0 4px 6px rgba(0,0,0,0.1);" fetchpriority="high" loading="eager" decoding="async">
            <h2 style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); font-family: 'Quicksand', sans-serif; font-weight: 900; font-size: clamp(18px, 5vw, 24px); color: #1E1E35; margin: 0; white-space: nowrap;">Privacy Policy</h2>
        </div>

        <div class="tc-content">
            <p>Welcome to School Bag!</p>
            <p>School Bag respects the privacy of its users. This Privacy Policy explains how we collect, use, and safeguard your information when you use our app and website.</p>

            <h3>1. Information We Collect</h3>
            <p>We may collect basic information required for educational purposes, including:</p>
            <ul>
                <li>Student name, class, and roll number</li>
                <li>Performance metrics (scores, attendance, reading progress)</li>
                <li>Device and usage information to improve our services</li>
            </ul>

            <h3>2. How We Use Your Information</h3>
            <p>We use the collected information only to:</p>
            <ul>
                <li>Provide and maintain the educational platform</li>
                <li>Track and display student progress</li>
                <li>Improve the app's functionality and user experience</li>
            </ul>

            <h3>3. Data Protection</h3>
            <p>We take appropriate security measures to protect your personal information from unauthorized access, alteration, or disclosure. We do not sell or rent student data to third parties.</p>

            <h3>4. Child Privacy</h3>
            <p>School Bag is designed for children. We do not knowingly collect personal information from children without parental or school consent. Parents and teachers can monitor and manage the student's account.</p>

            <h3>5. Third-Party Services</h3>
            <p>Our platform does not include third-party advertising. Any external links provided are strictly for educational resources and are pre-approved by the school.</p>

            <h3>6. Changes to This Policy</h3>
            <p>We may update our Privacy Policy from time to time. Any changes will be posted on this page, and significant updates will be communicated to users.</p>

            <h3>7. Contact Us</h3>
            <p>If you have any questions or concerns about this Privacy Policy or how your data is handled, please contact your school administrator or our support team.</p>
        </div>
    </div>
</div>
</body>
</html>
