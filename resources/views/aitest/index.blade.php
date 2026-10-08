@extends('layouts.student')

@section('content')

<style>
    /* Hide the bottom pencil navbar on this page */
    .sidebar { display: none !important; }
    /* Remove the default main container bottom padding reserved for the sidebar */
    .main { padding-bottom: 20px !important; }

    .btn-purple {
        background: linear-gradient(135deg, #a855f7, #9333ea);
        color: white;
        border: none;
        box-shadow: 0 4px 15px rgba(168, 85, 247, 0.4);
    }
    .btn-purple:hover {
        background: linear-gradient(135deg, #9333ea, #7e22ce);
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(147, 51, 234, 0.5);
        color: white;
    }
    .custom-card-hover {
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .custom-card-hover:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
    }
</style>

<div class="container mt-5 position-relative">

    <!-- Back Button -->
    <a href="{{ route('student.assigned_ebooks.details', $assigned_ebook_id) }}" style="position: fixed; top: 20px; left: 10px; z-index: 1000; transition: transform 0.15s;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
        <img src="{{ asset('uploads/images/buttons/Previous button.png') }}" alt="Back" style="height: 52px; object-fit: contain;">
    </a>

    @if(session('error'))
    <div id="error-alert" class="alert alert-danger mb-4 shadow-sm mt-4 mt-md-0" role="alert" style="border-radius: 12px;">
        <strong><i class="fas fa-exclamation-circle me-2"></i>Error!</strong> {{ session('error') }}
    </div>
    @endif

    <!-- BOOK + PAPERS CARD -->
    <div class="card shadow-lg border-0 mt-5 mt-md-4 mb-4" style="border-radius: 25px; overflow: hidden;">
        <div class="row g-0">

            <!-- LEFT: BOOK IMAGE (FIXED) -->
            <div class="col-md-5 col-lg-4 p-3 p-md-4 p-lg-5 border-end d-flex align-items-center justify-content-center" style="background-color: #f8f9fa;">
                @if(isset($data->pages[0]))
                    <a href="{{ $data->ebook_url }}" target="_blank" class="w-100 text-center">
                        <img src="{{ asset($data->pages[0]->url.'/1.jpg') }}"
                             class="img-fluid w-75 w-md-100" style="border-radius: 15px; box-shadow: 0 15px 35px rgba(0,0,0,0.15); transition: transform 0.3s; max-height: 450px;"
                             onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                    </a>
                @endif
            </div>

            <!-- RIGHT: SCROLLABLE CONTENT -->
            <div class="col-md-7 col-lg-8 p-3 p-md-4 p-lg-5">

                <!-- Book Info Top -->
                <div class="mb-4 pb-3 border-bottom text-center text-md-start">
                    <h2 class="bubblegum text-primary mb-2" style="font-size: clamp(1.8rem, 5vw, 2.8rem); letter-spacing: 1px;">
                        {{$data->name}}
                    </h2>
                    <h5 class="text-secondary fw-bold mt-2" style="font-size: clamp(1rem, 3vw, 1.2rem);">
                        <i class="fas fa-book-open me-2 text-info"></i> SUBJECT: {{$data->subject}}
                    </h5>
                    <p class="text-muted mt-2 d-none d-md-block" style="font-size: 1.1rem; line-height: 1.6;">
                        {{$data->remark}}
                    </p>
                </div>

                <!-- Papers -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card shadow-sm border-0" style="border-radius: 20px; background: linear-gradient(135deg, #f3e8ff 0%, #e9d5ff 100%);">
                            <div class="card-body p-4 text-center d-flex flex-column flex-md-row justify-content-between align-items-center">
                                <div class="text-md-start mb-3 mb-md-0">
                                    <h4 class="fw-bold mb-1 bubblegum" style="color: #6b21a8;"><i class="fas fa-robot me-2"></i> AI Test Paper Generator</h4>
                                    <p class="mb-0 text-muted" style="font-size: 0.95rem;">Create a customized test paper instantly using our AI assistant.</p>
                                </div>
                                <a target="" rel="noopener noreferrer" id="aiPaperBtn"
                                   class="btn fw-bold shadow-sm" style="background-color: #9333ea; color: white; font-size: 1.1rem; padding: 12px 30px; border-radius: 30px; transition: all 0.3s ease;">
                                    <i class="fas fa-magic me-2"></i> Generate Paper
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center align-items-md-center mb-4 gap-3 text-center text-md-start">
                    <h3 class="bubblegum mb-0" style="color: #6a1b9a; font-size: 2rem;">
                        <i class="fas fa-file-invoice me-2"></i> Generated Test Papers
                    </h3>
                </div>

                    @if($testPapers->count())
                        <div class="row g-4">
                            @foreach($testPapers as $paper)
                                <div class="col-xl-6">
                                    <div class="card h-100 shadow-sm border-0 custom-card-hover" style="border-radius: 18px; background-color: #ffffff; border: 1px solid #e9ecef !important;">
                                        <div class="card-body p-4 relative">
                                            <div class="position-absolute" style="top: 15px; right: 15px; opacity: 0.1;">
                                                <i class="fas fa-file-alt fa-4x text-primary"></i>
                                            </div>
                                            
                                            <h4 class="fw-bold mb-3 text-dark" style="font-size: 1.4rem;">
                                                Test Paper Set <span class="text-primary">#{{$paper->set_id}}</span>
                                            </h4>
                                            
                                            <p class="text-muted mb-2 fw-semibold" style="font-size: 1.05rem;">
                                                <i class="fas fa-list-ol me-2 text-warning"></i> {{$paper->total_questions}} Questions
                                            </p>
                                            
                                            <p class="text-muted small mb-4">
                                                <i class="far fa-clock me-2 text-secondary"></i> {{ \Carbon\Carbon::parse($paper->created_at)->format('d M Y, h:i A') }}
                                            </p>
                                            
                                            <a href="{{ route('student.aitest.showPaper', ['ebook' => $data->id, 'set' => $paper->set_id]) }}"
                                               target="_blank"
                                               class="btn btn-outline-primary fw-bold w-100" style="border-radius: 20px; padding: 10px;">
                                                Open Paper <i class="fas fa-arrow-right ms-2"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-light text-center shadow-sm" style="border-radius: 20px; border: 2px dashed #dee2e6; padding: 60px 20px;">
                            <i class="fas fa-file-alt fa-4x text-muted mb-4 opacity-50"></i>
                            <h4 class="text-muted fw-bold mb-3">No AI test papers yet!</h4>
                            <p class="mb-0 text-muted" style="font-size: 1.1rem;">Click the <strong class="text-purple">"Generate New AI Test Paper"</strong> button to create your first one.</p>
                        </div>
                    @endif
                </div>

            </div>

        </div>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const aiPaperBtn = document.getElementById('aiPaperBtn');
    if (!aiPaperBtn) return;

    const currentUrl = "{{ $data->ebook_url }}";
    const firstPages = {!! $firstPages ? json_encode($firstPages) : '[]' !!};

    let aiUrl = "{{ route('student.aitest.create') }}";

    if (firstPages.length > 0) {
        const pagesParam = firstPages.length === 1
            ? firstPages[0]
            : firstPages.join(',');

        aiUrl += `?ebook_id={{ $data->id }}&assigned_ebook_id={{ $assigned_ebook_id }}&base_url=${encodeURIComponent(currentUrl)}&pages=${pagesParam}`;
    } else {
        aiUrl += `?ebook_id={{ $data->id }}&assigned_ebook_id={{ $assigned_ebook_id }}&base_url=${encodeURIComponent(currentUrl)}`;
    }

    aiPaperBtn.setAttribute('href', aiUrl);
});
</script>

<script>
    setTimeout(function() {
        var errorAlert = document.getElementById('error-alert');
        if (errorAlert) {
            errorAlert.style.transition = "opacity 0.5s ease";
            errorAlert.style.opacity = "0";
            setTimeout(function() { errorAlert.remove(); }, 500);
        }
    }, 5000);
</script>

@endsection
