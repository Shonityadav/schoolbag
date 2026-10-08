@extends('layouts.student')
@section('title', 'Ebooks')
@section('nav_worksheets', 'active')

@push('styles')
<style>
.ebooks-page {
    padding: 8px 12px 120px;
    max-width: 700px;
    width: 100%;
    box-sizing: border-box;
    overflow-x: hidden;
    margin: 0 auto;
}
.eb-page-title {
    font-family: 'Bubblegum Sans', cursive;
    font-size: clamp(22px, 6vw, 32px);
    color: #5E4D3B;
    margin-bottom: 4px;
}
.eb-page-sub {
    font-size: 13px;
    font-weight: 700;
    color: #8D7E6A;
    margin-bottom: 24px;
}

/* ── QR Scanner & Uploader Styles ── */
.qr-container {
    background: rgba(255,255,255,0.85);
    border-radius: 24px;
    padding: 20px;
    box-shadow: 0 8px 0 rgba(0,0,0,0.06), 0 10px 25px rgba(0,0,0,0.08);
    margin-bottom: 32px;
    position: relative;
    z-index: 10;
}
.qr-tabs {
    display: flex;
    gap: 12px;
    margin-bottom: 20px;
}
.qr-tab-btn {
    flex: 1;
    height: 48px;
    border: none;
    border-radius: 14px;
    font-family: 'Bubblegum Sans', cursive;
    font-size: clamp(14px, 3.5vw, 17px);
    font-weight: 700;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: all 0.2s;
}
.qr-tab-btn.tab-cam {
    background: linear-gradient(175deg, #FFAA6A 0%, #E8803B 100%);
    color: #fff;
    box-shadow: 0 5px 0 #A84C18;
    text-shadow: 0 1px 2px rgba(0,0,0,0.2);
}
.qr-tab-btn.tab-cam.inactive {
    background: #E8E2D9;
    color: #8D7E6A;
    box-shadow: 0 5px 0 #BDB3A6;
    text-shadow: none;
}
.qr-tab-btn.tab-upload {
    background: linear-gradient(175deg, #6BBFFF 0%, #3B9EE8 100%);
    color: #fff;
    box-shadow: 0 5px 0 #1A6BAA;
    text-shadow: 0 1px 2px rgba(0,0,0,0.2);
}
.qr-tab-btn.tab-upload.inactive {
    background: #E8E2D9;
    color: #8D7E6A;
    box-shadow: 0 5px 0 #BDB3A6;
    text-shadow: none;
}
.qr-tab-btn:hover { transform: translateY(-2px); }
.qr-tab-btn:active { transform: translateY(2px); box-shadow: 0 2px 0 rgba(0,0,0,0.2) !important; }

/* QR Reader Camera box */
#qr-reader-wrap {
    text-align: center;
    overflow: hidden;
    border-radius: 18px;
    background: rgba(107,191,255,0.1);
    border: 3px dashed #3B9EE8;
    position: relative;
    min-height: 260px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 16px;
    box-sizing: border-box;
}
#qr-reader {
    width: 100%;
    max-width: 320px;
    border: 0 !important;
}
#qr-reader img {
    margin: 0 auto;
}
.btn-start-cam {
    background: linear-gradient(135deg, #7DDBA8, #4CBF88);
    color: #fff;
    border: none;
    border-radius: 999px;
    padding: 12px 28px;
    font-family: 'Bubblegum Sans', cursive;
    font-size: 18px;
    cursor: pointer;
    box-shadow: 0 5px 0 #1E7A50;
    transition: all 0.2s;
    text-shadow: 0 1px 2px rgba(0,0,0,0.2);
}
.btn-start-cam:hover { transform: translateY(-3px); box-shadow: 0 7px 0 #1E7A50; }
.btn-start-cam:active { transform: translateY(0); box-shadow: 0 2px 0 #1E7A50; }

/* Upload box */
#qr-upload-wrap {
    display: none;
    text-align: center;
}
.upload-dropzone {
    border: 3px dashed #3B9EE8;
    background: rgba(107,191,255,0.1);
    border-radius: 18px;
    padding: 36px 20px;
    cursor: pointer;
    transition: all 0.2s;
    display: block;
}
.upload-dropzone:hover {
    background: rgba(107,191,255,0.2);
    border-color: #1A6BAA;
    transform: scale(1.01);
}
.upload-icon {
    font-size: 48px;
    margin-bottom: 12px;
}
.upload-title {
    font-family: 'Bubblegum Sans', cursive;
    font-size: 20px;
    color: #1A6BAA;
    margin-bottom: 6px;
}
.upload-sub {
    font-size: 13px;
    color: #637381;
    font-weight: 700;
}

/* Status Notifications */
#qr-status {
    margin-top: 16px;
}
.status-loading {
    background: #FFF8E1;
    color: #F57C00;
    padding: 12px 16px;
    border-radius: 12px;
    font-weight: 800;
    font-size: 14px;
    text-align: center;
    border-left: 4px solid #F57C00;
}
.status-success {
    background: #E8F5E9;
    color: #2E7D32;
    padding: 12px 16px;
    border-radius: 12px;
    font-weight: 800;
    font-size: 15px;
    text-align: center;
    border-left: 4px solid #2E7D32;
}
.status-error {
    background: #FFEBEE;
    color: #D32F2F;
    padding: 12px 16px;
    border-radius: 12px;
    font-weight: 800;
    font-size: 14px;
    text-align: center;
    border-left: 4px solid #D32F2F;
}

/* Scanned QR Results Display */
.scanned-result-card {
    background: #ffffff;
    border: 2px solid #E2E8F0;
    border-radius: 20px;
    padding: 20px;
    margin-top: 16px;
    box-shadow: 0 6px 16px rgba(0,0,0,0.06);
    text-align: left;
}
.scanned-url-box {
    background: #F7FAFC;
    border: 1px solid #CBD5E0;
    border-radius: 12px;
    padding: 12px 14px;
    margin: 8px 0 16px;
    font-family: monospace;
    font-size: 14px;
    color: #2B6CB0;
    word-break: break-all;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
}
.scanned-url-box a {
    color: #3182CE;
    font-weight: 700;
    text-decoration: underline;
}
.scanned-action-btn {
    display: inline-block;
    background: linear-gradient(135deg, #6BBFFF, #3B9EE8);
    color: #fff !important;
    font-family: 'Quicksand', sans-serif;
    font-weight: 800;
    font-size: 13px;
    padding: 8px 16px;
    border-radius: 999px;
    text-decoration: none !important;
    box-shadow: 0 4px 0 #1A6BAA;
    transition: all 0.15s;
    white-space: nowrap;
}
.scanned-action-btn:hover { transform: translateY(-2px); box-shadow: 0 6px 0 #1A6BAA; }
.scanned-action-btn:active { transform: translateY(0); box-shadow: 0 1px 0 #1A6BAA; }

/* ── Results section ── */
.eb-results-wrap {
    position: relative;
    z-index: 1;
    margin-top: 28px;
}
.eb-results-title {
    font-family: 'Bubblegum Sans', cursive;
    font-size: clamp(18px, 4.5vw, 24px);
    color: #5E4D3B;
    margin-bottom: 14px;
}
.ebook-card {
    background: rgba(255,255,255,0.82);
    border-radius: 20px;
    padding: 0;
    display: flex;
    align-items: stretch;
    margin-bottom: 10px;
    box-shadow: 0 6px 0 rgba(0,0,0,0.06), 0 8px 16px rgba(0,0,0,0.05);
    transition: transform 0.15s;
    text-decoration: none;
    color: #5E4D3B;
    width: 100%;
    box-sizing: border-box;
    overflow: hidden;
    min-width: 0;
}
.ebook-card:hover { transform: translateY(-3px); }
.ebook-cover {
    width: 52px; height: 64px;
    border-radius: 8px; object-fit: cover;
    flex-shrink: 0; box-shadow: 0 4px 0 rgba(0,0,0,0.1);
}
.ebook-cover-placeholder {
    width: 52px; height: 64px; border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    font-size: 28px; flex-shrink: 0;
}
.ebook-info { flex: 1 1 120px; min-width: 120px; }
.ebook-title {
    font-family: 'Bubblegum Sans', cursive;
    font-size: clamp(14px, 3.5vw, 18px);
    margin-bottom: 4px;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.ebook-meta {
    font-size: 11px;
    font-weight: 700;
    color: #8D7E6A;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.ebook-tag {
    display: inline-block; background: #FFF3CC;
    border-radius: 999px; padding: 2px 10px;
    font-size: 10px; font-weight: 900;
    color: #A07800; margin-top: 4px;
}
.btn-read {
    background: linear-gradient(135deg, #9DE182, #5CAA44);
    color: #fff; border: none; border-radius: 999px; padding: 8px 16px;
    font-family: 'Quicksand', sans-serif; font-size: 12px; font-weight: 900;
    cursor: pointer; box-shadow: 0 4px 0 #3A7A28;
    transform: translateY(-2px); transition: all 0.15s;
    white-space: nowrap; text-decoration: none; display: inline-block;
}
.btn-read:hover  { transform: translateY(-4px); box-shadow: 0 6px 0 #3A7A28; color: #fff; }
.btn-read:active { transform: translateY(0);    box-shadow: 0 1px 0 #3A7A28; }
.eb-empty {
    text-align: center; padding: 36px 20px;
    color: #8D7E6A; font-size: 15px; font-weight: 700;
}
</style>
@endpush

@section('content')
<div class="ebooks-page">

    <div class="eb-page-title">📚 Ebooks</div>
    <div class="eb-page-sub">Scan or upload a QR code to unlock your ebooks!</div>

    <!-- ── QR Scanner & Uploader ── -->
    <div class="qr-container">
        <div class="qr-tabs">
            <button class="qr-tab-btn tab-cam inactive" id="btn-tab-cam" onclick="switchTab('cam')">
                <span>📷</span> Scan QR
            </button>
            <button class="qr-tab-btn tab-upload inactive" id="btn-tab-upload" onclick="switchTab('upload')">
                <span>📁</span> Upload QR
            </button>
            <input type="file" id="qr-file-input" accept="image/*" style="display: none;" onchange="handleFileUpload(this)">
        </div>

        {{-- Camera Panel --}}
        <div id="qr-reader-wrap" style="display: none;">
            <div id="cam-idle">
                
                <div style="color: #1A6BAA; font-family: 'Bubblegum Sans', cursive; font-size: 20px; margin-bottom: 16px;">
                    Scan Ebook QR Code from your textbook or card
                </div>
                <button class="btn-start-cam" onclick="startScanner()">▶ Start Camera</button>
            </div>
            <div id="qr-reader" style="display: none;"></div>
            <button id="btn-stop-cam" onclick="stopScanner()" style="display: none; margin-top: 12px; background: #E53E3E; color: #fff; border: none; border-radius: 999px; padding: 6px 18px; font-weight: 800; cursor: pointer;">⏹ Stop Camera</button>
        </div>

        {{-- Upload Panel --}}
        <div id="qr-upload-wrap" style="display: none;">
            <label for="qr-file-input" class="upload-dropzone">
                
                <div class="upload-title">Tap to Select QR Image</div>
                <div class="upload-sub">Supports PNG, JPG, or screenshot from your device</div>
            </label>
        </div>

        {{-- Status Notification Area --}}
        <div id="qr-status"></div>
    </div>

    <!-- ── Unlocked Ebook listing ── -->
    <div class="eb-results-wrap">
        <div class="eb-results-title">📖 My Unlocked Ebooks</div>

        @forelse($ebooks as $ebook)
            @php
                $coverUrl = asset('images/logo.png'); // fallback
                if (isset($ebook->external_url) && !empty($ebook->external_url)) {
                    $parsedUrl = parse_url($ebook->external_url);
                    $baseUrl = (isset($parsedUrl['scheme']) && isset($parsedUrl['host'])) ? ($parsedUrl['scheme'] . '://' . $parsedUrl['host']) : '';
                    if ($baseUrl) {
                        $coverUrl = $baseUrl . '/uploads/ebook/ebook-' . ($ebook->id ?? '') . '/1.jpg';
                    }
                } else {
                    $coverUrl = asset('uploads/ebook/ebook-' . ($ebook->id ?? '') . '/1.jpg');
                }
                
                $targetUrl = isset($ebook->external_url) && !empty($ebook->external_url) 
                             ? route('student.assigned_ebooks.details', $ebook->course_id)
                             : route('student.ebooks.show', $ebook->id);
            @endphp
        <a href="{{ $targetUrl }}" class="ebook-card">
            <div style="flex: 0 0 40%; max-width: 40%; position: relative; background: #FFF;">
                <img src="{{ $coverUrl }}" style="width: 100%; height: 100%; object-fit: cover; object-position: center left; position: absolute; inset: 0;" onerror="this.src='{{ asset('images/logo.png') }}'; this.style.objectFit='contain'; this.style.padding='10px';">
            </div>
            <div style="flex: 0 0 60%; max-width: 60%; min-width: 0; padding: 16px; display: flex; flex-direction: column; justify-content: center; box-sizing: border-box;">
                <div class="ebook-info">
                    <div class="ebook-title">{{ $ebook->name }}</div>
                    <div class="ebook-meta">
                        @if($ebook->publication)<span>📖 {{ $ebook->publication }}</span>@endif
                        @if($ebook->subject)<span> · {{ $ebook->subject }}</span>@endif
                    </div>
                    <div style="display:flex; gap:6px; flex-wrap:wrap; margin-top:4px;">
                        @if($ebook->standard)
                            <span class="ebook-tag">🎓 Class {{ $ebook->standard }}</span>
                        @endif
                        @if($ebook->series)
                            <span class="ebook-tag" style="background:#E8F5FF;color:#1A6BAA;">{{ $ebook->series }}</span>
                        @endif
                    </div>
                </div>
            </div>
        </a>
        @empty
        <div class="eb-empty">
            <div style="font-size:48px;margin-bottom:12px;">🔒</div>
            No ebooks unlocked yet.<br>Scan or upload a QR code above to add your first book!
        </div>
        @endforelse
    </div>

</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    const icon = type === 'success' ? '✨' : (type === 'error' ? '❌' : '⚠️');
    toast.innerHTML = `${icon} ${message}`;
    toast.style.position = 'fixed';
    toast.style.top = '24px';
    toast.style.right = '24px';
    toast.style.background = type === 'error' ? '#FFF5F5' : '#F3FDF5';
    toast.style.border = type === 'error' ? '2px solid #FEB2B2' : '2px solid #A5D6A7';
    toast.style.color = type === 'error' ? '#C53030' : '#1B5E20';
    toast.style.padding = '16px 24px';
    toast.style.borderRadius = '12px';
    toast.style.boxShadow = '0 8px 16px rgba(0,0,0,0.1)';
    toast.style.fontFamily = "'Nunito', sans-serif";
    toast.style.fontWeight = '800';
    toast.style.zIndex = '9999';
    toast.style.transition = 'opacity 0.5s ease-in-out';
    document.body.appendChild(toast);

    setTimeout(() => {
        toast.style.opacity = '0';
        setTimeout(() => toast.remove(), 500);
    }, 4000);
}

let html5QrCode = null;
let isCamRunning = false;

function switchTab(tab) {
    const camBtn = document.getElementById('btn-tab-cam');
    const uploadBtn = document.getElementById('btn-tab-upload');
    const camWrap = document.getElementById('qr-reader-wrap');
    const status = document.getElementById('qr-status');

    if (tab === 'cam') {
        if (camWrap.style.display === 'flex' || camWrap.style.display === 'block') {
            // It's currently open, so close it
            camWrap.style.display = 'none';
            camBtn.classList.add('inactive');
            stopScanner();
        } else {
            // It's closed, so open it
            status.innerHTML = '';
            camWrap.style.display = 'flex';
            camBtn.classList.remove('inactive');
        }
    } else if (tab === 'upload') {
        // Trigger file select directly
        document.getElementById('qr-file-input').click();
        
        // Close cam if open
        camWrap.style.display = 'none';
        camBtn.classList.add('inactive');
        stopScanner();
    }
}

function startScanner() {
    document.getElementById('cam-idle').style.display = 'none';
    document.getElementById('qr-reader').style.display = 'block';
    document.getElementById('btn-stop-cam').style.display = 'inline-block';
    document.getElementById('qr-status').innerHTML = '';

    if (!html5QrCode) {
        html5QrCode = new Html5Qrcode("qr-reader");
    }

    const config = { fps: 10, qrbox: { width: 220, height: 220 } };

    html5QrCode.start(
        { facingMode: "environment" },
        config,
        (decodedText) => {
            // Found a QR Code
            stopScanner();
            processQrCode(decodedText);
        },
        (errorMessage) => {
            // Ignore scan frames without QR code
        }
    ).then(() => {
        isCamRunning = true;

        // Auto-fix for phones defaulting to ultra-wide (0.5x) lens
        setTimeout(() => {
            const video = document.querySelector("#qr-reader video");
            if (video && video.srcObject) {
                const track = video.srcObject.getVideoTracks()[0];
                const capabilities = track.getCapabilities ? track.getCapabilities() : null;
                
                if (capabilities && capabilities.zoom) {
                    // Try to apply a zoom of 2.0 (which is typically the "1x" main lens on multi-lens phones)
                    // or fallback to the minimum allowed zoom + 1
                    let targetZoom = 2;
                    if (targetZoom < capabilities.zoom.min) targetZoom = capabilities.zoom.min;
                    if (targetZoom > capabilities.zoom.max) targetZoom = capabilities.zoom.max;
                    
                    track.applyConstraints({ advanced: [{ zoom: targetZoom }] })
                        .catch(err => console.log('Zoom auto-fix error:', err));
                }
            }
        }, 800);

    }).catch(err => {
        document.getElementById('cam-idle').style.display = 'block';
        document.getElementById('qr-reader').style.display = 'none';
        document.getElementById('btn-stop-cam').style.display = 'none';
        document.getElementById('qr-status').innerHTML = '';
        showToast('Camera access denied or not supported directly in this browser. Try uploading an image instead!', 'error');
    });
}

function stopScanner() {
    if (html5QrCode && isCamRunning) {
        html5QrCode.stop().then(() => {
            isCamRunning = false;
            document.getElementById('cam-idle').style.display = 'block';
            document.getElementById('qr-reader').style.display = 'none';
            document.getElementById('btn-stop-cam').style.display = 'none';
        }).catch(err => console.log(err));
    } else {
        document.getElementById('cam-idle').style.display = 'block';
        document.getElementById('qr-reader').style.display = 'none';
        document.getElementById('btn-stop-cam').style.display = 'none';
    }
}

function handleFileUpload(input) {
    if (!input.files || !input.files[0]) return;

    const file = input.files[0];
    document.getElementById('qr-status').innerHTML = `<div class="status-loading">⏳ Analyzing QR code from image...</div>`;

    const fileQr = new Html5Qrcode("qr-reader");
    fileQr.scanFile(file, true)
        .then(decodedText => {
            processQrCode(decodedText);
        })
        .catch(err => {
            document.getElementById('qr-status').innerHTML = '';
            showToast('Could not find a valid QR code in this image. Please make sure the image is clear and try again!', 'error');
            input.value = '';
        });
}

function processQrCode(decodedText) {
    document.getElementById('qr-status').innerHTML = `<div class="status-loading" style="margin-top: 16px;">⏳ Checking ebook link, please wait...</div>`;

    fetch("{{ route('student.ebooks.scan_qr') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ code: decodedText })
    })
    .then(res => res.json())
    .then(data => {
        const ebookUrl = data.initial_url || data.actual_url || decodedText;
        const isUrl = data.is_url || ebookUrl.startsWith('http://') || ebookUrl.startsWith('https://');
        const qrStatus = document.getElementById('qr-status');
        
        if (data.requires_confirmation) {
            const eb = data.ebook || {};
            qrStatus.innerHTML = `
                <div style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.6); z-index: 10000; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(4px);">
                    <div class="scanned-result-card" style="border-color: #90CDF4; background: #EBF8FF; padding: 28px; width: 90%; max-width: 420px; box-shadow: 0 16px 32px rgba(0,0,0,0.25); border-radius: 24px; position: relative; margin-top: 0;">
                        <button onclick="cancelQrAssignment()" style="position: absolute; top: 16px; right: 16px; background: transparent; border: none; font-size: 20px; color: #4A5568; cursor: pointer;">✖</button>
                        <div style="font-size: 15px; font-weight: 900; color: #2B6CB0; margin-bottom: 20px; text-align: center;">
                            ✨ Scan Successful! Ready to Assign
                        </div>
                        <div style="display: flex; flex-direction: column; align-items: center; gap: 14px; margin-bottom: 24px;">
                            <img src="${eb.cover_image}" alt="Cover" style="width: 140px; height: 180px; object-fit: cover; border-radius: 8px; box-shadow: 0 6px 16px rgba(0,0,0,0.2); border: 3px solid white;">
                            <div style="text-align: center;">
                                <div style="font-family: 'Bubblegum Sans', cursive; font-size: 28px; color: #2B6CB0; line-height: 1.2;">${eb.name || 'Ebook Preview'}</div>
                                <div style="font-size: 14px; font-weight: 700; color: #4A5568; margin-top: 6px;">
                                    ${eb.publication ? '📖 ' + eb.publication : ''}
                                    ${eb.subject ? ' · ' + eb.subject : ''}
                                    ${eb.standard ? ' (Class ' + eb.standard + ')' : ''}
                                </div>
                            </div>
                        </div>
                        <div style="display: flex; gap: 12px; justify-content: center; flex-wrap: wrap;">
                            <a href="${eb.url}" target="_blank" style="flex: 1; text-align: center; background: #EBF8FF; color: #2B6CB0; border: 2px solid #90CDF4; border-radius: 12px; font-size: 15px; font-weight: 800; padding: 12px 16px; cursor: pointer; text-decoration: none; box-shadow: 0 4px 0 #BEE3F8; transition: all 0.2s;">📖 View Preview</a>
                            <button onclick='confirmQrAssignment(${JSON.stringify(eb).replace(/'/g, "\\'")})' style="flex: 1; text-align: center; background: linear-gradient(135deg, #66BB6A, #388E3C); color: white; border: none; border-radius: 12px; box-shadow: 0 4px 0 #1B5E20; font-size: 15px; font-weight: 800; padding: 12px 16px; cursor: pointer; transition: all 0.2s;">Assign Ebook</button>
                        </div>
                    </div>
                </div>
            `;
        } else if (data.already_assigned || data.success) {
            const eb = data.ebook || {};
            qrStatus.innerHTML = `
                <div class="scanned-result-card" style="border-color: #A5D6A7; background: #F3FDF5; padding: 22px;">
                    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 14px;">
                        <div style="font-size: 36px;">📘</div>
                        <div>
                            <div style="font-family: 'Bubblegum Sans', cursive; font-size: 22px; color: #1B5E20;">${eb.name || 'Ebook Unlocked'}</div>
                            <div style="font-size: 13px; font-weight: 700; color: #388E3C;">
                                ${eb.publication ? '📖 ' + eb.publication : ''}
                                ${eb.subject ? ' · ' + eb.subject : ''}
                                ${eb.standard ? ' (Class ' + eb.standard + ')' : ''}
                            </div>
                        </div>
                    </div>
                    <div style="font-size: 14px; font-weight: 800; color: #2E7D32; margin-bottom: 16px;">
                        ✨ ${data.message}
                    </div>
                    <div>
                        <a href="${data.redirect_url}" class="btn-read" style="background: linear-gradient(135deg, #66BB6A, #388E3C); box-shadow: 0 4px 0 #1B5E20; font-size: 15px; padding: 10px 24px; display: inline-block;">📖 Open Ebook Now →</a>
                    </div>
                </div>
            `;
        } else if (isUrl) {
            qrStatus.innerHTML = `
                <div class="scanned-result-card" style="border-color: #90CDF4; background: #EBF8FF; padding: 22px;">
                    <div style="font-size: 12px; font-weight: 800; color: #2B6CB0; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">
                        🔗 Scanned Ebook URL:
                    </div>
                    <div class="scanned-url-box" style="background: #ffffff; border-color: #BEE3F8; margin: 8px 0 0; padding: 14px 16px;">
                        <div style="flex: 1; overflow: hidden;">
                            <a href="${ebookUrl}" target="_blank" style="color: #2B6CB0; font-size: 15px; font-weight: 700;">${ebookUrl}</a>
                        </div>
                        <a href="${ebookUrl}" target="_blank" class="scanned-action-btn" style="background: linear-gradient(135deg, #48BB78, #2F855A); box-shadow: 0 4px 0 #22543D; font-size: 14px; padding: 9px 20px;">Open Ebook ↗</a>
                    </div>
                </div>
            `;
        } else {
            document.getElementById('qr-status').innerHTML = '';
            showToast(data.message || 'Invalid or unrecognized QR code.', 'error');
        }
    })
    .catch(err => {
        document.getElementById('qr-status').innerHTML = '';
        showToast('Connection error while querying library. Please check your internet and try again.', 'error');
    });
}

window.cancelQrAssignment = function() {
    document.getElementById('qr-status').innerHTML = '';
};

window.confirmQrAssignment = function(ebookData) {
    document.getElementById('qr-status').innerHTML = `<div class="status-loading" style="margin-top: 16px;">⏳ Assigning ebook, please wait...</div>`;
    fetch("{{ route('student.ebooks.confirm_qr_assign') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ ebook: ebookData })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            sessionStorage.setItem('qr_toast_message', data.message);
            window.location.reload();
        } else {
            document.getElementById('qr-status').innerHTML = '';
            showToast(data.message || 'Error assigning ebook', 'error');
        }
    })
    .catch(err => {
        document.getElementById('qr-status').innerHTML = '';
        showToast('Connection error.', 'error');
    });
};

document.addEventListener('DOMContentLoaded', function() {
    const toastMsg = sessionStorage.getItem('qr_toast_message');
    if (toastMsg) {
        showToast(toastMsg, 'success');
        sessionStorage.removeItem('qr_toast_message');
    }
    
    // Auto-start scanner if ?scan=true
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('scan') === 'true') {
        const camWrap = document.getElementById('qr-reader-wrap');
        const camBtn = document.getElementById('btn-tab-cam');
        if (camWrap && camBtn) {
            camWrap.style.display = 'flex';
            camBtn.classList.remove('inactive');
            setTimeout(() => {
                startScanner();
            }, 300);
        }
        
        // Clean up URL so refresh doesn't trigger it again
        const newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
        window.history.replaceState({path:newUrl}, '', newUrl);
    }
});

window.generateMapIndex = function(courseId) {
    const btn = document.getElementById('btn-generate-map-' + courseId);
    if (!btn) return;
    btn.disabled = true;
    btn.innerHTML = '✨ Building...';
    
    fetch(`/student/assigned-ebooks/${courseId}/generate-map`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            alert('Oops: ' + (data.error || 'Something went wrong.'));
            btn.disabled = false;
            btn.innerHTML = '✨ Create Map';
        }
    })
    .catch(err => {
        console.error(err);
        alert('Failed to connect to the server.');
        btn.disabled = false;
        btn.innerHTML = '✨ Create Map';
    });
};
</script>
@endpush

