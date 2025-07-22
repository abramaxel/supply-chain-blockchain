@extends('layouts.app')

@section('title', 'Mutasi Stok - ' . $item->name)

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Mutasi Stok - {{ $item->name }}</h3>
        <a href="{{ route('inventory.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <strong>Kode Barang:</strong> {{ $item->item_code }}
                </div>
                <div class="col-md-3">
                    <strong>Nama:</strong> {{ $item->name }}
                </div>
                <div class="col-md-3">
                    <strong>Tipe:</strong> {{ $item->type }}
                </div>
                <div class="col-md-3">
                    <strong>Satuan:</strong> {{ $item->unit }}
                </div>
            </div>
        </div>
    </div>

    <h5 class="mb-3">Riwayat Batch Stok Masuk</h5>
    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Nomor Batch</th>
                    <th>Jumlah</th>
                    <th>Tanggal Kadaluarsa</th>
                    <th>Dibuat pada</th>
                </tr>
            </thead>
            <tbody>
                @forelse($batches as $batch)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $batch->batch_no }}</td>
                    <td class="text-end">{{ number_format($batch->quantity, 0, ',', '.') }}</td>
                    <td>{{ $batch->expiry_date ? \Carbon\Carbon::parse($batch->expiry_date)->format('d M Y') : '-' }}</td>
                    <td>{{ \Carbon\Carbon::parse($batch->created_at)->format('d M Y H:i') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted">Belum ada batch.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection