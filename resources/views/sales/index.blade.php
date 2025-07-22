@extends('layouts.app')

@section('title', 'Daftar Sales Order')

@section('content')
<div class="container py-4">
    <h3 class="mb-3">Daftar Sales Order</h3>

    <!-- Summary Cards -->
    <div class="row g-3 mb-3">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h6 class="card-title text-white">Total Sales Order</h6>
                    <h4 class="card-text">{{ $orders->total() }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6 class="card-title text-white">Terproses</h6>
                    <h4 class="card-text">{{ $orders->filter(fn($so) => $so->status === 'approved')->count() }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <h6 class="card-title text-white">Dibatalkan</h6>
                    <h4 class="card-text">{{ $orders->filter(fn($so) => $so->status === 'cancelled')->count() }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h6 class="card-title text-white">Menunggu</h6>
                    <h4 class="card-text">{{ $orders->filter(fn($so) => $so->status === 'open')->count() }}</h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Form -->
    <form class="mb-3" method="GET" action="{{ route('sales.index') }}">
        <div class="row g-2 align-items-center">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Cari nomor, customer, status ..." value="{{ request('search') }}">
            </div>
            <div class="col-md-1">
                <button class="btn btn-outline-primary w-100" type="submit">
                    <i class="bi bi-search"></i> Cari
                </button>
            </div>
            <div class="col-md-2 ms-auto text-end">
                <a href="{{ route('sales.create') }}" class="btn btn-success w-100">
                    <i class="bi bi-plus-circle"></i> Tambah Sales Order
                </a>
            </div>
            <div class="col-md-1">
                <a href="{{ route('sales.index') }}" class="btn btn-outline-secondary w-100">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </a>
            </div>
        </div>
    </form>

    <!-- Sales Order Table -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>No. SO</th>
                            <th>Tanggal</th>
                            <th>Pelanggan</th>
                            <th>Status</th>
                            <th>Total</th>
                            <th style="width: 120px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $so)
                        <tr>
                            <td>{{ ($orders->currentPage()-1) * $orders->perPage() + $loop->iteration }}</td>
                            <td>{{ $so->so_number }}</td>
                            <td>{{ \Carbon\Carbon::parse($so->order_date)->format('d/m/Y') }}</td>
                            <td>{{ $so->customer }}</td>
                            <td>
                                <span class="badge bg-{{ 
                                    $so->status == 'approved' ? 'success' : 
                                    ($so->status == 'cancelled' ? 'danger' : 
                                    ($so->status == 'pending' ? 'warning' : 'secondary')) }}">
                                    {{ ucfirst($so->status) }}
                                </span>
                            </td>
                            <td class="text-end">Rp {{ number_format($so->total_price, 0, ',', '.') }}</td>
                            <td class="text-nowrap">
                                <a href="{{ route('sales.show', $so->id) }}" class="btn btn-sm btn-primary" title="Lihat Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('sales.edit', $so->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="bi bi-inbox" style="font-size: 48px; color: #ccc;"></i>
                                <p class="text-muted mt-2">Belum ada sales order</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-3">
        {{ $orders->appends(request()->input())->links() }}
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2 @11"></script>
<script>
    // SweetAlert
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: '{{ session('success') }}',
            showConfirmButton: false,
            timer: 1800
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