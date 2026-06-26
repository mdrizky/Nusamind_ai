@extends('layouts.user')

@section('title', 'Riwayat Skor')

@section('content')
  <div class="page-header">
    <div>
      <h4 class="fw-700 font-heading mb-0" style="color:var(--dark);">Riwayat Skor</h4>
      <small class="text-muted">Perkembangan kesehatan bisnismu</small>
    </div>
    <a href="{{ route('user.score.index') }}" class="btn btn-nusa btn-sm">
      <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
  </div>

  @if($scores->count() > 0)
    <div class="card-nusa p-0">
      <div class="table-responsive">
        <table class="table table-nusa mb-0">
          <thead>
            <tr>
              <th>Tanggal</th>
              <th>Total</th>
              <th>Keuangan</th>
              <th>Marketing</th>
              <th>Penjualan</th>
              <th>Pelanggan</th>
              <th>Stok</th>
            </tr>
          </thead>
          <tbody>
            @foreach($scores as $s)
              @php
                $t = $s->total_score;
                if ($t >= 80) {
                  $td = '#10b981';
                } elseif ($t >= 60) {
                  $td = '#eab308';
                } elseif ($t >= 40) {
                  $td = '#f97316';
                } else {
                  $td = '#ef4444';
                }
              @endphp
              <tr>
                <td class="small">{{ $s->scored_at ? $s->scored_at->format('d M Y, H:i') : '-' }}</td>
                <td class="fw-700" style="color:{{ $td }};">{{ $t }}</td>
                <td>{{ $s->financial_score }}</td>
                <td>{{ $s->marketing_score }}</td>
                <td>{{ $s->sales_score }}</td>
                <td>{{ $s->customer_score }}</td>
                <td>{{ $s->stock_score }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  @else
    <div class="card-nusa text-center py-5">
      <i class="bi bi-clock-history fs-1 d-block mb-3" style="color:#d1d5db;"></i>
      <p class="text-muted">Belum ada riwayat skor. Hitung skor kesehatan bisnismu sekarang!</p>
      <a href="{{ route('user.score.index') }}" class="btn btn-nusa">Lihat NusaScore</a>
    </div>
  @endif
@endsection
