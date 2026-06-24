@extends('layouts.admin')

@section('title', 'Staff')
@section('admin_nav_staff', 'active')
@section('admin_page_title', 'Staff')

@section('admin_content')

{{-- Page Header --}}
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h5 class="fw-semibold mb-0" style="font-size:18px;">Staff Members</h5>
        <p class="mb-0" style="font-size:13px;color:var(--sb-muted);">
            {{ $staff->total() }} total staff accounts
        </p>
    </div>
    <a href="{{ route('admin.staff_details.create') }}"
       class="btn btn-sm text-white d-flex align-items-center gap-2"
       style="font-size:13px;border-radius:7px;background:var(--sb-accent);padding:8px 16px;">
        <i class="bi bi-plus-lg"></i> Add Staff
    </a>
</div>

{{-- Alerts now handled in layout as toasts --}}

{{-- Search Filter --}}
<div class="sb-panel mb-4">
    <div class="p-3">
        <form method="GET" action="{{ route('admin.staff_details.index') }}" class="row g-2 align-items-end">
            <div class="col-12 col-md-7">
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
            <div class="col-12 col-md-5 d-flex gap-2">
                <!-- Search button removed for automatic submission -->
                @if(request('search'))
                    <a href="{{ route('admin.staff_details.index') }}"
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
                    <th>Emp. ID</th>
                    <th>Staff Member</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Category</th>
                    <th>Salary</th>
                    <th>Permissions</th>
                    <th>Assigned Classes</th>
                    <th>Joined</th>
                    <th style="text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($staff as $member)
                <tr>
                    <td style="color:var(--sb-muted);font-size:12px;">
                        {{ ($staff->currentPage() - 1) * $staff->perPage() + $loop->iteration }}
                    </td>
                    <td style="color:var(--sb-muted);font-size:13px;font-weight:600;">
                        {{ $member->staff->employ_id ?? '—' }}
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="sb-avatar-sm" style="background:#F0FDF4;color:var(--sb-green);font-weight:700;font-size:12px;">
                                {{ mb_strtoupper(mb_substr($member->name, 0, 2)) }}
                            </div>
                            <span style="font-weight:600;font-size:13.5px;">{{ $member->name }}</span>
                        </div>
                    </td>
                    <td style="color:var(--sb-muted);">{{ $member->email }}</td>
                    <td style="color:var(--sb-muted);">{{ $member->phone ?? '—' }}</td>
                    <td>
                        <span style="background:#F0FDF4;color:var(--sb-green);padding:3px 8px;border-radius:4px;font-size:12px;font-weight:600;">
                            {{ $member->staff?->category?->name ?? 'N/A' }}
                        </span>
                    </td>
                    <td style="color:var(--sb-text);font-weight:600;font-size:13px;">
                        {{ $member->staff && $member->staff->salary ? '₹' . number_format($member->staff->salary, 2) : '—' }}
                    </td>
                    <td>
                        <div class="d-flex flex-wrap gap-1">
                            @forelse($member->permissions as $perm)
                                <span style="background:#EEF2FF;color:#4F46E5;padding:2px 6px;border-radius:4px;font-size:11px;font-weight:500;">
                                    {{ $perm->name }}
                                </span>
                            @empty
                                <span style="color:var(--sb-muted);font-size:12px;">None</span>
                            @endforelse
                        </div>
                    </td>
                    <td>
                        <div class="d-flex flex-wrap gap-1">
                            @forelse($member->classes as $cls)
                                <span style="background:#FFF7ED;color:#EA580C;padding:2px 6px;border-radius:4px;font-size:11px;font-weight:500;">
                                    {{ $cls->standard }} {{ $cls->section ? '- ' . $cls->section : '' }}
                                </span>
                            @empty
                                <span style="color:var(--sb-muted);font-size:12px;">None</span>
                            @endforelse
                        </div>
                    </td>
                    <td style="color:var(--sb-muted);font-size:12px;white-space:nowrap;">
                        {{ $member->created_at->format('d M Y') }}
                    </td>
                    <td>
                        <div class="d-flex gap-1 justify-content-end">
                            <a href="{{ route('admin.staff_details.edit', $member) }}"
                               class="sb-icon-btn" title="Edit"
                               style="width:32px;height:32px;font-size:14px;border-radius:6px;">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form method="POST" action="{{ route('admin.staff_details.destroy', $member) }}"
                                  onsubmit="return confirm('Delete {{ addslashes($member->name) }}? This cannot be undone.')">
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
                    <td colspan="11" class="text-center py-5" style="color:var(--sb-muted);">
                        <i class="bi bi-person-badge" style="font-size:32px;display:block;margin-bottom:8px;opacity:0.3;"></i>
                        No staff members found.
                        <a href="{{ route('admin.staff_details.create') }}" style="color:var(--sb-accent);">Add the first one</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($staff->hasPages())
    <div class="d-flex align-items-center justify-content-between px-4 py-3" style="border-top:1px solid var(--sb-border);">
        <div style="font-size:13px;color:var(--sb-muted);">
            Showing {{ $staff->firstItem() }}–{{ $staff->lastItem() }} of {{ $staff->total() }} members
        </div>
        <div class="d-flex gap-1">
            @if($staff->onFirstPage())
                <span class="sb-icon-btn" style="width:32px;height:32px;border-radius:6px;opacity:0.4;cursor:default;font-size:13px;">
                    <i class="bi bi-chevron-left"></i>
                </span>
            @else
                <a href="{{ $staff->previousPageUrl() }}" class="sb-icon-btn" style="width:32px;height:32px;border-radius:6px;font-size:13px;">
                    <i class="bi bi-chevron-left"></i>
                </a>
            @endif

            @foreach($staff->getUrlRange(max(1, $staff->currentPage()-2), min($staff->lastPage(), $staff->currentPage()+2)) as $page => $url)
                <a href="{{ $url }}"
                   class="sb-icon-btn"
                   style="width:32px;height:32px;border-radius:6px;font-size:13px;font-weight:600;
                          {{ $page == $staff->currentPage() ? 'background:var(--sb-accent);color:#fff;border-color:var(--sb-accent);' : '' }}">
                    {{ $page }}
                </a>
            @endforeach

            @if($staff->hasMorePages())
                <a href="{{ $staff->nextPageUrl() }}" class="sb-icon-btn" style="width:32px;height:32px;border-radius:6px;font-size:13px;">
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
