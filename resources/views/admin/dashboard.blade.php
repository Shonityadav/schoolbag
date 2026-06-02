@extends('layouts.admin')

@section('title', 'Dashboard')
@section('admin_nav_dashboard', 'active')
@section('admin_page_title', 'Dashboard')

@section('admin_content')

{{-- ══════════════════════════════════════
     PAGE HEADER
══════════════════════════════════════ --}}
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h5 class="fw-semibold mb-0" style="font-size:18px;">Good morning, Admin 👋</h5>
        <p class="mb-0" style="font-size:13px;color:var(--sb-muted);">Here's what's happening at your school today.</p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-2" style="font-size:13px;border-radius:7px;border-color:var(--sb-border);">
            <i class="bi bi-download"></i> Export
        </button>
        <button class="btn btn-sm text-white d-flex align-items-center gap-2" style="font-size:13px;border-radius:7px;background:var(--sb-accent);">
            <i class="bi bi-plus"></i> Add Student
        </button>
    </div>
</div>

{{-- ══════════════════════════════════════
     STAT CARDS ROW
══════════════════════════════════════ --}}
<div class="row g-3 mb-4">

    {{-- Total Students --}}
    <div class="col-6 col-xl-3">
        <div class="sb-stat-card">
            <div class="d-flex align-items-start justify-content-between mb-3">
                <div class="sb-stat-icon blue"><i class="bi bi-people-fill"></i></div>
                <span class="sb-stat-change up"><i class="bi bi-arrow-up"></i> 4.2%</span>
            </div>
            <div class="sb-stat-value">{{ number_format($stats['total_students']) }}</div>
            <div class="sb-stat-label">Total Students</div>
            <div class="mt-2" style="font-size:12px;color:var(--sb-muted);">
                <span style="color:var(--sb-green);font-weight:600;">+{{ $stats['new_admissions'] }}</span> new this month
            </div>
        </div>
    </div>

    {{-- Total Staff --}}
    <div class="col-6 col-xl-3">
        <div class="sb-stat-card">
            <div class="d-flex align-items-start justify-content-between mb-3">
                <div class="sb-stat-icon green"><i class="bi bi-person-badge-fill"></i></div>
                <span class="sb-stat-change up"><i class="bi bi-arrow-up"></i> 2.1%</span>
            </div>
            <div class="sb-stat-value">{{ $stats['total_staff'] }}</div>
            <div class="sb-stat-label">Total Staff</div>
            <div class="mt-2" style="font-size:12px;color:var(--sb-muted);">
                Teachers &amp; support team
            </div>
        </div>
    </div>

    {{-- Total Classes --}}
    <div class="col-6 col-xl-3">
        <div class="sb-stat-card">
            <div class="d-flex align-items-start justify-content-between mb-3">
                <div class="sb-stat-icon orange"><i class="bi bi-building-fill"></i></div>
                <span class="sb-stat-change neutral">+1 new</span>
            </div>
            <div class="sb-stat-value">{{ $stats['total_classes'] }}</div>
            <div class="sb-stat-label">Total Classes</div>
            <div class="mt-2" style="font-size:12px;color:var(--sb-muted);">
                {{ $stats['active_courses'] }} active courses running
            </div>
        </div>
    </div>

    {{-- Attendance Rate --}}
    <div class="col-6 col-xl-3">
        <div class="sb-stat-card">
            <div class="d-flex align-items-start justify-content-between mb-3">
                <div class="sb-stat-icon purple"><i class="bi bi-clipboard2-check-fill"></i></div>
                <span class="sb-stat-change down"><i class="bi bi-arrow-down"></i> 0.8%</span>
            </div>
            <div class="sb-stat-value">{{ $stats['attendance_rate'] }}%</div>
            <div class="sb-stat-label">Avg. Attendance</div>
            <div class="mt-2" style="font-size:12px;color:var(--sb-muted);">
                Monthly average across all classes
            </div>
        </div>
    </div>

</div>

{{-- ══════════════════════════════════════
     TRANSACTIONS + REVENUE
══════════════════════════════════════ --}}
<div class="row g-3 mb-4">

    {{-- Transactions Table --}}
    <div class="col-12 col-xl-8">
        <div class="sb-panel h-100">
            <div class="sb-panel-header">
                <div>
                    <div class="sb-panel-title">Recent Transactions</div>
                    <div style="font-size:12px;color:var(--sb-muted);margin-top:2px;">Latest fee payments &amp; dues</div>
                </div>
                <a href="#" class="sb-panel-action">View all <i class="bi bi-arrow-right"></i></a>
            </div>
            <div class="table-responsive">
                <table class="sb-table">
                    <thead>
                        <tr>
                            <th>Txn ID</th>
                            <th>Student</th>
                            <th>Class</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentTransactions as $txn)
                        <tr>
                            <td>
                                <code style="font-size:11.5px;color:var(--sb-muted);background:#F1F5F9;padding:2px 6px;border-radius:4px;">{{ $txn['id'] }}</code>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="sb-avatar-sm">{{ mb_strtoupper(mb_substr($txn['student'], 0, 1)) }}</div>
                                    <span class="fw-500">{{ $txn['student'] }}</span>
                                </div>
                            </td>
                            <td style="color:var(--sb-muted);">{{ $txn['class'] }}</td>
                            <td style="font-weight:600;font-variant-numeric:tabular-nums;">
                                ₹{{ number_format($txn['amount']) }}
                            </td>
                            <td><span class="sb-badge {{ $txn['status'] }}">{{ ucfirst($txn['status']) }}</span></td>
                            <td style="color:var(--sb-muted);font-size:12px;white-space:nowrap;">{{ $txn['date'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Revenue Summary --}}
    <div class="col-12 col-xl-4">
        <div class="sb-panel h-100">
            <div class="sb-panel-header">
                <div>
                    <div class="sb-panel-title">Revenue Summary</div>
                    <div style="font-size:12px;color:var(--sb-muted);margin-top:2px;">June 2025</div>
                </div>
            </div>
            <div class="p-4">
                <div class="sb-revenue-num">₹{{ number_format($stats['monthly_revenue']) }}</div>
                <div class="sb-revenue-sub">Total billed this month</div>

                {{-- Collection Progress --}}
                <div class="mt-4 mb-1 d-flex justify-content-between" style="font-size:12px;">
                    <span style="color:var(--sb-muted);">Collection rate</span>
                    @php $pct = round((($stats['monthly_revenue'] - $stats['pending_fees']) / $stats['monthly_revenue']) * 100); @endphp
                    <span style="font-weight:600;color:var(--sb-text);">{{ $pct }}%</span>
                </div>
                <div class="progress" style="height:6px;border-radius:999px;background:var(--sb-border);">
                    <div class="progress-bar" role="progressbar" style="width:{{ $pct }}%;background:var(--sb-accent);border-radius:999px;"></div>
                </div>

                {{-- KV pairs --}}
                <div class="mt-4">
                    <div class="sb-kv">
                        <span class="sb-kv-key">Collected</span>
                        <span class="sb-kv-val" style="color:var(--sb-green);">₹{{ number_format($stats['monthly_revenue'] - $stats['pending_fees']) }}</span>
                    </div>
                    <div class="sb-kv">
                        <span class="sb-kv-key">Pending Fees</span>
                        <span class="sb-kv-val" style="color:var(--sb-orange);">₹{{ number_format($stats['pending_fees']) }}</span>
                    </div>
                    <div class="sb-kv">
                        <span class="sb-kv-key">New Admissions</span>
                        <span class="sb-kv-val">{{ $stats['new_admissions'] }}</span>
                    </div>
                    <div class="sb-kv">
                        <span class="sb-kv-key">Active Courses</span>
                        <span class="sb-kv-val">{{ $stats['active_courses'] }}</span>
                    </div>
                </div>

                <a href="#" class="btn w-100 mt-4 d-flex align-items-center justify-content-center gap-2"
                   style="border:1px solid var(--sb-border);border-radius:8px;font-size:13px;color:var(--sb-accent);background:transparent;padding:9px;">
                    <i class="bi bi-download"></i> Download Report
                </a>
            </div>
        </div>
    </div>

</div>

{{-- ══════════════════════════════════════
     TOP CLASSES + RECENT ADMISSIONS
══════════════════════════════════════ --}}
<div class="row g-3">

    {{-- Top Performing Classes --}}
    <div class="col-12 col-xl-7">
        <div class="sb-panel">
            <div class="sb-panel-header">
                <div>
                    <div class="sb-panel-title">Top Performing Classes</div>
                    <div style="font-size:12px;color:var(--sb-muted);margin-top:2px;">Ranked by course completion rate</div>
                </div>
                <a href="#" class="sb-panel-action">View all <i class="bi bi-arrow-right"></i></a>
            </div>
            <div>
                @foreach($topClasses as $i => $cls)
                <div class="sb-list-item">
                    <div style="width:22px;height:22px;border-radius:5px;background:#F1F5F9;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;color:var(--sb-muted);flex-shrink:0;">
                        {{ $i + 1 }}
                    </div>
                    <div class="sb-list-avatar" style="border-radius:8px;background:#EEF2FF;">
                        <i class="bi bi-building" style="font-size:15px;"></i>
                    </div>
                    <div style="flex:1;">
                        <div class="sb-list-title">{{ $cls['name'] }}</div>
                        <div class="sb-list-sub">{{ $cls['students'] }} students &nbsp;·&nbsp; {{ $cls['attendance'] }}% attendance</div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <div class="sb-progress d-none d-sm-block">
                            <div class="sb-progress-fill" style="width:{{ $cls['progress'] }}%;"></div>
                        </div>
                        <span style="font-size:12.5px;font-weight:600;color:var(--sb-accent);width:36px;text-align:right;">{{ $cls['progress'] }}%</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Recent Admissions --}}
    <div class="col-12 col-xl-5">
        <div class="sb-panel">
            <div class="sb-panel-header">
                <div>
                    <div class="sb-panel-title">Recent Admissions</div>
                    <div style="font-size:12px;color:var(--sb-muted);margin-top:2px;">Latest enrolled students</div>
                </div>
                <a href="#" class="sb-panel-action">View all <i class="bi bi-arrow-right"></i></a>
            </div>
            <div>
                @foreach($recentStudents as $student)
                <div class="sb-list-item">
                    <div class="sb-list-avatar">{{ mb_strtoupper(mb_substr($student['name'], 0, 1)) }}</div>
                    <div>
                        <div class="sb-list-title">{{ $student['name'] }}</div>
                        <div class="sb-list-sub">{{ $student['class'] }}</div>
                    </div>
                    <div class="sb-list-meta">{{ $student['joined'] }}</div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

</div>

@endsection
