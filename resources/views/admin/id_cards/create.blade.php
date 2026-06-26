@extends('layouts.admin')
@section('admin_page_title', 'Create ID Card Template')

@section('admin_content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-6">
            <h3>Create ID Card Template</h3>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('admin.id_cards.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.id_cards.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label>Template Name</label>
                    <input type="text" name="name" class="form-control" required placeholder="e.g. 2026 Student ID Card">
                </div>
                <div class="mb-3">
                    <label>Card Type</label>
                    <select name="type" class="form-control" required>
                        <option value="student">Student</option>
                        <option value="staff">Staff</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label>Orientation</label>
                    <select name="orientation" class="form-control" required>
                        <option value="portrait">Portrait</option>
                        <option value="landscape">Landscape</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Create & Open Designer</button>
            </form>
        </div>
    </div>
</div>
@endsection
