@extends('layouts.app')

@section('title', 'Daftar Barang (Inventory)')

@section('content')
<div class="container py-4">
    <h3 class="mb-3">Daftar Barang</h3>
    <!-- Baris pencarian + tambah barang -->
    <form class="mb-3" method="GET" action="{{ route('inventory.index') }}">
        <div class="row g-2 align-items-center">
            <div class="col-md-4">
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
        </div>
    </form>
    <!-- ...lanjutan tabel, dst -->

    

    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Kode</th>
                    <th>Nama Barang</th>
                    <th>Tipe</th>
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
                    <td>{{ $item->unit }}</td>
                    <td>
                        <a href="{{ route('inventory.edit', $item->id) }}" class="btn btn-warning btn-sm"><i class="bi bi-pencil"></i> Edit</a>
                        {{-- <form action="{{ route('inventory.destroy', $item->id) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus?')"><i class="bi bi-trash"></i> Hapus</button>
                        </form> --}}
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
    <div class="mt-3">
        {{ $items->appends(request()->input())->links() }}
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
        text: '{{ session('error') }}',
        showConfirmButton: false,
        timer: 3000
    });
@endif
</script>
@endpush
