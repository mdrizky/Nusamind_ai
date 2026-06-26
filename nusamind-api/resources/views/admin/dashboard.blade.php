@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="row">
  <div class="col-xl-3 col-md-6">
    <div class="card info-card revenue-card">
      <div class="card-body">
        <h5 class="card-title">Total User</h5>
        <div class="d-flex align-items-center">
          <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
            <i class="bi bi-people"></i>
          </div>
          <div class="ps-3">
            <h6>{{ $totalUsers ?? 0 }}</h6>
            <span class="text-muted small">Terdaftar</span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-xl-3 col-md-6">
    <div class="card info-card customers-card">
      <div class="card-body">
        <h5 class="card-title">User Aktif</h5>
        <div class="d-flex align-items-center">
          <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
            <i class="bi bi-person-check"></i>
          </div>
          <div class="ps-3">
            <h6>{{ $activeUsers ?? 0 }}</h6>
            <span class="text-muted small">7 Hari Terakhir</span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-xl-3 col-md-6">
    <div class="card info-card revenue-card">
      <div class="card-body">
        <h5 class="card-title">Total Transaksi</h5>
        <div class="d-flex align-items-center">
          <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
            <i class="bi bi-cash-stack"></i>
          </div>
          <div class="ps-3">
            <h6>{{ $totalTransactions ?? 0 }}</h6>
            <span class="text-muted small">Tercatat</span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-xl-3 col-md-6">
    <div class="card info-card revenue-card">
      <div class="card-body">
        <h5 class="card-title">Pemakaian AI</h5>
        <div class="d-flex align-items-center">
          <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
            <i class="bi bi-cpu"></i>
          </div>
          <div class="ps-3">
            <h6>{{ $aiUsageToday ?? 0 }}</h6>
            <span class="text-muted small">Hari Ini</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-lg-6">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">AI Usage per Fitur <span class="text-muted small">| Hari Ini</span></h5>
        <div style="height:250px;">
          <canvas id="aiUsageChart"></canvas>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-6">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">Pertumbuhan User <span class="text-muted small">| 8 Minggu</span></h5>
        <div style="height:250px;">
          <canvas id="userGrowthChart"></canvas>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row mt-3">
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">Selamat Datang di Nusamind AI</h5>
        <p class="card-text">
          Panel admin untuk mengelola pengguna, memonitor pemakaian AI, dan moderasi konten.
        </p>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
  const featureColors = ['#0F9D8E', '#F2B705', '#e74c3c', '#3498db', '#9b59b6', '#1abc9c'];

  const usageData = @json($aiUsagePerFeature ?? []);
  if (Object.keys(usageData).length > 0) {
    new Chart(document.getElementById('aiUsageChart'), {
      type: 'bar',
      data: {
        labels: Object.keys(usageData).map(f => {
          const names = {finance: 'Finance', content: 'Konten', export: 'Export', briefing: 'Briefing'};
          return names[f] || f;
        }),
        datasets: [{
          label: 'Jumlah',
          data: Object.values(usageData),
          backgroundColor: featureColors.slice(0, Object.keys(usageData).length),
          borderRadius: 6,
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
          y: { beginAtZero: true, ticks: { stepSize: 1 } }
        }
      }
    });
  } else {
    document.getElementById('aiUsageChart').parentElement.innerHTML =
      '<div class="text-center text-muted py-5"><i class="bi bi-bar-chart fs-1 d-block mb-2"></i><small>Belum ada data AI hari ini</small></div>';
  }

  const growthData = @json($userGrowthWeekly ?? []);
  if (Object.keys(growthData).length > 0) {
    new Chart(document.getElementById('userGrowthChart'), {
      type: 'line',
      data: {
        labels: Object.keys(growthData),
        datasets: [{
          label: 'User Baru',
          data: Object.values(growthData),
          borderColor: '#0F9D8E',
          backgroundColor: 'rgba(15,157,142,0.1)',
          fill: true,
          tension: 0.4,
          pointBackgroundColor: '#0F9D8E',
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
          y: { beginAtZero: true, ticks: { stepSize: 1 } }
        }
      }
    });
  } else {
    document.getElementById('userGrowthChart').parentElement.innerHTML =
      '<div class="text-center text-muted py-5"><i class="bi bi-graph-up fs-1 d-block mb-2"></i><small>Belum ada data pertumbuhan</small></div>';
  }
});
</script>
@endpush
