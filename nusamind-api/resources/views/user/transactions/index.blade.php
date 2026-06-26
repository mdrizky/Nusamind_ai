@extends('layouts.user')

@section('title', 'Transaksi')
@section('content')
  <div class="page-header">
    <h5 class="fw-600 font-heading mb-0" style="color:var(--dark);">Transaksi</h5>
  </div>

  <div class="row g-3 mb-3">
    <div class="col-6">
      <div class="card-nusa d-flex align-items-center gap-3">
        <div class="card-icon tosca"><i class="bi bi-arrow-down-circle"></i></div>
        <div>
          <div class="stat-value" style="font-size:1.1rem;">Rp{{ number_format($totalIncome ?? 0, 0, ',', '.') }}</div>
          <div class="stat-label">Pemasukan</div>
        </div>
      </div>
    </div>
    <div class="col-6">
      <div class="card-nusa d-flex align-items-center gap-3">
        <div class="card-icon gold"><i class="bi bi-arrow-up-circle"></i></div>
        <div>
          <div class="stat-value" style="font-size:1.1rem;">Rp{{ number_format($totalExpense ?? 0, 0, ',', '.') }}</div>
          <div class="stat-label">Pengeluaran</div>
        </div>
      </div>
    </div>
  </div>

  <div class="d-flex gap-2 mb-2 flex-wrap">
    <a href="{{ route('user.transactions', ['filter' => $filter, 'period' => 'all']) }}" class="btn btn-sm rounded-pill {{ ($period ?? 'all') === 'all' ? 'btn-nusa' : 'btn-nusa-outline' }}">Semua</a>
    <a href="{{ route('user.transactions', ['filter' => $filter, 'period' => 'today']) }}" class="btn btn-sm rounded-pill {{ ($period ?? 'all') === 'today' ? 'btn-nusa' : 'btn-nusa-outline' }}">Hari Ini</a>
    <a href="{{ route('user.transactions', ['filter' => $filter, 'period' => 'week']) }}" class="btn btn-sm rounded-pill {{ ($period ?? 'all') === 'week' ? 'btn-nusa' : 'btn-nusa-outline' }}">Minggu Ini</a>
    <a href="{{ route('user.transactions', ['filter' => $filter, 'period' => 'month']) }}" class="btn btn-sm rounded-pill {{ ($period ?? 'all') === 'month' ? 'btn-nusa' : 'btn-nusa-outline' }}">Bulan Ini</a>
  </div>
  <div class="d-flex gap-2 mb-3">
    <a href="{{ route('user.transactions', ['period' => $period ?? 'all', 'filter' => 'all']) }}" class="btn btn-sm rounded-pill {{ $filter === 'all' ? 'btn-nusa' : 'btn-nusa-outline' }}">Semua</a>
    <a href="{{ route('user.transactions', ['period' => $period ?? 'all', 'filter' => 'pemasukan']) }}" class="btn btn-sm rounded-pill {{ $filter === 'pemasukan' ? 'btn-nusa' : 'btn-nusa-outline' }}">Pemasukan</a>
    <a href="{{ route('user.transactions', ['period' => $period ?? 'all', 'filter' => 'pengeluaran']) }}" class="btn btn-sm rounded-pill {{ $filter === 'pengeluaran' ? 'btn-nusa' : 'btn-nusa-outline' }}">Pengeluaran</a>
  </div>

  @if($transactions->count() > 0)
    <div class="card-nusa p-0">
      <div class="table-responsive">
        <table class="table table-nusa mb-0">
          <thead>
            <tr>
              <th>Item</th>
              <th>Jumlah</th>
              <th>Tipe</th>
              <th>Tanggal</th>
            </tr>
          </thead>
          <tbody>
            @foreach($transactions as $t)
              <tr>
                <td class="fw-500">{{ $t->item_name }}</td>
                <td class="fw-600">Rp{{ number_format($t->amount, 0, ',', '.') }}</td>
                <td>
                  <span class="badge {{ $t->type === 'pemasukan' ? 'badge-pemasukan' : 'badge-pengeluaran' }}">
                    {{ $t->type === 'pemasukan' ? 'Masuk' : 'Keluar' }}
                  </span>
                </td>
                <td class="text-muted small">{{ $t->transaction_date ? $t->transaction_date->format('d M Y') : '-' }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
    <div class="mt-3 d-flex justify-content-center">
      {{ $transactions->links() }}
    </div>
  @else
    <div class="card-nusa">
      <div class="empty-state">
        <i class="bi bi-inbox"></i>
        <p class="fw-500">Belum ada transaksi</p>
        <p class="small">Catat pemasukan dan pengeluaran usahamu menggunakan AI atau manual.</p>
      </div>
    </div>
  @endif
@endsection
