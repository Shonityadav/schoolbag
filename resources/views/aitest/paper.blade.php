@extends('aitest.layout')

@section('content')

<main class="container mx-auto px-6 py-12 md:py-16">

<div class="max-w-5xl mx-auto bg-white rounded-3xl shadow-xl p-8" id="paperArea">

    <!-- HEADER -->
    <div class="border-b pb-6 mb-8 flex justify-between items-start">
        <div>
            <h1 class="text-3xl font-bold text-slate-900">
                {{$ebook->name}} – AI Test Paper (Set {{$setId}})
            </h1>
            <p class="text-slate-500 mt-2">Subject: {{$ebook->subject}}</p>
        </div>

        <div class="flex gap-3 no-print">
            <button onclick="openAnswerModal()"
                class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-indigo-700">
                Answer Sheet
            </button>

            <button onclick="window.print()"
                class="bg-emerald-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-emerald-700">
                Print
            </button>
        </div>
    </div>

    @php
        $grouped = $questions->groupBy(fn($q) => $q->questionType->name ?? 'Other');
    @endphp

    @foreach($grouped as $type => $qs)
        <div class="mb-12 question-group border rounded-2xl p-6 relative">

            <!-- SECTION HEADER -->
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-xl font-bold text-indigo-700">
                        {{$type}} Questions
                    </h2>
                    <p class="text-sm text-slate-500 type-summary"></p>
                </div>

                <div class="flex items-center gap-4 no-print">
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-slate-600">Marks each:</span>
                        <input type="number"
                               value="{{ $qs->first()->questionType->marks ?? 1 }}"
                               class="section-marks w-16 border rounded px-2 py-1 text-sm">
                    </div>

                    <button onclick="toggleType(this)"
                        class="section-toggle text-red-500 text-sm font-semibold hover:underline">
                        Remove Section
                    </button>
                </div>
            </div>

            @foreach($qs as $q)
                <div class="mb-6 question-item relative" data-id="{{$q->id}}">
                    
                    <button onclick="toggleQuestion(this)"
                        class="question-toggle absolute right-0 top-0 text-red-500 text-xs font-semibold hover:underline no-print">
                        Remove
                    </button>

                    <h3 class="font-medium text-lg text-slate-800 pr-20">
                        <span class="q-number"></span>. {!! nl2br(e($q->question)) !!}
                    </h3>

                    @if(!empty($q->options))
                        @php $options = json_decode($q->options, true); @endphp
                        @if(is_array($options))
                            <ul class="mt-2 space-y-1 text-slate-700">
                                @foreach($options as $key => $opt)
                                    @if(is_string($key))
                                        <li><strong>{{$key}}.</strong> {{$opt}}</li>
                                    @else
                                        <li>• {{$opt}}</li>
                                    @endif
                                @endforeach
                            </ul>
                        @endif
                    @endif

                    @if($q->diagrams)
                        <img src="{{ asset('storage/'.$q->diagrams) }}"
                             class="mt-3 rounded-lg max-h-64">
                    @endif

                    <div class="answer hidden"
                         data-answer="{{ e($q->answer_text ?? $q->answer) }}">
                    </div>
                </div>
            @endforeach

        </div>
    @endforeach

</div>
</main>

<!-- ANSWER MODAL -->
<div id="answerModal"
     class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">

    <div class="bg-white w-full max-w-4xl rounded-2xl max-h-[85vh] overflow-y-auto relative">

        <div class="sticky top-0 bg-white border-b p-4 flex justify-between items-center z-10">
            <h2 class="text-xl font-bold">Answer Key</h2>

            <div class="flex gap-3">
                <button onclick="exportAnswers()"
                    class="bg-emerald-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-emerald-700">
                    Export
                </button>

                <button onclick="closeAnswerModal()"
                    class="text-red-500 text-xl font-bold">
                    ✕
                </button>
            </div>
        </div>

        <div id="answerContent" class="p-6 space-y-4 text-slate-700"></div>
    </div>
</div>

<style>
.no-print { display:block; }

/* Removed question container */
.question-item.removed {
    background-color: #f8fafc;
    border: 2px dashed #cbd5e1;
}

/* Fade ONLY the content inside question */
.question-item.removed h3,
.question-item.removed ul,
.question-item.removed img {
    opacity: 0.4;
}
/* Question toggle must always stay above content */
.question-toggle {
    position: absolute;
    z-index: 10;
    pointer-events: auto;
}

/* When question removed, keep restore button clickable */
.question-item.removed .question-toggle {
    opacity: 1 !important;
    pointer-events: auto !important;
}

/* Removed section visual */
/* Removed section style */
.question-group.removed {
    background-color: #f8fafc;
    border: 2px dashed #cbd5e1;
}

/* Fade only the content, NOT buttons */
.question-group.removed .question-item,
.question-group.removed h2,
.question-group.removed .type-summary {
    opacity: 0.5;
}

/* Disable question buttons in removed section */
.question-group.removed .question-toggle {
    pointer-events: none;
    cursor: not-allowed;
}

/* Restore buttons (green + full visibility) */
.restore-btn {
    color: #16a34a !important;
    opacity: 1 !important;
}

@media print {
    .no-print { display:none !important; }
    .question-item.removed,
    .question-group.removed { display:none !important; }
}
</style>

<script>

function updateSectionMarks() {
    document.querySelectorAll('.question-group').forEach(group => {

        const markEach = parseInt(group.querySelector('.section-marks').value) || 0;
        const count = group.querySelectorAll('.question-item:not(.removed)').length;
        const total = markEach * count;

        group.querySelector('.type-summary').innerText =
            count ? `(${markEach} × ${count} = ${total} Marks)` : '';
    });
}

function renumberQuestions() {
    let n = 1;
    document.querySelectorAll('.question-group:not(.removed) .question-item:not(.removed)')
        .forEach(q => q.querySelector('.q-number').innerText = n++);
}

function toggleQuestion(btn) {
    const item = btn.closest('.question-item');
    item.classList.toggle('removed');

    if (item.classList.contains('removed')) {
        btn.textContent = 'Restore';
        btn.classList.add('restore-btn');
    } else {
        btn.textContent = 'Remove';
        btn.classList.remove('restore-btn');
    }

    renumberQuestions();
    updateSectionMarks();
}

function toggleType(btn) {
    const group = btn.closest('.question-group');
    group.classList.toggle('removed');

    if (group.classList.contains('removed')) {
        btn.textContent = 'Restore Section';
        btn.classList.add('restore-btn');
    } else {
        btn.textContent = 'Remove Section';
        btn.classList.remove('restore-btn');
    }

    renumberQuestions();
    updateSectionMarks();
}

function openAnswerModal() {
    const modal = document.getElementById('answerModal');
    const container = document.getElementById('answerContent');
    container.innerHTML = '';

    let count = 1;

    document.querySelectorAll('.question-group:not(.removed) .question-item:not(.removed)')
        .forEach(el => {
            const ans = el.querySelector('.answer').dataset.answer;
            container.innerHTML += `
                <div>
                    <strong>Q${count++}:</strong>
                    <div class="mt-1 text-slate-600">${ans}</div>
                </div>`;
        });

    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeAnswerModal() {
    document.getElementById('answerModal').classList.add('hidden');
}

function exportAnswers() {
    let content = '';
    let count = 1;

    document.querySelectorAll('.question-group:not(.removed) .question-item:not(.removed)')
        .forEach(el => {
            content += `Q${count++}: ${el.querySelector('.answer').dataset.answer}\n\n`;
        });

    const blob = new Blob([content], {type:'text/plain'});
    const a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = 'AnswerKey.txt';
    a.click();
}

document.querySelectorAll('.section-marks')
    .forEach(i => i.addEventListener('input', updateSectionMarks));

renumberQuestions();
updateSectionMarks();

</script>

@endsection
