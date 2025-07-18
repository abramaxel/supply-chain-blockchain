@extends('layouts.app')

@section('title', 'Traceability Purchase Order')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Traceability PO: {{ $purchaseOrder->po_number }}</h3>
        <a href="{{ route('procurement.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    {{-- Info PO --}}
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <strong>Info Purchase Order</strong>
        </div>
        <div class="card-body">
            <div class="row mb-2">
                <div class="col-md-4">Supplier</div>
                <div class="col-md-8">{{ $purchaseOrder->supplier }}</div>
            </div>
            <div class="row mb-2">
                <div class="col-md-4">Tanggal</div>
                <div class="col-md-8">{{ \Carbon\Carbon::parse($purchaseOrder->order_date)->format('d/m/Y') }}</div>
            </div>
            <div class="row mb-2">
                <div class="col-md-4">Status</div>
                <div class="col-md-8">
                    <span class="badge bg-{{ $purchaseOrder->status == 'approved' ? 'success' : ($purchaseOrder->status == 'cancelled' ? 'danger' : 'secondary') }}">
                        {{ ucfirst($purchaseOrder->status) }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Blockchain Trace Log --}}
    <div class="card">
        <div class="card-header bg-dark text-white">
            <strong>Riwayat & Trace Blockchain</strong>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered m-0 align-middle">
                    <thead>
                        <tr>
                            <th style="width:10%;">Waktu</th>
                            <th style="width:10%;">Aksi</th>
                            <th style="width:30%;">Hash</th>
                            <th style="width:50%;">Data Perubahan</th> {{-- width 1% biar auto grow --}}
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($traceLogs as $log)
                        <tr>
                            <td style="white-space:nowrap;">{{ \Carbon\Carbon::parse($log->created_at)->format('d/m/Y H:i:s') }}</td>
                            <td>{{ $log->action }}</td>
                            <td style="font-family: monospace; word-break:break-all;">{{ $log->hash }}</td>
                            <td>
                                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="collapse" data-bs-target="#log-{{ $loop->iteration }}">
                                    Detail
                                </button>
                                <div class="collapse mt-2" id="log-{{ $loop->iteration }}">
                                    <pre class="bg-light p-2 small" style="white-space:pre-wrap;word-break:break-all;">{{ json_encode(json_decode($log->data), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">Belum ada log blockchain.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

    <h5 class="mb-3">Riwayat Blockchain per Item</h5>

    @foreach($purchaseOrder->items as $item)
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-info text-white">
            <strong>{{ $item->item->name ?? '-' }}</strong>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <div class="row mb-2">
                    <div class="col-md-4"><strong>Batch</strong></div>
                    <div class="col-md-8">{{ $item->batch_no }}</div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-4"><strong>Lokasi</strong></div>
                    <div class="col-md-8">{{ $item->batch->location ?? '-' }}</div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-4"><strong>Expiry Date</strong></div>
                    <div class="col-md-8">
                        {{ $item->expiry_date ? \Carbon\Carbon::parse($item->expiry_date)->format('d/m/Y') : '-' }}
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-4"><strong>Qty</strong></div>
                    <div class="col-md-8">{{ $item->quantity }}</div>
                </div>
            </div>
            <h6 class="mb-2">Blockchain Log:</h6>
            <div class="table-responsive" style="max-height: 250px;">
                <table class="table table-sm table-bordered mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="font-size:12px;">Block Hash</th>
                            <th style="font-size:12px;">Prev Hash</th>
                            <th style="font-size:12px;">Action</th>
                            <th style="font-size:12px;">User</th>
                            <th style="font-size:12px;">Timestamp</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logsByItem[$item->id] ?? [] as $log)
                            <tr>
                                <td style="font-size:10px; max-width:130px; word-break:break-all;">{{ $log->block_hash }}</td>
                                <td style="font-size:10px; max-width:130px; word-break:break-all;">{{ $log->previous_hash }}</td>
                                <td style="font-size:12px;">{{ $log->action }}</td>
                                <td style="font-size:12px;">{{ $log->user_id }}</td>
                                <td style="font-size:12px;">{{ \Carbon\Carbon::parse($log->created_at)->format('d/m/Y H:i:s') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">Belum ada log blockchain.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endforeach
</div>

@endsection
