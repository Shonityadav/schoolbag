@extends('layouts.student')
@section('title', 'Quiz Result')

@push('styles')
<style>
.result-hero{text-align:center;padding:48px 24px;position:relative}
.score-ring{width:160px;height:160px;margin:0 auto 24px;position:relative}
.score-ring svg{transform:rotate(-90deg)}
.score-center{position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center}
.score-pct{font-size:36px;font-weight:900}
.score-lbl{font-size:13px;color:#8888BB;font-weight:700}
.result-title{font-size:28px;font-weight:900;margin-bottom:8px}
.result-sub{color:#8888BB;font-size:15px}
.stats-row{display:flex;gap:16px;justify-content:center;margin:28px 0;flex-wrap:wrap}
.stat-chip{background:#1E1E35;border:1px solid #2A2A4A;border-radius:16px;padding:16px 24px;text-align:center;min-width:110px}
.stat-val{font-size:22px;font-weight:900;margin-bottom:4px}
.stat-label{font-size:12px;color:#8888BB;font-weight:700}
.actions{display:flex;gap:12px;justify-content:center;flex-wrap:wrap}
.btn-main{background:linear-gradient(135deg,#6C63FF,#5A51FF);color:#fff;border:none;border-radius:999px;padding:13px 32px;font-weight:900;font-size:15px;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center;gap:8px;transition:all .2s;font-family:inherit}
.btn-main:hover{transform:translateY(-2px)}
.btn-ghost{background:transparent;border:1px solid #2A2A4A;color:#8888BB;border-radius:999px;padding:13px 28px;font-weight:800;font-size:15px;text-decoration:none;display:inline-flex;align-items:center;gap:8px;transition:all .2s}
.btn-ghost:hover{border-color:#6C63FF;color:#6C63FF}
.confetti{position:fixed;pointer-events:none;top:0;left:0;width:100%;height:100%;z-index:999}
.q-review{background:#1E1E35;border:1px solid #2A2A4A;border-radius:14px;padding:18px;margin-bottom:12px}
.q-review.correct{border-color:rgba(0,212,170,.35)}
.q-review.wrong{border-color:rgba(255,101,132,.35)}
.q-review .q-num{font-size:11px;font-weight:800;color:#8888BB;margin-bottom:6px;text-transform:uppercase;letter-spacing:.5px}
.q-review .q-text{font-size:15px;font-weight:700;margin-bottom:10px}
.answer-row{display:flex;align-items:center;gap:8px;font-size:13px;font-weight:700}
.tag-correct{color:#00D4AA}
.tag-wrong{color:#FF6584}
@media (max-width: 600px) {
    .stat-chip { flex: 1 1 40%; }
    .btn-main, .btn-ghost { width: 100%; justify-content: center; }
}
</style>
@endpush

@section('content')
@php
    $pass = $attempt->status === 'pass';
    $pct  = $attempt->percentage;
    $circumference = 2 * pi() * 54;
    $offset = $circumference - ($pct / 100) * $circumference;
    $color  = $pass ? '#00D4AA' : '#FF6584';
@endphp

@if($pass)
<canvas class="confetti" id="confetti"></canvas>
@endif

<div class="result-hero">
    <div class="score-ring">
        <svg width="160" height="160" viewBox="0 0 120 120">
            <circle cx="60" cy="60" r="54" fill="none" stroke="#2A2A4A" stroke-width="10"/>
            <circle cx="60" cy="60" r="54" fill="none" stroke="{{ $color }}" stroke-width="10"
                stroke-dasharray="{{ $circumference }}"
                stroke-dashoffset="{{ $offset }}"
                stroke-linecap="round"
                style="transition:stroke-dashoffset 1.5s ease"/>
        </svg>
        <div class="score-center">
            <div class="score-pct" style="color:{{ $color }}">{{ $pct }}%</div>
            <div class="score-lbl">Score</div>
        </div>
    </div>

    <div class="result-title">
        {{ $pass ? '🎉 You Passed!' : '😅 Keep Trying!' }}
    </div>
    <div class="result-sub">
        {{ $pass ? 'Great job! You earned XP and unlocked the next chapter.' : "Score " . $attempt->quiz->chapter->unlock_threshold . "% or more to pass. You got {$pct}%." }}
    </div>

    <div class="stats-row">
        <div class="stat-chip">
            <div class="stat-val" style="color:#00D4AA">{{ $attempt->score }}</div>
            <div class="stat-label">Correct</div>
        </div>
        <div class="stat-chip">
            <div class="stat-val" style="color:#FF6584">{{ $attempt->total_questions - $attempt->score }}</div>
            <div class="stat-label">Wrong</div>
        </div>
        <div class="stat-chip">
            <div class="stat-val" style="color:#FFD700">+{{ $attempt->xp_earned }}</div>
            <div class="stat-label">XP Earned</div>
        </div>
        <div class="stat-chip">
            <div class="stat-val">{{ $attempt->total_questions }}</div>
            <div class="stat-label">Total Qs</div>
        </div>
    </div>

    <div class="actions">
        @if($pass && $nextChapter)
            <a href="{{ route('student.courses.show', $attempt->quiz->chapter->course_id) }}" class="btn-main">Next Chapter →</a>
        @elseif(!$pass)
            <a href="{{ route('student.quizzes.show', $attempt->quiz_id) }}" class="btn-main">Retry Quiz 🔄</a>
        @endif
        <a href="{{ route('student.dashboard') }}" class="btn-ghost">🏠 Dashboard</a>
    </div>
</div>

<!-- Question review -->
<div style="margin-top:32px">
    <div style="font-size:16px;font-weight:900;margin-bottom:14px">📋 Answer Review</div>
    @foreach($attempt->quiz->questions as $i => $q)
    @php $chosen = ($attempt->answers[$q->id] ?? null); $correct = $q->correct_option; $isRight = $chosen === $correct; @endphp
    <div class="q-review {{ $isRight ? 'correct' : 'wrong' }}">
        <div class="q-num">Q{{ $i+1 }}</div>
        <div class="q-text">{{ $q->question }}</div>
        @if($chosen)
        <div class="answer-row {{ $isRight ? 'tag-correct' : 'tag-wrong' }}">
            {{ $isRight ? '✅' : '❌' }} Your answer: {{ strtoupper($chosen) }}. {{ $q->{'option_'.$chosen} }}
        </div>
        @endif
        @if(!$isRight)
        <div class="answer-row tag-correct" style="margin-top:4px">
            ✅ Correct: {{ strtoupper($correct) }}. {{ $q->{'option_'.$correct} }}
        </div>
        @endif
    </div>
    @endforeach
</div>
@endsection

@if($pass)
@push('scripts')
<script>
// Simple confetti
const canvas=document.getElementById('confetti'),ctx=canvas.getContext('2d');
canvas.width=window.innerWidth;canvas.height=window.innerHeight;
const pieces=Array.from({length:120},()=>({x:Math.random()*canvas.width,y:Math.random()*-200,r:Math.random()*8+4,color:`hsl(${Math.random()*360},90%,65%)`,speed:Math.random()*3+2,angle:Math.random()*360}));
function draw(){ctx.clearRect(0,0,canvas.width,canvas.height);pieces.forEach(p=>{ctx.save();ctx.translate(p.x,p.y);ctx.rotate(p.angle*Math.PI/180);ctx.fillStyle=p.color;ctx.fillRect(-p.r/2,-p.r/2,p.r,p.r);ctx.restore();p.y+=p.speed;p.angle+=2;if(p.y>canvas.height)p.y=-20;});requestAnimationFrame(draw);}
draw();setTimeout(()=>canvas.remove(),5000);
</script>
@endpush
@endif
