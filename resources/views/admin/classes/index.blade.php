@extends('layouts.admin')

@section('title', 'Classes')
@section('admin_nav_classes', 'active')
@section('admin_page_title', 'Classes')

@section('admin_content')

{{-- Page Header --}}
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h5 class="fw-semibold mb-0" style="font-size:18px;">Classes</h5>
        <p class="mb-0" style="font-size:13px;color:var(--sb-muted);">
            {{ $classes->total() }} total classes found
        </p>
    </div>
    <a href="{{ route('admin.classes.create') }}"
       class="btn btn-sm text-white d-flex align-items-center gap-2"
       style="font-size:13px;border-radius:7px;background:var(--sb-accent);padding:8px 16px;">
        <i class="bi bi-plus-lg"></i> Add Class
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

{{-- Search Filter --}}
<div class="sb-panel mb-4">
    <div class="p-3">
        <form method="GET" action="{{ route('admin.classes.index') }}" class="row g-2 align-items-end">
            <div class="col-12 col-md-7">
                <label class="form-label" style="font-size:12px;font-weight:600;color:var(--sb-muted);">Search</label>
                <div class="input-group" style="height:36px;">
                    <span class="input-group-text" style="background:var(--sb-bg);border-color:var(--sb-border);color:var(--sb-muted);font-size:13px;">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}"
                           class="form-control" placeholder="Search by class name..."
                           style="font-size:13px;border-color:var(--sb-border);">
                </div>
            </div>
            <div class="col-12 col-md-5 d-flex gap-2">
                <button type="submit" class="btn btn-sm text-white flex-fill"
                        style="font-size:13px;border-radius:7px;background:var(--sb-accent);height:36px;">
                    <i class="bi bi-funnel"></i> Search
                </button>
                @if(request('search'))
                    <a href="{{ route('admin.classes.index') }}"
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
                    <th>Standard</th>
                    <th>Section</th>
                    <th>Students</th>
                    <th>Description</th>
                    <th style="text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($classes as $cls)
                <tr>
                    <td style="color:var(--sb-muted);font-size:12px;">
                        {{ ($classes->currentPage() - 1) * $classes->perPage() + $loop->iteration }}
                    </td>
                    <td>
                        <span style="font-weight:600;font-size:13.5px;color:var(--sb-accent);">{{ $cls->standard }}</span>
                    </td>
                    <td>
                        <span style="font-weight:600;color:var(--sb-text);">{{ $cls->section }}</span>
                    </td>
                    <td>
                        <span style="font-weight:600;color:var(--sb-text);">{{ $cls->students_count }}</span>
                    </td>
                    <td style="color:var(--sb-muted);font-size:13px;max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                        {{ $cls->description ?: '-' }}
                    </td>
                    <td>
                        <div class="d-flex gap-1 justify-content-end">
                            <a href="{{ route('admin.classes.edit', $cls) }}"
                               class="sb-icon-btn" title="Edit"
                               style="width:32px;height:32px;font-size:14px;border-radius:6px;">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form method="POST" action="{{ route('admin.classes.destroy', $cls) }}"
                                  onsubmit="return confirm('Delete {{ addslashes($cls->standard) }} - {{ addslashes($cls->section) }}? This cannot be undone.')">
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
                    <td colspan="6" class="text-center py-5" style="color:var(--sb-muted);">
                        <i class="bi bi-building" style="font-size:32px;display:block;margin-bottom:8px;opacity:0.3;"></i>
                        No classes found.
                        <a href="{{ route('admin.classes.create') }}" style="color:var(--sb-accent);">Add the first one</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($classes->hasPages())
    <div class="d-flex align-items-center justify-content-between px-4 py-3" style="border-top:1px solid var(--sb-border);">
        <div style="font-size:13px;color:var(--sb-muted);">
            Showing {{ $classes->firstItem() }}–{{ $classes->lastItem() }} of {{ $classes->total() }} classes
        </div>
        <div class="d-flex gap-1">
            @if($classes->onFirstPage())
                <span class="sb-icon-btn" style="width:32px;height:32px;border-radius:6px;opacity:0.4;cursor:default;font-size:13px;">
                    <i class="bi bi-chevron-left"></i>
                </span>
            @else
                <a href="{{ $classes->previousPageUrl() }}" class="sb-icon-btn" style="width:32px;height:32px;border-radius:6px;font-size:13px;">
                    <i class="bi bi-chevron-left"></i>
                </a>
            @endif

            @foreach($classes->getUrlRange(max(1, $classes->currentPage()-2), min($classes->lastPage(), $classes->currentPage()+2)) as $page => $url)
                <a href="{{ $url }}"
                   class="sb-icon-btn"
                   style="width:32px;height:32px;border-radius:6px;font-size:13px;font-weight:600;
                          {{ $page == $classes->currentPage() ? 'background:var(--sb-accent);color:#fff;border-color:var(--sb-accent);' : '' }}">
                    {{ $page }}
                </a>
            @endforeach

            @if($classes->hasMorePages())
                <a href="{{ $classes->nextPageUrl() }}" class="sb-icon-btn" style="width:32px;height:32px;border-radius:6px;font-size:13px;">
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
