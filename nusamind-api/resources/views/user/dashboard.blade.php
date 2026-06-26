@extends('layouts.user')

@section('title', 'Beranda')

@section('content')
  <div class="d-flex align-items-center gap-2 mb-3">
    <div class="avatar" style="width:48px;height:48px;border-radius:50%;background:var(--primary-light);color:var(--primary);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:1.25rem;">
      {{ substr(Auth::user()->name, 0, 1) }}
    </div>
    <div>
      <h5 class="mb-0 fw-600 font-heading" style="color:var(--dark);">Halo, {{ Auth::user()->name }}! 👋</h5>
      <small class="text-muted">Selamat datang di Nusamind AI</small>
    </div>
  </div>

  @if(!$business)
    <div class="card-nusa" style="background:linear-gradient(135deg,var(--primary) 0%,#0c8578 100%);color:white;">
      <div class="d-flex align-items-center gap-3">
        <div style="font-size:2.5rem;">🚀</div>
        <div>
          <h5 class="mb-1 fw-600" style="color:white;">Mulai Catat Usahamu!</h5>
          <p class="mb-3 small opacity-75">Catat pemasukan & pengeluaran, kelola produk, dan dapatkan insight bisnis dengan AI.</p>
          <a href="{{ route('user.business') }}" class="btn btn-light btn-sm rounded-pill fw-500">Lengkapi Profil Usaha →</a>
        </div>
      </div>
    </div>
  @endif

  @if(isset($latestInsight) && $latestInsight)
    <div class="card-nusa mb-3" style="background:linear-gradient(135deg,#f0fdfa 0%,#e6f7f5 100%);border:1px solid #b8e5e0;">
      <div class="d-flex align-items-start gap-2">
        <div style="font-size:1.5rem;">📊</div>
        <div>
          <h6 class="fw-600 mb-1" style="color:var(--dark);">Briefing Mingguan</h6>
          <p class="small mb-1" style="color:#374151;">{{ $latestInsight->narrative_text }}</p>
          <small class="text-muted">
            {{ $latestInsight->period_start ? \Carbon\Carbon::parse($latestInsight->period_start)->format('d M') : '' }} -
            {{ $latestInsight->period_end ? \Carbon\Carbon::parse($latestInsight->period_end)->format('d M') : '' }}
          </small>
        </div>
      </div>
    </div>
  @endif

  <div class="row g-3 mb-3">
    <div class="col-6">
      <div class="card-nusa d-flex align-items-center gap-3">
        <div class="card-icon tosca"><i class="bi bi-cash-stack"></i></div>
        <div>
          <div class="stat-value">Rp{{ number_format($totalIncome ?? 0, 0, ',', '.') }}</div>
          <div class="stat-label">Pemasukan</div>
        </div>
      </div>
    </div>
    <div class="col-6">
      <div class="card-nusa d-flex align-items-center gap-3">
        <div class="card-icon gold"><i class="bi bi-cart"></i></div>
        <div>
          <div class="stat-value">Rp{{ number_format($totalExpense ?? 0, 0, ',', '.') }}</div>
          <div class="stat-label">Pengeluaran</div>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-3 mb-3">
    <div class="col-4">
      <div class="card-nusa text-center">
        <div class="stat-value" style="font-size:1.25rem;">{{ $transactionCount ?? 0 }}</div>
        <div class="stat-label">Transaksi</div>
      </div>
    </div>
    <div class="col-4">
      <div class="card-nusa text-center">
        <div class="stat-value" style="font-size:1.25rem;">{{ $productCount ?? 0 }}</div>
        <div class="stat-label">Produk</div>
      </div>
    </div>
    <div class="col-4">
      <div class="card-nusa text-center">
        <div class="stat-value" style="font-size:1.25rem;">{{ $aiUsageCount ?? 0 }}</div>
        <div class="stat-label">Pakai AI</div>
      </div>
    </div>
  </div>

  <h6 class="fw-600 font-heading mb-2" style="color:var(--dark);">Fitur Cepat</h6>
  <div class="row g-2 mb-3">
    <div class="col-4">
      <a href="{{ route('user.reply.index') }}" class="text-decoration-none">
        <div class="card-nusa text-center py-3">
          <div class="card-icon mx-auto mb-1" style="background:#06B6D415;color:#06B6D4;width:40px;height:40px;border-radius:10px;display:flex;align-items:center;justify-content:center;">
            <i class="bi bi-chat-dots" style="font-size:1.2rem;"></i>
          </div>
          <div class="stat-label">Balas</div>
        </div>
      </a>
    </div>
    <div class="col-4">
      <a href="{{ route('user.stock.index') }}" class="text-decoration-none">
        <div class="card-nusa text-center py-3">
          <div class="card-icon mx-auto mb-1" style="background:#10B98115;color:#10B981;width:40px;height:40px;border-radius:10px;display:flex;align-items:center;justify-content:center;">
            <i class="bi bi-box-seam" style="font-size:1.2rem;"></i>
          </div>
          <div class="stat-label">Stok</div>
        </div>
      </a>
    </div>
    <div class="col-4">
      <a href="{{ route('user.campaign.index') }}" class="text-decoration-none">
        <div class="card-nusa text-center py-3">
          <div class="card-icon mx-auto mb-1" style="background:#F59E0B15;color:#F59E0B;width:40px;height:40px;border-radius:10px;display:flex;align-items:center;justify-content:center;">
            <i class="bi bi-bullseye" style="font-size:1.2rem;"></i>
          </div>
          <div class="stat-label">Promo</div>
        </div>
      </a>
    </div>
    <div class="col-4">
      <a href="{{ route('user.price.index') }}" class="text-decoration-none">
        <div class="card-nusa text-center py-3">
          <div class="card-icon mx-auto mb-1" style="background:#EF444415;color:#EF4444;width:40px;height:40px;border-radius:10px;display:flex;align-items:center;justify-content:center;">
            <i class="bi bi-tags" style="font-size:1.2rem;"></i>
          </div>
          <div class="stat-label">Harga</div>
        </div>
      </a>
    </div>
    <div class="col-4">
      <a href="{{ route('user.catalog.index') }}" class="text-decoration-none">
        <div class="card-nusa text-center py-3">
          <div class="card-icon mx-auto mb-1" style="background:#6366F115;color:#6366F1;width:40px;height:40px;border-radius:10px;display:flex;align-items:center;justify-content:center;">
            <i class="bi bi-collection" style="font-size:1.2rem;"></i>
          </div>
          <div class="stat-label">Katalog</div>
        </div>
      </a>
    </div>
    <div class="col-4">
      <a href="{{ route('user.coach.index') }}" class="text-decoration-none">
        <div class="card-nusa text-center py-3">
          <div class="card-icon mx-auto mb-1" style="background:#7C3AED15;color:#7C3AED;width:40px;height:40px;border-radius:10px;display:flex;align-items:center;justify-content:center;">
            <i class="bi bi-person-workspace" style="font-size:1.2rem;"></i>
          </div>
          <div class="stat-label">Mentor</div>
        </div>
      </a>
    </div>
  </div>

  <div class="d-flex align-items-center justify-content-between mb-2">
    <h6 class="fw-600 font-heading mb-0" style="color:var(--dark);">Transaksi Terbaru</h6>
    <a href="{{ route('user.transactions') }}" class="text-decoration-none small" style="color:var(--primary);">Lihat Semua →</a>
  </div>

  @if($recentTransactions->count() > 0)
    <div class="card-nusa p-0">
      <div class="table-responsive">
        <table class="table table-nusa mb-0">
          <thead>
            <tr>
              <th>Item</th>
              <th>Jumlah</th>
              <th>Tgl</th>
            </tr>
          </thead>
          <tbody>
            @foreach($recentTransactions as $t)
              <tr>
                <td>
                  <span class="badge {{ $t->type === 'pemasukan' ? 'badge-pemasukan' : 'badge-pengeluaran' }} me-1">{{ $t->type === 'pemasukan' ? '+' : '-' }}</span>
                  {{ $t->item_name }}
                </td>
                <td class="fw-600">Rp{{ number_format($t->amount, 0, ',', '.') }}</td>
                <td class="text-muted small">{{ $t->transaction_date ? $t->transaction_date->format('d/m') : '-' }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  @else
    <div class="card-nusa text-center text-muted py-4">
      <i class="bi bi-inbox fs-1 d-block mb-2" style="color:#d1d5db;"></i>
      <small>Belum ada transaksi. Yuk catat pemasukan & pengeluaran usahamu!</small>
    </div>
  @endif
@endsection
