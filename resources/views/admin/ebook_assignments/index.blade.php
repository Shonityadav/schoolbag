@extends('layouts.admin')

@section('title', 'Ebook Assignments')
@section('admin_nav_ebooks', 'active')
@section('admin_page_title', 'Ebook Assignments')

@section('admin_content')

{{-- Page Header --}}
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h5 class="fw-semibold mb-0" style="font-size:18px;">Assign Ebooks to Classes</h5>
        <p class="mb-0" style="font-size:13px;color:var(--sb-muted);">
            {{ $ebooks->total() }} total ebooks found
        </p>
    </div>
    <a href="{{ route('admin.classes.index') }}" class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-2" style="font-size:13px;border-radius:7px;height:36px;">
        <i class="bi bi-arrow-left"></i> Back to Classes
    </a>
</div>

{{-- Alerts --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-4" role="alert" style="border-radius:8px;font-size:13.5px;">
        <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
    </div>
@endif
@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 mb-4" role="alert" style="border-radius:8px;font-size:13.5px;">
        <i class="bi bi-exclamation-triangle-fill"></i> Please select at least one ebook and a class.
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Search Filter --}}
<div class="sb-panel mb-4">
    <div class="p-3">
        <form method="GET" action="{{ route('admin.ebook_assignments.index') }}" class="row g-2 align-items-end">

            <div class="col-12 col-md-3">
                <label class="form-label" style="font-size:12px;font-weight:600;color:var(--sb-muted);">Subject</label>
                <select name="subject" class="form-select" onchange="this.form.submit()" style="font-size:13px;height:36px;border-color:var(--sb-border);">
                    <option value="">All Subjects</option>
                    @foreach($filters['subjects'] as $subject)
                        <option value="{{ $subject }}" {{ request('subject') == $subject ? 'selected' : '' }}>{{ $subject }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label" style="font-size:12px;font-weight:600;color:var(--sb-muted);">Publication</label>
                <select name="publication" class="form-select" onchange="this.form.submit()" style="font-size:13px;height:36px;border-color:var(--sb-border);">
                    <option value="">All Publications</option>
                    @foreach($filters['publications'] as $pub)
                        <option value="{{ $pub }}" {{ request('publication') == $pub ? 'selected' : '' }}>{{ $pub }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label" style="font-size:12px;font-weight:600;color:var(--sb-muted);">Class/Standard</label>
                <select name="standard" class="form-select" onchange="this.form.submit()" style="font-size:13px;height:36px;border-color:var(--sb-border);">
                    <option value="">All Standards</option>
                    @foreach($filters['standards'] as $std)
                        <option value="{{ $std }}" {{ request('standard') == $std ? 'selected' : '' }}>{{ $std }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-3 d-flex gap-2">
                <noscript>
                    <button type="submit" class="btn btn-sm text-white flex-fill"
                            style="font-size:13px;border-radius:7px;background:var(--sb-accent);height:36px;">
                        <i class="bi bi-funnel"></i> Filter
                    </button>
                </noscript>
                @if(request()->hasAny(['subject', 'publication', 'standard']))
                    <a href="{{ route('admin.ebook_assignments.index', request('class_id') ? ['class_id' => request('class_id')] : []) }}"
                       class="btn btn-sm btn-outline-secondary flex-fill"
                       style="font-size:13px;border-radius:7px;height:36px;display:flex;align-items:center;justify-content:center;">
                        Clear
                    </a>
                @endif
                <button type="submit" form="assignEbooksForm" class="btn btn-sm text-white flex-fill"
                        style="font-size:13px;border-radius:7px;background:var(--sb-accent);height:36px;font-weight:600;">
                    <i class="bi bi-check2-circle"></i> Assign Now
                </button>
            </div>

            <div class="col-12 mt-3 pt-2" style="border-top: 1px dashed var(--sb-border);">
                <div class="d-flex align-items-center gap-2">
                    <label class="form-label mb-0" style="font-size:13px;font-weight:600;color:var(--sb-accent);white-space:nowrap;">Assign To Class:</label>
                    <select name="class_id" class="form-select form-select-sm" onchange="this.form.submit()" required style="width:250px;font-size:13px;border-color:var(--sb-accent);background-color:#F8FAFC;">
                        <option value="">-- Select Class --</option>
                        @foreach($classes as $cls)
                            <option value="{{ $cls->id }}" {{ request('class_id') == $cls->id ? 'selected' : '' }}>
                                Class {{ $cls->standard }} {{ $cls->section }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Assignment Form & Table --}}
<form id="assignEbooksForm" action="{{ route('admin.ebook_assignments.assign') }}" method="POST">
    @csrf
    @if(request('class_id'))
        <input type="hidden" name="class_id" value="{{ request('class_id') }}">
    @endif

    <div class="sb-panel">
        <div class="table-responsive">
            <table class="sb-table">
                <thead>
                    <tr>
                        <th style="width: 50px;">
                            <input class="form-check-input" type="checkbox" id="selectAll">
                        </th>
                        <th>Name</th>
                        <th>Subject</th>
                        <th>Standard</th>
                        <th>Publication</th>
                        <th>Currently Assigned To</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ebooks as $ebook)
                    <tr>
                        <td>
                            <input class="form-check-input ebook-checkbox" type="checkbox" name="ebook_ids[]" value="{{ $ebook->id }}">
                        </td>
                        <td>
                            <div class="fw-semibold" style="color:var(--sb-text);font-size:13.5px;">{{ $ebook->name }}</div>
                            <div style="font-size:12px;color:var(--sb-muted);">{{ $ebook->author }}</div>
                        </td>
                        <td><span style="font-weight:500;color:var(--sb-accent);">{{ $ebook->subject }}</span></td>
                        <td>{{ $ebook->standard }}</td>
                        <td>{{ $ebook->publication }}</td>
                        <td>
                            @if(isset($assignedEbooks[$ebook->id]))
                                <div class="d-flex flex-wrap gap-1">
                                    @foreach($assignedEbooks[$ebook->id] as $assignment)
                                        <span class="badge" style="background:#EEF2FF;color:var(--sb-accent);font-weight:600;border-radius:4px;font-size:11px;">
                                            {{ $assignment->classModel->standard ?? '?' }} {{ $assignment->classModel->section ?? '' }}
                                        </span>
                                    @endforeach
                                </div>
                            @else
                                <span style="font-size:12px;color:var(--sb-muted);">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5" style="color:var(--sb-muted);">
                            <i class="bi bi-book" style="font-size:32px;display:block;margin-bottom:8px;opacity:0.3;"></i>
                            No ebooks found matching the filters.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($ebooks->hasPages())
        <div class="d-flex align-items-center justify-content-between px-4 py-3" style="border-top:1px solid var(--sb-border);">
            <div style="font-size:13px;color:var(--sb-muted);">
                Showing {{ $ebooks->firstItem() }}–{{ $ebooks->lastItem() }} of {{ $ebooks->total() }}
            </div>
            <div>
                {{ $ebooks->appends(request()->query())->links('pagination::bootstrap-4') }}
            </div>
        </div>
        @endif
    </div>
</form>

@endsection

@push('admin-styles')
<style>
    .pagination { margin: 0; }
    .page-link { font-size: 13px; color: var(--sb-accent); }
    .page-item.active .page-link { background-color: var(--sb-accent); border-color: var(--sb-accent); }
</style>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.ebook-checkbox');
    
    if (selectAll) {
        selectAll.addEventListener('change', function() {
            checkboxes.forEach(cb => cb.checked = this.checked);
        });
    }
});
</script>
@endpush