@extends('layouts.student')
@section('title', 'Ebook Details')
@section('nav_worksheets', 'active')

@push('styles')
<style>
/* Hide Layout Navbars */
.sidebar { display: none !important; }
.main    { padding-bottom: 0 !important; margin: 0 !important; width: 100% !important; background: transparent !important; }
.content { padding: 0 !important; background: transparent !important; }

.details-page {
    padding: 20px 16px 40px;
    max-width: 600px;
    margin: 0 auto;
    font-family: 'Quicksand', sans-serif;
}
.cover-container {
    text-align: center;
    margin-bottom: 24px;
    background: #fff;
    border-radius: 24px;
    padding: 30px 20px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.05);
}
.cover-img {
    width: 160px;
    height: 200px;
    object-fit: cover;
    border-radius: 12px;
    box-shadow: 0 12px 24px rgba(0,0,0,0.15);
    margin-bottom: 24px;
}
.book-title {
    font-size: 32px;
    font-weight: 900;
    color: #1A202C;
    margin-bottom: 4px;
}
.book-subject {
    font-size: 14px;
    font-weight: 800;
    color: #5A67D8;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 8px;
}
.book-about {
    font-size: 15px;
    color: #718096;
    font-weight: 600;
    margin-bottom: 20px;
}
.btn-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
    margin-top: 16px;
}
.grid-btn {
    border-radius: 20px;
    padding: 24px 16px;
    text-align: center;
    color: #fff !important;
    text-decoration: none !important;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    transition: transform 0.2s, box-shadow 0.2s;
    border: none;
    cursor: pointer;
    font-family: 'Quicksand', sans-serif;
    width: 100%;
}
.grid-btn:hover {
    transform: translateY(-3px);
}
.grid-btn:active {
    transform: translateY(2px);
}
.icon-box {
    width: 52px;
    height: 52px;
    background: rgba(255,255,255,0.3);
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 26px;
    margin-bottom: 14px;
    color: white;
}
.btn-title {
    font-size: 17px;
    font-weight: 900;
    margin-bottom: 4px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.btn-sub {
    font-size: 12px;
    font-weight: 600;
    opacity: 0.95;
}
.btn-orange {
    background: linear-gradient(135deg, #FF9800, #F57C00);
    box-shadow: 0 6px 0 #E65100, 0 10px 20px rgba(230,81,0,0.2);
}
.btn-orange:active { box-shadow: 0 2px 0 #E65100; }

.btn-blue {
    background: linear-gradient(135deg, #42A5F5, #1E88E5);
    box-shadow: 0 6px 0 #1565C0, 0 10px 20px rgba(21,101,192,0.2);
}
.btn-blue:active { box-shadow: 0 2px 0 #1565C0; }

.btn-green {
    background: linear-gradient(135deg, #66BB6A, #43A047);
    box-shadow: 0 6px 0 #2E7D32, 0 10px 20px rgba(46,125,50,0.2);
}
.btn-green:active { box-shadow: 0 2px 0 #2E7D32; }

.btn-purple {
    background: linear-gradient(135deg, #9F7AEA, #6B46C1);
    box-shadow: 0 6px 0 #553C9A, 0 10px 20px rgba(107,70,193,0.2);
}
.btn-purple:active { box-shadow: 0 2px 0 #553C9A; }

/* Responsive adjustments */
@media (max-width: 400px) {
    .btn-grid {
        grid-template-columns: 1fr;
    }
}

/* Spinner Animation */
@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
.animate-spin {
    animation: spin 1s linear infinite;
    transform-origin: center;
}
</style>
@endpush

@section('content')
<a href="{{ route('student.ebooks') }}" style="position: fixed; top: 20px; left: 10px; z-index: 1000; transition: transform 0.15s;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
    <img src="{{ asset('uploads/images/buttons/Previous button.png') }}" alt="Back" style="height: 52px; object-fit: contain;">
</a>

<div class="details-page"> 

    <div class="cover-container">
        @php
            $coverUrl = asset('images/logo.png'); // fallback
            $ebookBaseUrl = '';
            if (isset($course->ebook_url) && !empty($course->ebook_url)) {
                $parsedUrl = parse_url($course->ebook_url);
                $baseUrl = (isset($parsedUrl['scheme']) && isset($parsedUrl['host'])) ? ($parsedUrl['scheme'] . '://' . $parsedUrl['host']) : '';
                if ($baseUrl) {
                    $ebookBaseUrl = $baseUrl . '/uploads/ebook/ebook-' . ($course->ebook_id ?? '');
                    $coverUrl = $ebookBaseUrl . '/1.jpg';
                }
            }

            // Extract index pages for chat with ebook
            $ebook = \App\Models\Ebook::find($course->ebook_id ?? 0);
            $index_pages = $ebook ? $ebook->index_page : '';
        @endphp
        <img src="{{ $coverUrl }}" alt="{{ $course->title }}" class="cover-img" onerror="this.src='{{ asset('images/logo.png') }}'; this.style.objectFit='contain';">
        
        <div class="book-title">{{ $course->title }}</div>
        @if($course->subject)
            <div class="book-subject">SUBJECT: {{ strtoupper($course->subject) }}</div>
        @endif
        <div class="book-about">About This Book:</div>
        
        <div class="btn-grid">
            <a href="{{ $course->ebook_url }}" target="_blank" class="grid-btn btn-orange">
                <div class="icon-box">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="28" height="28"><path d="M11.25 4.533A9.707 9.707 0 006 3a9.735 9.735 0 00-3.25.555.75.75 0 00-.5.707v14.25a.75.75 0 001 .707A8.237 8.237 0 016 18.75c1.995 0 3.823.707 5.25 1.886V4.533zM12.75 20.636A8.214 8.214 0 0118 18.75c1.68 0 3.282.515 4.75 1.407A.75.75 0 0024 19.462V5.212a.75.75 0 00-.5-.707A9.735 9.735 0 0018 3a9.707 9.707 0 00-5.25 1.533v16.103z" /></svg>
                </div>
                <div class="btn-title">FLIPBOOK</div>
                <div class="btn-sub">Read digital version</div>
            </a>
            
            @if($course->chapters()->count() > 0)
                <a href="{{ route('student.assigned_ebooks.show', $course->id) }}" class="grid-btn btn-blue">
                    <div class="icon-box">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="28" height="28"><path fill-rule="evenodd" d="M8.161 2.58a1.5 1.5 0 011.678 0l4.992 3.328a3 3 0 003.338 0l3.668-2.446A1.5 1.5 0 0124 4.717v13.626a1.5 1.5 0 01-1.661 1.493l-3.906-2.604a3 3 0 00-3.328 0l-4.992 3.328a1.5 1.5 0 01-1.678 0l-3.668-2.445a3 3 0 00-3.338 0l-2.26 1.506A1.5 1.5 0 010 18.423V4.797a1.5 1.5 0 011.661-1.493l3.906 2.604a3 3 0 003.328 0l3.266-2.176z" clip-rule="evenodd" /></svg>
                    </div>
                    <div class="btn-title">MAP VIEW</div>
                    <div class="btn-sub">Interactive learning</div>
                </a>
            @else
                <button id="btn-generate-map-{{ $course->id }}" onclick="generateMapIndex({{ $course->id }})" class="grid-btn btn-green">
                    <div class="icon-box">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="28" height="28"><path fill-rule="evenodd" d="M9 4.5a.75.75 0 01.721.544l.813 2.846a3.75 3.75 0 002.576 2.576l2.846.813a.75.75 0 010 1.442l-2.846.813a3.75 3.75 0 00-2.576 2.576l-.813 2.846a.75.75 0 01-1.442 0l-.813-2.846a3.75 3.75 0 00-2.576-2.576l-2.846-.813a.75.75 0 010-1.442l2.846-.813A3.75 3.75 0 007.466 7.89l.813-2.846A.75.75 0 019 4.5zM18 1.5a.75.75 0 01.728.568l.258 1.036c.236.94.97 1.674 1.91 1.91l1.036.258a.75.75 0 010 1.456l-1.036.258c-.94.236-1.674.97-1.91 1.91l-.258 1.036a.75.75 0 01-1.456 0l-.258-1.036a2.625 2.625 0 00-1.91-1.91l-1.036-.258a.75.75 0 010-1.456l1.036-.258a2.625 2.625 0 001.91-1.91l.258-1.036A.75.75 0 0118 1.5zM16.5 15a.75.75 0 01.712.513l.394 1.183c.15.447.5.799.948.948l1.183.395a.75.75 0 010 1.422l-1.183.395c-.447.15-.799.5-.948.948l-.395 1.183a.75.75 0 01-1.422 0l-.395-1.183a1.5 1.5 0 00-.948-.948l-1.183-.395a.75.75 0 010-1.422l1.183-.395c.447-.15.799-.5.948-.948l.395-1.183A.75.75 0 0116.5 15z" clip-rule="evenodd" /></svg>
                    </div>
                    <div class="btn-title">CREATE MAP</div>
                    <div class="btn-sub">Build interactive map</div>
                </button>
            @endif
            
            <a href="{{ route('student.aitest.create', ['ebook_id' => $course->ebook_id, 'assigned_ebook_id' => $course->id, 'base_url' => $ebookBaseUrl]) }}" class="grid-btn btn-purple">
                <div class="icon-box">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="28" height="28">
                      <path fill-rule="evenodd" d="M9.315 7.584C12.195 3.883 16.695 1.5 21.75 1.5a.75.75 0 01.75.75c0 5.056-2.383 9.555-6.084 12.436A11.894 11.894 0 0112 15c-1.74 0-3.393-.37-4.887-1.042l-4.707 4.707a.75.75 0 01-1.06-1.06l4.707-4.707A11.894 11.894 0 015.012 8.012a11.956 11.956 0 014.303-.428zM14.25 7.5a.75.75 0 000 1.5h1.5v1.5a.75.75 0 001.5 0v-1.5h1.5a.75.75 0 000-1.5h-1.5v-1.5a.75.75 0 00-1.5 0v1.5h-1.5z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="btn-title">AI TEST PAPER</div>
                <div class="btn-sub">Generate Test</div> 
            </a>
            
            <a href="{{ route('student.chat_with_paper', ['ebook_id' => $course->ebook_id, 'ebook_url' => $ebookBaseUrl, 'index_pages' => $index_pages]) }}" class="grid-btn btn-blue" style="background: linear-gradient(135deg, #3B82F6, #1D4ED8); box-shadow: 0 6px 0 #1E3A8A, 0 10px 20px rgba(29,78,216,0.2);">
                <div class="icon-box">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="28" height="28">
                      <path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75c2.39 0 4.582-.862 6.273-2.29l3.477.87a.75.75 0 00.916-.916l-.87-3.477A9.704 9.704 0 0021.75 12c0-5.385-4.365-9.75-9.75-9.75zM8.25 10.5a1.5 1.5 0 100-3 1.5 1.5 0 000 3zm5.25 0a1.5 1.5 0 100-3 1.5 1.5 0 000 3zm3.75 1.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="btn-title">CHAT WITH EBOOK</div>
                <div class="btn-sub">Interactive AI Chat</div>
            </a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
window.generateMapIndex = function(courseId) {
    const btn = document.getElementById('btn-generate-map-' + courseId);
    if (!btn) return;
    btn.disabled = true;
    
    // Change UI state
    btn.querySelector('.icon-box').innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="28" height="28" class="animate-spin"><path d="M12 2v4a8 8 0 0 1 8 8h4a12 12 0 0 0-12-12zm0 20v-4a8 8 0 0 1-8-8H0a12 12 0 0 0 12 12z"/></svg>';
    btn.querySelector('.btn-title').innerHTML = 'BUILDING...';
    btn.querySelector('.btn-sub').innerHTML = 'Please wait';
    
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
            btn.querySelector('.icon-box').innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="28" height="28"><path fill-rule="evenodd" d="M9 4.5a.75.75 0 01.721.544l.813 2.846a3.75 3.75 0 002.576 2.576l2.846.813a.75.75 0 010 1.442l-2.846.813a3.75 3.75 0 00-2.576 2.576l-.813 2.846a.75.75 0 01-1.442 0l-.813-2.846a3.75 3.75 0 00-2.576-2.576l-2.846-.813a.75.75 0 010-1.442l2.846-.813A3.75 3.75 0 007.466 7.89l.813-2.846A.75.75 0 019 4.5zM18 1.5a.75.75 0 01.728.568l.258 1.036c.236.94.97 1.674 1.91 1.91l1.036.258a.75.75 0 010 1.456l-1.036.258c-.94.236-1.674.97-1.91 1.91l-.258 1.036a.75.75 0 01-1.456 0l-.258-1.036a2.625 2.625 0 00-1.91-1.91l-1.036-.258a.75.75 0 010-1.456l1.036-.258a2.625 2.625 0 001.91-1.91l.258-1.036A.75.75 0 0118 1.5zM16.5 15a.75.75 0 01.712.513l.394 1.183c.15.447.5.799.948.948l1.183.395a.75.75 0 010 1.422l-1.183.395c-.447.15-.799.5-.948.948l-.395 1.183a.75.75 0 01-1.422 0l-.395-1.183a1.5 1.5 0 00-.948-.948l-1.183-.395a.75.75 0 010-1.422l1.183-.395c.447-.15.799-.5.948-.948l.395-1.183A.75.75 0 0116.5 15z" clip-rule="evenodd" /></svg>';
            btn.querySelector('.btn-title').innerHTML = 'CREATE MAP';
            btn.querySelector('.btn-sub').innerHTML = 'Build interactive map';
        }
    })
    .catch(err => {
        console.error(err);
        alert('Failed to connect to the server.');
        btn.disabled = false;
        btn.querySelector('.icon-box').innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="28" height="28"><path fill-rule="evenodd" d="M9 4.5a.75.75 0 01.721.544l.813 2.846a3.75 3.75 0 002.576 2.576l2.846.813a.75.75 0 010 1.442l-2.846.813a3.75 3.75 0 00-2.576 2.576l-.813 2.846a.75.75 0 01-1.442 0l-.813-2.846a3.75 3.75 0 00-2.576-2.576l-2.846-.813a.75.75 0 010-1.442l2.846-.813A3.75 3.75 0 007.466 7.89l.813-2.846A.75.75 0 019 4.5zM18 1.5a.75.75 0 01.728.568l.258 1.036c.236.94.97 1.674 1.91 1.91l1.036.258a.75.75 0 010 1.456l-1.036.258c-.94.236-1.674.97-1.91 1.91l-.258 1.036a.75.75 0 01-1.456 0l-.258-1.036a2.625 2.625 0 00-1.91-1.91l-1.036-.258a.75.75 0 010-1.456l1.036-.258a2.625 2.625 0 001.91-1.91l.258-1.036A.75.75 0 0118 1.5zM16.5 15a.75.75 0 01.712.513l.394 1.183c.15.447.5.799.948.948l1.183.395a.75.75 0 010 1.422l-1.183.395c-.447.15-.799.5-.948.948l-.395 1.183a.75.75 0 01-1.422 0l-.395-1.183a1.5 1.5 0 00-.948-.948l-1.183-.395a.75.75 0 010-1.422l1.183-.395c.447-.15.799-.5.948-.948l.395-1.183A.75.75 0 0116.5 15z" clip-rule="evenodd" /></svg>';
        btn.querySelector('.btn-title').innerHTML = 'CREATE MAP';
        btn.querySelector('.btn-sub').innerHTML = 'Build interactive map';
    });
};
</script>
@endpush
