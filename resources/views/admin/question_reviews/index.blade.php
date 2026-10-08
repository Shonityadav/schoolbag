@extends('layouts.admin')

@section('title', 'Question Reviews')
@section('admin_page_title', 'Question Reviews')
@section('admin_nav_question_reviews', 'active')

@section('admin_content')
<div class="sb-panel mb-4">
    <div class="sb-panel-header">
        <h2 class="sb-panel-title">Stage 4 Submissions</h2>
    </div>
    
    <div class="p-4">
        <!-- Tabs -->
        <ul class="nav nav-pills mb-4" id="reviewTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="pending-tab" data-bs-toggle="pill" data-bs-target="#pending" type="button" role="tab" aria-controls="pending" aria-selected="true">
                    Pending Reviews <span class="badge bg-danger ms-1">{{ $pendingReviews->count() }}</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="completed-tab" data-bs-toggle="pill" data-bs-target="#completed" type="button" role="tab" aria-controls="completed" aria-selected="false">
                    Completed Reviews <span class="badge bg-success ms-1">{{ $completedReviews->count() }}</span>
                </button>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content" id="reviewTabsContent">
            
            <!-- Pending Tab -->
            <div class="tab-pane fade show active" id="pending" role="tabpanel" aria-labelledby="pending-tab">
                <div class="table-responsive">
                    <table class="sb-table">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Class</th>
                                <th>Subject</th>
                                <th>Level</th>
                                <th>Chapter</th>
                                <th>Submitted At</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pendingReviews as $review)
                            <tr @if(request('highlight') == $review->id) style="background: #FEF3C7; transition: background 2s;" @endif>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="sb-avatar-sm">{{ substr($review->user->name, 0, 1) }}</div>
                                        <span class="fw-semibold">{{ $review->user->name }}</span>
                                    </div>
                                </td>
                                <td>{{ $review->user->studentClass->standard ?? 'N/A' }} {{ $review->user->studentClass->section ?? '' }}</td>
                                <td>{{ $review->subject ?? 'N/A' }}</td>
                                <td>{{ $review->chapter->chapter_number ?? 'N/A' }}</td>
                                <td>{{ $review->chapter->chapter_name ?? 'Stage 4' }}</td>
                                <td>{{ $review->created_at->format('M d, Y h:i A') }}</td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-sm btn-primary" onclick="openReviewModal({{ $review->id }})">
                                        Review & Grade
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">No pending reviews found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Completed Tab -->
            <div class="tab-pane fade" id="completed" role="tabpanel" aria-labelledby="completed-tab">
                <div class="table-responsive">
                    <table class="sb-table">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Class</th>
                                <th>Subject</th>
                                <th>Level</th>
                                <th>Chapter</th>
                                <th>Score</th>
                                <th>Submitted At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($completedReviews as $review)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="sb-avatar-sm">{{ substr($review->user->name, 0, 1) }}</div>
                                        <span class="fw-semibold">{{ $review->user->name }}</span>
                                    </div>
                                </td>
                                <td>{{ $review->user->studentClass->standard ?? 'N/A' }} {{ $review->user->studentClass->section ?? '' }}</td>
                                <td>{{ $review->subject ?? 'N/A' }}</td>
                                <td>{{ $review->chapter->chapter_number ?? 'N/A' }}</td>
                                <td>{{ $review->chapter->chapter_name ?? 'Stage 4' }}</td>
                                <td><span class="sb-badge paid">{{ $review->score }} XP</span></td>
                                <td>{{ $review->created_at->format('M d, Y h:i A') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">No completed reviews found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
        </div>
    </div>
</div>

<!-- Review Modal Template -->
@foreach($pendingReviews as $review)
<div class="modal fade" id="reviewModal{{ $review->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-bottom px-4 py-3" style="background: #F8FAFC;">
                <h5 class="modal-title fw-bold">Review Stage 4: {{ $review->user->name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form action="{{ route('admin.question_reviews.update', $review->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body px-4 py-4" style="background: #F1F5F9; max-height: 60vh; overflow-y: auto;">
                    
                    @php
                        $answers = is_string($review->answers) ? json_decode($review->answers, true) : ($review->answers ?? []);
                        $images = is_string($review->answer_img) ? json_decode($review->answer_img, true) : ($review->answer_img ?? []);
                        
                        $questionIds = is_array($answers) ? array_keys($answers) : [];
                        $questionsMap = \App\Models\EbookQuestion::whereIn('id', $questionIds)->pluck('question', 'id');
                        
                        $lessonChapterOrder = ($review->chapter->chapter_number ?? 1) - 1;
                        $lesson = \App\Models\Lesson::where('order', 3)->whereHas('chapter', function($q) use ($review, $lessonChapterOrder) {
                            $q->where('order', $lessonChapterOrder)->whereHas('course', function($q2) use ($review) {
                                $q2->where('ebook_id', $review->ebook_id);
                            });
                        })->first();
                        $maxScore = $lesson ? $lesson->xp_reward : 10;
                    @endphp
                    
                    @if(empty($answers) && empty($images))
                        <div class="alert alert-warning">No answers submitted in JSON.</div>
                    @else
                        @if(is_array($answers))
                            @foreach($answers as $qId => $answer)
                                <div class="card mb-3 border-0 shadow-sm">
                                    <div class="card-body">
                                        <h6 class="text-muted mb-2">Question {{ $loop->iteration }}</h6>
                                        <p class="fw-semibold mb-2">{{ isset($questionsMap[$qId]) ? $questionsMap[$qId] : "Question text unavailable" }}</p>
                                        <p class="fs-6 mb-0 text-primary"><strong>Answer:</strong> {{ is_array($answer) ? json_encode($answer) : $answer }}</p>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="card mb-3 border-0 shadow-sm">
                                <div class="card-body">
                                    <p class="fs-6 mb-0">{{ $answers }}</p>
                                </div>
                            </div>
                        @endif
                        
                        @if(is_array($images))
                            @foreach($images as $img)
                                <div class="card mb-3 border-0 shadow-sm">
                                    <div class="card-body">
                                        <h6 class="text-muted mb-2">Attached Image</h6>
                                        <img src="{{ asset($img) }}" alt="Answer Image" class="img-fluid rounded">
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    @endif
                    
                </div>
                
                <div class="modal-footer px-4 py-3 border-top" style="background: #F8FAFC; justify-content: space-between;">
                    <div class="d-flex align-items-center gap-3">
                        <label for="score{{ $review->id }}" class="fw-semibold mb-0 text-dark">Assign Score (XP) [Max: {{ $maxScore }}]:</label>
                        <input type="number" name="score" id="score{{ $review->id }}" class="form-control text-center" style="width: 100px; font-weight: bold; border-color: #CBD5E1;" value="{{ min($review->score ?? $maxScore, $maxScore) }}" min="0" max="{{ $maxScore }}" required>
                    </div>
                    <div>
                        <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary px-4 fw-semibold">Save Score</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<script>
    function openReviewModal(id) {
        var modal = new bootstrap.Modal(document.getElementById('reviewModal' + id));
        modal.show();
    }
</script>
@endsection
