@extends('layouts.app')

@section('title', 'Detail Pengadaan')

@section('content')
<div class="container py-4">
    {{-- Judul dan tombol kembali sebaris --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Detail Purchase Order</h3>
        <a href="{{ route('procurement.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Kembali ke Daftar Pengadaan
        </a>
    </div>

    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <strong>Info Purchase Order</strong>
        </div>
        <div class="card-body">
            <div class="row mb-2">
                <div class="col-md-3">No. PO</div>
                <div class="col-md-9">{{ $order->po_number }}</div>
            </div>
            <div class="row mb-2">
                <div class="col-md-3">Tanggal</div>
                <div class="col-md-9">{{ \Carbon\Carbon::parse($order->order_date)->format('d/m/Y') }}</div>
            </div>
            <div class="row mb-2">
                <div class="col-md-3">Supplier</div>
                <div class="col-md-9">{{ $order->supplier }}</div>
            </div>
            <div class="row mb-2">
                <div class="col-md-3">Status</div>
                <div class="col-md-9">
                    <span class="badge bg-{{ $order->status == 'approved' ? 'success' : ($order->status == 'cancelled' ? 'danger' : 'secondary') }}">
                        {{ ucfirst($order->status) }}
                    </span>
                </div>
            </div>
            <a href="{{ route('procurement.previewPdf', $order->id) }}" target="_blank" class="btn btn-primary">
                <i class="bi bi-eye"></i> Lihat PDF
            </a>

            <a href="{{ route('procurement.downloadPdf', $order->id) }}" class="btn btn-danger">
                <i class="bi bi-file-earmark-pdf"></i> Download PDF
            </a>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header bg-info text-white">
            <strong>Daftar Item dalam Pengadaan</strong>
        </div>
        <div class="card-body p-0">
            <table class="table table-striped m-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama Barang</th>
                        <th>Batch No</th>
                        <th>Jumlah</th>
                        <th>Harga Satuan</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $item->item->name ?? '-' }}</td>
                        <td>{{ $item->batch_no }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>Rp. {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                        <td>Rp. {{ number_format($item->total_price, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
