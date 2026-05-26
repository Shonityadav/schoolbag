@extends('layouts.student')
@section('title', $quiz->title)

@push('styles')
<style>
.back-link{display:inline-flex;align-items:center;gap:6px;color:#8888BB;text-decoration:none;font-size:14px;font-weight:700;margin-bottom:20px}
.back-link:hover{color:#6C63FF}
.quiz-header{background:linear-gradient(135deg,rgba(255,213,0,.12),rgba(255,101,132,.08));border:1px solid rgba(255,213,0,.25);border-radius:18px;padding:24px 28px;margin-bottom:24px;display:flex;align-items:center;gap:16px}
.quiz-header h1{font-size:20px;font-weight:900;margin-bottom:6px}
.quiz-meta{font-size:13px;color:#8888BB;display:flex;gap:14px;flex-wrap:wrap}
.timer{background:rgba(255,213,0,.15);border:1px solid rgba(255,213,0,.3);border-radius:999px;padding:6px 18px;font-size:15px;font-weight:900;color:#FFD700;display:inline-flex;align-items:center;gap:6px}
.q-card{background:#1E1E35;border:1px solid #2A2A4A;border-radius:16px;padding:24px;margin-bottom:14px;transition:border-color .2s}
.q-card:focus-within{border-color:rgba(108,99,255,.4)}
.q-number{font-size:12px;font-weight:800;color:#6C63FF;margin-bottom:8px;text-transform:uppercase;letter-spacing:.5px}
.q-text{font-size:16px;font-weight:700;margin-bottom:18px;line-height:1.6}
.options{display:grid;grid-template-columns:1fr 1fr;gap:10px}
.option{position:relative}
.option input[type=radio]{position:absolute;opacity:0;width:0;height:0}
.option label{display:flex;align-items:center;gap:12px;padding:12px 16px;background:rgba(255,255,255,.04);border:2px solid #2A2A4A;border-radius:12px;cursor:pointer;font-size:14px;font-weight:700;transition:all .2s}
.option label:hover{border-color:#6C63FF;background:rgba(108,99,255,.1)}
.option input[type=radio]:checked+label{border-color:#6C63FF;background:rgba(108,99,255,.18);color:#A89FFF}
.opt-letter{width:28px;height:28px;border-radius:50%;background:#2A2A4A;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:900;flex-shrink:0}
option input[type=radio]:checked+label .opt-letter{background:#6C63FF;color:#fff}
.submit-row{display:flex;align-items:center;justify-content:space-between;margin-top:24px;padding:20px 24px;background:#1E1E35;border:1px solid #2A2A4A;border-radius:16px}
.btn-submit{background:linear-gradient(135deg,#FFD700,#FFA500);color:#1E1E35;border:none;border-radius:999px;padding:14px 36px;font-weight:900;font-size:16px;cursor:pointer;transition:all .2s;font-family:inherit}
.btn-submit:hover{transform:translateY(-2px);box-shadow:0 8px 24px rgba(255,213,0,.4)}
.progress-bar{height:6px;background:#2A2A4A;border-radius:999px;overflow:hidden;margin-bottom:20px}
.progress-fill{height:100%;background:linear-gradient(90deg,#FFD700,#FFA500);border-radius:999px;transition:width .3s}
@media (max-width: 600px) {
    .quiz-header { flex-direction: column; text-align: center; }
    .quiz-meta { justify-content: center; }
    .options { grid-template-columns: 1fr; }
    .submit-row { flex-direction: column; gap: 14px; text-align: center; }
    .btn-submit { width: 100%; }
}
</style>
@endpush

@section('content')
<a href="{{ route('student.courses.show', $quiz->chapter->course_id) }}" class="back-link">← {{ $quiz->chapter->course->title }}</a>

<div class="quiz-header">
    <div style="font-size:48px">🎯</div>
    <div style="flex:1">
        <h1>{{ $quiz->title }}</h1>
        <div class="quiz-meta">
            <span>📝 {{ $quiz->questions->count() }} questions</span>
            <span>⏱ {{ $quiz->time_limit_minutes }} minutes</span>
            <span>⚡ +{{ $quiz->xp_reward }} XP on pass</span>
            <span>🎯 Pass threshold: {{ $quiz->chapter->unlock_threshold }}%</span>
        </div>
    </div>
    <div class="timer" id="timer">⏱ <span id="time">{{ $quiz->time_limit_minutes }}:00</span></div>
</div>

@if($bestAttempt)
<div style="background:rgba(0,212,170,.08);border:1px solid rgba(0,212,170,.2);border-radius:12px;padding:12px 18px;margin-bottom:20px;font-size:13px;font-weight:700;color:#00D4AA;display:flex;align-items:center;gap:8px">
    🏆 Best score: {{ $bestAttempt->percentage }}% ({{ $bestAttempt->status === 'pass' ? 'Passed ✓' : 'Failed' }}) — retake to improve!
</div>
@endif

<div class="progress-bar"><div class="progress-fill" id="progressFill" style="width:0%"></div></div>

<form method="POST" action="{{ route('student.quizzes.submit', $quiz->id) }}" id="quizForm">
    @csrf
    @foreach($quiz->questions as $i => $q)
    <div class="q-card" id="q{{ $q->id }}" data-index="{{ $i }}">
        <div class="q-number">Question {{ $i + 1 }} of {{ $quiz->questions->count() }}</div>
        <div class="q-text">{{ $q->question }}</div>
        <div class="options">
            @foreach($q->options as $key => $text)
            <div class="option">
                <input type="radio" name="answers[{{ $q->id }}]" id="q{{ $q->id }}_{{ $key }}" value="{{ $key }}" required>
                <label for="q{{ $q->id }}_{{ $key }}">
                    <span class="opt-letter">{{ strtoupper($key) }}</span>
                    {{ $text }}
                </label>
            </div>
            @endforeach
        </div>
    </div>
    @endforeach

    <div class="submit-row">
        <div style="color:#8888BB;font-size:13px;font-weight:700">Answer all questions before submitting</div>
        <button type="submit" class="btn-submit">Submit Quiz 🚀</button>
    </div>
</form>
@endsection

@push('scripts')
<script>
// Timer
let totalSeconds = {{ $quiz->time_limit_minutes * 60 }};
const timerEl = document.getElementById('time');
const interval = setInterval(() => {
    totalSeconds--;
    if (totalSeconds <= 0) { clearInterval(interval); document.getElementById('quizForm').submit(); return; }
    const m = Math.floor(totalSeconds/60), s = totalSeconds%60;
    timerEl.textContent = `${m}:${s.toString().padStart(2,'0')}`;
    if (totalSeconds < 60) timerEl.style.color = '#FF6584';
}, 1000);

// Progress tracker
const total = {{ $quiz->questions->count() }};
function updateProgress() {
    let answered = 0;
    for (let i=1; i<=total; i++) {
        const name = `answers[${document.querySelectorAll('input[type=radio]')[0].name.match(/\[(\d+)\]/)[1]}]`;
    }
    const radios = document.querySelectorAll('input[type=radio]:checked');
    const unique = new Set([...radios].map(r => r.name));
    document.getElementById('progressFill').style.width = `${(unique.size/total)*100}%`;
}
document.querySelectorAll('input[type=radio]').forEach(r => r.addEventListener('change', updateProgress));
</script>
@endpush
