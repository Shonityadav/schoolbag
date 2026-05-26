@extends('layouts.student')
@section('title', $ebook->name . ' — Pages')
@section('nav_worksheets', 'active')

@push('styles')
<style>
/* ── Page wrapper ── */
.ep-page {
    padding: 8px 12px 120px;
    max-width: 700px;
    margin: 0 auto;
}

/* ── Back link ── */
.ep-back {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: rgba(255,255,255,0.7);
    border-radius: 999px;
    padding: 6px 14px;
    font-family: 'Quicksand', sans-serif;
    font-size: 13px;
    font-weight: 800;
    color: #5E4D3B;
    text-decoration: none;
    margin-bottom: 16px;
    box-shadow: 0 3px 0 rgba(0,0,0,0.06);
    transition: transform 0.15s;
}
.ep-back:hover { transform: translateX(-3px); color: #5E4D3B; }

/* ── Ebook header card ── */
.ep-header {
    background: linear-gradient(135deg, #FFF9F0, #FFF3E0);
    border-radius: 20px;
    padding: 18px 20px;
    margin-bottom: 22px;
    box-shadow: 0 6px 0 rgba(0,0,0,0.06), 0 8px 20px rgba(0,0,0,0.05);
}
.ep-title {
    font-family: 'Bubblegum Sans', cursive;
    font-size: clamp(20px, 5vw, 28px);
    color: #5E4D3B;
    margin-bottom: 6px;
}
.ep-meta {
    font-size: 12px;
    font-weight: 800;
    color: #8D7E6A;
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    align-items: center;
}
.ep-badge {
    display: inline-block;
    border-radius: 999px;
    padding: 3px 10px;
    font-size: 11px;
    font-weight: 900;
    font-family: 'Quicksand', sans-serif;
}
.ep-badge.cls  { background: #FFF3CC; color: #A07800; }
.ep-badge.pub  { background: #E8F5FF; color: #1A6BAA; }
.ep-badge.sub  { background: #F0FFF4; color: #1E7A50; }
.ep-badge.prc  { background: #FFF0F3; color: #CC1A3A; }

/* ── Pages count label ── */
.ep-section-title {
    font-family: 'Bubblegum Sans', cursive;
    font-size: clamp(18px, 4.5vw, 24px);
    color: #5E4D3B;
    margin-bottom: 14px;
}

/* ── Grid of pages ── */
.ep-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
    gap: 14px;
}

/* ── Single page card ── */
.ep-card {
    background: rgba(255,255,255,0.88);
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 6px 0 rgba(0,0,0,0.07), 0 8px 18px rgba(0,0,0,0.06);
    transition: transform 0.18s, box-shadow 0.18s;
    display: flex;
    flex-direction: column;
}
.ep-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 0 rgba(0,0,0,0.07), 0 14px 24px rgba(0,0,0,0.09);
}

/* Thumbnail */
.ep-thumb-wrap {
    width: 100%;
    aspect-ratio: 3/4;
    background: linear-gradient(135deg, #E8F5FF, #C8E8FF);
    overflow: hidden;
    position: relative;
}
.ep-thumb {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    transition: transform 0.3s;
}
.ep-card:hover .ep-thumb { transform: scale(1.04); }

/* Fallback placeholder */
.ep-thumb-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 6px;
    font-size: 36px;
    color: #7BBFDD;
}
.ep-thumb-placeholder span {
    font-family: 'Quicksand', sans-serif;
    font-size: 11px;
    font-weight: 800;
    color: #8AAABB;
}

/* Page number badge */
.ep-page-num {
    position: absolute;
    top: 6px;
    left: 6px;
    background: rgba(0,0,0,0.45);
    color: #fff;
    border-radius: 999px;
    padding: 2px 8px;
    font-size: 10px;
    font-weight: 900;
    font-family: 'Quicksand', sans-serif;
    backdrop-filter: blur(4px);
}

/* Card footer */
.ep-card-foot {
    padding: 8px 10px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 6px;
}
.ep-page-title-txt {
    font-family: 'Quicksand', sans-serif;
    font-size: 11px;
    font-weight: 800;
    color: #5E4D3B;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    flex: 1;
    min-width: 0;
}

/* Open button */
.ep-open-btn {
    display: inline-flex;
    align-items: center;
    gap: 3px;
    background: linear-gradient(135deg, #6BBFFF, #3B9EE8);
    color: #fff;
    border-radius: 999px;
    padding: 5px 10px;
    font-family: 'Quicksand', sans-serif;
    font-size: 10px;
    font-weight: 900;
    text-decoration: none;
    box-shadow: 0 3px 0 #1A6BAA;
    transition: all 0.15s;
    white-space: nowrap;
    flex-shrink: 0;
}
.ep-open-btn:hover  { transform: translateY(-2px); box-shadow: 0 5px 0 #1A6BAA; color: #fff; }
.ep-open-btn:active { transform: translateY(0); box-shadow: 0 1px 0 #1A6BAA; }

/* Empty state */
.ep-empty {
    text-align: center;
    padding: 48px 20px;
    color: #8D7E6A;
    font-size: 15px;
    font-weight: 700;
}
/* TOC Loading State */
.ep-toc-loading {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 40px 20px;
    background: rgba(255, 255, 255, 0.7);
    border-radius: 16px;
    margin-bottom: 24px;
    border: 2px dashed #D0E4F5;
}
.ep-toc-spinner {
    width: 40px;
    height: 40px;
    border: 4px solid #D0E4F5;
    border-top: 4px solid #3B9EE8;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin-bottom: 12px;
}
@keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
.ep-toc-text {
    font-family: 'Quicksand', sans-serif;
    font-weight: 800;
    color: #5E4D3B;
    font-size: 14px;
}

/* TOC Items */
.toc-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
    margin-bottom: 30px;
}
.toc-item {
    background: #fff;
    border-radius: 16px;
    padding: 16px;
    box-shadow: 0 4px 0 rgba(0,0,0,0.04), 0 6px 12px rgba(0,0,0,0.03);
    border-left: 6px solid #FFB347;
    cursor: pointer;
    transition: transform 0.2s, box-shadow 0.2s;
    user-select: none;
}
.toc-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 0 rgba(0,0,0,0.04), 0 8px 16px rgba(0,0,0,0.05);
}
.toc-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.toc-title {
    font-family: 'Bubblegum Sans', cursive;
    font-size: 20px;
    color: #5E4D3B;
    margin-bottom: 4px;
}
.toc-meta {
    font-family: 'Quicksand', sans-serif;
    font-size: 12px;
    font-weight: 800;
    color: #8D7E6A;
}
.toc-toggle-icon {
    color: #FFB347;
    font-size: 18px;
    font-weight: bold;
    transition: transform 0.3s ease;
}
.toc-item.active .toc-toggle-icon {
    transform: rotate(180deg);
}
.toc-stages-wrapper {
    display: none;
    margin-top: 16px;
    padding-top: 16px;
    border-top: 1px dashed #EEDFCD;
}
.toc-item.active .toc-stages-wrapper {
    display: block;
}
.toc-stages {
    display: flex;
    flex-direction: column;
    gap: 8px;
}
.toc-stage-badge {
    background: #F0F7FF;
    color: #1A6BAA;
    padding: 8px 14px;
    border-radius: 12px;
    font-size: 13px;
    font-weight: 800;
    font-family: 'Quicksand', sans-serif;
    border: 1px solid #D0E4F5;
    display: flex;
    align-items: center;
    justify-content: space-between;
    transition: background 0.2s;
}
.toc-stage-badge:hover {
    background: #D0E4F5;
}
</style>
@endpush

@section('content')
<div class="ep-page">

    {{-- Back --}}
    <a href="{{ route('student.ebooks') }}" class="ep-back">← Back to Ebooks</a>

    {{-- Ebook header --}}
    <div class="ep-header">
        <div class="ep-title">📚 {{ $ebook->name }}</div>
        <div class="ep-meta">
            @if($ebook->publication)
                <span class="ep-badge pub">📖 {{ $ebook->publication }}</span>
            @endif
            @if($ebook->standard)
                <span class="ep-badge cls">🎓 Class {{ $ebook->standard }}</span>
            @endif
            @if($ebook->subject)
                <span class="ep-badge sub">📝 {{ $ebook->subject }}</span>
            @endif
            @if($ebook->series)
                <span class="ep-badge pub">{{ $ebook->series }}</span>
            @endif
            @if($ebook->price)
                <span class="ep-badge prc">₹{{ number_format($ebook->price, 0) }}</span>
            @endif
        </div>
    </div>

    {{-- Extracted Chapters (TOC) --}}
    <div class="ep-section-title">
        📑 Table of Contents (AI Extracted)
    </div>
    
    <div id="toc-container">
        <div class="ep-toc-loading">
            <div class="ep-toc-spinner"></div>
            <div class="ep-toc-text">Gemini AI is analyzing the book structure...</div>
            <div style="font-size: 11px; color: #8D7E6A; margin-top: 4px;">This usually takes 5-10 seconds.</div>
        </div>
    </div>

    {{-- Pages grid --}}
    <div class="ep-section-title">
        🗂️ Pages
        <span style="font-size:14px; font-family:'Quicksand',sans-serif; font-weight:800; color:#8D7E6A;">
            ({{ $ebook->pages->count() }})
        </span>
    </div>

    @if($ebook->pages->count())
    <div class="ep-grid">
        @foreach($ebook->pages as $page)
        @php
            // If url already ends with an image extension, use it as-is.
            // Otherwise it's a folder path — append /{position}.jpg
            $rawUrl  = trim($page->url ?? '');
            $ext     = strtolower(pathinfo($rawUrl, PATHINFO_EXTENSION));
            $fileUrl = in_array($ext, ['jpg','jpeg','png','gif','webp'])
                        ? $rawUrl
                        : rtrim($rawUrl, '/') . '/' . $page->position . '.jpg';
        @endphp
        <div class="ep-card">
            <div class="ep-thumb-wrap">
                @if($rawUrl)
                    <img class="ep-thumb"
                         src="{{ asset($fileUrl) }}"
                         alt="{{ $page->title ?: 'Page '.$page->position }}"
                         loading="lazy"
                         onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                    <div class="ep-thumb-placeholder" style="display:none;">
                        📄<span>No preview</span>
                    </div>
                @else
                    <div class="ep-thumb-placeholder">
                        📄<span>No preview</span>
                    </div>
                @endif
                <div class="ep-page-num">{{ $page->position }}</div>
            </div>
            <div class="ep-card-foot">
                <span class="ep-page-title-txt">
                    {{ $page->title ?: 'Page '.$page->position }}
                </span>
                <a class="ep-open-btn"
                   href="{{ asset($fileUrl) }}"
                   target="_blank">
                    Open ↗
                </a>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="ep-empty">
        <div style="font-size:48px; margin-bottom:12px;">📄</div>
        No pages found for this ebook yet.
    </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    fetch("{{ route('student.ebooks.toc', $ebook->id) }}")
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('toc-container');
            container.innerHTML = ''; // Clear loading state
            
            if (data.error) {
                container.innerHTML = `<div class="ep-empty" style="padding: 20px;">⚠️ ${data.error}</div>`;
                return;
            }

            if (!data.chapters || data.chapters.length === 0) {
                container.innerHTML = `<div class="ep-empty" style="padding: 20px;">No chapters found.</div>`;
                return;
            }

            const list = document.createElement('div');
            list.className = 'toc-list';

            data.chapters.forEach(ch => {
                const item = document.createElement('div');
                item.className = 'toc-item';
                
                let pagesStr = `Page ${ch.start_page}`;
                if (ch.end_page) pagesStr += ` - ${ch.end_page}`;
                
                let stagesHtml = '';
                if (ch.stages) {
                    ch.stages.forEach(stage => {
                        stagesHtml += `
                            <div class="toc-stage-badge">
                                <span>🧩 Stage ${stage.stage_number}: ${stage.stage_name}</span>
                                <span style="font-size:16px;">→</span>
                            </div>
                        `;
                    });
                }

                item.innerHTML = `
                    <div class="toc-header">
                        <div>
                            <div class="toc-title">${ch.chapter_name}</div>
                            <div class="toc-meta">📍 ${pagesStr} | 🧩 ${ch.total_stages} Stages</div>
                        </div>
                        <div class="toc-toggle-icon">▼</div>
                    </div>
                    <div class="toc-stages-wrapper">
                        <div class="toc-stages">${stagesHtml}</div>
                    </div>
                `;
                
                item.addEventListener('click', function() {
                    this.classList.toggle('active');
                });
                
                list.appendChild(item);
            });

            container.appendChild(list);
        })
        .catch(err => {
            document.getElementById('toc-container').innerHTML = 
                `<div class="ep-empty" style="padding: 20px;">⚠️ Failed to load chapters.</div>`;
        });
});
</script>
@endpush
