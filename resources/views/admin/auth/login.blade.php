<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - School Bag</title>
    {{-- PWA Setup --}}
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#2563EB">
    <link rel="apple-touch-icon" href="{{ asset('icons/icon-192x192.png') }}">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root {
            --sb-bg: #f8fafc;
            --sb-card: #ffffff;
            --sb-text: #1e293b;
            --sb-muted: #64748b;
            --sb-border: #e2e8f0;
            --sb-accent: #4f46e5;
            --sb-accent-hover: #4338ca;
        }
        body {
            background-color: var(--sb-bg);
            color: var(--sb-text);
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .auth-card {
            background: var(--sb-card);
            border: 1px solid var(--sb-border);
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05), 0 2px 4px -2px rgba(0,0,0,0.02);
            padding: 2.5rem;
            width: 100%;
            max-width: 440px;
        }
        .form-control {
            font-size: 14px;
            padding: 12px 16px;
            border-radius: 8px;
            border-color: var(--sb-border);
        }
        .form-control:focus {
            border-color: var(--sb-accent);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.15);
        }
        .btn-primary {
            background: var(--sb-accent);
            border: none;
            padding: 12px 16px;
            font-size: 14.5px;
            font-weight: 600;
            border-radius: 8px;
        }
        .btn-primary:hover {
            background: var(--sb-accent-hover);
        }
    </style>
</head>
<body>

    <div class="auth-card">
        <div class="text-center mb-4">
            <div class="d-inline-flex align-items-center justify-content-center" style="width:56px;height:56px;background:#EEF2FF;color:var(--sb-accent);border-radius:14px;font-size:24px;margin-bottom:16px;">
                <i class="bi bi-shield-lock-fill"></i>
            </div>
            <h4 class="fw-bold mb-1">Admin Portal</h4>
            <p style="color:var(--sb-muted);font-size:14px;">Sign in to your dashboard</p>
        </div>

        @if($errors->any())
            <div class="alert alert-danger" style="border-radius:8px;font-size:13.5px;padding:12px;">
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.login.submit') }}">
            @csrf
            
            <div class="mb-3">
                <label class="form-label" style="font-size:13px;font-weight:600;">Email or Phone</label>
                <div class="input-group">
                    <span class="input-group-text" style="background:transparent;border-color:var(--sb-border);border-right:none;color:var(--sb-muted);">
                        <i class="bi bi-person"></i>
                    </span>
                    <input type="text" name="login" class="form-control" placeholder="Enter email or phone" value="{{ old('login') }}" style="border-left:none;padding-left:0;">
                </div>
            </div>

            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <label class="form-label mb-0" style="font-size:13px;font-weight:600;">Password</label>
                    <a href="#" style="font-size:12.5px;color:var(--sb-accent);text-decoration:none;">Forgot password?</a>
                </div>
                <div class="input-group">
                    <span class="input-group-text" style="background:transparent;border-color:var(--sb-border);border-right:none;color:var(--sb-muted);">
                        <i class="bi bi-key"></i>
                    </span>
                    <input type="password" name="password" class="form-control" placeholder="Enter your password" style="border-left:none;padding-left:0;">
                </div>
            </div>

            <div class="mb-4 form-check">
                <input type="checkbox" class="form-check-input" id="remember" name="remember" style="cursor:pointer;">
                <label class="form-check-label" for="remember" style="font-size:13.5px;cursor:pointer;user-select:none;">Remember me</label>
            </div>

            <button type="submit" class="btn btn-primary w-100 mb-3">Sign In</button>

            <div class="text-center" style="font-size:13.5px;color:var(--sb-muted);">
                Don't have an account? <a href="{{ route('admin.register') }}" style="color:var(--sb-accent);text-decoration:none;font-weight:600;">Register your school</a>
            </div>
        </form>
    </div>

    {{-- PWA Service Worker Registration --}}
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/sw.js').then(function(registration) {
                    console.log('ServiceWorker registration successful with scope: ', registration.scope);
                }, function(err) {
                    console.log('ServiceWorker registration failed: ', err);
                });
            });
        }
    </script>
</body>
</html>
