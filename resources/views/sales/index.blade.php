@extends('layouts.app')

@section('title', 'Daftar Sales Order')

@section('content')
    <div class="container py-4">
    <h3 class="mb-3">Daftar Sales Order</h3>
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
            <div class="col-md-3 ms-auto text-end">
                <a href="{{ route('sales.create') }}" class="btn btn-success w-100">
                    <i class="bi bi-plus-circle"></i> Tambah Sales Order
                </a>
            </div>
        </div>
    </form>
    <!-- lanjutkan tabel sales order di sini -->


    <!-- Tabel sales order -->
    <div class="card">
        <div class="card-body p-0">
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
                        <td>Rp {{ number_format($so->total_price, 0, ',', '.') }}</td>
                        <td>
                            <a href="{{ route('sales.show', $so->id) }}" class="btn btn-sm btn-primary">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('sales.edit', $so->id) }}" class="btn btn-sm btn-warning">
                                <i class="bi bi-pencil"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center">Belum ada sales order.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-3">
        {{ $orders->links() }}
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
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
