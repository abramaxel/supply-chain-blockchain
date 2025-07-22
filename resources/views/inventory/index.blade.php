@extends('layouts.app')

@section('title', 'Daftar Barang (Inventory)')

@section('content')
<div class="container py-4">
    <h3 class="mb-3">Daftar Barang</h3>

    <!-- Baris pencarian + tambah barang -->
    <form class="mb-3" method="GET" action="{{ route('inventory.index') }}">
        <div class="row g-2 align-items-center">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control" placeholder="Cari kode/nama/tipe/satuan ..." value="{{ request('search') }}">
            </div>
            <div class="col-md-1">
                <button class="btn btn-outline-primary w-100" type="submit">
                    <i class="bi bi-search"></i> Cari
                </button>
            </div>
            <div class="col-md-2 ms-auto text-end">
                <a href="{{ route('inventory.create') }}" class="btn btn-primary w-100">
                    <i class="bi bi-plus-circle"></i> Tambah Barang
                </a>
            </div>
            <div class="col-md-2">
                <select class="form-select" onchange="window.location.href=this.value">
                    <option value="{{ route('inventory.index', array_merge(request()->all(), ['per_page' => 10])) }}" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10 per halaman</option>
                    <option value="{{ route('inventory.index', array_merge(request()->all(), ['per_page' => 25])) }}" {{ request('per_page', 10) == 25 ? 'selected' : '' }}>25 per halaman</option>
                    <option value="{{ route('inventory.index', array_merge(request()->all(), ['per_page' => 50])) }}" {{ request('per_page', 10) == 50 ? 'selected' : '' }}>50 per halaman</option>
                </select>
            </div>
            <div class="col-md-1">
                <a href="{{ route('inventory.index') }}" class="btn btn-outline-secondary btn-sm w-100">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </a>
            </div>
        </div>
    </form>

    <!-- Tabel -->
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
                    <td>{{ ($items->currentPage()-1) * $items->perPage() + $loop->iteration }}</td>
                    <td>{{ $item->item_code }}</td>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->type }}</td>
                    <td class="text-end">{{ number_format($item->total_stock, 0, ',', '.') }}</td>
                    <td>{{ $item->unit }}</td>
                    <td>
                        <a href="{{ route('inventory.edit', $item->id) }}" class="btn btn-warning btn-sm"><i class="bi bi-pencil"></i> Edit</a>
                        <form action="{{ route('inventory.destroy', $item->id) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button type="button" class="btn btn-danger btn-sm btn-delete" data-name="{{ $item->name }}">
                                <i class="bi bi-trash"></i> Hapus
                            </button>
                        </form>
                        <a href="{{ route('inventory.mutation', $item->id) }}" class="btn btn-info btn-sm">
                            <i class="bi bi-eye"></i> Cek Mutasi
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted">Belum ada barang.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-3">
        {{ $items->appends(request()->input())->links() }}
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2 @11"></script>
<script>
    // Alert sukses/gagal
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
            text: '{{ session('error') }}',
            showConfirmButton: false,
            timer: 3000
        });
    @endif

    // Tombol hapus dengan SweetAlert
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