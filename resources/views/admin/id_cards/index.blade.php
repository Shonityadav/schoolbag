@extends('layouts.admin')
@section('admin_page_title', 'ID Card Templates')

@section('admin_content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-6">
            <h3>ID Card Templates</h3>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('admin.id_cards.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Create Template</a>
            <a href="{{ route('admin.id_cards.settings') }}" class="btn btn-secondary"><i class="fas fa-cog"></i> Settings</a>
            <a href="{{ route('admin.id_cards.downloads') }}" class="btn btn-info"><i class="fas fa-download"></i> Downloads</a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Orientation</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($templates as $t)
                    <tr>
                        <td>{{ $t->name }}</td>
                        <td>{{ ucfirst($t->type) }}</td>
                        <td>
                            @if($t->status === 'Published')
                                <span class="badge bg-success">Published</span>
                            @elseif($t->status === 'Draft')
                                <span class="badge bg-warning">Draft</span>
                            @else
                                <span class="badge bg-secondary">Archived</span>
                            @endif
                        </td>
                        <td>{{ ucfirst($t->orientation) }}</td>
                        <td>
                            <a href="{{ route('admin.id_cards.designer', $t->uuid) }}" class="btn btn-sm btn-primary" title="Open Designer">
                                <i class="fas fa-paint-brush"></i> Designer
                            </a>
                            <form action="{{ route('admin.id_cards.duplicate', $t->uuid) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-info" title="Duplicate"><i class="fas fa-copy"></i></button>
                            </form>
                            @if($t->status !== 'Published')
                            <form action="{{ route('admin.id_cards.publish', $t->uuid) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success" title="Publish" onclick="return confirm('Publishing this template will archive the currently published {{ $t->type }} template. Continue?')"><i class="fas fa-check-circle"></i> Publish</button>
                            </form>
                            @endif
                            <form action="{{ route('admin.id_cards.destroy', $t->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this template permanently?');"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center">No templates found. Create one to get started!</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
