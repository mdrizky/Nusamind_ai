@extends('layouts.user')

@section('title', 'Stok - NusaStock')
@section('content')
  <div class="page-header">
    <h5 class="fw-600 font-heading mb-0" style="color:var(--dark);">NusaStock</h5>
    <p class="text-muted small mb-0">Manajemen Stok dengan AI</p>
  </div>

  <div class="row g-3 mb-3">
    <div class="col-4">
      <div class="card-nusa text-center">
        <div class="stat-value" style="font-size:1.5rem;color:#16a34a;">{{ $statusCounts['aman'] }}</div>
        <div class="stat-label">Aman</div>
      </div>
    </div>
    <div class="col-4">
      <div class="card-nusa text-center">
        <div class="stat-value" style="font-size:1.5rem;color:#ca8a04;">{{ $statusCounts['menipis'] }}</div>
        <div class="stat-label">Menipis</div>
      </div>
    </div>
    <div class="col-4">
      <div class="card-nusa text-center">
        <div class="stat-value" style="font-size:1.5rem;color:#dc2626;">{{ $statusCounts['habis'] }}</div>
        <div class="stat-label">Habis</div>
      </div>
    </div>
  </div>

  <div class="card-nusa mb-3">
    <div class="d-flex align-items-center justify-content-between">
      <div>
        <h6 class="fw-600 font-heading mb-1" style="color:var(--dark);">Rekomendasi AI</h6>
        <p class="small text-muted mb-0">Analisis stok dan dapatkan rekomendasi restock</p>
      </div>
      <form method="POST" action="{{ route('user.stock.ai-recommend') }}">
        @csrf
        <button type="submit" class="btn btn-nusa btn-sm">
          <i class="bi bi-stars"></i> Analisis
        </button>
      </form>
    </div>
  </div>

  @if(session('recommendations'))
    @foreach(session('recommendations') as $rec)
      <div class="card-nusa mb-2" style="border-left:4px solid {{ $rec['status'] === 'habis' ? '#dc2626' : ($rec['status'] === 'menipis' ? '#ca8a04' : '#16a34a') }};">
        <div class="d-flex justify-content-between align-items-start">
          <div>
            <h6 class="fw-600 small mb-1" style="color:var(--dark);">{{ $rec['product_name'] ?? $rec['name'] ?? '-' }}</h6>
            <p class="small mb-1" style="color:#374151;">{{ $rec['reason'] ?? '' }}</p>
            @if(isset($rec['recommended_restock']) && $rec['recommended_restock'] > 0)
              <span class="badge bg-tosca-light text-tosca small">Rekomendasi restock: {{ $rec['recommended_restock'] }}</span>
            @endif
          </div>
          <span class="badge {{ $rec['status'] === 'habis' ? 'bg-danger' : ($rec['status'] === 'menipis' ? 'bg-warning text-dark' : 'bg-success') }}" style="font-size:0.7rem;">
            {{ $rec['status'] }}
          </span>
        </div>
      </div>
    @endforeach
  @endif

  <h6 class="fw-600 font-heading mb-3 mt-4" style="color:var(--dark);">Semua Produk</h6>
  @if($products->count() > 0)
    <div class="card-nusa p-0">
      <div class="table-responsive">
        <table class="table table-nusa mb-0">
          <thead>
            <tr>
              <th>Produk</th>
              <th>Stok</th>
              <th>Min. Alert</th>
              <th>Status</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            @foreach($products as $p)
              @php
                $status = $p->stock <= 0 ? 'habis' : ($p->stock <= $p->min_stock_alert ? 'menipis' : 'aman');
                $badgeClass = $status === 'habis' ? 'bg-danger' : ($status === 'menipis' ? 'bg-warning text-dark' : 'bg-success');
              @endphp
              <tr>
                <td class="fw-500">{{ $p->name }}</td>
                <td>{{ $p->stock ?? 0 }}</td>
                <td>{{ $p->min_stock_alert ?? 5 }}</td>
                <td><span class="badge {{ $badgeClass }}" style="font-size:0.7rem;">{{ $status }}</span></td>
                <td>
                  <button class="btn btn-sm btn-nusa-outline" data-bs-toggle="modal" data-bs-target="#adjustModal"
                          data-product-id="{{ $p->id }}" data-product-name="{{ $p->name }}">
                    <i class="bi bi-pencil"></i> Adjust
                  </button>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  @else
    <div class="card-nusa">
      <div class="empty-state">
        <i class="bi bi-box"></i>
        <p class="fw-500">Belum ada produk</p>
        <p class="small">Tambahkan produk terlebih dahulu untuk memantau stok.</p>
      </div>
    </div>
  @endif

  <div class="modal fade" id="adjustModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content" style="border-radius:16px;border:none;">
        <div class="modal-header border-0">
          <h6 class="fw-600 font-heading mb-0" style="color:var(--dark);">Adjust Stok</h6>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form method="POST" action="{{ route('user.stock.adjust') }}">
          @csrf
          <div class="modal-body">
            <input type="hidden" name="product_id" id="adjustProductId">
            <p class="small text-muted mb-3">Menyesuaikan stok <strong id="adjustProductName"></strong></p>
            <div class="mb-3">
              <label class="form-label small fw-500">Jumlah Perubahan</label>
              <input type="number" name="quantity" class="form-control" required placeholder="Gunakan + atau - (contoh: 10 atau -5)">
              <small class="text-muted">Gunakan nilai positif untuk menambah stok, negatif untuk mengurangi.</small>
            </div>
            <div class="mb-3">
              <label class="form-label small fw-500">Alasan <small class="text-muted">(opsional)</small></label>
              <input type="text" name="reason" class="form-control" placeholder="Misal: Restock, retur, rusak">
            </div>
          </div>
          <div class="modal-footer border-0">
            <button type="button" class="btn btn-nusa-outline btn-sm" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-nusa btn-sm">Simpan</button>
          </div>
        </form>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
<script>
  document.getElementById('adjustModal').addEventListener('show.bs.modal', function (e) {
    var btn = e.relatedTarget;
    document.getElementById('adjustProductId').value = btn.getAttribute('data-product-id');
    document.getElementById('adjustProductName').textContent = btn.getAttribute('data-product-name');
  });
</script>
@endpush
