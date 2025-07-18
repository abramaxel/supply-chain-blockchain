@extends('layouts.app')

@section('title', 'Detail Block')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Detail Blockchain Block #{{ $block->id }}</h3>
        <a href="{{ route('blockchain.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Kembali ke Explorer
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <strong>Informasi Block</strong>
        </div>
        <div class="card-body">
            <div class="row mb-2">
                <div class="col-md-3 fw-bold">ID</div>
                <div class="col-md-9">{{ $block->id }}</div>
            </div>
            <div class="row mb-2">
                <div class="col-md-3 fw-bold">Block Hash</div>
                <div class="col-md-9"><code style="font-size:12px">{{ $block->block_hash }}</code></div>
            </div>
            <div class="row mb-2">
                <div class="col-md-3 fw-bold">Previous Hash</div>
                <div class="col-md-9"><code style="font-size:12px">{{ $block->previous_hash ?? '-' }}</code></div>
            </div>
            <div class="row mb-2">
                <div class="col-md-3 fw-bold">Entity Type</div>
                <div class="col-md-9">{{ $block->entity_type }}</div>
            </div>
            <div class="row mb-2">
                <div class="col-md-3 fw-bold">Entity ID</div>
                <div class="col-md-9">{{ $block->entity_id }}</div>
            </div>
            <div class="row mb-2">
                <div class="col-md-3 fw-bold">Action</div>
                <div class="col-md-9">
                    <span class="badge bg-{{ $block->action == 'CANCELLED' ? 'danger' : ($block->action == 'CREATE' ? 'success' : 'info') }}">
                        {{ strtoupper($block->action) }}
                    </span>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-3 fw-bold">User</div>
                <div class="col-md-9">{{ $block->user_id ?? '-' }}</div>
            </div>
            <div class="row mb-2">
                <div class="col-md-3 fw-bold">Waktu</div>
                <div class="col-md-9">{{ \Carbon\Carbon::parse($block->created_at)->format('d/m/Y H:i:s') }}</div>
            </div>
        </div>
    </div>

    <div class="card mt-4 shadow-sm">
        <div class="card-header bg-info text-white">
            <strong>Data Perubahan (Payload)</strong>
        </div>
        <div class="card-body">
            <pre class="bg-light p-3 small" style="white-space:pre-wrap;word-break:break-all;">{{ $block->data_pretty ?? $block->data }}</pre>
        </div>
    </div>

    <div class="text-end mt-3">
        <button class="btn btn-outline-success" id="verifyBtn">
            <i class="bi bi-shield-check"></i> Verify Block
        </button>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('verifyBtn').onclick = function() {
    fetch('/blockchain/{{ $block->id }}/verify')
        .then(response => response.json())
        .then(resp => {
            Swal.fire({
                icon: resp.isValid ? 'success' : 'error',
                title: resp.isValid ? 'Block VALID!' : 'Block TIDAK VALID!',
                html: `<b>Hash di DB:</b><br><code>${resp.originalHash}</code><br><br>`
                    + `<b>Hash dihitung ulang:</b><br><code>${resp.rehashed}</code>`
            });
        });
};
</script>
@endpush
