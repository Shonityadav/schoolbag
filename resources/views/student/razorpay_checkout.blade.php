<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Processing Secure Payment</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #F4F7FE;
        }
        .loader-card {
            background: white;
            padding: 40px;
            border-radius: 24px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            text-align: center;
            max-width: 400px;
            width: 90%;
        }
        .spinner {
            border: 4px solid #E3F2FD;
            border-left-color: #1E88E5;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
            margin: 0 auto 24px;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        h2 {
            color: #2B3674;
            margin: 0 0 12px 0;
            font-size: 22px;
            font-weight: 800;
        }
        p {
            color: #A3AED0;
            margin: 0;
            font-size: 14px;
            line-height: 1.5;
        }
        .safe-badge {
            margin-top: 24px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #E8F5E9;
            color: #2E7D32;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
    </style>
</head>
<body>

    <div class="loader-card">
        <div class="spinner"></div>
        <h2>Secure Checkout</h2>
        <p>Please wait while we redirect you to Razorpay. Do not close or refresh this window.</p>
        
        <div class="safe-badge">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
            256-bit SSL Encrypted
        </div>
    </div>

    <form action="{{ route('student.fees.callback') }}" method="POST" id="razorpayForm">
        @csrf
        <input type="hidden" name="razorpay_order_id" id="razorpay_order_id" value="{{ $orderId }}">
        <input type="hidden" name="razorpay_payment_id" id="razorpay_payment_id">
        <input type="hidden" name="razorpay_signature" id="razorpay_signature">
        <input type="hidden" name="fee_structure_id" value="{{ $feeStructureId ?? '' }}">
    </form>

    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script>
        var options = {
            "key": "{{ $key }}",
            "amount": "{{ $amount * 100 }}", 
            "currency": "INR",
            "name": "Schoolbag",
            "description": "Fee Payment for {{ $user->name }}",
            "order_id": "{{ $orderId }}",
            "handler": function (response) {
                document.getElementById('razorpay_payment_id').value = response.razorpay_payment_id;
                document.getElementById('razorpay_signature').value = response.razorpay_signature;
                document.getElementById('razorpayForm').submit();
            },
            "prefill": {
                "name": "{{ $user->name }}",
                "email": "{{ $user->email }}",
                "contact": "{{ $user->phone ?? '' }}"
            },
            "theme": {
                "color": "#1E88E5"
            },
            "modal": {
                "ondismiss": function() {
                    window.location.href = "{{ route('student.fees.cancel') }}";
                }
            }
        };
        var rzp1 = new Razorpay(options);
        
        window.onload = function() {
            rzp1.open();
        };
    </script>

</body>
</html>
