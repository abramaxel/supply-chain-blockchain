@extends('layouts.app')

@section('title', 'Detail Sales Order')

@section('content')
<div class="container py-4">
    <h3>Detail Sales Order</h3>

    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <strong>Nomor SO:</strong> {{ $order->so_number }}
                </div>
                <div class="col-md-4">
                    <strong>Tanggal Order:</strong> {{ \Carbon\Carbon::parse($order->order_date)->format('d M Y') }}
                </div>
                <div class="col-md-4">
                    <strong>Status:</strong>
                    <span class="badge bg-{{ $order->status === 'closed' ? 'success' : ($order->status === 'cancelled' ? 'danger' : 'primary') }}">
                        {{ ucfirst($order->status) }}
                    </span>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-4">
                    <strong>Nama Customer:</strong> {{ $order->customer_name }}
                </div>
            </div>
        </div>
    </div>

    <h5>Item Penjualan</h5>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead class="table-light">
                <tr>
                    <th>Nama Barang</th>
                    <th>Batch</th>
                    <th>Jumlah</th>
                    <th>Harga Satuan</th>
                    <th>Total Harga</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->items as $item)
                <tr>
                    <td>{{ $item->item->name }}</td>
                    <td>{{ $item->batch_no }}</td>
                    <td class="text-end">{{ number_format($item->quantity, 0, ',', '.') }}</td>
                    <td class="text-end">Rp{{ number_format($item->unit_selling_price, 0, ',', '.') }}</td>
                    <td class="text-end">Rp{{ number_format($item->total_price, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        <a href="{{ route('sales.index') }}" class="btn btn-secondary">Kembali</a>
    </div>
</div>
@endsection