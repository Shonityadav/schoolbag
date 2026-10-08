<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>AI Test Paper Generator</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="ebook-id" content="{{ $ebook_id }}">
  <meta name="assigned-ebook-id" content="{{ $assigned_ebook_id }}">

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />

  <!-- FontAwesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet" />

  <!-- Custom CSS -->
  <link rel="stylesheet" href="{{ asset('aitest/style.css') }}" /> 
  <style>
    /* ── Quit Modal Styles (Matching Stage Map) ── */
    .ch-overlay { position: fixed; inset: 0; background: rgba(15,23,42,0.75); backdrop-filter: blur(8px); z-index: 2000; opacity: 1; transition: opacity 0.3s ease; display: flex; }
    .ch-overlay.hidden { opacity: 0; pointer-events: none; }
    .ch-overlay.hidden .ch-modal { transform: scale(0.9); }
    .ch-modal { background: #FFF9E5; border: 4px solid #FFE8AC !important; box-shadow: 0 24px 48px rgba(0,0,0,0.25); transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
    .ch-title { font-family: 'Bubblegum Sans', cursive; font-size: 26px; color: #5E4D3B; }
  </style>
</head>

<body>
  <!-- Quit Confirmation Modal -->
  <div id="quit-modal" class="ch-overlay justify-content-center align-items-center hidden" onclick="if(event.target===this) closeQuitModal()">
      <div class="ch-modal position-relative rounded-4 p-4 text-center" style="width:90%; max-width:400px;">
          <h3 class="ch-title mb-3" style="color: #ef4444;">Are you sure?</h3>
          <p style="font-family: 'Quicksand', sans-serif; font-size: 16px; font-weight: 600; color: #5E4D3B;" class="mb-4">
              Do you want to quit? Your generated test paper progress will be lost.
          </p>
          <div class="d-flex justify-content-center gap-3">
              <button class="btn btn-secondary rounded-pill px-4 fw-bold" onclick="closeQuitModal()" style="font-family: 'Quicksand', sans-serif;">Cancel</button>
              <a href="{{ route('student.assigned_ebooks.details', $assigned_ebook_id) }}" class="btn btn-danger rounded-pill px-4 fw-bold" style="font-family: 'Quicksand', sans-serif; background: #ef4444; border: none;">Quit</a>
          </div>
      </div>
  </div>
  <div class="container-fluid p-3 d-flex align-items-center shadow-sm" style="background: #ffffff; position: sticky; top: 0; z-index: 1020; border-bottom: 1px solid var(--border-color);">
      <button onclick="handleBackButton(event, '{{ route('student.assigned_ebooks.details', $assigned_ebook_id) }}')" class="btn rounded-circle shadow-sm" style="width: 45px; height: 45px; display: inline-flex; align-items: center; justify-content: center; border: none; background: #0ea5e9; transition: transform 0.2s ease;">
          <i class="fas fa-arrow-left text-white"></i>
      </button>
      <h4 class="mb-0 ms-3 fw-bold" style="font-family: 'Bubblegum Sans', cursive; color: #6a1b9a; letter-spacing: 1px;">AI Test Paper Generator</h4>
  </div>

  <div class="page-container">
    <!-- Vertical wizard indicator -->
    <div class="wizard-steps-vertical" id="wizard-steps">
      <div class="step-dot" data-step="1" title="1 — Chapters">
        <span>1</span>
      </div>
      <div class="step-line-vertical"></div>
      <div class="step-dot" data-step="2" title="2 — Format">
        <span>2</span>
      </div>
      <div class="step-line-vertical"></div>
      <div class="step-dot" data-step="3" title="3 — Review & Build">
        <span>3</span>
      </div>
      <div class="step-line-vertical"></div>
      <div class="step-dot" data-step="4" title="4 — Final Layout">
        <span>4</span>
      </div>
    </div>

    <!-- PAGE 1: Chapters -->
    <div id="page-1-chapter-selection">
      <div class="text-center">
        <h1 class="page-title" id="page-title">Test Paper Generator</h1>
        <p class="page-subtitle" id="page-subtitle">
          Step 1 — Select chapters
        </p>
      </div>

      <!-- Container will be populated by API -->
      <div class="single-subject-container" id="subject-card-container">
        <!-- JS will inject the subject card here -->
      </div>

      <div class="text-center mt-4">
        <button id="fetchQuestionsBtn" class="btn btn-primary">
          Next: Format Paper <i class="fas fa-arrow-right ms-2"></i>
        </button>
      </div>
    </div>

    <!-- PAGE 2: Format -->
    <div id="page-2-format" class="d-none">
      <div class="text-center">
        <h2 class="page-title">Step 2 — Format Paper</h2>
        <p class="page-subtitle">Set counts, exam type, and duration</p>
      </div>

      <div class="main-format-card">
        <div class="row g-3 justify-content-center">
          <div class="col-md-3">
            <label class="form-label">Exam Type</label>
            <select id="selectExamType" class="form-select">
              <option value="Class Test">Class Test</option>
              <option value="Sessional Exam">Sessional Exam</option>
              <option value="Pre-Board Exam">Pre-Board Exam</option>
              <option value="Board Exam">Board Exam</option>
              <option value="Final Exam">Final Exam</option>
              <option value="Custom">Custom...</option>
            </select>
          </div>

          <div class="col-md-2">
            <label class="form-label">Duration (min)</label>
            <input id="inputDuration" type="number" class="form-control" min="5" value="30" />
          </div>

          <!-- ADDED: Date field from old HTML -->
          <div class="col-md-2">
            <label for="inputTestDate" class="form-label">Test Date</label>
            <input type="date" class="form-control" id="inputTestDate">
          </div>

          <div class="col-md-3">
            <label class="form-label">Total Questions</label>
            <input id="totalQuestions" type="number" class="form-control" min="1" value="10" />
          </div>

          <div class="col-md-2">
            <label class="form-label">MCQ</label>
            <input id="numMCQ" type="number" class="form-control" min="0" value="5" />
          </div>
          <div class="col-md-2">
            <label class="form-label">Short</label>
            <input id="numShort" type="number" class="form-control" min="0" value="2" />
          </div>
          <div class="col-md-2">
            <label class="form-label">Long</label>
            <input id="numLong" type="number" class="form-control" min="0" value="1" />
          </div>

          <div class="col-md-2">
            <label class="form-label">True/False</label>
            <input id="numTF" type="number" class="form-control" min="0" value="1" />
          </div>
          <div class="col-md-3">
            <label class="form-label">Fill in the Blanks</label>
            <input id="numFITB" type="number" class="form-control" min="0" value="1" />
          </div>

          <div class="col-12">
            <div id="format-validation" class="small text-danger text-center d-none"></div>
          </div>

          <div class="col-12 text-center mt-3">
            <a href="#" id="backFromFormatBtn" class="btn btn-secondary me-2"><i class="fas fa-arrow-left me-1"></i>
              Back</a>
            <button id="toReviewBtn" class="btn btn-primary">
              Next: Review & Build <i class="fas fa-arrow-right ms-2"></i>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- PAGE 3: REVIEW & BUILD -->
    <div id="page-3-review" class="d-none">
      <div class="text-center">
        <h2 class="page-title">Step 3 — Review & Build Paper</h2>
        <p class="page-subtitle">
          Click AI-generated questions on the left to add them to the paper (right).
        </p>
      </div>

      <div class="content-wrapper-2-col mt-3">
        <aside class="question-bank-col">
          <div class="sidebar-header">AI Question Bank</div>

          <div class="question-bank-controls mb-2">
            <p class="form-label">Filter</p>
            <div id="question-bank-filters" class="btn-group w-100" role="group">
              <button type="button" class="btn btn-sm active" data-filter="All">
                All
              </button>
              <button type="button" class="btn btn-sm" data-filter="mcq">
                MCQ
              </button>
              <button type="button" class="btn btn-sm" data-filter="short">
                Short
              </button>
              <button type="button" class="btn btn-sm" data-filter="long">
                Long
              </button>
              <button type="button" class="btn btn-sm" data-filter="tf">
                T/F
              </button>
              <button type="button" class="btn btn-sm" data-filter="fitb">
                FITB
              </button>
            </div>
          </div>

          <div id="question-bank-list" class="mt-2"></div>
        </aside>

        <main class="main-content-col-wrapper">
          <div class="paper-controls">
            <div class="row g-3">
              <div class="col-12 d-flex justify-content-between align-items-center">
                <div>
                  <a href="#" id="backToFormatBtn" class="btn btn-secondary btn-sm"><i
                      class="fas fa-arrow-left me-2"></i> Back to Step 2</a>
                </div>
                <button id="toFinalLayoutBtn" class="btn btn-primary btn-sm">
                  Next: Final Layout <i class="fas fa-arrow-right ms-2"></i>
                </button>
              </div>

              <div class="col-12">
                <label for="sectionName" class="form-label">Add Section Header</label>
                <div class="input-group">
                  <input id="sectionName" type="text" class="form-control" placeholder="e.g., Section A" />
                  <button id="addSectionBtn" class="btn btn-secondary" type="button">
                    <i class="fas fa-plus"></i> Add
                  </button>
                </div>
              </div>

            </div>
          </div>

          <div class="paper-preview">
            <div class="preview-body">
              <!-- PAPER HEADER (on-screen) -->
              <div id="print-header" class="paper-header">
                <h2 id="print-pub-name">My Awesome Publication</h2>
                <h4 id="print-subject">Subject Name (Class)</h4>
                <div class="print-meta-bar">
                  <span id="print-marks">Total Marks: 0</span>
                  <span id="print-duration">Duration: 30 minutes</span>
                  <span id="print-date">Date: --</span>
                </div>
                <div id="print-counts" class="small text-muted mt-2"></div>
              </div>

              <!-- QUESTIONS -->
              <div id="paper-questions-list"></div>
            </div>
          </div>
        </main>
      </div>

    </div>

    <!-- PAGE 4: FINAL LAYOUT & PRINT -->
    <div id="page-4-final" class="d-none">
      <div class="text-center">
        <h2 class="page-title">Step 4 — Final Layout & Print</h2>
        <p class="page-subtitle">
          Drag questions here to reorder. Use the Print button to print only the paper.
        </p>
      </div>

      <div class="final-preview-container">
        <div class="final-preview-controls text-center mb-3 d-flex justify-content-center gap-2">
          <button id="backToBuildBtn" class="btn btn-secondary"><i class="fas fa-arrow-left me-2"></i> Back to Build</button>
          <button id="finalPrintBtn" class="btn btn-success"><i class="fas fa-print me-2"></i> Print Paper</button>
          <button id="savePaperBtn" class="btn btn-primary"><i class="fas fa-save me-2"></i> Save & Finish</button>
        </div>

        <div class="paper-preview" id="final-paper-preview">
          <div class="preview-body">
            <!-- This header will be populated by JS for printing -->
            <div id="final-print-header" class="final-print-header"></div>
            <!-- Questions will be populated by JS -->
            <div id="final-paper-questions-list"></div>
          </div>
        </div>
      </div>

    </div>

  </div>
  <!-- page-container -->

  <!-- === PARAM MODAL === -->
  <div class="modal fade" id="paramModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content shadow-lg border-0" style="background-color: #ffffff; border-radius: 20px; overflow: hidden;">
        <div class="modal-header text-white" style="background: linear-gradient(135deg, #0ea5e9 0%, #9333ea 100%); border: none;">
          <h5 class="modal-title fw-bold" style="font-family: 'Bubblegum Sans', cursive; letter-spacing: 1px;"><i class="fas fa-book-open me-2"></i> E-book Details Required</h5>
        </div>
        <div class="modal-body p-4" style="color: #475569;">
          <form id="paramForm" onsubmit="return false;">
            <!-- WRAPPED in a div -->
            <div id="baseUrlInputGroup">
              <p class="small mb-3">Please provide the E-book URL  to begin.</p>
              <div class="mb-3">
                <label for="inputBaseUrl" class="form-label">Base URL</label>
                <input type="text" class="form-control" id="inputBaseUrl" placeholder="https://example.com/myebook/75">
                <div class="invalid-feedback">Please enter a valid URL.</div>
              </div>
            </div>
            <!-- WRAPPED in a div -->
            <div id="indexPagesInputGroup">
              <p class="small mb-3">Please provide the page number  to begin.</p>
              <div class="mb-3">
                <label for="inputIndexPages" class="form-label">Index Page Numbers</label>
                <input type="text" class="form-control" id="inputIndexPages" placeholder="e.g., 1,2,3">
                <div class="invalid-feedback">Please enter page numbers.</div>
              </div>
            </div>

          </form>
        </div>
        <div class="modal-footer" style="border-top: none; padding: 1.5rem;">
          <button type="button" class="btn text-white fw-bold shadow-sm" style="background-color: #0ea5e9; border-radius: 30px; padding: 10px 25px;" id="submitParamModalBtn">Load Chapters <i class="fas fa-arrow-right ms-2"></i></button>
        </div>
      </div>
    </div>
  </div>
  <!-- === END PARAM MODAL === -->


  <!-- === MODAL (for alerts) === -->
  <!-- This wrapper was missing -->
  <div class="modal fade" id="alertModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content" style="background-color: var(--panel-bg); border-color: var(--border-color);">
        <div class="modal-header bg-danger text-white" id="alertModalHeader">
          <h5 class="modal-title">⚠️ Alert</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body" id="alertModalBody" style="color: var(--primary-color);">
          <!-- Error message will be injected here -->
        </div>
        <div class="modal-footer" style="border-top-color: var(--border-color);">
          <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
        </div>
      </div>
    </div>
  </div>
  <!-- === END MODAL === -->


  <!-- Sortable.js -->
  <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <!-- App JS -->
  <script src="{{ asset('aitest/script.js') }}"></script>

</body>

</html>
