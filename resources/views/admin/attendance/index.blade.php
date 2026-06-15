@extends('layouts.admin')

@section('title', 'Attendance Management')
@section('admin_page_title', 'Attendance')
@section('admin_nav_attendance', 'active')

@section('admin_content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1" style="font-weight: 600; color: var(--sb-text);">User Attendance</h4>
        <p class="text-muted mb-0" style="font-size: 13px;">Manage daily attendance for students and staff.</p>
    </div>
    
    <div class="d-flex align-items-center gap-3">
        <form action="{{ route('admin.attendance.index') }}" method="GET" class="d-flex align-items-center gap-2">
            <select name="user_type" class="form-select form-select-sm" style="width: auto; cursor: pointer; box-shadow: none;" onchange="this.form.submit()">
                @if(auth()->user()->hasPermission('student_details.view'))
                    <option value="3" {{ $userType == '3' ? 'selected' : '' }}>
                        Students
                    </option>
                @endif

                @if(auth()->user()->hasPermission('staff.view'))
                    <option value="2" {{ $userType == '2' ? 'selected' : '' }}>
                        Staff
                    </option>
                @endif
            </select>
            
            @if($userType == '3')
            <div class="position-relative d-flex align-items-center">
                <select name="class_id" id="classFilterSelect" class="form-select form-select-sm {{ !empty($classId) ? 'pe-5' : '' }}" style="width: auto; cursor: pointer; box-shadow: none;" onchange="this.form.submit()">
                    <option value="">Select Class</option>
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}" {{ $classId == $c->id ? 'selected' : '' }}>Class {{ $c->standard }} - {{ $c->section }}</option>
                    @endforeach
                </select>
                @if(!empty($classId))
                    <i class="bi bi-x" style="position: absolute; right: 32px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #475569; font-size: 18px; font-weight: bold; z-index: 10;" onclick="document.getElementById('classFilterSelect').value=''; document.getElementById('classFilterSelect').form.submit();" title="Clear Selection"></i>
                @endif
            </div>
            @endif

            <input type="date" name="date" id="date" value="{{ $date }}" class="form-control form-control-sm" style="width: auto; box-shadow: none;" onchange="this.form.submit()">
        </form>
        
        @if($users->count() > 0)
            <button type="submit" form="bulkAttendanceForm" id="saveAttendanceBtn" class="btn btn-sm btn-primary px-3 d-flex align-items-center gap-1" style="background-color: #1B74F3; border: none; font-weight: 500; box-shadow: none;" disabled>
                <i class="bi bi-save"></i> Save
            </button>
        @endif
    </div>
</div>

<div class="sb-panel pt-3">
    <form id="bulkAttendanceForm" action="{{ route('admin.attendance.markBulk') }}" method="POST">
        @csrf
        <input type="hidden" name="attendance_date" value="{{ $date }}">

    <div class="table-responsive">
        <table class="sb-table">
            <thead>
                <tr>
                    <th>Name</th>
                    @if($userType == '3')
                    <th>Class</th>
                    <th>Section</th>
                    @endif
                    <th>Status</th>
                    <th class="text-end">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    @php
                        $attendance = $user->attendances->first();
                        $status = $attendance ? $attendance->status : 'Unmarked';
                    @endphp
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="sb-avatar-sm">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="fw-semibold">{{ $user->name }}</div>
                                    <div style="font-size: 11px; color: var(--sb-muted);">{{ $user->email }}</div>
                                </div>
                            </div>
                        </td>
                        
                        @if($userType == '3')
                        <td>{{ $user->studentClass ? $user->studentClass->standard : 'N/A' }}</td>
                        <td>{{ $user->studentClass ? $user->studentClass->section : 'N/A' }}</td>
                        @endif

                        <td>
                            @if($status === 'Present')
                                <span class="sb-badge paid"><i class="bi bi-check-circle-fill" style="margin-right: 4px;"></i> Present</span>
                            @elseif($status === 'Absent')
                                <span class="sb-badge overdue"><i class="bi bi-x-circle-fill" style="margin-right: 4px;"></i> Absent</span>
                            @else
                                <span class="sb-badge pending" style="background: #e2e8f0; color: #475569;"><i class="bi bi-dash-circle-fill" style="margin-right: 4px;"></i> Unmarked</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <input type="hidden" name="records[{{ $loop->index }}][created_for]" value="{{ $user->id }}">
                            <div class="d-flex gap-3 justify-content-end align-items-center">
                                <div class="form-check form-check-inline mb-0">
                                    <input class="form-check-input attendance-radio" type="radio" name="records[{{ $loop->index }}][status]" id="status_present_{{ $user->id }}" value="Present" {{ $status === 'Present' ? 'checked' : '' }} style="cursor: pointer;">
                                    <label class="form-check-label" style="color: var(--sb-green); font-weight: 700; cursor: pointer;" for="status_present_{{ $user->id }}">Present</label>
                                </div>
                                <div class="form-check form-check-inline mb-0">
                                    <input class="form-check-input attendance-radio" type="radio" name="records[{{ $loop->index }}][status]" id="status_absent_{{ $user->id }}" value="Absent" {{ $status === 'Absent' ? 'checked' : '' }} style="cursor: pointer;">
                                    <label class="form-check-label" style="color: var(--sb-red); font-weight: 700; cursor: pointer;" for="status_absent_{{ $user->id }}">Absent</label>
                                </div>
                                <div class="form-check form-check-inline mb-0 me-0">
                                    <input class="form-check-input attendance-radio" type="radio" name="records[{{ $loop->index }}][status]" id="status_clear_{{ $user->id }}" value="Clear" {{ $status === 'Unmarked' ? 'checked' : '' }} style="cursor: pointer;">
                                    <label class="form-check-label text-muted fw-bold" style="cursor: pointer;" for="status_clear_{{ $user->id }}">Unmarked</label>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $userType == '3' ? 5 : 4 }}" class="text-center text-muted py-5">
                            <i class="bi bi-people" style="font-size: 32px; color: #cbd5e1; display: block; margin-bottom: 8px;"></i>
                            @if($userType == '3' && empty($classId))
                                <span style="font-weight: 500; font-size: 16px;">Please select a Class to view student_details.</span>
                            @else
                                <span style="font-weight: 500; font-size: 16px;">No users found.</span>
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    </form>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const radios = document.querySelectorAll('.attendance-radio');
        const saveBtn = document.getElementById('saveAttendanceBtn');

        if(saveBtn && radios.length > 0) {
            radios.forEach(radio => {
                radio.addEventListener('change', function() {
                    saveBtn.disabled = false;
                });
            });
        }
    });
</script>

@endsection
