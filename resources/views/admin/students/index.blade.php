@extends('layouts.admin')

@section('title', 'Students')
@section('admin_nav_students', 'active')
@section('admin_page_title', 'Students')

@section('admin_content')

{{-- Page Header --}}
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h5 class="fw-semibold mb-0" style="font-size:18px;">Students</h5>
        <p class="mb-0" style="font-size:13px;color:var(--sb-muted);">
            {{ $students->total() }} total students registered
        </p>
    </div>
    <a href="{{ route('admin.students.create') }}"
       class="btn btn-sm text-white d-flex align-items-center gap-2"
       style="font-size:13px;border-radius:7px;background:var(--sb-accent);padding:8px 16px;">
        <i class="bi bi-plus-lg"></i> Add Student
    </a>
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
        <form method="GET" action="{{ route('admin.students.index') }}" class="row g-2 align-items-end">
            <div class="col-12 col-md-5">
                <label class="form-label" style="font-size:12px;font-weight:600;color:var(--sb-muted);">Search</label>
                <div class="input-group" style="height:36px;">
                    <span class="input-group-text" style="background:var(--sb-bg);border-color:var(--sb-border);color:var(--sb-muted);font-size:13px;">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}"
                           class="form-control" placeholder="Name, email or phone…"
                           style="font-size:13px;border-color:var(--sb-border);">
                </div>
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label" style="font-size:12px;font-weight:600;color:var(--sb-muted);">Class</label>
                <select name="class_id" class="form-select" style="font-size:13px;border-color:var(--sb-border);height:36px;padding-top:6px;padding-bottom:6px;">
                    <option value="">All Classes</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                            {{ $class->standard }} - {{ $class->section }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-sm text-white flex-fill"
                        style="font-size:13px;border-radius:7px;background:var(--sb-accent);height:36px;">
                    <i class="bi bi-funnel"></i> Filter
                </button>
                @if(request()->hasAny(['search','class_id']))
                    <a href="{{ route('admin.students.index') }}"
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
                    <th>#</th>
                    <th>Student</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Class</th>
                    <th>XP</th>
                    <th>Joined</th>
                    <th style="text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($students as $student)
                <tr>
                    <td style="color:var(--sb-muted);font-size:12px;">
                        {{ ($students->currentPage() - 1) * $students->perPage() + $loop->iteration }}
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
                    <td>
                        <span style="font-weight:600;color:var(--sb-text);">{{ number_format($student->total_xp) }}</span>
                        <span style="font-size:11px;color:var(--sb-muted);"> XP</span>
                    </td>
                    <td style="color:var(--sb-muted);font-size:12px;white-space:nowrap;">
                        {{ $student->created_at->format('d M Y') }}
                    </td>
                    <td>
                        <div class="d-flex gap-1 justify-content-end">
                            <a href="{{ route('admin.students.edit', $student) }}"
                               class="sb-icon-btn" title="Edit"
                               style="width:32px;height:32px;font-size:14px;border-radius:6px;">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form method="POST" action="{{ route('admin.students.destroy', $student) }}"
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
                    <td colspan="8" class="text-center py-5" style="color:var(--sb-muted);">
                        <i class="bi bi-people" style="font-size:32px;display:block;margin-bottom:8px;opacity:0.3;"></i>
                        No students found.
                        <a href="{{ route('admin.students.create') }}" style="color:var(--sb-accent);">Add the first one</a>
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

@endsection
