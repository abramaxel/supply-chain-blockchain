@extends('layouts.app')

@section('title', 'Dashboard')
@section('module-title', 'Dashboard')

@section('content')
<div class="row">
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="card shadow rounded-3">
            <div class="card-body text-center">
                <i class="bi bi-box-seam display-4 mb-2 text-primary"></i>
                <h5 class="card-title">Total Block</h5>
                <p class="display-6" id="dashboard-total-block">...</p>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="card shadow rounded-3">
            <div class="card-body text-center">
                <i class="bi bi-layers display-4 mb-2 text-success"></i>
                <h5 class="card-title">Total Barang</h5>
                <p class="display-6" id="dashboard-total-item">...</p>
            </div>
        </div>
    </div>
    <!-- Tambah widget lain di sini -->
</div>

<div class="card mt-4 shadow rounded-3">
    <div class="card-body">
        <h5 class="card-title mb-3">Ringkasan Aktivitas</h5>
        <div>
            <canvas id="dashboard-block-chart" height="120"></canvas>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Fetch ringkasan (contoh sederhana)
    fetch('/api/blocks')
        .then(res => res.json())
        .then(data => {
            document.getElementById('dashboard-total-block').textContent = data.count || 0;
            // Jika ada API untuk total item, bisa fetch juga
            // document.getElementById('dashboard-total-item').textContent = ...;
            // Render Chart.js contoh di bawah:
            let labels = [];
            let series = {};
            (data.data || []).slice(-10).forEach(b => {
                let d = new Date(b.created_at);
                let jam = d.getHours() + ':' + String(d.getMinutes()).padStart(2, '0');
                labels.push(jam);
                series[b.event_type] = (series[b.event_type] || 0) + 1;
            });
            let chartLabels = Object.keys(series);
            let chartData = Object.values(series);
            const ctx = document.getElementById('dashboard-block-chart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: chartLabels,
                    datasets: [{
                        label: 'Frekuensi Event',
                        data: chartData,
                        backgroundColor: 'rgba(54,162,235,0.4)',
                        borderColor: 'rgba(54,162,235,1)',
                        borderWidth: 2
                    }]
                },
                options: {
                    plugins: { legend: { display: false } },
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: { title: { display: true, text: 'Event Type' } },
                        y: { title: { display: true, text: 'Jumlah' }, beginAtZero: true }
                    }
                }
            });
        });
});
</script>
@endpush
