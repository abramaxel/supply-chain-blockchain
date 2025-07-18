@extends('layouts.app')

@section('title', 'Edit Pengadaan (Purchase Order)')

@section('content')

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Edit Purchase Order</h3>
        <a href="{{ route('procurement.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

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

    <form method="POST" action="{{ route('procurement.update', $order->id) }}">
        @csrf
        @method('PUT')

        <div class="card mb-4">
            <div class="card-header">Info Purchase Order</div>
            <div class="card-body row g-3">
                <div class="col-md-4">
                    <label for="po_number" class="form-label">No. PO <span class="text-danger">*</span></label>
                    <input type="text" name="po_number" id="po_number" class="form-control" value="{{ old('po_number', $order->po_number) }}" required>
                </div>
                <div class="col-md-4">
                    <label for="order_date" class="form-label">Tanggal <span class="text-danger">*</span></label>
                    <input type="date" name="order_date" id="order_date" class="form-control" value="{{ old('order_date', $order->order_date ? \Carbon\Carbon::parse($order->order_date)->format('Y-m-d') : '') }}" required>
                </div>
                <div class="col-md-4">
                    <label for="supplier" class="form-label">Supplier <span class="text-danger">*</span></label>
                    <input type="text" name="supplier" id="supplier" class="form-control" value="{{ old('supplier', $order->supplier) }}" required>
                </div>
                <div class="col-md-4">
                    <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                    <select name="status" id="status" class="form-select" required>
                        <option value="open" {{ old('status', $order->status) == 'open' ? 'selected' : '' }}>Open</option>
                        <option value="approved" {{ old('status', $order->status) == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="closed" {{ old('status', $order->status) == 'closed' ? 'selected' : '' }}>Closed</option>
                        <option value="cancelled" {{ old('status', $order->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- <div class="card mb-4">
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
                            @php
                                $oldItems = old('items', []);
                                $poItems = count($oldItems) > 0 ? $oldItems : $order->items->toArray();
                            @endphp
                            @foreach($poItems as $i => $poItem)
                            <tr>
                                <td>
                                    <select name="items[{{ $i }}][item_id]" class="form-select" required>
                                        <option value="">Pilih...</option>
                                        @foreach($items as $item)
                                            <option value="{{ $item->id }}"
                                                {{ (isset($poItem['item_id']) ? $poItem['item_id'] : ($poItem['item']['id'] ?? null)) == $item->id ? 'selected' : '' }}>
                                                {{ $item->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="text" name="items[{{ $i }}][batch_no]" class="form-control" value="{{ $poItem['batch_no'] ?? '' }}">
                                </td>
                                <td>
                                    <select name="items[{{ $i }}][location]" class="form-select" required>
                                        <option value="">Pilih Lokasi...</option>
                                        @foreach(['Gudang A','Gudang B','Gudang C','Gudang D','Gudang E'] as $loc)
                                            <option value="{{ $loc }}" {{ ($poItem['location'] ?? '') == $loc ? 'selected' : '' }}>
                                                {{ $loc }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="date" name="items[{{ $i }}][expiry_date]" class="form-control" value="{{ $poItem['expiry_date'] ?? '' }}">
                                </td>
                                <td>
                                    <input type="number" name="items[{{ $i }}][quantity]" class="form-control qty-input" min="1" value="{{ $poItem['quantity'] ?? '' }}" required>
                                </td>
                                <td>
                                    <input type="number" step="0.01" name="items[{{ $i }}][unit_price]" class="form-control price-input" min="0" value="{{ $poItem['unit_price'] ?? '' }}" required>
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
        </div> --}}

        <div class="text-end">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save"></i> Update Purchase Order
            </button>
        </div>
    </form>
</div>
@endsection
