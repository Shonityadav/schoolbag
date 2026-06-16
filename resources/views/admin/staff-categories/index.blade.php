@extends('layouts.admin')

@section('title', 'Staff Categories')
@section('admin_nav_staff_categories', 'active')
@section('admin_page_title', 'Staff Categories')

@section('admin_content')

{{-- Page Header --}}
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h5 class="fw-semibold mb-0" style="font-size:18px;">Staff Categories</h5>
        <p class="mb-0" style="font-size:13px;color:var(--sb-muted);">
            {{ $categories->total() }} total categories found
        </p>
    </div>
    <a href="{{ route('admin.staff-categories.create') }}"
       class="btn btn-sm text-white d-flex align-items-center gap-2"
       style="font-size:13px;border-radius:7px;background:var(--sb-accent);padding:8px 16px;">
        <i class="bi bi-plus-lg"></i> Add Category
    </a>
</div>

{{-- Search Filter --}}
<div class="sb-panel mb-4">
    <div class="p-3">
        <form method="GET" action="{{ route('admin.staff-categories.index') }}" class="row g-2 align-items-end">
            <div class="col-12 col-md-7">
                <label class="form-label" style="font-size:12px;font-weight:600;color:var(--sb-muted);">Search</label>
                <div class="input-group" style="height:36px;">
                    <span class="input-group-text" style="background:var(--sb-bg);border-color:var(--sb-border);color:var(--sb-muted);font-size:13px;">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}"
                           class="form-control" placeholder="Search by category name..."
                           style="font-size:13px;border-color:var(--sb-border);">
                </div>
            </div>
            <div class="col-12 col-md-5 d-flex gap-2">
                <button type="submit" class="btn btn-sm text-white flex-fill"
                        style="font-size:13px;border-radius:7px;background:var(--sb-accent);height:36px;">
                    <i class="bi bi-funnel"></i> Search
                </button>
                @if(request('search'))
                    <a href="{{ route('admin.staff-categories.index') }}"
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
                    <th>Category Name</th>
                    <th>Description</th>
                    <th>Staff Count</th>
                    <th style="text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $category)
                <tr>
                    <td style="color:var(--sb-muted);font-size:12px;">
                        {{ ($categories->currentPage() - 1) * $categories->perPage() + $loop->iteration }}
                    </td>
                    <td>
                        <span style="font-weight:600;font-size:13.5px;color:var(--sb-accent);">{{ $category->name }}</span>
                    </td>
                    <td style="color:var(--sb-muted);font-size:13px;max-width:250px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                        {{ $category->description ?: '-' }}
                    </td>
                    <td>
                        <span style="font-weight:600;color:var(--sb-text);">{{ $category->staffs()->count() }}</span>
                    </td>
                    <td>
                        <div class="d-flex gap-1 justify-content-end">
                            <a href="{{ route('admin.staff-categories.edit', $category) }}"
                               class="sb-icon-btn" title="Edit"
                               style="width:32px;height:32px;font-size:14px;border-radius:6px;">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form method="POST" action="{{ route('admin.staff-categories.destroy', $category) }}"
                                  onsubmit="return confirm('Delete {{ addslashes($category->name) }}? This cannot be undone.')">
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
                    <td colspan="5" class="text-center py-5" style="color:var(--sb-muted);">
                        <i class="bi bi-tags" style="font-size:32px;display:block;margin-bottom:8px;opacity:0.3;"></i>
                        No staff categories found.
                        <a href="{{ route('admin.staff-categories.create') }}" style="color:var(--sb-accent);">Add the first one</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($categories->hasPages())
    <div class="d-flex align-items-center justify-content-between px-4 py-3" style="border-top:1px solid var(--sb-border);">
        <div style="font-size:13px;color:var(--sb-muted);">
            Showing {{ $categories->firstItem() }}–{{ $categories->lastItem() }} of {{ $categories->total() }} categories
        </div>
        <div class="d-flex gap-1">
            @if($categories->onFirstPage())
                <span class="sb-icon-btn" style="width:32px;height:32px;border-radius:6px;opacity:0.4;cursor:default;font-size:13px;">
                    <i class="bi bi-chevron-left"></i>
                </span>
            @else
                <a href="{{ $categories->previousPageUrl() }}" class="sb-icon-btn" style="width:32px;height:32px;border-radius:6px;font-size:13px;">
                    <i class="bi bi-chevron-left"></i>
                </a>
            @endif

            @foreach($categories->getUrlRange(max(1, $categories->currentPage()-2), min($categories->lastPage(), $categories->currentPage()+2)) as $page => $url)
                <a href="{{ $url }}"
                   class="sb-icon-btn"
                   style="width:32px;height:32px;border-radius:6px;font-size:13px;font-weight:600;
                          {{ $page == $categories->currentPage() ? 'background:var(--sb-accent);color:#fff;border-color:var(--sb-accent);' : '' }}">
                    {{ $page }}
                </a>
            @endforeach

            @if($categories->hasMorePages())
                <a href="{{ $categories->nextPageUrl() }}" class="sb-icon-btn" style="width:32px;height:32px;border-radius:6px;font-size:13px;">
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
