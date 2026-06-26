@extends('layouts.admin')

@section('title', 'Staff Details')
@section('admin_nav_staff', 'active')
@section('admin_page_title', 'Staff Details')

@section('admin_content')

<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('admin.staff_details.index') }}"
       class="sb-icon-btn" style="width:34px;height:34px;border-radius:7px;font-size:16px;">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div>
        <h5 class="fw-semibold mb-0" style="font-size:18px;">Staff Profile</h5>
        <p class="mb-0" style="font-size:13px;color:var(--sb-muted);">
            Viewing full details for {{ $staff->name }}.
        </p>
    </div>
</div>

<div class="row g-4">
    <div class="col-12 col-md-4">
        <div class="sb-panel p-4 text-center">
            <div class="mb-3 position-relative d-inline-block">
                @if(!empty($staff->user_img))
                    <img src="{{ asset($staff->user_img) }}" alt="Photo" style="width:120px;height:120px;object-fit:cover;border-radius:50%;border:4px solid #fff;box-shadow:0 4px 12px rgba(0,0,0,0.1);">
                @else
                    <div style="width:120px;height:120px;border-radius:50%;background:#F0FDF4;display:flex;align-items:center;justify-content:center;color:#4ADE80;font-size:48px;margin:auto;box-shadow:0 4px 12px rgba(0,0,0,0.1);">
                        <i class="bi bi-person"></i>
                    </div>
                @endif
            </div>
            <h5 class="fw-bold mb-1">{{ $staff->name }}</h5>
            <p style="font-size:14px;color:var(--sb-muted);margin-bottom:15px;">{{ $staff->email }}</p>
            <div class="d-flex justify-content-center gap-2">
                <a href="{{ route('admin.staff_details.edit', $staff) }}" class="btn btn-sm btn-outline-primary" style="font-size:13px;border-radius:6px;">
                    <i class="bi bi-pencil me-1"></i> Edit
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-12 col-md-8">
        <div class="sb-panel">
            <div class="sb-panel-header">
                <div class="sb-panel-title"><i class="bi bi-briefcase-fill me-2"></i>Employment Details</div>
            </div>
            <div class="p-4">
                <div class="row g-4">
                    <div class="col-sm-6">
                        <label style="font-size:12px;font-weight:600;color:var(--sb-muted);text-transform:uppercase;">Employee ID</label>
                        <div style="font-size:14px;font-weight:500;color:var(--sb-text);">{{ $staff->staff->employee_id ?? '—' }}</div>
                    </div>
                    <div class="col-sm-6">
                        <label style="font-size:12px;font-weight:600;color:var(--sb-muted);text-transform:uppercase;">Category</label>
                        <div style="font-size:14px;font-weight:500;color:var(--sb-text);">{{ $staff->staff?->category?->name ?? '—' }}</div>
                    </div>
                    <div class="col-sm-6">
                        <label style="font-size:12px;font-weight:600;color:var(--sb-muted);text-transform:uppercase;">Designation</label>
                        <div style="font-size:14px;font-weight:500;color:var(--sb-text);">{{ $staff->staff->designation ?? '—' }}</div>
                    </div>
                    <div class="col-sm-6">
                        <label style="font-size:12px;font-weight:600;color:var(--sb-muted);text-transform:uppercase;">Department</label>
                        <div style="font-size:14px;font-weight:500;color:var(--sb-text);">{{ $staff->staff->department ?? '—' }}</div>
                    </div>
                    <div class="col-sm-6">
                        <label style="font-size:12px;font-weight:600;color:var(--sb-muted);text-transform:uppercase;">Phone Number</label>
                        <div style="font-size:14px;font-weight:500;color:var(--sb-text);">{{ $staff->phone ?? '—' }}</div>
                    </div>
                    <div class="col-sm-6">
                        <label style="font-size:12px;font-weight:600;color:var(--sb-muted);text-transform:uppercase;">Salary</label>
                        <div style="font-size:14px;font-weight:500;color:var(--sb-text);">{{ $staff->staff && $staff->staff->salary ? '₹' . number_format($staff->staff->salary, 2) : '—' }}</div>
                    </div>
                    <div class="col-sm-6">
                        <label style="font-size:12px;font-weight:600;color:var(--sb-muted);text-transform:uppercase;">Date Joined</label>
                        <div style="font-size:14px;font-weight:500;color:var(--sb-text);">{{ $staff->created_at->format('d M Y, h:i A') }}</div>
                    </div>
                </div>
            </div>
            
            <div class="sb-panel-header mt-2" style="border-top:1px solid var(--sb-border);">
                <div class="sb-panel-title"><i class="bi bi-shield-lock-fill me-2"></i>Permissions & Access</div>
            </div>
            <div class="p-4">
                <div class="row g-4">
                    <div class="col-12">
                        <label style="font-size:12px;font-weight:600;color:var(--sb-muted);text-transform:uppercase;margin-bottom:8px;display:block;">Permissions</label>
                        @if($staff->permissions->count() > 0)
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($staff->permissions as $perm)
                                    <span style="background:#EEF2FF;color:#4F46E5;padding:6px 12px;border-radius:6px;font-size:13px;font-weight:500;">
                                        {{ $perm->name }}
                                    </span>
                                @endforeach
                            </div>
                        @else
                            <div style="color:var(--sb-muted);font-size:13px;">No special permissions assigned.</div>
                        @endif
                    </div>
                    <div class="col-12">
                        <label style="font-size:12px;font-weight:600;color:var(--sb-muted);text-transform:uppercase;margin-bottom:8px;display:block;">Assigned Classes</label>
                        @if($staff->classes->count() > 0)
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($staff->classes as $cls)
                                    <span style="background:#FFF7ED;color:#EA580C;padding:6px 12px;border-radius:6px;font-size:13px;font-weight:500;">
                                        {{ $cls->standard }} {{ $cls->section ? '- ' . $cls->section : '' }}
                                    </span>
                                @endforeach
                            </div>
                        @else
                            <div style="color:var(--sb-muted);font-size:13px;">No classes assigned.</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
