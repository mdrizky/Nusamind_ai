@extends('layouts.user')

@section('title', 'Skor - NusaScore')

@section('content')
  <div class="page-header">
    <div>
      <h4 class="fw-700 font-heading mb-0" style="color:var(--dark);">NusaScore</h4>
      <small class="text-muted">Kesehatan Bisnismu</small>
    </div>
    <a href="{{ route('user.score.index') }}" class="btn btn-nusa btn-sm">
      <i class="bi bi-arrow-clockwise me-1"></i>Refresh Skor
    </a>
  </div>

  @if($score)
    @php
      $total = $score->total_score;
      $circumference = 2 * pi() * 70;
      $offset = $circumference - ($total / 100) * $circumference;
      if ($total >= 80) {
        $color = '#10b981';
        $label = 'Sehat';
      } elseif ($total >= 60) {
        $color = '#eab308';
        $label = 'Cukup';
      } elseif ($total >= 40) {
        $color = '#f97316';
        $label = 'Kurang';
      } else {
        $color = '#ef4444';
        $label = 'Kritis';
      }
    @endphp

    <div class="card-nusa text-center">
      <div style="position:relative;width:180px;height:180px;margin:0 auto;">
        <svg width="180" height="180" viewBox="0 0 180 180">
          <circle cx="90" cy="90" r="70" fill="none" stroke="#e5e7eb" stroke-width="12"/>
          <circle cx="90" cy="90" r="70" fill="none" stroke="{{ $color }}" stroke-width="12"
            stroke-dasharray="{{ $circumference }}"
            stroke-dashoffset="{{ $offset }}"
            stroke-linecap="round"
            transform="rotate(-90 90 90)"
            style="transition: stroke-dashoffset 0.8s ease;"/>
        </svg>
        <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);text-align:center;">
          <div class="font-heading fw-700" style="font-size:2.5rem;color:var(--dark);">{{ $total }}</div>
          <div class="small fw-600" style="color:{{ $color }};">{{ $label }}</div>
        </div>
      </div>
    </div>

    <div class="row g-2 mb-3">
      @php
        $categories = [
          ['label' => 'Keuangan', 'score' => $score->financial_score, 'icon' => 'bi-wallet2'],
          ['label' => 'Marketing', 'score' => $score->marketing_score, 'icon' => 'bi-megaphone'],
          ['label' => 'Penjualan', 'score' => $score->sales_score, 'icon' => 'bi-graph-up'],
          ['label' => 'Pelanggan', 'score' => $score->customer_score, 'icon' => 'bi-people'],
          ['label' => 'Stok', 'score' => $score->stock_score, 'icon' => 'bi-box-seam'],
        ];
      @endphp
      @foreach($categories as $cat)
        @php
          $c = $cat['score'];
          if ($c >= 80) {
            $barColor = '#10b981';
          } elseif ($c >= 60) {
            $barColor = '#eab308';
          } elseif ($c >= 40) {
            $barColor = '#f97316';
          } else {
            $barColor = '#ef4444';
          }
        @endphp
        <div class="col-6 col-md mb-2">
          <div class="card-nusa text-center p-3 h-100 mb-0">
            <i class="bi {{ $cat['icon'] }} fs-4 mb-1" style="color:var(--primary);"></i>
            <div class="stat-label mb-1">{{ $cat['label'] }}</div>
            <div class="fw-700 font-heading mb-2" style="font-size:1.5rem;color:var(--dark);">{{ $c }}</div>
            <div style="height:6px;background:#e5e7eb;border-radius:3px;overflow:hidden;">
              <div style="height:100%;width:{{ $c }}%;background:{{ $barColor }};border-radius:3px;transition:width 0.6s ease;"></div>
            </div>
          </div>
        </div>
      @endforeach
    </div>

    <div class="card-nusa">
      <h6 class="fw-600 font-heading mb-2" style="color:var(--dark);">Rincian Skor</h6>
      <p class="small mb-0" style="color:#374151;">{{ $score->breakdown_text }}</p>
    </div>

    <div class="card-nusa">
      <h6 class="fw-600 font-heading mb-2" style="color:var(--dark);">Rekomendasi</h6>
      <ul class="mb-0 small" style="color:#374151;padding-left:20px;">
        @foreach($score->recommendations as $rec)
          <li class="mb-1">{{ $rec }}</li>
        @endforeach
      </ul>
    </div>

    <div class="text-center mt-3">
      <a href="{{ route('user.score.history') }}" class="btn btn-nusa-outline btn-sm">
        <i class="bi bi-graph-up me-1"></i>Lihat Riwayat Skor
      </a>
    </div>
  @else
    <div class="card-nusa text-center py-5">
      <i class="bi bi-clipboard-data fs-1 d-block mb-3" style="color:#d1d5db;"></i>
      <p class="text-muted">Lengkapi profil usaha untuk melihat skor kesehatan bisnis.</p>
      <a href="{{ route('user.business') }}" class="btn btn-nusa">Lengkapi Profil Usaha</a>
    </div>
  @endif
@endsection
