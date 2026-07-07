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
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('admin.staff_details.upload-photos') }}"
           class="btn btn-sm text-white d-flex align-items-center gap-2"
           style="font-size:13px;border-radius:7px;background:var(--sb-accent);padding:8px 16px;">
            <i class="bi bi-file-image"></i> Upload Photos
        </a>
        <a href="{{ route('admin.staff_details.create') }}"
           class="btn btn-sm text-white d-flex align-items-center gap-2"
           style="font-size:13px;border-radius:7px;background:var(--sb-accent);padding:8px 16px;">
            <i class="bi bi-plus-lg"></i> Add Staff
        </a>
    </div>
</div>

{{-- Alerts now handled in layout as toasts --}}

{{-- Search Filter --}}
<div class="sb-panel mb-4">
    <div class="p-3">
        <form method="GET" action="{{ route('admin.staff_details.index') }}" class="row g-2 align-items-end">
            <div class="col-12 col-md-6">
                <label class="form-label" style="font-size:12px;font-weight:600;color:var(--sb-muted);">Search</label>
                <div class="input-group" style="height:36px;">
                    <span class="input-group-text" style="background:var(--sb-bg);border-color:var(--sb-border);color:var(--sb-muted);font-size:13px;">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}"
                           class="form-control" placeholder="Search staff members..."
                           style="font-size:13px;border-color:var(--sb-border);">
                </div>
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label" style="font-size:12px;font-weight:600;color:var(--sb-muted);">Category</label>
                <select name="category_id" class="form-select" style="font-size:13px;border-color:var(--sb-border);height:36px;" onchange="const el = this.form.querySelector('input[name=search]'); if(el) el.dispatchEvent(new Event('input'));">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-2 d-flex gap-2">
                @if(request('search') || request('category_id'))
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
                    <th style="width: 40px; text-align: center;">
                        <input class="form-check-input" type="checkbox" id="selectAll">
                    </th>
                    <th>Photo</th>
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
                    <td style="text-align: center; vertical-align: middle;">
                        <input class="form-check-input staff-checkbox" type="checkbox" value="{{ $member->id }}">
                    </td>
                    <td style="text-align: center; vertical-align: middle;">
                        @if(!empty($member->user_img))
                            <img src="{{ asset($member->user_img) }}" alt="Photo" style="width:38px;height:38px;object-fit:cover;border-radius:50%;border:2px solid #fff;box-shadow:0 2px 6px rgba(0,0,0,0.08);">
                        @else
                            <div style="width:38px;height:38px;border-radius:50%;background:#F0FDF4;display:flex;align-items:center;justify-content:center;color:#4ADE80;font-size:16px;margin:auto;">
                                <i class="bi bi-person"></i>
                            </div>
                        @endif
                    </td>
                    <td style="color:var(--sb-muted);font-size:13px;font-weight:600;">
                        {{ $member->staff->employee_id ?? '—' }}
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
                            <a href="{{ route('admin.staff_details.show', $member) }}"
                               class="sb-icon-btn" title="View Details"
                               style="width:32px;height:32px;font-size:14px;border-radius:6px;background:#F0FDF4;color:#16A34A;border:1px solid #BBF7D0;">
                                <i class="bi bi-eye"></i>
                            </a>
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
            <input type="hidden" name="type" value="staff">
            
            <div class="modal-header">
                <h5 class="modal-title">Generate Staff ID Cards</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info py-2" style="font-size:13.5px;">
                    <i class="bi bi-info-circle"></i> You are generating ID cards for <strong id="modalSelectedCount">0</strong> staff member(s).
                </div>
                
                @php
                    $templates = \App\Models\IdCardTemplate::where('institute_id', auth()->user()->institute_id)
                        ->where('status', 'Published')
                        ->where('type', 'staff')
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
                        <div class="form-text text-danger">No published templates available for staff. Please create and publish one first.</div>
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
    const checkboxes = document.querySelectorAll('.staff-checkbox');
    const bulkActionBar = document.getElementById('bulkActionBar');
    const selectedCount = document.getElementById('selectedCount');

    function updateBulkAction() {
        const checked = document.querySelectorAll('.staff-checkbox:checked');
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
    const checked = Array.from(document.querySelectorAll('.staff-checkbox:checked')).map(cb => cb.value);
    document.getElementById('modalUserIds').value = checked.join(',');
    document.getElementById('modalSelectedCount').innerText = checked.length;
    
    var myModal = new bootstrap.Modal(document.getElementById('generateIdCardModal'));
    myModal.show();
}
</script>

@endsection
