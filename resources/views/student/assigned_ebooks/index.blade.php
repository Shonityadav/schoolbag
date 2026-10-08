@extends('layouts.student')
@section('title', 'Subjects')
@section('nav_courses', 'active')

@push('styles')
<style>
.page-head{margin-bottom:28px}
.page-head h1{font-size:24px;font-weight:900;margin-bottom:4px;color:#5D1A1A;font-family:'Bubblegum Sans',cursive;}
.page-head p{color:#8D7E6A;font-size:14px;font-family:'Quicksand',sans-serif;}
.subject-card{background:linear-gradient(160deg,#FFFDF5 0%,#FFF3CC 100%);border:2px solid #F0D590;border-radius:20px;padding:20px;text-align:left;text-decoration:none;color:#5D1A1A;display:flex;align-items:center;transition:all .3s;position:relative;overflow:hidden;box-shadow:0 8px 16px rgba(220,180,90,0.15);}
.subject-card::before{content:'';position:absolute;inset:0;background:linear-gradient(135deg,rgba(255,255,255,0.6),rgba(255,255,255,0));opacity:0;transition:opacity .3s}
.subject-card:hover{transform:translateY(-6px);border-color:#FF9800;box-shadow:0 12px 24px rgba(255,152,0,.25)}
.subject-card:hover::before{opacity:1}
.subject-name{font-size:20px;font-weight:900;margin-bottom:6px;position:relative;font-family:'Bubblegum Sans',cursive;letter-spacing:0.5px;line-height:1.2;}
.subject-detail{font-size:13px;color:#8D7E6A;position:relative;font-family:'Quicksand',sans-serif;margin-bottom:3px;font-weight:700;}
.subject-detail{font-size:13px;color:#8D7E6A;position:relative;font-family:'Quicksand',sans-serif;margin-bottom:3px;font-weight:700;}
.subject-badge{position:absolute;top:14px;right:14px;background:#4CAF50;color:#FFF;border-radius:999px;padding:4px 12px;font-size:11px;font-weight:800;font-family:'Quicksand',sans-serif;box-shadow:0 2px 4px rgba(76,175,80,0.3);}
</style>
@endpush

@section('content')

<div style="padding: 24px;">
    <div class="page-head">
        <h1>🗺️ My Subjects</h1>
        <p>Class {{ isset($user->studentClass) ? $user->studentClass->standard . ' ' . $user->studentClass->section : ($user->user_type == 1 ? 'Admin' : ($user->user_type == 2 ? 'Staff' : '')) }} — Choose a subject to explore chapters</p>
    </div>

    <div class="grid-3" style="display: grid; gap: 15px; grid-template-columns: 1fr;">
        @forelse($courses as $course)
            @php
                $coverUrl = asset('images/logo.png'); // fallback
                if ($course->ebook_url) {
                    $parsedUrl = parse_url($course->ebook_url);
                    $baseUrl = (isset($parsedUrl['scheme']) && isset($parsedUrl['host'])) ? ($parsedUrl['scheme'] . '://' . $parsedUrl['host']) : '';
                    if ($baseUrl) {
                        $coverUrl = $baseUrl . '/uploads/ebook/ebook-' . $course->ebook_id . '/1.jpg';
                    }
                }
            @endphp
        <a href="{{ route('student.assigned_ebooks.show', $course->id) }}" class="subject-card" onclick="showGlobalLoader(event, this.href)">
            <div class="subject-badge">{{ $course->chapters_count }} chapters</div>
            
            <div style="flex-shrink:0; width: 85px; height: 120px; margin-right: 18px; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.15); background: #FFF;">
                <img src="{{ $coverUrl }}" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.src='{{ asset('images/logo.png') }}'; this.style.objectFit='contain'; this.style.padding='10px';">
            </div>
            
            <div style="flex-grow: 1; padding-right: 70px;">
                <div class="subject-name">{{ $course->title }}</div>
                <div class="subject-detail">📚 Subject: {{ $course->subject ?? 'N/A' }}</div>
                <div class="subject-detail">🎓 Class: {{ $course->standard ?? 'N/A' }}</div>
                <div class="subject-detail" style="margin-top: 4px; color: #FF9800;">🏢 {{ $course->publication ?? 'AceTech' }}</div>
            </div>
        </a>
        @empty
        <div style="grid-column:1/-1;text-align:center;padding:60px 0;color:#8888BB">
            <div style="font-size:64px;margin-bottom:12px">📭</div>
            <div style="font-size:18px;font-weight:800;font-family:'Quicksand',sans-serif;">No subjects yet</div>
            <div style="font-size:14px;margin-top:6px;font-family:'Quicksand',sans-serif;">Your teacher will add subjects soon!</div>
        </div>
        @endforelse
    </div>
</div>

@endsection
