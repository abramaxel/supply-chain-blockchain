@extends('layouts.app')

@section('title', 'Buat Sales Order Baru')

@section('content')

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Buat Sales Order Baru</h3>
        <a href="{{ route('sales.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

```
{{-- Pesan error --}}
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

<form method="POST" action="{{ route('sales.store') }}">
    @csrf
    <div class="card mb-4">
        <div class="card-header">Info Sales Order</div>
        <div class="card-body row g-3">
            <div class="col-md-4">
                <label for="so_number" class="form-label">No. SO <span class="text-danger">*</span></label>
                <input type="text" name="so_number" id="so_number" class="form-control" value="{{ old('so_number') }}" required>
            </div>
            <div class="col-md-4">
                <label for="order_date" class="form-label">Tanggal <span class="text-danger">*</span></label>
                <input type="date" name="order_date" id="order_date" class="form-control" value="{{ old('order_date', date('Y-m-d')) }}" required>
            </div>
            <div class="col-md-4">
                <label for="customer" class="form-label">Customer <span class="text-danger">*</span></label>
                <input type="text" name="customer" id="customer" class="form-control" value="{{ old('customer') }}" required>
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
        <div class="card-header">Daftar Barang Terjual</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered m-0" id="items-table">
                    <thead>
                        <tr>
                            <th style="width:22%;">Barang <span class="text-danger">*</span></th>
                            <th>Batch No <span class="text-danger">*</span></th>
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
                                <select name="items[{{ $i }}][item_id]" class="form-select item-select" data-row="{{ $i }}" required>
                                    <option value="">Pilih barang...</option>
                                    @foreach($items as $item)
                                        <option value="{{ $item->id }}" {{ ($oldItem['item_id'] ?? '') == $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <select name="items[{{ $i }}][batch_no]" class="form-select batch-select" data-row="{{ $i }}" @if(isset($oldItem['batch_no'])) data-old="{{ $oldItem['batch_no'] }}" @endif required>
                                    <option value="">Pilih batch...</option>
                                    {{-- Options batch akan diisi JS --}}
                                </select>
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
                    <select name="item_id_template" class="form-select item-select" data-row="__row__" required disabled>
                        <option value="">Pilih barang...</option>
                        @foreach($items as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <select name="batch_no_template" class="form-select batch-select" data-row="__row__" required disabled>
                        <option value="">Pilih batch...</option>
                    </select>
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
            <i class="bi bi-save"></i> Simpan Sales Order
        </button>
    </div>
</form>
```

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Data batch dari controller (hanya yang stoknya > 0)
    window.batchData = @json($batches);

    function bindBatchDropdowns() {
        document.querySelectorAll('.item-select').forEach(function(sel) {
            sel.onchange = function() {
                const row = this.getAttribute('data-row');
                const batchSelect = document.querySelector('.batch-select[data-row="' + row + '"]');
                const itemId = this.value;

                // Kosongkan dulu
                batchSelect.innerHTML = '<option value="">Pilih batch...</option>';

                if (itemId && window.batchData[itemId]) {
                    window.batchData[itemId].forEach(function(batch) {
                        // Tampilkan batch + sisa stok
                        const label = `${batch.batch_no} (${batch.available} tersedia)`;
                        const option = document.createElement('option');
                        option.value = batch.batch_no;
                        option.textContent = label;
                        batchSelect.appendChild(option);
                    });

                    // Restore old value jika ada
                    if (batchSelect.dataset.old) {
                        batchSelect.value = batchSelect.dataset.old;
                    }
                }
            };
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        bindBatchDropdowns();

        // Trigger change untuk isi batch saat reload
        document.querySelectorAll('.item-select').forEach(sel => {
            sel.dispatchEvent(new Event('change'));
        });

        // Update subtotal
        function updateSubtotals() {
            document.querySelectorAll('#items-table tbody tr').forEach(function(row) {
                let qty = parseFloat(row.querySelector('.qty-input')?.value) || 0;
                let price = parseFloat(row.querySelector('.price-input')?.value) || 0;
                let subtotal = qty * price;
                row.querySelector('.subtotal').value = subtotal ? subtotal.toLocaleString('id-ID') : '';
            });
        }

        document.querySelector('#items-table').addEventListener('input', function(e) {
            if (e.target.classList.contains('qty-input') || e.target.classList.contains('price-input')) {
                updateSubtotals();
            }
        });

        updateSubtotals();

        // Tambah baris
        document.getElementById('add-row').addEventListener('click', function() {
            let idx = document.querySelectorAll('#items-table tbody tr').length;
            let template = document.getElementById('row-template').outerHTML
                .replace(/_template/g, '['+idx+']')
                .replace(/__row__/g, idx)
                .replace(/disabled/g, '');
            let tr = document.createElement('tr');
            tr.innerHTML = template.match(/<tr[^>]*>([\s\S]*)<\/tr>/i)[1];
            document.querySelector('#items-table tbody').appendChild(tr);
            bindBatchDropdowns();
            updateSubtotals();
        });

        // Hapus baris
        document.querySelector('#items-table tbody').addEventListener('click', function(e) {
            if (e.target.closest('.remove-row')) {
                let trs = document.querySelectorAll('#items-table tbody tr');
                if (trs.length > 1) e.target.closest('tr').remove();
            }
        });
    });
</script>
@endpush