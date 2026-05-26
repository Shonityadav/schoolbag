@extends('layouts.student')
@section('title', $lesson->title)

@push('styles')
<style>
.back-link{display:inline-flex;align-items:center;gap:6px;color:#8888BB;text-decoration:none;font-size:14px;font-weight:700;margin-bottom:20px}
.back-link:hover{color:#6C63FF}
.lesson-card{background:#1E1E35;border:1px solid #2A2A4A;border-radius:18px;overflow:hidden}
.lesson-header{padding:24px 28px;background:linear-gradient(135deg,rgba(108,99,255,.12),rgba(255,101,132,.06));border-bottom:1px solid #2A2A4A;display:flex;align-items:center;gap:16px}
.lesson-header h1{font-size:20px;font-weight:900;margin-bottom:6px}
.lesson-header .meta{font-size:13px;color:#8888BB;display:flex;gap:12px}
.lesson-body{padding:28px;line-height:1.85;font-size:16px;color:#CCCCEE}
.lesson-body h2{font-size:18px;font-weight:900;margin:24px 0 10px;color:#E8E8FF}
.lesson-body strong{color:#E8E8FF;font-weight:800}
.lesson-footer{padding:20px 28px;border-top:1px solid #2A2A4A;display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap}
.xp-pill{background:rgba(0,212,170,.12);border:1px solid rgba(0,212,170,.3);color:#00D4AA;border-radius:999px;padding:6px 16px;font-size:14px;font-weight:800}
.btn-complete{background:linear-gradient(135deg,#6C63FF,#5A51FF);color:#fff;border:none;border-radius:999px;padding:12px 28px;font-weight:900;font-size:15px;cursor:pointer;transition:all .2s;font-family:inherit}
.btn-complete:hover{transform:translateY(-2px)}
.done-banner{background:rgba(0,212,170,.1);border:1px solid rgba(0,212,170,.25);border-radius:12px;padding:12px 18px;color:#00D4AA;font-weight:800;font-size:14px}
.type-badge{background:rgba(108,99,255,.2);border:1px solid rgba(108,99,255,.3);border-radius:999px;padding:5px 14px;font-size:12px;font-weight:800;color:#6C63FF}
</style>
@endpush

@section('content')
<a href="{{ route('student.courses.show', $lesson->chapter->course_id) }}" class="back-link">← {{ $lesson->chapter->course->title }}</a>

<div class="lesson-card">
    <div class="lesson-header">
        <div style="font-size:40px">{{ $lesson->type_icon }}</div>
        <div style="flex:1">
            <span class="type-badge">{{ ucfirst($lesson->type) }}</span>
            <h1 style="margin-top:8px">{{ $lesson->title }}</h1>
            <div class="meta">
                <span>⏱ {{ $lesson->duration_minutes }} min</span>
                <span>⚡ +{{ $lesson->xp_reward }} XP</span>
                <span>📖 {{ $lesson->chapter->title }}</span>
            </div>
        </div>
    </div>

    <div class="lesson-body">
        {!! nl2br(e($lesson->content)) !!}
    </div>

    <div class="lesson-footer">
        @if($isCompleted)
            <div class="done-banner">✅ Lesson completed!</div>
            @if($nextLesson)
                <a href="{{ route('student.lessons.show', $nextLesson->id) }}" class="btn-complete" style="text-decoration:none;display:inline-flex;align-items:center;gap:8px">Next →</a>
            @elseif($lesson->chapter->quiz)
                <a href="{{ route('student.quizzes.show', $lesson->chapter->quiz->id) }}" class="btn-complete" style="text-decoration:none">🎯 Take Quiz</a>
            @endif
        @else
            <div class="xp-pill">+{{ $lesson->xp_reward }} XP</div>
            <form method="POST" action="{{ route('student.lessons.complete', $lesson->id) }}">
                @csrf
                <button type="submit" class="btn-complete">✓ Mark Done</button>
            </form>
        @endif
    </div>
</div>
@endsection
