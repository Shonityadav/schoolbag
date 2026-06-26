@extends('layouts.admin')

@section('title', 'Upload Photos')
@section('admin_nav_staff', 'active')
@section('admin_page_title', 'Upload Photos')

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

/* ── Drop zone ── */
.csv-dropzone {
    border:2px dashed var(--sb-border); border-radius:10px;
    padding:40px 20px; text-align:center; cursor:pointer;
    transition:border-color .2s, background .2s; position:relative;
}
.csv-dropzone:hover, .csv-dropzone.dragover {
    border-color:var(--sb-accent); background:#EFF6FF;
}
.csv-dropzone input[type=file] {
    position:absolute; inset:0; opacity:0; cursor:pointer; width:100%; height:100%;
}
.csv-dropzone-icon { font-size:36px; color:var(--sb-muted); margin-bottom:10px; line-height:1; }
.csv-dropzone-title { font-size:14px; font-weight:600; color:var(--sb-text); margin-bottom:4px; }
.csv-dropzone-sub { font-size:12.5px; color:var(--sb-muted); }
.csv-dropzone.has-file { border-color:var(--sb-green); background:#F0FDF4; }
.csv-dropzone.has-file .csv-dropzone-icon { color:var(--sb-green); }
</style>
@endpush

@section('admin_content')

{{-- Page Header --}}
<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('admin.staff_details.index') }}"
       class="sb-icon-btn" style="width:34px;height:34px;border-radius:7px;font-size:16px;">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div>
        <h5 class="fw-semibold mb-0" style="font-size:18px;">Upload Staff Photos</h5>
        <p class="mb-0" style="font-size:13px;color:var(--sb-muted);">
            Upload photos individually or via a ZIP file.
        </p>
    </div>
</div>

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

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert" style="border-radius:8px;font-size:13.5px;">
        <ul class="mb-0 ps-3">
            @foreach ($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="sb-panel mb-4">
    <div class="sb-tabs">
        <button type="button" class="sb-tab-btn active" data-tab="single" onclick="switchTab('single')">
            <i class="bi bi-person-bounding-box"></i> Single Upload
        </button>
        <button type="button" class="sb-tab-btn" data-tab="bulk" onclick="switchTab('bulk')">
            <i class="bi bi-file-earmark-zip"></i> Bulk Upload (ZIP)
        </button>
    </div>

    {{-- Single Upload --}}
    <div id="tab-single" class="sb-tab-pane active p-4">
        <form action="{{ route('admin.staff_details.upload-photos.submit') }}" method="POST" enctype="multipart/form-data" id="single-upload-form">
            @csrf
            <input type="hidden" name="upload_type" value="single">
            
            <div class="row g-4 mb-4">
                <div class="col-12 col-md-6">
                    <label class="form-label" style="font-size:13px;font-weight:600;">Select Staff</label>
                    <select name="user_id" id="student_select" class="form-select" style="font-size:13.5px;border-color:var(--sb-border);border-radius:8px;" required>
                        <option value="">-- Choose Staff --</option>
                        @foreach($staffMembers as $st)
                            <option value="{{ $st['id'] }}" data-has-image="{{ $st['has_image'] ? 'true' : 'false' }}">
                                {{ $st['name'] }} (Emp ID: {{ $st['employee_id'] ?? 'N/A' }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label" style="font-size:13px;font-weight:600;">Photo (JPG, PNG)</label>
                    <div class="csv-dropzone" id="single-dropzone">
                        <input type="file" name="photo" id="single-photo-input" accept=".jpg,.jpeg,.png" required>
                        <i class="bi bi-cloud-arrow-up csv-dropzone-icon"></i>
                        <div class="csv-dropzone-title" id="single-file-name">Click or drag photo to upload</div>
                        <div class="csv-dropzone-sub">Max file size 5MB.</div>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn text-white px-4 py-2" style="background:var(--sb-accent);border-radius:8px;font-size:13.5px;">
                <i class="bi bi-upload me-2"></i> Upload Photo
            </button>
        </form>
    </div>

    {{-- Bulk Upload --}}
    <div id="tab-bulk" class="sb-tab-pane p-4">
        <form action="{{ route('admin.staff_details.upload-photos.submit') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="upload_type" value="bulk">
            
            <div class="mb-4">
                <div style="font-size:13px;color:#0C4A6E;line-height:1.6;margin-bottom:15px;background:#F0F9FF;padding:15px;border-radius:8px;border:1px solid #BAE6FD;">
                    <div class="fw-bold mb-1">ZIP Format Guide</div>
                    Create a ZIP file containing images (.jpg, .jpeg, .png).<br>
                    The filename of each image MUST match the <b>Employee ID</b> of the staff exactly (e.g., <code>EMP-001.jpg</code>).<br>
                    Images that do not match an existing employee ID will be skipped.
                </div>

                <label class="form-label" style="font-size:13px;font-weight:600;">Upload ZIP File</label>
                <div class="csv-dropzone mb-3" id="bulk-dropzone">
                    <input type="file" name="zip_file" id="bulk-zip-input" accept=".zip" required>
                    <i class="bi bi-file-earmark-zip csv-dropzone-icon"></i>
                    <div class="csv-dropzone-title" id="bulk-file-name">Click or drag ZIP file here</div>
                    <div class="csv-dropzone-sub">Max file size 50MB.</div>
                </div>
                
                <div class="form-check" style="font-size:13px;color:var(--sb-text);">
                    <input class="form-check-input" type="checkbox" value="1" id="overwrite_existing" name="overwrite_existing">
                    <label class="form-check-label" for="overwrite_existing">
                        Overwrite existing images if present
                    </label>
                </div>

                <div id="zip-preview-container" class="mt-4" style="display:none;">
                    <div class="fw-bold mb-2" style="font-size:14px;">Preview: Staff to be updated</div>
                    <div id="zip-preview-loading" style="font-size:13px;color:var(--sb-muted);"><i class="spinner-border spinner-border-sm me-2"></i> Analyzing ZIP file...</div>
                    <div id="zip-preview-content"></div>
                </div>
            </div>

            <button type="submit" class="btn text-white px-4 py-2" style="background:var(--sb-accent);border-radius:8px;font-size:13.5px;">
                <i class="bi bi-file-earmark-zip me-2"></i> Upload & Process ZIP
            </button>
        </form>
    </div>
</div>

@endsection

@push('admin-scripts')
<script>
function switchTab(tabId) {
    document.querySelectorAll('.sb-tab-btn').forEach(btn => btn.classList.remove('active'));
    document.querySelectorAll('.sb-tab-pane').forEach(pane => pane.classList.remove('active'));
    
    document.querySelector(`button[data-tab="${tabId}"]`).classList.add('active');
    document.getElementById(`tab-${tabId}`).classList.add('active');
}

document.getElementById('single-upload-form').addEventListener('submit', function(e) {
    const select = document.getElementById('student_select');
    const selectedOption = select.options[select.selectedIndex];
    
    if (selectedOption && selectedOption.getAttribute('data-has-image') === 'true') {
        const confirmOverwrite = confirm('Image already present. Do you want to replace the image or cancel upload?');
        if (!confirmOverwrite) {
            e.preventDefault(); // Cancel submission
        }
    }
});

// Drag and drop functionality
function setupDropzone(dropzoneId, inputId, fileNameId) {
    const dropzone = document.getElementById(dropzoneId);
    const input = document.getElementById(inputId);
    const fileName = document.getElementById(fileNameId);

    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropzone.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    ['dragenter', 'dragover'].forEach(eventName => {
        dropzone.addEventListener(eventName, () => dropzone.classList.add('dragover'), false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropzone.addEventListener(eventName, () => dropzone.classList.remove('dragover'), false);
    });

    dropzone.addEventListener('drop', function(e) {
        let dt = e.dataTransfer;
        if (dt && dt.files && dt.files.length) {
            let file = dt.files[0];
            let accept = input.getAttribute('accept');
            if (accept) {
                let allowedExtensions = accept.split(',').map(ext => ext.trim().toLowerCase().replace('.', ''));
                let fileExtension = file.name.split('.').pop().toLowerCase();
                if (!allowedExtensions.includes(fileExtension)) {
                    alert('Invalid file type. Allowed types: ' + accept);
                    return;
                }
            }
            input.files = dt.files;
            input.dispatchEvent(new Event('change'));
        }
    }, false);

    input.addEventListener('change', function(e) {
        if(this.files && this.files[0]) {
            let file = this.files[0];
            let accept = input.getAttribute('accept');
            if (accept) {
                let allowedExtensions = accept.split(',').map(ext => ext.trim().toLowerCase().replace('.', ''));
                let fileExtension = file.name.split('.').pop().toLowerCase();
                if (!allowedExtensions.includes(fileExtension)) {
                    alert('Invalid file type. Allowed types: ' + accept);
                    this.value = ''; // clear input
                    dropzone.classList.remove('has-file');
                    fileName.innerHTML = dropzoneId === 'single-dropzone' ? 'Click or drag photo to upload' : 'Click or drag ZIP file here';
                    dropzone.querySelector('.csv-dropzone-icon').classList.replace('bi-file-earmark-check', dropzoneId === 'single-dropzone' ? 'bi-cloud-arrow-up' : 'bi-file-earmark-zip');
                    if (dropzoneId === 'bulk-dropzone') {
                        document.getElementById('zip-preview-container').style.display = 'none';
                    }
                    return;
                }
            }

            dropzone.classList.add('has-file');
            fileName.innerHTML = file.name;
            dropzone.querySelector('.csv-dropzone-icon').classList.replace('bi-cloud-arrow-up', 'bi-file-earmark-check');
            dropzone.querySelector('.csv-dropzone-icon').classList.replace('bi-file-earmark-zip', 'bi-file-earmark-check');

            if (dropzoneId === 'bulk-dropzone') {
                previewZip(this.files[0]);
            }
        } else {
            dropzone.classList.remove('has-file');
            fileName.innerHTML = dropzoneId === 'single-dropzone' ? 'Click or drag photo to upload' : 'Click or drag ZIP file here';
            dropzone.querySelector('.csv-dropzone-icon').classList.replace('bi-file-earmark-check', dropzoneId === 'single-dropzone' ? 'bi-cloud-arrow-up' : 'bi-file-earmark-zip');
            if (dropzoneId === 'bulk-dropzone') {
                document.getElementById('zip-preview-container').style.display = 'none';
            }
        }
    });
}

function previewZip(file) {
    const container = document.getElementById('zip-preview-container');
    const loading = document.getElementById('zip-preview-loading');
    const content = document.getElementById('zip-preview-content');

    container.style.display = 'block';
    loading.style.display = 'block';
    content.innerHTML = '';

    const formData = new FormData();
    formData.append('zip_file', file);
    formData.append('_token', '{{ csrf_token() }}');

    fetch('{{ route("admin.staff_details.upload-photos.preview") }}', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        loading.style.display = 'none';
        if(data.success) {
            let html = '';
            if (data.matched.length > 0) {
                html += '<ul class="list-group mb-3" style="font-size:13px;">';
                data.matched.forEach(item => {
                    let badge = item.has_existing ? '<span class="badge bg-warning text-dark ms-2">Will overwrite if checked</span>' : '<span class="badge bg-success ms-2">New</span>';
                    html += `<li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <i class="bi bi-person me-2"></i> ${item.name} <span class="text-muted">(Emp ID: ${item.employee_id})</span>
                        </div>
                        ${badge}
                    </li>`;
                });
                html += '</ul>';
            } else {
                html += '<div class="alert alert-warning py-2" style="font-size:13px;">No matching staff found for the images in this ZIP.</div>';
            }

            if (data.unmatched.length > 0) {
                html += '<div class="fw-bold mb-1 mt-3 text-danger" style="font-size:13px;">Unmatched Files (will be skipped):</div>';
                html += '<div style="font-size:12.5px;color:var(--sb-muted);">' + data.unmatched.join(', ') + '</div>';
            }

            content.innerHTML = html;
        } else {
            content.innerHTML = `<div class="text-danger" style="font-size:13px;">${data.message || 'Error processing ZIP preview.'}</div>`;
        }
    })
    .catch(err => {
        loading.style.display = 'none';
        content.innerHTML = `<div class="text-danger" style="font-size:13px;">Error processing ZIP preview. Ensure the file is a valid ZIP.</div>`;
    });
}

setupDropzone('single-dropzone', 'single-photo-input', 'single-file-name');
setupDropzone('bulk-dropzone', 'bulk-zip-input', 'bulk-file-name');

// Auto-hide alerts after 5 seconds
setTimeout(() => {
    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(alert => {
        // Only auto-hide success/error alerts, not form validation error lists if you prefer
        // But since both are .alert, we can hide them. Let's use Bootstrap's alert close
        const bsAlert = new bootstrap.Alert(alert);
        bsAlert.close();
    });
}, 5000);
</script>
@endpush
