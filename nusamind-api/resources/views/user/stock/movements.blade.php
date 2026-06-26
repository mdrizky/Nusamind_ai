@extends('layouts.user')

@section('title', 'Riwayat Stok - NusaStock')
@section('content')
  <div class="page-header">
    <h5 class="fw-600 font-heading mb-0" style="color:var(--dark);">Riwayat Stok</h5>
    <p class="text-muted small mb-0">Perubahan stok produk</p>
  </div>

  @if($movements->count() > 0)
    <div class="card-nusa p-0">
      <div class="table-responsive">
        <table class="table table-nusa mb-0">
          <thead>
            <tr>
              <th>Produk</th>
              <th>Tipe</th>
              <th>Jumlah</th>
              <th>Alasan</th>
              <th>Tanggal</th>
            </tr>
          </thead>
          <tbody>
            @foreach($movements as $m)
              <tr>
                <td class="fw-500">{{ $m->product->name ?? '-' }}</td>
                <td>
                  <span class="badge {{ $m->movement_type === 'adjustment' ? 'bg-warning text-dark' : ($m->movement_type === 'sale' ? 'badge-pengeluaran' : 'badge-pemasukan') }}">
                    {{ $m->movement_type }}
                  </span>
                </td>
                <td class="fw-600 {{ $m->quantity > 0 ? 'text-success' : 'text-danger' }}">
                  {{ $m->quantity > 0 ? '+' : '' }}{{ $m->quantity }}
                </td>
                <td class="text-muted small">{{ $m->reason ?? '-' }}</td>
                <td class="text-muted small">{{ $m->created_at ? $m->created_at->format('d M Y H:i') : '-' }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
    <div class="mt-3 d-flex justify-content-center">
      {{ $movements->links() }}
    </div>
  @else
    <div class="card-nusa">
      <div class="empty-state">
        <i class="bi bi-arrow-left-right"></i>
        <p class="fw-500">Belum ada riwayat stok</p>
        <p class="small">Perubahan stok akan tercatat di sini.</p>
      </div>
    </div>
  @endif
@endsection
