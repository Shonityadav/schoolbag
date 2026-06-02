@extends('layouts.admin')

@php $isEdit = isset($class) && $class !== null; @endphp
@section('title', $isEdit ? 'Edit Class' : 'Add Class')
@section('admin_nav_classes', 'active')
@section('admin_page_title', $isEdit ? 'Edit Class' : 'Add Class')

@section('admin_content')

{{-- Header --}}
<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('admin.classes.index') }}"
       class="sb-icon-btn" style="width:34px;height:34px;border-radius:7px;font-size:16px;">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div>
        <h5 class="fw-semibold mb-0" style="font-size:18px;">{{ $isEdit ? 'Edit Class' : 'Add New Class' }}</h5>
        <p class="mb-0" style="font-size:13px;color:var(--sb-muted);">
            {{ $isEdit ? 'Update class details below.' : 'Create a new class group or section.' }}
        </p>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-12 col-lg-7">
        <div class="sb-panel">
            <div class="sb-panel-header">
                <div class="sb-panel-title">
                    <i class="bi bi-building-gear me-2"></i>
                    {{ $isEdit ? 'Class Details' : 'New Class' }}
                </div>
                @if($isEdit)
                    <span style="font-size:12px;color:var(--sb-muted);">ID: #{{ $class->id }}</span>
                @endif
            </div>

            <form method="POST"
                  action="{{ $isEdit ? route('admin.classes.update', $class) : route('admin.classes.store') }}"
                  class="p-4">
                @csrf
                @if($isEdit) @method('PUT') @endif

                @if($errors->any())
                    <div class="alert alert-danger d-flex gap-2 mb-4" style="border-radius:8px;font-size:13px;">
                        <i class="bi bi-exclamation-triangle-fill mt-1 flex-shrink-0"></i>
                        <div>
                            <div class="fw-bold mb-1">Please fix the following errors:</div>
                            <ul class="mb-0 ps-3">
                                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                <div class="row g-3">
                    <div class="col-12 col-md-8">
                        <label class="form-label" style="font-size:13px;font-weight:600;">Class Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $class?->name) }}"
                               class="form-control @error('name') is-invalid @enderror"
                               placeholder="e.g. Class 1A"
                               style="font-size:13.5px;border-color:var(--sb-border);border-radius:8px;">
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12 col-md-4">
                        <label class="form-label" style="font-size:13px;font-weight:600;">Level <span class="text-danger">*</span></label>
                        <input type="number" name="level" value="{{ old('level', $class?->level ?? 1) }}"
                               class="form-control @error('level') is-invalid @enderror"
                               min="1" max="12"
                               style="font-size:13.5px;border-color:var(--sb-border);border-radius:8px;">
                        @error('level')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label" style="font-size:13px;font-weight:600;">Theme Color</label>
                        <div class="d-flex align-items-center gap-2">
                            <input type="color" name="color" value="{{ old('color', $class?->color ?? '#4F46E5') }}"
                                   class="form-control form-control-color"
                                   style="width:50px;border-radius:8px;padding:4px;cursor:pointer;">
                            <span style="font-size:12.5px;color:var(--sb-muted);">Hex Color Code</span>
                        </div>
                        @error('color')<div class="text-danger" style="font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label" style="font-size:13px;font-weight:600;">Icon Class</label>
                        <input type="text" name="icon" value="{{ old('icon', $class?->icon ?? 'bi-building') }}"
                               class="form-control @error('icon') is-invalid @enderror"
                               placeholder="e.g. bi-building"
                               style="font-size:13.5px;border-color:var(--sb-border);border-radius:8px;">
                        <div style="font-size:11.5px;color:var(--sb-muted);margin-top:6px;">
                            Uses <a href="https://icons.getbootstrap.com/" target="_blank" style="color:var(--sb-accent);">Bootstrap Icons</a>
                        </div>
                        @error('icon')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4 pt-3" style="border-top:1px solid var(--sb-border);">
                    <button type="submit"
                            class="btn text-white d-flex align-items-center gap-2"
                            style="font-size:13.5px;border-radius:8px;background:var(--sb-accent);padding:10px 22px;">
                        <i class="bi bi-{{ $isEdit ? 'check-lg' : 'plus-lg' }}"></i>
                        {{ $isEdit ? 'Save Changes' : 'Create Class' }}
                    </button>
                    <a href="{{ route('admin.classes.index') }}"
                       class="btn btn-outline-secondary"
                       style="font-size:13.5px;border-radius:8px;padding:10px 20px;border-color:var(--sb-border);">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
