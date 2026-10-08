@extends('layouts.admin')

@section('title', 'Homework Assignment')
@section('admin_page_title', 'Homework Assignment')
@section('admin_nav_homework', 'active')

@push('admin-styles')
<style>
    .sb-homework-container {
        max-width: 900px;
        margin: 0 auto;
    }
    .homework-form-card {
        background: var(--sb-card);
        border-radius: 12px;
        border: 1px solid var(--sb-border);
        padding: 24px;
        margin-bottom: 24px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.03);
    }
    .homework-form-card .form-label {
        font-weight: 600;
        font-size: 13px;
        color: var(--sb-text);
        margin-bottom: 8px;
    }
    .homework-form-card .form-control, .homework-form-card .form-select {
        border-radius: 8px;
        border: 1px solid var(--sb-border);
        font-size: 14px;
        padding: 10px 14px;
    }
    .homework-form-card .form-control:focus, .homework-form-card .form-select:focus {
        border-color: var(--sb-accent);
        box-shadow: 0 0 0 3px rgba(37,99,235,0.1);
    }
    .btn-submit {
        background: var(--sb-accent);
        color: #fff;
        border: none;
        padding: 10px 24px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 14px;
        transition: background 0.2s;
    }
    .btn-submit:hover {
        background: var(--sb-accent-hover);
        color: #fff;
    }
    .homework-list-card {
        background: var(--sb-card);
        border-radius: 12px;
        border: 1px solid var(--sb-border);
        overflow: hidden;
    }
    .hw-content-preview {
        max-width: 300px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>
@endpush

@section('admin_content')
<div class="sb-homework-container">

    <div class="homework-form-card">
        <h5 class="mb-4" style="font-weight: 700; color: var(--sb-text);">
            <i class="bi bi-pencil-square me-2 text-primary"></i> Assign Homework
        </h5>

        <form action="{{ route('admin.homework.store') }}" method="POST">
            @csrf
            
            <div class="row mb-3">
                <div class="col-md-6 mb-3 mb-md-0">
                    <label class="form-label">Select Class</label>
                    <select name="class_id" class="form-select" required>
                        <option value="">-- Choose Class --</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" {{ old('class_id') == $c->id ? 'selected' : '' }}>
                                {{ $c->standard }} {{ $c->section }}
                            </option>
                        @endforeach
                    </select>
                    @error('class_id') <span class="text-danger small">{{ $message }}</span> @enderror
                </div>
                
                <div class="col-md-6">
                    <label class="form-label">Date</label>
                    <input type="date" name="for_date" class="form-control" value="{{ old('for_date', date('Y-m-d')) }}" required>
                    @error('for_date') <span class="text-danger small">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label">Homework Content</label>
                <textarea name="content" rows="4" class="form-control" placeholder="E.g. English - Read Chapter 2&#10;Maths - Complete Ex 4.1" required>{{ old('content') }}</textarea>
                <div class="form-text mt-2" style="font-size: 12px; color: var(--sb-muted);">
                    Use bullet points or plain text. This will be shown exactly as written on the student's workspace.
                </div>
                @error('content') <span class="text-danger small">{{ $message }}</span> @enderror
            </div>

            <div class="d-flex justify-content-end">
                <button type="submit" class="btn-submit">
                    <i class="bi bi-send me-2"></i> Post Homework
                </button>
            </div>
        </form>
    </div>

    <h5 class="mb-3" style="font-weight: 700; color: var(--sb-text);">Recent Assignments</h5>
    
    <div class="homework-list-card">
        <div class="table-responsive">
            <table class="sb-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Class</th>
                        <th>Content</th>
                        <th>Posted By</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($homeworks as $hw)
                    <tr>
                        <td>
                            <div style="font-weight: 600; color: var(--sb-text);">
                                {{ \Carbon\Carbon::parse($hw->for_date)->format('d M Y') }}
                            </div>
                            @if(\Carbon\Carbon::parse($hw->for_date)->isToday())
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25" style="font-size: 10px;">Today</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25">
                                {{ $hw->studentClass->standard ?? '' }} {{ $hw->studentClass->section ?? '' }}
                            </span>
                        </td>
                        <td>
                            <div class="hw-content-preview text-muted" title="{{ $hw->content }}">
                                {{ Str::limit($hw->content, 50) }}
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="sb-avatar-sm">
                                    {{ strtoupper(substr($hw->teacher->name ?? 'A', 0, 1)) }}
                                </div>
                                <span style="font-size: 12px; font-weight: 500;">{{ $hw->teacher->name ?? 'Unknown' }}</span>
                            </div>
                        </td>
                        <td class="text-end">
                            <form action="{{ route('admin.homework.destroy', $hw->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Are you sure you want to delete this homework?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="bi bi-journal-x fs-1 d-block mb-3" style="color: var(--sb-border);"></i>
                            No homework assigned recently.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
