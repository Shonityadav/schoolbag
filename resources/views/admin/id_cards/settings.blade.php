@extends('layouts.admin')
@section('admin_page_title', 'ID Card Settings')

@section('admin_content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-6">
            <h3>ID Card Settings</h3>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('admin.id_cards.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.id_cards.settings.update') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label>Primary Color</label>
                        <input type="color" name="primary_color" class="form-control form-control-color w-100" value="{{ $settings->primary_color }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>Secondary Color</label>
                        <input type="color" name="secondary_color" class="form-control form-control-color w-100" value="{{ $settings->secondary_color }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>Text Color</label>
                        <input type="color" name="text_color" class="form-control form-control-color w-100" value="{{ $settings->text_color }}">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="show_qr" id="show_qr" value="1" {{ $settings->show_qr ? 'checked' : '' }}>
                            <label class="form-check-label" for="show_qr">Show QR Code</label>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="show_barcode" id="show_barcode" value="1" {{ $settings->show_barcode ? 'checked' : '' }}>
                            <label class="form-check-label" for="show_barcode">Show Barcode</label>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="show_signature" id="show_signature" value="1" {{ $settings->show_signature ? 'checked' : '' }}>
                            <label class="form-check-label" for="show_signature">Show Principal Signature</label>
                        </div>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary">Save Settings</button>
            </form>
        </div>
    </div>
</div>
@endsection
