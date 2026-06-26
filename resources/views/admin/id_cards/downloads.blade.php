@extends('layouts.admin')
@section('admin_page_title', 'ID Card Downloads')

@section('admin_content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-6">
            <h3>ID Card Download Center</h3>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('admin.id_cards.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Date Requested</th>
                        <th>Requested By</th>
                        <th>Status</th>
                        <th>Progress</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($downloads as $d)
                    <tr>
                        <td>{{ $d->created_at->format('Y-m-d H:i') }}</td>
                        <td>{{ $d->requestedBy->name }}</td>
                        <td>
                            @if($d->status === 'Completed')
                                <span class="badge bg-success">Completed</span>
                            @elseif($d->status === 'Failed')
                                <span class="badge bg-danger">Failed</span>
                            @else
                                <span class="badge bg-warning">{{ $d->status }}</span>
                            @endif
                        </td>
                        <td>
                            <div class="progress">
                                <div class="progress-bar" role="progressbar" style="width: {{ $d->progress }}%;" aria-valuenow="{{ $d->progress }}" aria-valuemin="0" aria-valuemax="100">{{ $d->progress }}%</div>
                            </div>
                        </td>
                        <td>
                            @if($d->status === 'Completed' && $d->file_path)
                                <a href="{{ asset($d->file_path) }}" target="_blank" class="btn btn-sm btn-success"><i class="fas fa-download"></i> Download</a>
                            @elseif($d->status === 'Failed')
                                <button class="btn btn-sm btn-secondary" disabled>Failed</button>
                            @else
                                <button class="btn btn-sm btn-secondary" disabled>Wait</button>
                            @endif
                            <form action="#" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-sm btn-danger disabled" title="Delete coming soon"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center">No downloads available.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    </div>
</div>

<script>
    // Auto refresh the page every 5 seconds if there are queued/processing jobs
    let hasProcessing = {{ $downloads->whereIn('status', ['Pending', 'Processing'])->count() > 0 ? 'true' : 'false' }};
    if (hasProcessing) {
        setTimeout(function() {
            window.location.reload();
        }, 5000);
    }
</script>
@endsection
