@extends('layouts.admin')

@section('title', 'student_details')
@section('admin_nav_students', 'active')
@section('admin_page_title', 'student_details')

@section('admin_content')

{{-- Page Header --}}
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h5 class="fw-semibold mb-0" style="font-size:18px;">Students</h5>
        <p class="mb-0" style="font-size:13px;color:var(--sb-muted);">
            {{ $students->total() }} total students registered
        </p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('admin.student_details.upload-photos') }}"
           class="btn btn-sm text-white d-flex align-items-center gap-2"
           style="font-size:13px;border-radius:7px;background:var(--sb-accent);padding:8px 16px;">
            <i class="bi bi-file-image"></i> Upload Photos
        </a>
        <a href="{{ route('admin.student_details.create') }}"
           class="btn btn-sm text-white d-flex align-items-center gap-2"
           style="font-size:13px;border-radius:7px;background:var(--sb-accent);padding:8px 16px;">
            <i class="bi bi-plus-lg"></i> Add Student
        </a>
    </div>
</div>

{{-- Alerts --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-4" role="alert" style="border-radius:8px;font-size:13.5px;">
        <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 mb-4" role="alert" style="border-radius:8px;font-size:13.5px;">
        <i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Filters --}}
<div class="sb-panel mb-4">
    <div class="p-3">
        <form method="GET" action="{{ route('admin.student_details.index') }}" class="row g-2 align-items-end">
            <div class="col-12 col-md-5">
                <label class="form-label" style="font-size:12px;font-weight:600;color:var(--sb-muted);">Search</label>
                <div class="input-group" style="height:36px;">
                    <span class="input-group-text" style="background:var(--sb-bg);border-color:var(--sb-border);color:var(--sb-muted);font-size:13px;">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}"
                           class="form-control" placeholder="Search and press enter..."
                           style="font-size:13px;border-color:var(--sb-border);">
                </div>
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label" style="font-size:12px;font-weight:600;color:var(--sb-muted);">Class</label>
                <select name="class_id" class="form-select" onchange="this.form.submit()" style="font-size:13px;border-color:var(--sb-border);height:36px;padding-top:6px;padding-bottom:6px;">
                    <option value="">All Classes</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                            {{ $class->standard }} - {{ $class->section }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-3 d-flex gap-2">
                <!-- Filter button removed for automatic submission -->
                @if(request()->hasAny(['search','class_id']))
                    <a href="{{ route('admin.student_details.index') }}"
                       class="btn btn-sm btn-outline-secondary flex-fill"
                       style="font-size:13px;border-radius:7px;height:36px;display:flex;align-items:center;justify-content:center;">
                        Clear
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

{{-- Table --}}
<div class="sb-panel">
    <div class="table-responsive">
        <table class="sb-table">
            <thead>
                <tr>
                    <th style="width: 40px; text-align: center;">
                        <input class="form-check-input" type="checkbox" id="selectAll">
                    </th>
                    <th>Photo</th>
                    <th>Adm. No</th>
                    <th>Student</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Class</th>
                    <th>Fee (Rs.)</th>
                    <th>Period</th>
                    <th>Joined</th>
                    <th style="text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($students as $student)
                <tr>
                    <td style="text-align: center; vertical-align: middle;">
                        <input class="form-check-input student-checkbox" type="checkbox" value="{{ $student->id }}">
                    </td>
                    <td style="text-align: center; vertical-align: middle;">
                        @if(!empty($student->user_img))
                            <img src="{{ asset($student->user_img) }}" alt="Photo" style="width:38px;height:38px;object-fit:cover;border-radius:50%;border:2px solid #fff;box-shadow:0 2px 6px rgba(0,0,0,0.08);">
                        @else
                            <div style="width:38px;height:38px;border-radius:50%;background:#F1F5F9;display:flex;align-items:center;justify-content:center;color:#94A3B8;font-size:16px;margin:auto;">
                                <i class="bi bi-person"></i>
                            </div>
                        @endif
                    </td>
                    <td style="color:var(--sb-muted);font-size:13px;">
                        {{ $student->student->admission_number ?? '—' }}
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="sb-avatar-sm" style="background:#EEF2FF;color:var(--sb-accent);font-weight:700;font-size:12px;">
                                {{ mb_strtoupper(mb_substr($student->name, 0, 2)) }}
                            </div>
                            <span style="font-weight:600;font-size:13.5px;">{{ $student->name }}</span>
                        </div>
                    </td>
                    <td style="color:var(--sb-muted);">{{ $student->email }}</td>
                    <td style="color:var(--sb-muted);">{{ $student->phone ?? '—' }}</td>
                    <td>
                        @if($student->student && $student->student->class)
                            <span style="background:#EEF2FF;color:var(--sb-accent);padding:3px 8px;border-radius:4px;font-size:12px;font-weight:600;">
                                {{ $student->student->class->standard }}
                                -
                                {{ $student->student->class->section }}
                            </span>
                        @else
                            <span style="color:var(--sb-muted);">—</span>
                        @endif
                    </td>
                    <td style="color:var(--sb-text);font-weight:600;font-size:13px;">
                        {{ $student->student && $student->student->fee ? '₹' . number_format($student->student->fee, 2) : '—' }}
                    </td>
                    <td style="color:var(--sb-muted);">{{ $student->student?->fee_period ?? '—' }}</td>

                    <td style="color:var(--sb-muted);font-size:12px;white-space:nowrap;">
                        {{ $student->created_at->format('d M Y') }}
                    </td>
                    <td>
                        <div class="d-flex gap-1 justify-content-end">
                            <a href="{{ route('admin.student_details.show', $student) }}"
                               class="sb-icon-btn" title="View Details"
                               style="width:32px;height:32px;font-size:14px;border-radius:6px;background:#F0FDF4;color:#16A34A;border:1px solid #BBF7D0;">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('admin.student_details.edit', $student) }}"
                               class="sb-icon-btn" title="Edit"
                               style="width:32px;height:32px;font-size:14px;border-radius:6px;">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form method="POST" action="{{ route('admin.student_details.destroy', $student) }}"
                                  onsubmit="return confirm('Delete {{ addslashes($student->name) }}? This cannot be undone.')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="sb-icon-btn" title="Delete"
                                        style="width:32px;height:32px;font-size:14px;border-radius:6px;color:#DC2626;border-color:#FECACA;background:transparent;">
                                    <i class="bi bi-trash3"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="text-center py-5" style="color:var(--sb-muted);">
                        <i class="bi bi-people" style="font-size:32px;display:block;margin-bottom:8px;opacity:0.3;"></i>
                        No students found.
                        <a href="{{ route('admin.student_details.create') }}" style="color:var(--sb-accent);">Add the first one</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($students->hasPages())
    <div class="d-flex align-items-center justify-content-between px-4 py-3" style="border-top:1px solid var(--sb-border);">
        <div style="font-size:13px;color:var(--sb-muted);">
            Showing {{ $students->firstItem() }}–{{ $students->lastItem() }} of {{ $students->total() }} students
        </div>
        <div class="d-flex gap-1">
            @if($students->onFirstPage())
                <span class="sb-icon-btn" style="width:32px;height:32px;border-radius:6px;opacity:0.4;cursor:default;font-size:13px;">
                    <i class="bi bi-chevron-left"></i>
                </span>
            @else
                <a href="{{ $students->previousPageUrl() }}" class="sb-icon-btn" style="width:32px;height:32px;border-radius:6px;font-size:13px;">
                    <i class="bi bi-chevron-left"></i>
                </a>
            @endif

            @foreach($students->getUrlRange(max(1, $students->currentPage()-2), min($students->lastPage(), $students->currentPage()+2)) as $page => $url)
                <a href="{{ $url }}"
                   class="sb-icon-btn"
                   style="width:32px;height:32px;border-radius:6px;font-size:13px;font-weight:600;
                          {{ $page == $students->currentPage() ? 'background:var(--sb-accent);color:#fff;border-color:var(--sb-accent);' : '' }}">
                    {{ $page }}
                </a>
            @endforeach

            @if($students->hasMorePages())
                <a href="{{ $students->nextPageUrl() }}" class="sb-icon-btn" style="width:32px;height:32px;border-radius:6px;font-size:13px;">
                    <i class="bi bi-chevron-right"></i>
                </a>
            @else
                <span class="sb-icon-btn" style="width:32px;height:32px;border-radius:6px;opacity:0.4;cursor:default;font-size:13px;">
                    <i class="bi bi-chevron-right"></i>
                </span>
            @endif
        </div>
    </div>
    @endif
</div>

{{-- Bulk Action Bar --}}
<div id="bulkActionBar" class="position-fixed bottom-0 start-50 translate-middle-x mb-4 bg-white shadow-lg rounded-pill px-4 py-3 d-none align-items-center gap-3" style="z-index: 1050; border: 1px solid var(--sb-border);">
    <span class="fw-semibold text-dark"><span id="selectedCount">0</span> selected</span>
    <div style="width: 1px; height: 20px; background: var(--sb-border);"></div>
    <button type="button" class="btn btn-sm btn-primary rounded-pill px-3" onclick="openGenerateModal()">
        <i class="bi bi-person-badge"></i> Generate ID Cards
    </button>
</div>

{{-- Generate ID Card Modal --}}
<div class="modal fade" id="generateIdCardModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('admin.id_cards.bulkPrint') }}" method="POST" class="modal-content">
            @csrf
            <input type="hidden" name="user_ids" id="modalUserIds">
            <input type="hidden" name="type" value="student">
            
            <div class="modal-header">
                <h5 class="modal-title">Generate ID Cards</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info py-2" style="font-size:13.5px;">
                    <i class="bi bi-info-circle"></i> You are generating ID cards for <strong id="modalSelectedCount">0</strong> student(s).
                </div>
                
                @php
                    $templates = \App\Models\IdCardTemplate::where('institute_id', auth()->user()->institute_id)
                        ->where('status', 'Published')
                        ->where('type', 'student')
                        ->get();
                @endphp
                
                <div class="mb-3">
                    <label class="form-label fw-semibold">Select Template</label>
                    <select name="template_id" class="form-select" required>
                        <option value="">-- Choose Template --</option>
                        @foreach($templates as $tpl)
                            <option value="{{ $tpl->id }}">{{ $tpl->name }}</option>
                        @endforeach
                    </select>
                    @if($templates->isEmpty())
                        <div class="form-text text-danger">No published templates available for students. Please create and publish one first.</div>
                    @endif
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Export Format</label>
                    <select name="export_type" class="form-select" required>
                        <option value="single_pdf">Single PDF (Multiple cards per page for printing)</option>
                        <option value="zip">ZIP Archive (Individual image files)</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary" {{ $templates->isEmpty() ? 'disabled' : '' }}>Generate & Download</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.student-checkbox');
    const bulkActionBar = document.getElementById('bulkActionBar');
    const selectedCount = document.getElementById('selectedCount');

    function updateBulkAction() {
        const checked = document.querySelectorAll('.student-checkbox:checked');
        if(checked.length > 0) {
            bulkActionBar.classList.remove('d-none');
            bulkActionBar.classList.add('d-flex');
            selectedCount.innerText = checked.length;
        } else {
            bulkActionBar.classList.add('d-none');
            bulkActionBar.classList.remove('d-flex');
        }
        
        selectAll.checked = (checked.length === checkboxes.length && checkboxes.length > 0);
    }

    if(selectAll) {
        selectAll.addEventListener('change', function() {
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
            updateBulkAction();
        });
    }

    checkboxes.forEach(cb => {
        cb.addEventListener('change', updateBulkAction);
    });
});

function openGenerateModal() {
    const checked = Array.from(document.querySelectorAll('.student-checkbox:checked')).map(cb => cb.value);
    document.getElementById('modalUserIds').value = checked.join(',');
    document.getElementById('modalSelectedCount').innerText = checked.length;
    
    var myModal = new bootstrap.Modal(document.getElementById('generateIdCardModal'));
    myModal.show();
}
</script>

@endsection
