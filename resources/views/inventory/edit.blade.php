@extends('layouts.app')

@section('title', 'Edit Barang')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Edit Barang</h3>
        <a href="{{ route('inventory.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    {{-- Pesan error --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Oops!</strong> Ada masalah pada input:
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('inventory.update', $item->id) }}">
        @csrf
        @method('PUT')
        <div class="card">
            <div class="card-header">
                Data Barang
            </div>
            <div class="card-body row g-3">
                <div class="col-md-6 mb-3">
                    <label for="item_code" class="form-label">Kode Barang <span class="text-danger">*</span></label>
                    <input type="text" name="item_code" id="item_code" class="form-control" value="{{ old('item_code', $item->item_code) }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label">Nama Barang <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $item->name) }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="type" class="form-label">Tipe Barang <span class="text-danger">*</span></label>
                    <select name="type" id="type" class="form-select" required>
                        @foreach($types as $key => $label)
                            <option value="{{ $key }}" {{ old('type', $item->type) == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="unit" class="form-label">Satuan</label>
                    <input type="text" name="unit" id="unit" class="form-control" value="{{ old('unit', $item->unit) }}">
                </div>
            </div>
        </div>
        <div class="text-end mt-3">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save"></i> Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection
