<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ID Card Verification</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .verification-card { max-width: 500px; margin: 50px auto; background: #fff; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); overflow: hidden; }
        .v-header { padding: 20px; text-align: center; color: #fff; }
        .v-header.Active { background: #28a745; }
        .v-header.Expired { background: #ffc107; color: #000; }
        .v-header.Revoked, .v-header.Lost, .v-header.Invalid { background: #dc3545; }
        .v-photo { width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 4px solid #fff; margin-top: -60px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); background:#fff; }
        .v-body { padding: 30px 20px 20px; text-align: center; }
        .v-details { text-align: left; margin-top: 20px; }
        .v-details-row { display: flex; padding: 10px 0; border-bottom: 1px solid #eee; }
        .v-details-row:last-child { border-bottom: none; }
        .v-details-label { width: 40%; font-weight: 600; color: #555; font-size: 14px; }
        .v-details-val { width: 60%; color: #222; font-size: 14px; font-weight: 500; }
    </style>
</head>
<body>

<div class="container">
    <div class="verification-card">
        @if($status === 'Invalid')
            <div class="v-header Invalid">
                <h2><i class="fas fa-times-circle"></i> Invalid Card</h2>
                <p class="mb-0">Authentication Failed</p>
            </div>
            <div class="v-body">
                <p class="text-danger fw-bold">{{ $message }}</p>
            </div>
        @else
            <div class="v-header {{ $status }}">
                <h2>
                    @if($status === 'Active') <i class="fas fa-check-circle"></i> Active
                    @elseif($status === 'Expired') <i class="fas fa-exclamation-triangle"></i> Expired
                    @else <i class="fas fa-ban"></i> {{ $status }}
                    @endif
                </h2>
                <p class="mb-0">{{ $institute->name ?? 'Acetech Verification System' }}</p>
            </div>
            <div class="v-body">
                @if($photo && file_exists(public_path($photo)))
                    <img src="{{ asset($photo) }}" class="v-photo" alt="Photo">
                @else
                    <div class="v-photo d-inline-flex align-items-center justify-content-center text-muted fs-1 bg-light">
                        <i class="fas fa-user"></i>
                    </div>
                @endif
                
                <h4 class="mt-3 mb-1 fw-bold">{{ $name }}</h4>
                <p class="text-muted mb-0">{{ $details['Type'] ?? 'Member' }}</p>
                
                <div class="v-details">
                    @foreach($details as $key => $val)
                        @if($key !== 'Type')
                        <div class="v-details-row">
                            <div class="v-details-label">{{ $key }}</div>
                            <div class="v-details-val">{{ $val }}</div>
                        </div>
                        @endif
                    @endforeach
                    <div class="v-details-row">
                        <div class="v-details-label">Issued On</div>
                        <div class="v-details-val">{{ $card->issued_on ? $card->issued_on->format('d M Y') : 'N/A' }}</div>
                    </div>
                    <div class="v-details-row">
                        <div class="v-details-label">Valid Until</div>
                        <div class="v-details-val">{{ $card->expires_on ? $card->expires_on->format('d M Y') : 'N/A' }}</div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

</body>
</html>
