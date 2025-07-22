@extends('layouts.app')

@section('title', 'Edit Sales Order')

@section('content')
<div class="container py-4">
    <h3>Edit Sales Order</h3>

    <form action="{{ route('sales.update', $order->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label">Nomor Sales Order</label>
            <input type="text" name="so_number" class="form-control" value="{{ old('so_number', $order->so_number) }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Tanggal Order</label>
            <input type="date" name="order_date" class="form-control" value="{{ old('order_date', $order->order_date) }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Nama Customer</label>
            <input type="text" name="customer" class="form-control" value="{{ old('customer', $order->customer) }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-control" required>
                <option value="open" {{ $order->status == 'open' ? 'selected' : '' }}>Open</option>
                <option value="approved" {{ $order->status == 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="closed" {{ $order->status == 'closed' ? 'selected' : '' }}>Closed</option>
                <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
        </div>

        <!-- Items -->
        <h5>Item Penjualan</h5>
        <div id="items-container">
            @foreach ($order->items as $item)
            <div class="row mb-2 item-row">
                <div class="col-md-3">
                    <select name="items[{{ $loop->index }}][item_id]" class="form-control item-select" required>
                        <option value="">-- Pilih Item --</option>
                        @foreach ($items as $itemOption)
                        <option value="{{ $itemOption->id }}" data-unit="{{ $itemOption->unit }}" {{ $item->item_id == $itemOption->id ? 'selected' : '' }}>
                            {{ $itemOption->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="text" name="items[{{ $loop->index }}][batch_no]" class="form-control" placeholder="Batch No" value="{{ $item->batch_no }}" required>
                </div>
                <div class="col-md-2">
                    <input type="number" name="items[{{ $loop->index }}][quantity]" class="form-control quantity-input" value="{{ $item->quantity }}" min="1" required>
                </div>
                <div class="col-md-2">
                    <input type="number" name="items[{{ $loop->index }}][unit_price]" class="form-control price-input" value="{{ $item->unit_selling_price }}" min="0" required>
                </div>
                <div class="col-md-2">
                    <input type="text" class="form-control total-price" value="{{ $item->total_price }}" readonly>
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-danger btn-sm remove-item">X</button>
                </div>
            </div>
            @endforeach
        </div>

        <button type="button" class="btn btn-secondary btn-sm mb-3" id="add-item">+ Tambah Item</button>

        <div class="d-flex justify-content-between mt-4">
            <a href="{{ route('sales.index') }}" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    document.getElementById('add-item').addEventListener('click', function () {
        const container = document.getElementById('items-container');
        const index = container.querySelectorAll('.item-row').length;
        const newItem = document.createElement('div');
        newItem.className = 'row mb-2 item-row';
        newItem.innerHTML = `
            <div class="col-md-3">
                <select name="items[${index}][item_id]" class="form-control item-select" required>
                    <option value="">-- Pilih Item --</option>
                    @foreach ($items as $item)
                    <option value="{{ $item->id }}" data-unit="{{ $item->unit }}">{{ $item->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <input type="text" name="items[${index}][batch_no]" class="form-control" placeholder="Batch No" required>
            </div>
            <div class="col-md-2">
                <input type="number" name="items[${index}][quantity]" class="form-control quantity-input" min="1" required>
            </div>
            <div class="col-md-2">
                <input type="number" name="items[${index}][unit_price]" class="form-control price-input" min="0" required>
            </div>
            <div class="col-md-2">
                <input type="text" class="form-control total-price" readonly>
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-danger btn-sm remove-item">X</button>
            </div>
        `;
        container.appendChild(newItem);

        // Event listener untuk perhitungan otomatis
        newItem.querySelector('.quantity-input, .price-input').addEventListener('input', calculateTotal);
    });

    document.querySelectorAll('.quantity-input, .price-input').forEach(input => {
        input.addEventListener('input', calculateTotal);
    });

    function calculateTotal(e) {
        const row = e.target.closest('.item-row');
        const quantity = parseFloat(row.querySelector('.quantity-input').value) || 0;
        const price = parseFloat(row.querySelector('.price-input').value) || 0;
        row.querySelector('.total-price').value = (quantity * price).toLocaleString();
    }

    document.addEventListener('click', function (e) {
        if (e.target && e.target.classList.contains('remove-item')) {
            e.target.closest('.item-row').remove();
        }
    });
</script>
@endpush
@endsection