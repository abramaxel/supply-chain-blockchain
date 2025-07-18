@extends('layouts.app')

@section('title', 'Blockchain Explorer')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Blockchain Explorer</h3>
        <form class="d-flex" method="GET" action="{{ route('blockchain.index') }}">
            <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm me-2" placeholder="Cari hash/entity">
            <select name="entity_type" class="form-select form-select-sm me-2">
                <option value="">Semua Entitas</option>
                @foreach($entityTypes as $type)
                    <option value="{{ $type }}" {{ request('entity_type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                @endforeach
            </select>
            <select name="user_id" class="form-select form-select-sm me-2">
                <option value="">Semua User</option>
                @foreach($users as $uid)
                    <option value="{{ $uid }}" {{ request('user_id') == $uid ? 'selected' : '' }}>{{ $uid }}</option>
                @endforeach
            </select>
            <button class="btn btn-sm btn-primary" type="submit">Filter</button>
        </form>
    </div>

    @if($blocks->count() === 0)
        <div class="alert alert-info">Belum ada blok blockchain yang tercatat.</div>
    @endif

    @foreach($blocks as $block)
    <div class="card mb-3 shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <span class="badge bg-secondary me-2">ID #{{ $block->id }}</span>
                <span style="font-size:13px"><b>Hash:</b> <code>{{ $block->block_hash }}</code></span>
            </div>
            <div>
                <span class="badge bg-{{ $block->action == 'CANCELLED' ? 'danger' : ($block->action == 'CREATE' ? 'success' : 'info') }} me-2">{{ strtoupper($block->action) }}</span>
                <button class="btn btn-outline-success btn-sm verify-btn" data-id="{{ $block->id }}">
                    <i class="bi bi-shield-check"></i> Verify
                </button>
                <a href="{{ route('blockchain.detail', $block->id) }}" class="btn btn-outline-secondary btn-sm ms-2">Detail</a>
            </div>
        </div>
        <div class="card-body">
            <ul class="mb-2 small">
                <li><strong>Entitas:</strong> {{ $block->entity_type }} / <b>ID:</b> {{ $block->entity_id }}</li>
                <li><strong>User:</strong> {{ $block->user_id ?? '-' }}</li>
                <li><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($block->created_at)->format('d/m/Y H:i:s') }}</li>
                <li><strong>Prev Hash:</strong> <code>{{ $block->previous_hash ?? '-' }}</code></li>
            </ul>
            <button class="btn btn-outline-primary btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#block-{{ $block->id }}">
                Lihat Trace/Log
            </button>
            <div class="collapse mt-2" id="block-{{ $block->id }}">
                <pre class="bg-light p-2 small" style="white-space:pre-wrap;word-break:break-all;">{{ json_encode(json_decode($block->data), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            </div>
        </div>
    </div>
    @endforeach

    <div class="mt-3">
        {{ $blocks->appends(request()->except('page'))->links() }}
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.verify-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const blockId = btn.getAttribute('data-id');
                fetch(`/blockchain/${blockId}/verify`)
                    .then(response => response.json())
                    .then(resp => {
                        Swal.fire({
                            icon: resp.isValid ? 'success' : 'error',
                            title: resp.isValid ? 'Block VALID!' : 'Block TIDAK VALID!',
                            html: `<b>Hash di DB:</b><br><code>${resp.originalHash}</code><br><br>`
                                + `<b>Hash dihitung ulang:</b><br><code>${resp.rehashed}</code>`
                        });
                    });
            });
        });
    });
</script>
@endpush
