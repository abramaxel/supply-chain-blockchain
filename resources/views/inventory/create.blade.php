@extends('layouts.app')

@section('title', 'Tambah Barang (Inventory)')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Tambah Barang Baru</h3>
        <a href="{{ route('inventory.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    {{-- Error alert --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Oops!</strong> Ada masalah pada input:<br>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('inventory.store') }}" method="POST">
        @csrf
        <div class="card">
            <div class="card-body row g-3">
                <div class="col-md-4">
                    <label for="item_code" class="form-label">Kode Barang <span class="text-danger">*</span></label>
                    <input type="text" name="item_code" id="item_code" class="form-control" value="{{ old('item_code') }}" required>
                </div>
                <div class="col-md-8">
                    <label for="name" class="form-label">Nama Barang <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
                </div>
                <div class="col-md-4">
                    <label for="type" class="form-label">Tipe <span class="text-danger">*</span></label>
                    <select name="type" id="type" class="form-select" required>
                        <option value="">Pilih tipe...</option>
                        <option value="raw_material" {{ old('type') == 'raw_material' ? 'selected' : '' }}>Raw Material</option>
                        <option value="finished_good" {{ old('type') == 'finished_good' ? 'selected' : '' }}>Finished Good</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="unit" class="form-label">Satuan</label>
                    <input type="text" name="unit" id="unit" class="form-control" value="{{ old('unit') }}">
                </div>
            </div>
            <div class="card-footer text-end mt-3">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Simpan Barang
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
