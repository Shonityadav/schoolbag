<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Your OTP Code</title>
    <style>
        body { font-family: 'Arial', sans-serif; background-color: #FDF6E9; margin: 0; padding: 20px; text-align: center; }
        .container { background-color: #FFFFFF; max-width: 500px; margin: 0 auto; padding: 30px; border-radius: 20px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .title { font-size: 24px; font-weight: bold; color: #5B3B24; margin-bottom: 20px; }
        .otp-box { background: #FFC145; padding: 15px; border-radius: 12px; font-size: 32px; font-weight: bold; letter-spacing: 5px; color: #1E1E35; margin: 20px 0; }
        .message { font-size: 16px; color: #554433; line-height: 1.5; }
        .footer { margin-top: 30px; font-size: 12px; color: #A09080; }
    </style>
</head>
<body>
    <div class="container">
        <div class="title">Password Reset Code</div>
        <div class="message">
            Hi there, <br><br>
            You have requested to reset your password. Please use the following 6-digit verification code to proceed. This code will expire in 10 minutes.
        </div>
        <div class="otp-box">
            {{ $otp }}
        </div>
        <div class="message">
            If you did not request a password reset, please ignore this email.
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} Little Learner. All rights reserved.
        </div>
    </div>
</body>
</html>
