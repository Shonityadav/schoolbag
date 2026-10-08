@extends('layouts.admin')

@section('title', 'Fee Structures')
@section('admin_nav_fees', 'active')
@section('admin_page_title', 'Fee Structures')

@section('admin_content')
<div class="mb-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
    <div>
        <h2 class="h3 font-weight-bold mb-1 text-dark">Fee Structures</h2>
        <p class="text-muted mb-0">Manage fee plans for your classes</p>
    </div>
    <button type="button" class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#createFeeModal">
        <i class="bi bi-plus-lg me-1"></i> Create Fee Structure
    </button>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger border-0 shadow-sm">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="px-4 py-3 text-uppercase text-secondary" style="font-size: 0.75rem; font-weight: 600; letter-spacing: 0.5px;">Class</th>
                        <th class="px-4 py-3 text-uppercase text-secondary" style="font-size: 0.75rem; font-weight: 600; letter-spacing: 0.5px;">Fee Title</th>
                        <th class="px-4 py-3 text-uppercase text-secondary" style="font-size: 0.75rem; font-weight: 600; letter-spacing: 0.5px;">Amount</th>
                        <th class="px-4 py-3 text-uppercase text-secondary" style="font-size: 0.75rem; font-weight: 600; letter-spacing: 0.5px;">Frequency</th>
                        <th class="px-4 py-3 text-uppercase text-secondary" style="font-size: 0.75rem; font-weight: 600; letter-spacing: 0.5px;">Created By</th>
                        <th class="px-4 py-3 text-uppercase text-secondary text-end" style="font-size: 0.75rem; font-weight: 600; letter-spacing: 0.5px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($fees as $fee)
                        <tr>
                            <td class="px-4 py-3">
                                <span class="badge bg-secondary">Class {{ $fee->classModel->standard }} {{ $fee->classModel->section }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="fw-bold text-dark">{{ $fee->title }}</div>
                                @if($fee->description)
                                    <div class="text-muted" style="font-size: 0.85rem;">{{ Str::limit($fee->description, 50) }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 font-monospace">₹{{ number_format($fee->amount, 2) }}</td>
                            <td class="px-4 py-3">
                                @php
                                    $freqColors = [
                                        'monthly' => 'primary',
                                        'quarterly' => 'info',
                                        'half_yearly' => 'warning',
                                        'yearly' => 'success',
                                        'one_time' => 'dark'
                                    ];
                                    $color = $freqColors[$fee->frequency] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $color }} bg-opacity-10 text-{{ $color }} border border-{{ $color }} border-opacity-25 rounded-pill px-3">
                                    {{ str_replace('_', ' ', Str::title($fee->frequency)) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-muted" style="font-size: 0.9rem;">
                                {{ $fee->creator->name ?? 'System' }}
                            </td>
                            <td class="px-4 py-3 text-end text-nowrap">
                                <button type="button" class="btn btn-sm btn-outline-primary rounded-circle p-2 me-1" data-bs-toggle="modal" data-bs-target="#editFeeModal{{ $fee->id }}" title="Edit Fee">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form action="{{ route('admin.fees.destroy', $fee->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this fee structure? This cannot be undone.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle p-2" title="Delete Fee">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>

                                <!-- Edit Fee Modal -->
                                <div class="modal fade text-start" id="editFeeModal{{ $fee->id }}" tabindex="-1" aria-labelledby="editFeeModalLabel{{ $fee->id }}" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow">
                                            <div class="modal-header border-bottom-0 pb-0">
                                                <h5 class="modal-title font-weight-bold" id="editFeeModalLabel{{ $fee->id }}">Edit Fee Structure</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form action="{{ route('admin.fees.update', $fee->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold text-secondary small text-uppercase">Class</label>
                                                        <select class="form-select shadow-sm" name="class_id" required>
                                                            <option value="">Select a class...</option>
                                                            @foreach($classes as $class)
                                                                <option value="{{ $class->id }}" {{ $fee->class_id == $class->id ? 'selected' : '' }}>Class {{ $class->standard }} {{ $class->section }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold text-secondary small text-uppercase">Fee Title</label>
                                                        <input type="text" class="form-control shadow-sm" name="title" value="{{ $fee->title }}" required>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label fw-bold text-secondary small text-uppercase">Amount (₹)</label>
                                                            <input type="number" step="0.01" class="form-control shadow-sm" name="amount" value="{{ $fee->amount }}" required min="0">
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label fw-bold text-secondary small text-uppercase">Frequency</label>
                                                            <select class="form-select shadow-sm" name="frequency" required>
                                                                <option value="monthly" {{ $fee->frequency == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                                                <option value="quarterly" {{ $fee->frequency == 'quarterly' ? 'selected' : '' }}>Quarterly</option>
                                                                <option value="half_yearly" {{ $fee->frequency == 'half_yearly' ? 'selected' : '' }}>Half-Yearly</option>
                                                                <option value="yearly" {{ $fee->frequency == 'yearly' ? 'selected' : '' }}>Yearly</option>
                                                                <option value="one_time" {{ $fee->frequency == 'one_time' ? 'selected' : '' }}>One-Time</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold text-secondary small text-uppercase">Description (Optional)</label>
                                                        <textarea class="form-control shadow-sm" name="description" rows="2">{{ $fee->description }}</textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-top-0 pt-0">
                                                    <button type="button" class="btn btn-light shadow-sm" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-primary shadow-sm">Update Fee Structure</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="text-muted mb-3">
                                    <i class="bi bi-credit-card" style="font-size: 3rem; opacity: 0.5;"></i>
                                </div>
                                <h5 class="text-dark">No fee structures found</h5>
                                <p class="text-muted">Create your first fee structure to start collecting payments.</p>
                                <button type="button" class="btn btn-primary btn-sm mt-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#createFeeModal">
                                    <i class="bi bi-plus-lg me-1"></i> Create Fee Structure
                                </button>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Create Fee Modal -->
<div class="modal fade" id="createFeeModal" tabindex="-1" aria-labelledby="createFeeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title font-weight-bold" id="createFeeModalLabel">Create Fee Structure</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.fees.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary small text-uppercase">Class</label>
                        <select class="form-select shadow-sm" name="class_id" required>
                            <option value="">Select a class...</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}">Class {{ $class->standard }} {{ $class->section }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary small text-uppercase">Fee Title</label>
                        <input type="text" class="form-control shadow-sm" name="title" placeholder="e.g. Monthly Tuition Fee" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-secondary small text-uppercase">Amount (₹)</label>
                            <input type="number" step="0.01" class="form-control shadow-sm" name="amount" placeholder="0.00" required min="0">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-secondary small text-uppercase">Frequency</label>
                            <select class="form-select shadow-sm" name="frequency" required>
                                <option value="monthly">Monthly</option>
                                <option value="quarterly">Quarterly</option>
                                <option value="half_yearly">Half-Yearly</option>
                                <option value="yearly">Yearly</option>
                                <option value="one_time">One-Time</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary small text-uppercase">Description (Optional)</label>
                        <textarea class="form-control shadow-sm" name="description" rows="2" placeholder="Any details about this fee..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-light shadow-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary shadow-sm">Save Fee Structure</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
