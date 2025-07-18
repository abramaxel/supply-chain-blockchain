{{-- resources/views/procurement/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Daftar Pengadaan (Purchase Order)')

@section('content')
<div class="container py-4">
    <h3 class="mb-3">Daftar Pengadaan</h3>
    <form class="mb-3" method="GET" action="{{ route('procurement.index') }}">
        <div class="row g-2 align-items-center">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Cari nomor, supplier, status ..." value="{{ request('search') }}">
            </div>
            <div class="col-md-1">
                <button class="btn btn-outline-primary w-100" type="submit">
                    <i class="bi bi-search"></i> Cari
                </button>
            </div>
            <div class="col-md-3 ms-auto text-end">
                <a href="{{ route('procurement.create') }}" class="btn btn-primary w-100">
                    <i class="bi bi-plus-circle"></i> Tambah Pengadaan
                </a>
            </div>
        </div>
    </form>
    <!-- lanjutkan tabel pengadaan di sini -->

    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>No. PO</th>
                    <th>Tanggal</th>
                    <th>Supplier</th>
                    <th>Status</th>
                    <th>Jumlah Item</th>
                    <th>Traceability</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($purchaseOrders as $order)
                <tr>
                    <td>{{ ($purchaseOrders->currentPage()-1) * $purchaseOrders->perPage() + $loop->iteration }}</td>
                    <td>{{ $order->po_number }}</td>
                    <td>{{ \Carbon\Carbon::parse($order->order_date)->format('d/m/Y') }}</td>
                    <td>{{ $order->supplier }}</td>
                    <td>
                        <span class="badge bg-{{ $order->status == 'approved' ? 'success' : ($order->status == 'cancelled' ? 'danger' : 'secondary') }}">
                            {{ ucfirst($order->status) }}
                        </span>
                    </td>
                    <td>{{ $order->items->count() }}</td>
                    <td>
                        <a href="{{ route('procurement.trace', $order->id) }}" class="btn btn-outline-info btn-sm">
                            <i class="bi bi-eye"></i> Lihat Trace
                        </a>
                    </td>
                    <td>
                        <a href="{{ route('procurement.show', $order->id) }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-search"></i> Detail
                        </a>
                        <a href="{{ route('procurement.edit', $order->id) }}" class="btn btn-warning btn-sm">
                            <i class="bi bi-pencil-square"></i> Update
                        </a>
                        <form action="{{ route('procurement.destroy', $order->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus pengadaan ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">
                                <i class="bi bi-trash"></i> Delete
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center text-muted">Belum ada data pengadaan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">
        {{ $purchaseOrders->appends(request()->input())->links() }}
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
@if(session('success'))
    Swal.fire({
        icon: 'success',
        title: 'Sukses',
        text: '{{ session('success') }}',
        showConfirmButton: false,
        timer: 2000
    });
@endif

@if(session('error'))
    Swal.fire({
        icon: 'error',
        title: 'Gagal',
        text: `{!! session('error') !!}`,
        showConfirmButton: true
    });
@endif
</script>
@endpush

