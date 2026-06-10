@extends('layouts.student')
@section('title', 'Subjects')
@section('nav_courses', 'active')

@push('styles')
<style>
.page-head{margin-bottom:28px}
.page-head h1{font-size:24px;font-weight:900;margin-bottom:4px}
.page-head p{color:#8888BB;font-size:14px}
.subject-card{background:#1E1E35;border:2px solid #2A2A4A;border-radius:20px;padding:24px;text-align:center;text-decoration:none;color:inherit;display:block;transition:all .3s;position:relative;overflow:hidden}
.subject-card::before{content:'';position:absolute;inset:0;background:linear-gradient(135deg,var(--c1,.2),var(--c2,.1));opacity:0;transition:opacity .3s}
.subject-card:hover{transform:translateY(-6px);border-color:#6C63FF;box-shadow:0 12px 40px rgba(108,99,255,.25)}
.subject-card:hover::before{opacity:1}
.subject-icon{font-size:52px;display:block;margin-bottom:12px;position:relative}
.subject-name{font-size:17px;font-weight:900;margin-bottom:4px;position:relative}
.subject-count{font-size:13px;color:#8888BB;position:relative}
.subject-badge{position:absolute;top:14px;right:14px;background:rgba(0,212,170,.15);border:1px solid rgba(0,212,170,.3);color:#00D4AA;border-radius:999px;padding:3px 10px;font-size:11px;font-weight:800}
</style>
@endpush

@section('content')
<div style="padding: 24px;">
    <div class="page-head">
        <h1>🗺️ My Subjects</h1>
        <p>{{ $user->studentClass->name ?? '' }} — Choose a subject to explore chapters</p>
    </div>

    <div class="grid-3">
        @forelse($courses as $course)
        <a href="{{ route('student.courses.show', $course->id) }}" class="subject-card" style="--c1:{{ $course->color }}33;--c2:{{ $course->color }}11">
            <div class="subject-badge">{{ $course->chapters_count }} chapters</div>
            <span class="subject-icon">{{ $course->icon }}</span>
            <div class="subject-name">{{ $course->title }}</div>
            <div class="subject-count">{{ $course->description ?? 'Tap to explore' }}</div>
        </a>
        @empty
        <div style="grid-column:1/-1;text-align:center;padding:60px 0;color:#8888BB">
            <div style="font-size:64px;margin-bottom:12px">📭</div>
            <div style="font-size:18px;font-weight:800">No subjects yet</div>
            <div style="font-size:14px;margin-top:6px">Your teacher will add subjects soon!</div>
        </div>
        @endforelse
    </div>
</div>
@endsection
