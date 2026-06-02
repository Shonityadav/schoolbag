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
                    <div class="col-12 col-md-6">
                        <label class="form-label" style="font-size:13px;font-weight:600;">Standard <span class="text-danger">*</span></label>
                        <input type="text" name="standard" value="{{ old('standard', $class?->standard) }}"
                               class="form-control @error('standard') is-invalid @enderror"
                               placeholder="e.g. 10th, 11th, XII"
                               style="font-size:13.5px;border-color:var(--sb-border);border-radius:8px;">
                        @error('standard')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label" style="font-size:13px;font-weight:600;">Section <span class="text-danger">*</span></label>
                        <input type="text" name="section" value="{{ old('section', $class?->section) }}"
                               class="form-control @error('section') is-invalid @enderror"
                               placeholder="e.g. A, B, Science"
                               style="font-size:13.5px;border-color:var(--sb-border);border-radius:8px;">
                        @error('section')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label" style="font-size:13px;font-weight:600;">Description</label>
                        <textarea name="description"
                                  class="form-control @error('description') is-invalid @enderror"
                                  rows="3"
                                  placeholder="Optional details about this class"
                                  style="font-size:13.5px;border-color:var(--sb-border);border-radius:8px;">{{ old('description', $class?->description) }}</textarea>
                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
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
