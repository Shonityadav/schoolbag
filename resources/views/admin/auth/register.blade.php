<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Institute - School Bag</title>
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
            padding: 2rem 1rem;
        }
        .auth-card {
            background: var(--sb-card);
            border: 1px solid var(--sb-border);
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05), 0 2px 4px -2px rgba(0,0,0,0.02);
            padding: 2.5rem;
            width: 100%;
            max-width: 800px;
        }
        .form-control {
            font-size: 13.5px;
            padding: 10px 14px;
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
            padding: 12px 24px;
            font-size: 14.5px;
            font-weight: 600;
            border-radius: 8px;
        }
        .btn-primary:hover {
            background: var(--sb-accent-hover);
        }
        .section-title {
            font-size: 14px;
            font-weight: 600;
            color: var(--sb-accent);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 1rem;
            border-bottom: 2px solid var(--sb-border);
            padding-bottom: 0.5rem;
        }
    </style>
</head>
<body>

    <div class="auth-card">
        <div class="text-center mb-4 pb-2">
            <div class="d-inline-flex align-items-center justify-content-center" style="width:56px;height:56px;background:#EEF2FF;color:var(--sb-accent);border-radius:14px;font-size:24px;margin-bottom:16px;">
                <i class="bi bi-buildings-fill"></i>
            </div>
            <h4 class="fw-bold mb-1">Register Your Institute</h4>
            <p style="color:var(--sb-muted);font-size:14px;">Create your school workspace and admin account</p>
        </div>

        @if(session('error'))
            <div class="alert alert-danger" style="border-radius:8px;font-size:13.5px;padding:12px;">
                <i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger" style="border-radius:8px;font-size:13.5px;padding:12px;">
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.register.submit') }}">
            @csrf
            
            <div class="row g-4">
                {{-- School Information --}}
                <div class="col-12 col-md-6">
                    <div class="section-title">School Information</div>
                    
                    <div class="mb-3">
                        <label class="form-label" style="font-size:13px;font-weight:600;">School Name <span class="text-danger">*</span></label>
                        <input type="text" name="school_name" class="form-control" value="{{ old('school_name') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-size:13px;font-weight:600;">School Phone Number</label>
                        <input type="text" name="school_number" class="form-control" value="{{ old('school_number') }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-size:13px;font-weight:600;">School Address</label>
                        <textarea name="school_address" class="form-control" rows="2">{{ old('school_address') }}</textarea>
                    </div>
                </div>

                {{-- Admin Account --}}
                <div class="col-12 col-md-6">
                    <div class="section-title">Admin Details</div>

                    <div class="mb-3">
                        <label class="form-label" style="font-size:13px;font-weight:600;">Admin Name <span class="text-danger">*</span></label>
                        <input type="text" name="admin_name" class="form-control" value="{{ old('admin_name') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-size:13px;font-weight:600;">Email Address <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-size:13px;font-weight:600;">Personal Phone Number</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label" style="font-size:13px;font-weight:600;">Password <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control" required minlength="6">
                        </div>
                        <div class="col-6">
                            <label class="form-label" style="font-size:13px;font-weight:600;">Confirm Password <span class="text-danger">*</span></label>
                            <input type="password" name="password_confirmation" class="form-control" required minlength="6">
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center mt-4 pt-3" style="border-top:1px solid var(--sb-border);">
                <button type="submit" class="btn btn-primary px-5 py-2 mb-3">Create Institute & Admin Account</button>
                <div style="font-size:13.5px;color:var(--sb-muted);">
                    Already registered? <a href="{{ route('admin.login') }}" style="color:var(--sb-accent);text-decoration:none;font-weight:600;">Sign in here</a>
                </div>
            </div>
        </form>
    </div>

</body>
</html>
