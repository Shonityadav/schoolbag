@extends('layouts.admin')

@section('title', 'Student Details')
@section('admin_nav_students', 'active')
@section('admin_page_title', 'Student Details')

@section('admin_content')

<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('admin.student_details.index') }}"
       class="sb-icon-btn" style="width:34px;height:34px;border-radius:7px;font-size:16px;">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div>
        <h5 class="fw-semibold mb-0" style="font-size:18px;">Student Profile</h5>
        <p class="mb-0" style="font-size:13px;color:var(--sb-muted);">
            Viewing full details for {{ $student->name }}.
        </p>
    </div>
</div>

<div class="row g-4">
    <div class="col-12 col-md-4">
        <div class="sb-panel p-4 text-center">
            <div class="mb-3 position-relative d-inline-block">
                @if(!empty($student->user_img))
                    <img src="{{ asset($student->user_img) }}" alt="Photo" style="width:120px;height:120px;object-fit:cover;border-radius:50%;border:4px solid #fff;box-shadow:0 4px 12px rgba(0,0,0,0.1);">
                @else
                    <div style="width:120px;height:120px;border-radius:50%;background:#F1F5F9;display:flex;align-items:center;justify-content:center;color:#94A3B8;font-size:48px;margin:auto;box-shadow:0 4px 12px rgba(0,0,0,0.1);">
                        <i class="bi bi-person"></i>
                    </div>
                @endif
            </div>
            <h5 class="fw-bold mb-1">{{ $student->name }}</h5>
            <p style="font-size:14px;color:var(--sb-muted);margin-bottom:15px;">{{ $student->email }}</p>
            <div class="d-flex justify-content-center gap-2">
                <a href="{{ route('admin.student_details.edit', $student) }}" class="btn btn-sm btn-outline-primary" style="font-size:13px;border-radius:6px;">
                    <i class="bi bi-pencil me-1"></i> Edit
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-12 col-md-8">
        <div class="sb-panel">
            <div class="sb-panel-header">
                <div class="sb-panel-title"><i class="bi bi-person-lines-fill me-2"></i>Academic Details</div>
            </div>
            <div class="p-4">
                <div class="row g-4">
                    <div class="col-sm-6">
                        <label style="font-size:12px;font-weight:600;color:var(--sb-muted);text-transform:uppercase;">Admission Number</label>
                        <div style="font-size:14px;font-weight:500;color:var(--sb-text);">{{ $student->student->admission_number ?? '—' }}</div>
                    </div>
                    <div class="col-sm-6">
                        <label style="font-size:12px;font-weight:600;color:var(--sb-muted);text-transform:uppercase;">Roll Number</label>
                        <div style="font-size:14px;font-weight:500;color:var(--sb-text);">{{ $student->student->roll_no ?? '—' }}</div>
                    </div>
                    <div class="col-sm-6">
                        <label style="font-size:12px;font-weight:600;color:var(--sb-muted);text-transform:uppercase;">Class & Section</label>
                        <div style="font-size:14px;font-weight:500;color:var(--sb-text);">
                            @if($student->student && $student->student->class)
                                {{ $student->student->class->standard }} - {{ $student->student->class->section }}
                            @else
                                —
                            @endif
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <label style="font-size:12px;font-weight:600;color:var(--sb-muted);text-transform:uppercase;">Phone Number</label>
                        <div style="font-size:14px;font-weight:500;color:var(--sb-text);">{{ $student->phone ?? '—' }}</div>
                    </div>
                    <div class="col-sm-6">
                        <label style="font-size:12px;font-weight:600;color:var(--sb-muted);text-transform:uppercase;">Fee Amount</label>
                        <div style="font-size:14px;font-weight:500;color:var(--sb-text);">{{ $student->student && $student->student->fee ? '₹' . number_format($student->student->fee, 2) : '—' }}</div>
                    </div>
                    <div class="col-sm-6">
                        <label style="font-size:12px;font-weight:600;color:var(--sb-muted);text-transform:uppercase;">Fee Period</label>
                        <div style="font-size:14px;font-weight:500;color:var(--sb-text);">{{ $student->student->fee_period ?? '—' }}</div>
                    </div>
                    <div class="col-sm-6">
                        <label style="font-size:12px;font-weight:600;color:var(--sb-muted);text-transform:uppercase;">Date Joined</label>
                        <div style="font-size:14px;font-weight:500;color:var(--sb-text);">{{ $student->created_at->format('d M Y, h:i A') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
