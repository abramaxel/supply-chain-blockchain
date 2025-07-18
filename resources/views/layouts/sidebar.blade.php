<nav class="nav flex-column">
    <a class="nav-link text-white" href="{{ route('dashboard') }}">
        <i class="bi bi-house-door"></i> <span>Dashboard</span>
    </a>
    <a class="nav-link text-white" href="{{ route('items.index') }}">
        <i class="bi bi-box-seam"></i> <span>Master Barang</span>
    </a>
    <a class="nav-link text-white" href="{{ route('batches.index') }}">
        <i class="bi bi-hash"></i> <span>Batch</span>
    </a>
    <a class="nav-link text-white" href="{{ route('procurement.index') }}">
        <i class="bi bi-bag-check"></i> <span>Pengadaan</span>
    </a>
    <a class="nav-link text-white" href="{{ route('inventory.index') }}">
        <i class="bi bi-archive"></i> <span>Stok & Mutasi</span>
    </a>
    <a class="nav-link text-white" href="{{ route('sales.index') }}">
        <i class="bi bi-cart-check"></i> <span>Penjualan</span>
    </a>
    <a class="nav-link text-white" href="{{ route('blockchain.index') }}">
        <i class="bi bi-link-45deg"></i> <span>Blockchain & Trace</span>
    </a>
    <a class="nav-link text-white" href="{{ route('reports.index') }}">
        <i class="bi bi-bar-chart"></i> <span>Laporan</span>
    </a>
    <div class="divider"></div>
    <a class="logout nav-link text-white" href="#" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
        <i class="bi bi-box-arrow-right"></i> <span>Logout</span>
    </a>
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>
</nav>
