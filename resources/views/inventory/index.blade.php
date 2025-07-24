@extends('layouts.app')

@section('title', 'Daftar Barang (Inventory)')

@section('content')
<div class="container py-4">
    <h3 class="mb-3">Daftar Barang</h3>

    <!-- Form Pencarian & Filter -->
    <form class="mb-3" method="GET" action="{{ route('inventory.index') }}">
        <div class="row g-2 align-items-center">
            <!-- Input Pencarian -->
            <div class="col-md-3">
                <input type="text" name="search" class="form-control" placeholder="Cari kode/nama/tipe/satuan ..." value="{{ request('search') }}">
            </div>

            <!-- Tombol Cari -->
            <div class="col-md-1">
                <button class="btn btn-outline-primary w-100" type="submit">
                    <i class="bi bi-search"></i> Cari
                </button>
            </div>

            <!-- Tombol Tambah Barang -->
            <div class="col-md-2 ms-auto text-end">
                <a href="{{ route('inventory.create') }}" class="btn btn-primary w-100">
                    <i class="bi bi-plus-circle"></i> Tambah Barang
                </a>
            </div>

            <!-- Dropdown Jumlah Per Halaman -->
            <div class="col-md-2">
                <select class="form-select" onchange="window.location.href=this.value">
                    <option value="{{ route('inventory.index', array_merge(request()->all(), ['per_page' => 10])) }}" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>
                        10 per halaman
                    </option>
                    <option value="{{ route('inventory.index', array_merge(request()->all(), ['per_page' => 25])) }}" {{ request('per_page', 10) == 25 ? 'selected' : '' }}>
                        25 per halaman
                    </option>
                    <option value="{{ route('inventory.index', array_merge(request()->all(), ['per_page' => 50])) }}" {{ request('per_page', 10) == 50 ? 'selected' : '' }}>
                        50 per halaman
                    </option>
                </select>
            </div>

            <!-- Tombol Reset -->
            <div class="col-md-1">
                <a href="{{ route('inventory.index') }}" class="btn btn-outline-secondary btn-sm w-100">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </a>
            </div>
        </div>
    </form>

    <!-- Tabel Data Barang -->
    <div class="table-responsive">
        <table class="table table-bordered align-middle table-hover table-sm">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Kode</th>
                    <th>Nama Barang</th>
                    <th>Tipe</th>
                    <th>Stok</th>
                    <th>Satuan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                <tr>
                    <td>{{ ($items->currentPage() - 1) * $items->perPage() + $loop->iteration }}</td>
                    <td>{{ $item->item_code }}</td>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->type }}</td>
                    <td class="text-end">{{ number_format($item->net_stock, 0, ',', '.') }}</td>
                    <td>{{ $item->unit }}</td>
                    <td class="text-nowrap">
                        <!-- Edit -->
                        <a href="{{ route('inventory.edit', $item->id) }}" class="btn btn-warning btn-sm" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </a>

                        <!-- Hapus (dengan konfirmasi SweetAlert) -->
                        <form action="{{ route('inventory.destroy', $item->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="btn btn-danger btn-sm btn-delete" data-name="{{ $item->name }}" title="Hapus">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>

                        <!-- Cek Mutasi -->
                        <a href="{{ route('inventory.mutation', $item->id) }}" class="btn btn-info btn-sm" title="Cek Mutasi Stok">
                            <i class="bi bi-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">
                        <i class="bi bi-inbox" style="font-size: 48px; color: #ccc;"></i>
                        <p class="mt-2 mb-0">Belum ada barang.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-3">
        {{ $items->appends(request()->except('page'))->links() }}
    </div>
</div>
@endsection

@push('scripts')
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2 @11"></script>
<script>
    // Notifikasi Sukses
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Sukses',
            text: '{{ session('success') }}',
            showConfirmButton: false,
            timer: 2000,
            toast: true,
            position: 'top-end',
            background: '#a5dc86',
            color: '#fff'
        });
    @endif

    // Notifikasi Error
    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Gagal',
            text: '{{ session('error') }}',
            showConfirmButton: true,
            position: 'top-end',
            toast: true,
            background: '#f27474',
            color: '#fff'
        });
    @endif

    // Konfirmasi Hapus dengan SweetAlert
    document.querySelectorAll('.btn-delete').forEach(button => {
        button.addEventListener('click', function () {
            const name = this.getAttribute('data-name');
            const form = this.closest('form');

            Swal.fire({
                title: 'Yakin hapus?',
                text: 'Anda akan menghapus barang: ' + name,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
@endpush