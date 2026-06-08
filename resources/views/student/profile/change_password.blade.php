@extends('layouts.student')
@section('title', 'Change Password')

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
    z-index: 4;
}

.cp-card {
    background: #FFF2D1;
    border: 3px solid #FFEAC2;
    border-radius: 20px;
    width: 85%;
    height: 360px;
    padding: 30px 20px 40px;
    text-align: center;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    position: relative;
    z-index: 2;
}

.cp-card-title {
    font-weight: 800;
    font-size: 14px;
    color: #1E1E35;
    margin-bottom: 20px;
    text-align: left;
    padding-left: 10px;
}

.otp-inputs {
    display: flex;
    justify-content: space-between;
    gap: 8px;
    margin-bottom: 30px;
    padding: 0 10px;
}

.otp-inputs input {
    width: calc(100% / 6 - 8px);
    aspect-ratio: 1;
    border: none;
    background: #FFC145;
    opacity: 0.8;
    border-radius: 8px;
    text-align: center;
    font-size: 24px;
    font-weight: 800;
    color: #1E1E35;
}
.otp-inputs input:focus {
    outline: 2px solid #FFB300;
    opacity: 1;
}

.cp-confirm-btn {
    background: #5CA1FF;
    color: white;
    font-weight: 900;
    font-size: 16px;
    border: none;
    border-radius: 999px;
    padding: 12px 48px;
    box-shadow: 0 4px 0 #3C81DF;
    transition: transform 0.1s, box-shadow 0.1s;
    cursor: pointer;
    display: inline-block;
}
.cp-confirm-btn:active {
    transform: translateY(4px);
    box-shadow: 0 0 0 #3C81DF;
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
.cp-input {
    width: 100%;
    background: #FFFFFF;
    border: none;
    border-radius: 8px;
    padding: 12px 16px;
    font-size: 14px;
    font-weight: 700;
    color: #1E1E35;
}
.cp-input:focus {
    outline: 2px solid #FFC145;
}
.cp-input-help {
    font-size: 9px;
    font-weight: 800;
    color: #A09080;
    margin-top: 6px;
    display: block;
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
        
        <div class="cp-pill">Change / Reset Password</div>

        <div class="cp-card">
            <div id="cp-message" style="display: none; padding: 12px; border-radius: 8px; margin-bottom: 15px; font-weight: bold; font-size: 13px;"></div>
            
            <!-- OTP Section -->
            <div id="otp-section">
                <div class="cp-card-title">Enter Verification Code</div>
                
                <form action="#" method="POST" id="otp-form">
                    @csrf
                    <div class="otp-inputs">
                        <input type="text" maxlength="1" class="otp-box">
                        <input type="text" maxlength="1" class="otp-box">
                        <input type="text" maxlength="1" class="otp-box">
                        <input type="text" maxlength="1" class="otp-box">
                        <input type="text" maxlength="1" class="otp-box">
                        <input type="text" maxlength="1" class="otp-box">
                    </div>

                    <button type="button" class="cp-confirm-btn" id="confirm-otp-btn">Confirm</button>
                    
                    <div style="margin-top: 15px;">
                        <a href="#" id="resend-otp-btn" style="color: #5CA1FF; font-size: 14px; font-weight: 700; text-decoration: none;">
                            @if(isset($cooldown) && $cooldown > 0)
                                Resend OTP ({{ $cooldown }}s)
                            @else
                                Resend OTP
                            @endif
                        </a>
                    </div>
                </form>
            </div>

            <!-- New Password Section -->
            <div id="password-section" style="display: none;">
                <div class="cp-card-title" style="font-size: 18px; margin-bottom: 30px;">Create New Password</div>
                
                <form action="#" method="POST" id="password-form">
                    @csrf
                    <div class="cp-input-group">
                        <label class="cp-input-label">New Password</label>
                        <input type="password" class="cp-input">
                        <span class="cp-input-help">Must be at least 8 characters.</span>
                    </div>
                    
                    <div class="cp-input-group">
                        <label class="cp-input-label">Confirm Password</label>
                        <input type="password" class="cp-input">
                    </div>

                    <button type="button" class="cp-save-btn">Save</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Auto-focus next input in OTP
document.querySelectorAll('.otp-box').forEach((input, index, inputs) => {
    input.addEventListener('input', function() {
        if (this.value.length === 1 && index < inputs.length - 1) {
            inputs[index + 1].focus();
        }
    });
    input.addEventListener('keydown', function(e) {
        if (e.key === 'Backspace' && this.value === '' && index > 0) {
            inputs[index - 1].focus();
        }
    });
});

// Helper for CSRF
const csrfToken = document.querySelector('input[name="_token"]').value;

function showMessage(msg, isError = false) {
    const msgBox = document.getElementById('cp-message');
    msgBox.style.display = 'block';
    msgBox.innerText = msg;
    if (isError) {
        msgBox.style.backgroundColor = '#FFE5E5';
        msgBox.style.color = '#D32F2F';
        msgBox.style.border = '1px solid #FFCDCD';
    } else {
        msgBox.style.backgroundColor = '#E8F5E9';
        msgBox.style.color = '#2E7D32';
        msgBox.style.border = '1px solid #C8E6C9';
    }
}

// Verify OTP
document.getElementById('confirm-otp-btn').addEventListener('click', function() {
    let otp = '';
    document.querySelectorAll('.otp-box').forEach(input => {
        otp += input.value;
    });

    if (otp.length !== 6) {
        showMessage('Please enter a valid 6-digit OTP.', true);
        return;
    }

    const btn = this;
    const originalText = btn.innerText;
    btn.innerText = 'Verifying...';
    btn.disabled = true;

    fetch("{{ route('student.profile.verify_otp') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ otp: otp })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('cp-message').style.display = 'none'; // hide message on success
            document.getElementById('otp-section').style.display = 'none';
            document.getElementById('password-section').style.display = 'block';
        } else {
            showMessage(data.message || 'Invalid OTP.', true);
        }
    })
    .catch(error => {
        showMessage('An error occurred. Please try again.', true);
    })
    .finally(() => {
        btn.innerText = originalText;
        btn.disabled = false;
    });
});

// Resend OTP
let cooldown = {{ isset($cooldown) ? $cooldown : 0 }};
const resendBtn = document.getElementById('resend-otp-btn');

function updateResendButton() {
    if (cooldown > 0) {
        resendBtn.innerText = `Resend OTP (${cooldown}s)`;
        resendBtn.style.color = '#A09080';
        resendBtn.style.pointerEvents = 'none';
        cooldown--;
        setTimeout(updateResendButton, 1000);
    } else {
        resendBtn.innerText = 'Resend OTP';
        resendBtn.style.color = '#5CA1FF';
        resendBtn.style.pointerEvents = 'auto';
    }
}

if (cooldown > 0) {
    updateResendButton();
}

resendBtn.addEventListener('click', function(e) {
    e.preventDefault();
    if (cooldown > 0) return;

    resendBtn.innerText = 'Sending...';
    resendBtn.style.pointerEvents = 'none';

    fetch("{{ route('student.profile.resend_otp') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showMessage('A new OTP has been sent to your email.', false);
            cooldown = 60;
            updateResendButton();
        } else {
            showMessage(data.message || 'Failed to resend OTP.', true);
            resendBtn.innerText = 'Resend OTP';
            resendBtn.style.pointerEvents = 'auto';
        }
    })
    .catch(error => {
        showMessage('An error occurred. Please try again.', true);
        resendBtn.innerText = 'Resend OTP';
        resendBtn.style.pointerEvents = 'auto';
    });
});

// Update Password
document.querySelector('.cp-save-btn').addEventListener('click', function() {
    const password = document.querySelectorAll('.cp-input')[0].value;
    const password_confirmation = document.querySelectorAll('.cp-input')[1].value;

    if (password.length < 8) {
        showMessage('Password must be at least 8 characters long.', true);
        return;
    }

    if (password !== password_confirmation) {
        showMessage('Passwords do not match.', true);
        return;
    }

    const btn = this;
    const originalText = btn.innerText;
    btn.innerText = 'Saving...';
    btn.disabled = true;

    fetch("{{ route('student.profile.update_password') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ 
            password: password, 
            password_confirmation: password_confirmation 
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showMessage('Password updated successfully!', false);
            setTimeout(() => {
                window.location.href = "{{ route('student.profile') }}";
            }, 1500);
        } else {
            showMessage(data.message || 'Validation failed.', true);
        }
    })
    .catch(error => {
        showMessage('An error occurred. Please try again.', true);
    })
    .finally(() => {
        btn.innerText = originalText;
        btn.disabled = false;
    });
});
</script>
@endpush
@endsection
