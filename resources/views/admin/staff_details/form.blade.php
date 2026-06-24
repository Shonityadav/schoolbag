@extends('layouts.admin')

@php $isEdit = isset($member) && $member !== null; @endphp
@section('title', $isEdit ? 'Edit Staff' : 'Add Staff')
@section('admin_nav_staff', 'active')
@section('admin_page_title', $isEdit ? 'Edit Staff Member' : 'Add Staff Member')

@push('admin-styles')
<style>
.sb-tabs { display:flex; gap:0; border-bottom:1px solid var(--sb-border); padding:0 22px; background:#FAFBFD; }
.sb-tab-btn {
    padding:12px 18px; font-size:13px; font-weight:600; color:var(--sb-muted);
    border:none; background:transparent; cursor:pointer; border-bottom:2px solid transparent;
    transition:all .15s; display:flex; align-items:center; gap:7px; margin-bottom:-1px;
}
.sb-tab-btn:hover { color:var(--sb-text); }
.sb-tab-btn.active { color:var(--sb-accent); border-bottom-color:var(--sb-accent); }
.sb-tab-pane { display:none; }
.sb-tab-pane.active { display:block; }
.csv-dropzone {
    border:2px dashed var(--sb-border); border-radius:10px;
    padding:40px 20px; text-align:center; cursor:pointer;
    transition:border-color .2s, background .2s; position:relative;
}
.csv-dropzone:hover, .csv-dropzone.dragover { border-color:var(--sb-accent); background:#EFF6FF; }
.csv-dropzone input[type=file] { position:absolute; inset:0; opacity:0; cursor:pointer; width:100%; height:100%; }
.csv-dropzone-icon { font-size:36px; color:var(--sb-muted); margin-bottom:10px; line-height:1; }
.csv-dropzone-title { font-size:14px; font-weight:600; color:var(--sb-text); margin-bottom:4px; }
.csv-dropzone-sub { font-size:12.5px; color:var(--sb-muted); }
.csv-dropzone.has-file { border-color:var(--sb-green); background:#F0FDF4; }
.csv-dropzone.has-file .csv-dropzone-icon { color:var(--sb-green); }
.csv-preview-wrap { margin-top:20px; overflow-x:auto; border-radius:8px; border:1px solid var(--sb-border); }
.csv-preview-wrap table { width:100%; border-collapse:collapse; min-width:400px; }
.csv-preview-wrap th { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.6px;
    color:var(--sb-muted); background:#F8FAFC; padding:8px 14px; border-bottom:1px solid var(--sb-border); white-space:nowrap; }
.csv-preview-wrap td { font-size:12.5px; padding:9px 14px; border-bottom:1px solid var(--sb-border); color:var(--sb-text); }
.csv-preview-wrap tr:last-child td { border-bottom:none; }
.csv-preview-wrap tr:hover td { background:#FAFBFD; }
.csv-row-ok  { color:var(--sb-green); }
.csv-row-err { color:var(--sb-red); }
.import-result { display:flex; gap:16px; flex-wrap:wrap; margin-bottom:16px; }
.import-chip { padding:10px 18px; border-radius:8px; font-size:13px; font-weight:600; display:flex; align-items:center; gap:8px; }
.import-chip.success { background:#F0FDF4; color:var(--sb-green); border:1px solid #BBF7D0; }
.import-chip.error   { background:#FEF2F2; color:var(--sb-red);   border:1px solid #FECACA; }
</style>
@endpush

@section('admin_content')

<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('admin.staff_details.index') }}"
       class="sb-icon-btn" style="width:34px;height:34px;border-radius:7px;font-size:16px;">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div>
        <h5 class="fw-semibold mb-0" style="font-size:18px;">{{ $isEdit ? 'Edit Staff Member' : 'Add Staff Member' }}</h5>
        <p class="mb-0" style="font-size:13px;color:var(--sb-muted);">
            {{ $isEdit ? 'Update staff account details.' : 'Add individually or import multiple staff via CSV.' }}
        </p>
    </div>
</div>

{{-- Import Results --}}
@if(session('import_result'))
    @php $res = session('import_result'); @endphp
    <div class="import-result mb-3">
        <div class="import-chip success"><i class="bi bi-check-circle-fill"></i> {{ $res['imported'] }} imported successfully</div>
        @if($res['skipped'] > 0)
        <div class="import-chip error"><i class="bi bi-x-circle-fill"></i> {{ $res['skipped'] }} skipped</div>
        @endif
    </div>
    @if(!empty($res['errors']))
    <div class="sb-panel mb-4">
        <div class="sb-panel-header">
            <div class="sb-panel-title" style="color:var(--sb-red);"><i class="bi bi-exclamation-triangle me-2"></i>Import Errors</div>
        </div>
        <div class="p-3">
            @foreach($res['errors'] as $err)
                <div style="font-size:12.5px;padding:5px 0;border-bottom:1px solid var(--sb-border);color:var(--sb-red);">
                    <span class="fw-bold">Row {{ $err['row'] }}:</span> {{ $err['message'] }}
                </div>
            @endforeach
        </div>
    </div>
    @endif
@endif

<div class="row justify-content-center">
    <div class="{{ $isEdit ? 'col-12 col-lg-7' : 'col-12 col-lg-9' }}">
        <div class="sb-panel">

            @if(!$isEdit)
            <div class="sb-tabs" id="staff-tabs">
                <button class="sb-tab-btn active" data-tab="single" onclick="switchTab('staff-tabs','single',this)">
                    <i class="bi bi-person-plus"></i> Single Member
                </button>
                <button class="sb-tab-btn" data-tab="bulk" onclick="switchTab('staff-tabs','bulk',this)">
                    <i class="bi bi-cloud-upload"></i> Bulk Import (CSV)
                </button>
            </div>
            @else
            <div class="sb-panel-header">
                <div class="sb-panel-title"><i class="bi bi-person-badge me-2"></i>Staff Details</div>
                <span style="font-size:12px;color:var(--sb-muted);">ID: #{{ $member->id }}</span>
            </div>
            @endif

            {{-- ── TAB 1: Single Form ── --}}
            <div class="sb-tab-pane active" id="tab-single">
                <form method="POST"
                      action="{{ $isEdit ? route('admin.staff_details.update', $member) : route('admin.staff_details.store') }}"
                      class="p-4">
                    @csrf
                    @if($isEdit) @method('PUT') @endif

                    @if($errors->any())
                        <div class="alert alert-danger d-flex gap-2 mb-4" style="border-radius:8px;font-size:13px;">
                            <i class="bi bi-exclamation-triangle-fill mt-1 flex-shrink-0"></i>
                            <div>
                                <div class="fw-bold mb-1">Please fix the following errors:</div>
                                <ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                            </div>
                        </div>
                    @endif

                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label" style="font-size:13px;font-weight:600;">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" value="{{ old('name', $member?->name) }}"
                                   class="form-control @error('name') is-invalid @enderror"
                                   placeholder="e.g. Priya Sharma"
                                   style="font-size:13.5px;border-color:var(--sb-border);border-radius:8px;">
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" style="font-size:13px;font-weight:600;">Email Address <span class="text-danger">*</span></label>
                            <input type="email" name="email" value="{{ old('email', $member?->email) }}"
                                   class="form-control @error('email') is-invalid @enderror"
                                   placeholder="staff@school.com"
                                   style="font-size:13.5px;border-color:var(--sb-border);border-radius:8px;">
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" style="font-size:13px;font-weight:600;">Phone</label>
                            <input type="text" name="phone" value="{{ old('phone', $member?->phone) }}"
                                   class="form-control @error('phone') is-invalid @enderror"
                                   placeholder="+91 98765 43210"
                                   style="font-size:13.5px;border-color:var(--sb-border);border-radius:8px;">
                            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" style="font-size:13px;font-weight:600;">Staff Category <span class="text-danger">*</span></label>
                            <select name="staff_category_id" class="form-select @error('staff_category_id') is-invalid @enderror" style="font-size:13.5px;border-color:var(--sb-border);border-radius:8px;">
                                <option value="">Select Category</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('staff_category_id', $member?->staff?->staff_category_id) == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('staff_category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" style="font-size:13px;font-weight:600;">Designation</label>
                            <input type="text" name="designation" value="{{ old('designation', $member?->staff?->designation) }}"
                                   class="form-control @error('designation') is-invalid @enderror"
                                   placeholder="e.g. Senior Teacher"
                                   style="font-size:13.5px;border-color:var(--sb-border);border-radius:8px;">
                            @error('designation')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" style="font-size:13px;font-weight:600;">Department</label>
                            <input type="text" name="department" value="{{ old('department', $member?->staff?->department) }}"
                                   class="form-control @error('department') is-invalid @enderror"
                                   placeholder="e.g. Mathematics"
                                   style="font-size:13.5px;border-color:var(--sb-border);border-radius:8px;">
                            @error('department')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" style="font-size:13px;font-weight:600;">Employee ID</label>
                            <input type="text" name="employ_id" value="{{ old('employ_id', $member?->staff?->employ_id) }}"
                                   class="form-control @error('employ_id') is-invalid @enderror"
                                   placeholder="e.g. EMP-001"
                                   style="font-size:13.5px;border-color:var(--sb-border);border-radius:8px;">
                            @error('employ_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" style="font-size:13px;font-weight:600;">Salary (₹)</label>
                            <input type="number" step="0.01" min="0" name="salary" value="{{ old('salary', $member?->staff?->salary) }}"
                                   class="form-control @error('salary') is-invalid @enderror"
                                   placeholder="e.g. 50000.00"
                                   style="font-size:13.5px;border-color:var(--sb-border);border-radius:8px;">
                            @error('salary')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <div style="border-top:1px solid var(--sb-border);padding-top:16px;font-size:13px;font-weight:600;">
                                Access & Assignments
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label" style="font-size:13px;font-weight:600;">Permissions</label>
                            <div class="d-flex flex-wrap gap-3">
                                @php $assignedPerms = $isEdit ? $member->permissions->pluck('id')->toArray() : []; @endphp
                                @foreach($allPermissions as $perm)
                                    <div class="form-check form-switch" style="font-size:13px;">
                                        <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $perm->id }}" id="perm_{{ $perm->id }}"
                                            {{ in_array($perm->id, old('permissions', $assignedPerms)) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="perm_{{ $perm->id }}">
                                            {{ $perm->name }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label" style="font-size:13px;font-weight:600;">Assigned Classes</label>
                            <div class="d-flex flex-wrap gap-3">
                                @php $assignedClasses = $isEdit ? $member->classes->pluck('id')->toArray() : []; @endphp
                                @foreach($allClasses as $cls)
                                    <div class="form-check form-switch" style="font-size:13px;">
                                        <input class="form-check-input" type="checkbox" name="classes[]" value="{{ $cls->id }}" id="cls_{{ $cls->id }}"
                                            {{ in_array($cls->id, old('classes', $assignedClasses)) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="cls_{{ $cls->id }}">
                                            {{ $cls->standard }} {{ $cls->section ? '- ' . $cls->section : '' }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label" style="font-size:13px;font-weight:600;">Managed Categories</label>
                            <div class="d-flex flex-wrap gap-3">
                                @php $managedCats = $isEdit ? $member->managedCategories->pluck('id')->toArray() : []; @endphp
                                @foreach($categories as $cat)
                                    <div class="form-check form-switch" style="font-size:13px;">
                                        <input class="form-check-input" type="checkbox" name="managed_categories[]" value="{{ $cat->id }}" id="mcat_{{ $cat->id }}"
                                            {{ in_array($cat->id, old('managed_categories', $managedCats)) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="mcat_{{ $cat->id }}">
                                            {{ $cat->name }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="col-12">
                            <div style="border-top:1px solid var(--sb-border);padding-top:16px;font-size:13px;font-weight:600;">
                                {{ $isEdit ? 'Change Password' : 'Set Password' }}
                                @if($isEdit)<span style="font-size:12px;color:var(--sb-muted);font-weight:400;"> — leave blank to keep current</span>@endif
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" style="font-size:13px;font-weight:600;">Password @if(!$isEdit)<span class="text-danger">*</span>@endif</label>
                            <input type="password" name="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   placeholder="Min. 6 characters"
                                   style="font-size:13.5px;border-color:var(--sb-border);border-radius:8px;">
                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" style="font-size:13px;font-weight:600;">Confirm Password</label>
                            <input type="password" name="password_confirmation"
                                   class="form-control" placeholder="Repeat password"
                                   style="font-size:13.5px;border-color:var(--sb-border);border-radius:8px;">
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-4 pt-3" style="border-top:1px solid var(--sb-border);">
                        <button type="submit" class="btn text-white d-flex align-items-center gap-2"
                                style="font-size:13.5px;border-radius:8px;background:var(--sb-accent);padding:10px 22px;">
                            <i class="bi bi-{{ $isEdit ? 'check-lg' : 'person-plus' }}"></i>
                            {{ $isEdit ? 'Save Changes' : 'Create Staff Member' }}
                        </button>
                        <a href="{{ route('admin.staff_details.index') }}"
                           class="btn btn-outline-secondary"
                           style="font-size:13.5px;border-radius:8px;padding:10px 20px;border-color:var(--sb-border);">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>

            {{-- ── TAB 2: CSV Bulk Import ── --}}
            @if(!$isEdit)
            <div class="sb-tab-pane" id="tab-bulk">
                <div class="p-4">
                    <div class="d-flex align-items-start gap-3 p-3 mb-4"
                         style="background:#F0F9FF;border:1px solid #BAE6FD;border-radius:9px;">
                        <i class="bi bi-info-circle-fill" style="color:#0284C7;font-size:18px;flex-shrink:0;margin-top:1px;"></i>
                        <div style="font-size:13px;color:#0C4A6E;line-height:1.6;">
                            <div class="fw-bold mb-1">CSV Format Guide</div>
                            Required columns: <code>name</code>, <code>email</code>, <code>password</code><br>
                            Optional columns: <code>phone</code>, <code>employ_id</code>, <code>salary</code><br>
                            First row must be the header row. Passwords must be at least 6 characters.
                        </div>
                        <a href="{{ route('admin.staff_details.sample-csv') }}"
                           class="btn btn-sm ms-auto flex-shrink-0 d-flex align-items-center gap-2"
                           style="font-size:12px;border-radius:7px;border:1px solid #BAE6FD;color:#0284C7;background:#fff;white-space:nowrap;padding:6px 12px;">
                            <i class="bi bi-download"></i> Sample CSV
                        </a>
                    </div>

                    <div class="csv-dropzone" id="csv-dropzone-staff">
                        <input type="file" id="csv-file-staff" accept=".csv,text/csv">
                        <div class="csv-dropzone-icon"><i class="bi bi-cloud-arrow-up"></i></div>
                        <div class="csv-dropzone-title" id="csv-dz-title-staff">Drag &amp; drop your CSV file here</div>
                        <div class="csv-dropzone-sub">or <u>click to browse</u> — .csv files only</div>
                    </div>

                    <div id="csv-preview-staff" style="display:none;">
                        <div class="d-flex align-items-center justify-content-between mt-4 mb-2">
                            <div style="font-size:13px;font-weight:600;">
                                Preview — <span id="csv-count-staff">0</span> rows found
                            </div>
                            <button onclick="clearCsv('staff')" class="btn btn-sm btn-outline-secondary"
                                    style="font-size:12px;border-radius:6px;padding:4px 10px;">
                                <i class="bi bi-x"></i> Clear
                            </button>
                        </div>
                        <div class="csv-preview-wrap">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Row</th><th>Name</th><th>Email</th><th>Phone</th><th>Emp. ID</th><th>Salary</th><th>Status</th>
                                    </tr>
                                </thead>
                                <tbody id="csv-tbody-staff"></tbody>
                            </table>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('admin.staff_details.import') }}"
                          enctype="multipart/form-data" id="csv-form-staff">
                        @csrf
                        <input type="file" name="csv_file" id="csv-hidden-staff" style="display:none;" accept=".csv">

                        <div class="mb-3 mt-4" style="max-width:300px;">
                            <label class="form-label" style="font-size:13px;font-weight:600;">Assign Category to Imported Staff <span class="text-danger">*</span></label>
                            <select name="staff_category_id" class="form-select" style="font-size:13.5px;border-color:var(--sb-border);border-radius:8px;" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="d-flex gap-2 mt-4 pt-3" style="border-top:1px solid var(--sb-border);">
                            <button type="submit" id="csv-submit-staff"
                                    class="btn text-white d-flex align-items-center gap-2"
                                    style="font-size:13.5px;border-radius:8px;background:var(--sb-accent);padding:10px 22px;" disabled>
                                <i class="bi bi-cloud-upload"></i> Import Staff
                            </button>
                            <a href="{{ route('admin.staff_details.index') }}"
                               class="btn btn-outline-secondary"
                               style="font-size:13.5px;border-radius:8px;padding:10px 20px;border-color:var(--sb-border);">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
            @endif

        </div>
    </div>
</div>

@endsection

@push('admin-scripts')
<script>
function switchTab(groupId, tabId, btn) {
    document.querySelectorAll('#' + groupId + ' .sb-tab-btn').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.sb-tab-pane').forEach(p => p.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById('tab-' + tabId).classList.add('active');
}

function initCsvDropzone(suffix, columns) {
    const dz      = document.getElementById('csv-dropzone-' + suffix);
    const fileIn  = document.getElementById('csv-file-' + suffix);
    const hiddenIn= document.getElementById('csv-hidden-' + suffix);
    const preview = document.getElementById('csv-preview-' + suffix);
    const tbody   = document.getElementById('csv-tbody-' + suffix);
    const count   = document.getElementById('csv-count-' + suffix);
    const title   = document.getElementById('csv-dz-title-' + suffix);
    const submit  = document.getElementById('csv-submit-' + suffix);
    if (!dz) return;

    ['dragenter','dragover'].forEach(e => dz.addEventListener(e, ev => { ev.preventDefault(); dz.classList.add('dragover'); }));
    ['dragleave','drop'].forEach(e => dz.addEventListener(e, ev => { ev.preventDefault(); dz.classList.remove('dragover'); }));
    dz.addEventListener('drop', ev => { const f = ev.dataTransfer.files[0]; if(f) processFile(f); });
    fileIn.addEventListener('change', () => { if(fileIn.files[0]) processFile(fileIn.files[0]); });

    function processFile(file) {
        if (!file.name.endsWith('.csv')) { alert('Please select a .csv file.'); return; }
        title.textContent = file.name;
        dz.classList.add('has-file');
        const dt = new DataTransfer(); dt.items.add(file); hiddenIn.files = dt.files;
        const reader = new FileReader();
        reader.onload = e => renderPreview(parseCSV(e.target.result));
        reader.readAsText(file);
    }

    function parseCSV(text) {
        const lines = text.trim().split('\n').filter(l => l.trim());
        if (lines.length < 2) return [];
        const headers = lines[0].split(',').map(h => h.trim().toLowerCase().replace(/[^a-z_]/g,''));
        return lines.slice(1).map((line, i) => {
            const vals = line.split(',').map(v => v.trim().replace(/^"|"$/g,''));
            const obj  = {}; headers.forEach((h,idx) => obj[h] = vals[idx] || ''); obj._row = i+2; return obj;
        });
    }

    function renderPreview(rows) {
        tbody.innerHTML = ''; count.textContent = rows.length;
        rows.slice(0, 50).forEach(r => {
            const ok = r.name && r.email && r.email.includes('@') && r.password && r.password.length >= 6;
            const statusIcon = ok
                ? '<span class="csv-row-ok"><i class="bi bi-check-circle-fill"></i> Ready</span>'
                : '<span class="csv-row-err"><i class="bi bi-x-circle-fill"></i> ' +
                  (!r.name ? 'Missing name' : !r.email || !r.email.includes('@') ? 'Invalid email' : 'Password too short') + '</span>';
            const extra = suffix === 'student_details' ? `<td style="color:var(--sb-muted);">${r.class_name||'—'}</td>` : `<td style="color:var(--sb-muted);">${r.employ_id||'—'}</td><td style="color:var(--sb-muted);">${r.salary||'—'}</td>`;
            tbody.innerHTML += `<tr>
                <td style="color:var(--sb-muted);font-size:12px;">${r._row}</td>
                <td>${r.name||'<span style="color:var(--sb-red)">—</span>'}</td>
                <td style="color:var(--sb-muted);">${r.email||'<span style="color:var(--sb-red)">—</span>'}</td>
                <td style="color:var(--sb-muted);">${r.phone||'—'}</td>
                ${extra}<td>${statusIcon}</td>
            </tr>`;
        });
        if (rows.length > 50) tbody.innerHTML += `<tr><td colspan="7" style="text-align:center;color:var(--sb-muted);font-size:12px;padding:10px;">… and ${rows.length-50} more rows</td></tr>`;
        preview.style.display = 'block';
        submit.disabled = rows.length === 0;
    }
}

function clearCsv(suffix) {
    document.getElementById('csv-preview-' + suffix).style.display = 'none';
    document.getElementById('csv-dropzone-' + suffix).classList.remove('has-file');
    document.getElementById('csv-dz-title-' + suffix).textContent = 'Drag & drop your CSV file here';
    document.getElementById('csv-submit-' + suffix).disabled = true;
    document.getElementById('csv-file-' + suffix).value = '';
}

initCsvDropzone('staff');
</script>
@endpush
