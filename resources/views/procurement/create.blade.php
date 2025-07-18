@extends('layouts.app')

@section('title', 'Buat Pengadaan Baru')

@section('content')

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Buat Purchase Order Baru</h3>
        <a href="{{ route('procurement.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

```
{{-- Tampilkan pesan error jika ada --}}
@if ($errors->any())
    <div class="alert alert-danger">
        <strong>Oops!</strong> Ada masalah pada input:<br>
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('procurement.store') }}">
    @csrf
    <div class="card mb-4">
        <div class="card-header">Info Purchase Order</div>
        <div class="card-body row g-3">
            <div class="col-md-4">
                <label for="po_number" class="form-label">No. PO <span class="text-danger">*</span></label>
                <input type="text" name="po_number" id="po_number" class="form-control" value="{{ old('po_number') }}" required>
            </div>
            <div class="col-md-4">
                <label for="order_date" class="form-label">Tanggal <span class="text-danger">*</span></label>
                <input type="date" name="order_date" id="order_date" class="form-control" value="{{ old('order_date', date('Y-m-d')) }}" required>
            </div>
            <div class="col-md-4">
                <label for="supplier" class="form-label">Supplier <span class="text-danger">*</span></label>
                <input type="text" name="supplier" id="supplier" class="form-control" value="{{ old('supplier') }}" required>
            </div>
            <div class="col-md-4">
                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                <select name="status" id="status" class="form-select" required>
                    <option value="open" {{ old('status') == 'open' ? 'selected' : '' }}>Open</option>
                    <option value="approved" {{ old('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="closed" {{ old('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                    <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">Daftar Barang</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered m-0" id="items-table">
                    <thead>
                        <tr>
                            <th style="width:22%;">Barang <span class="text-danger">*</span></th>
                            <th>Batch No</th>
                            <th>Lokasi <span class="text-danger">*</span></th>
                            <th>Expiry Date</th>
                            <th>Qty <span class="text-danger">*</span></th>
                            <th>Harga Satuan <span class="text-danger">*</span></th>
                            <th>Subtotal</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $oldItems = old('items', [[]]); @endphp
                        @foreach($oldItems as $i => $oldItem)
                        <tr>
                            <td>
                                <select name="items[{{ $i }}][item_id]" class="form-select" required>
                                    <option value="">Pilih...</option>
                                    @foreach($items as $item)
                                        <option value="{{ $item->id }}" {{ ($oldItem['item_id'] ?? '') == $item->id ? 'selected' : '' }}>
                                            {{ $item->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input type="text" name="items[{{ $i }}][batch_no]" class="form-control" value="{{ $oldItem['batch_no'] ?? '' }}">
                            </td>
                            <td>
                                <select name="items[{{ $i }}][location]" class="form-select" required>
                                    <option value="">Pilih Lokasi...</option>
                                    @foreach(['Gudang A','Gudang B','Gudang C','Gudang D','Gudang E'] as $loc)
                                        <option value="{{ $loc }}" {{ ($oldItem['location'] ?? '') == $loc ? 'selected' : '' }}>
                                            {{ $loc }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input type="date" name="items[{{ $i }}][expiry_date]" class="form-control" value="{{ $oldItem['expiry_date'] ?? '' }}">
                            </td>
                            <td>
                                <input type="number" name="items[{{ $i }}][quantity]" class="form-control qty-input" min="1" value="{{ $oldItem['quantity'] ?? '' }}" required>
                            </td>
                            <td>
                                <input type="number" step="0.01" name="items[{{ $i }}][unit_price]" class="form-control price-input" min="0" value="{{ $oldItem['unit_price'] ?? '' }}" required>
                            </td>
                            <td>
                                <input type="text" class="form-control-plaintext subtotal" value="" readonly>
                            </td>
                            <td>
                                <button type="button" class="btn btn-danger btn-sm remove-row"><i class="bi bi-trash"></i></button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="p-2">
                    <button type="button" class="btn btn-success btn-sm" id="add-row">
                        <i class="bi bi-plus-circle"></i> Tambah Barang
                    </button>
                </div>
            </div>
            <!-- Row template (hidden) -->
            <table style="display:none;"><tbody><tr id="row-template">
                <td>
                    <select name="item_id_template" class="form-select" required disabled>
                        <option value="">Pilih...</option>
                        @foreach($items as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <input type="text" name="batch_no_template" class="form-control">
                </td>
                <td>
                    <select name="location_template" class="form-select" required disabled>
                        <option value="">Pilih Lokasi...</option>
                        <option value="Gudang A">Gudang A</option>
                        <option value="Gudang B">Gudang B</option>
                        <option value="Gudang C">Gudang C</option>
                        <option value="Gudang D">Gudang D</option>
                        <option value="Gudang E">Gudang E</option>
                    </select>
                </td>
                <td>
                    <input type="date" name="expiry_date_template" class="form-control">
                </td>
                <td>
                    <input type="number" name="quantity_template" class="form-control qty-input" min="1" required disabled>
                </td>
                <td>
                    <input type="number" step="0.01" name="unit_price_template" class="form-control price-input" min="0" required disabled>
                </td>
                <td>
                    <input type="text" class="form-control-plaintext subtotal" value="" readonly>
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm remove-row"><i class="bi bi-trash"></i></button>
                </td>
            </tr></tbody></table>
        </div>
    </div>

    <div class="text-end">
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-save"></i> Simpan Purchase Order
        </button>
    </div>
</form>


</div>

@push('scripts')

<script>
    function recalcSubtotal(tr) {
        let qty = parseFloat(tr.find('.qty-input').val()) || 0;
        let price = parseFloat(tr.find('.price-input').val()) || 0;
        tr.find('.subtotal').val(qty * price ? (qty * price).toLocaleString('id-ID') : '');
    }

    $(document).ready(function () {
        let rowIdx = $('#items-table tbody tr').length;

        $('#add-row').click(function () {
            let $newRow = $('#row-template').clone().removeAttr('id').show();
            // Langsung enable seluruh input/select/button
            $newRow.find('select, input, button').prop('disabled', false);

            // Ganti semua atribut name dengan format items[rowIdx][...]
            $newRow.find('select, input').each(function () {
                let name = $(this).attr('name');
                if(name && name.endsWith('_template')) {
                    let field = name.replace('_template','');
                    $(this).attr('name', `items[${rowIdx}][${field}]`);
                }
            });

            $('#items-table tbody').append($newRow);
            rowIdx++;
        });


        $('#items-table').on('click', '.remove-row', function () {
            $(this).closest('tr').remove();
        });

        $('#items-table').on('input', '.qty-input, .price-input', function () {
            let tr = $(this).closest('tr');
            recalcSubtotal(tr);
        });

        $('#items-table tbody tr').each(function () {
            recalcSubtotal($(this));
        });
    });
</script>
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

@endsection
