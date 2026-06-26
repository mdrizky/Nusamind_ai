@extends('layouts.user')

@section('title', 'Harga - NusaPrice')

@section('content')
  <div class="page-header">
    <div>
      <h5 class="fw-600 font-heading mb-0" style="color:var(--dark);">NusaPrice</h5>
      <small class="text-muted">Analisis Harga dengan AI</small>
    </div>
  </div>

  <div class="card-nusa">
    <h6 class="fw-600 font-heading mb-3" style="color:var(--dark);">Rekomendasi Harga</h6>
    <form method="POST" action="{{ route('user.price.recommend') }}">
      @csrf
      <div class="mb-3">
        <label class="form-label fw-500 small">Produk</label>
        <select name="product_id" class="form-select" required>
          <option value="">-- Pilih Produk --</option>
          @foreach($products as $p)
            <option value="{{ $p->id }}">{{ $p->name }} (Rp{{ number_format($p->price, 0, ',', '.') }})</option>
          @endforeach
        </select>
      </div>
      <div class="mb-3">
        <label class="form-label fw-500 small">Harga Kompetitor <span class="text-muted">(opsional)</span></label>
        <input type="number" name="competitor_price" class="form-control" placeholder="Rp" min="0" step="0.01">
      </div>
      <button type="submit" class="btn btn-nusa w-100">
        <i class="bi bi-graph-up-arrow me-1"></i> Rekomendasi Harga
      </button>
    </form>
  </div>

  @if(session('price_result'))
    @php $r = session('price_result'); @endphp
    <div class="card-nusa" style="border-left:3px solid var(--primary);">
      <h6 class="fw-600 font-heading mb-3" style="color:var(--dark);">Hasil Rekomendasi</h6>
      <div class="mb-2">
        <small class="text-muted d-block">Produk</small>
        <span class="fw-600">{{ $r['product_name'] ?? '' }}</span>
      </div>
      <div class="row g-2 mb-2">
        <div class="col-4">
          <small class="text-muted d-block">Harga Saat Ini</small>
          <span class="fw-600">Rp{{ number_format($r['current_price'] ?? 0, 0, ',', '.') }}</span>
        </div>
        @if(isset($r['cost_price']))
          <div class="col-4">
            <small class="text-muted d-block">Modal (HPP)</small>
            <span class="fw-600">Rp{{ number_format($r['cost_price'], 0, ',', '.') }}</span>
          </div>
        @endif
        <div class="col-4">
          <small class="text-muted d-block">Margin</small>
          <span class="fw-600">{{ $r['margin'] ?? 0 }}%</span>
        </div>
      </div>
      <div class="row g-2 mb-3">
        <div class="col-4">
          <small class="text-muted d-block">Harga Rekomendasi</small>
          <span class="fw-700" style="color:var(--primary); font-size:1.1rem;">Rp{{ number_format($r['recommended_price'] ?? 0, 0, ',', '.') }}</span>
        </div>
        <div class="col-4">
          <small class="text-muted d-block">Harga Min</small>
          <span>Rp{{ number_format($r['min_price'] ?? 0, 0, ',', '.') }}</span>
        </div>
        <div class="col-4">
          <small class="text-muted d-block">Harga Max</small>
          <span>Rp{{ number_format($r['max_price'] ?? 0, 0, ',', '.') }}</span>
        </div>
      </div>
      <div class="p-3 rounded" style="background:var(--primary-light);">
        <small class="text-muted d-block mb-1">Alasan</small>
        <p class="small mb-0" style="color:var(--dark);">{{ $r['reasoning'] ?? '' }}</p>
      </div>
    </div>
  @endif

  <div class="card-nusa">
    <h6 class="fw-600 font-heading mb-3" style="color:var(--dark);">Kalkulator HPP</h6>
    <p class="small text-muted mb-3">Catat estimasi modal (Harga Pokok Penjualan) produkmu untuk analisis harga yang lebih akurat.</p>
    <form method="POST" action="{{ route('user.products.update', '__product__') }}" id="hppForm">
      @csrf
      @method('PUT')
      <div class="mb-3">
        <label class="form-label fw-500 small">Produk</label>
        <select name="product_hpp" id="hppProduct" class="form-select" required>
          <option value="">-- Pilih Produk --</option>
          @foreach($products as $p)
            <option value="{{ $p->id }}" data-cost="{{ $p->cost_estimate ?? 0 }}">{{ $p->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="mb-3">
        <label class="form-label fw-500 small">Estimasi Modal (HPP)</label>
        <input type="number" name="cost_estimate" id="hppCost" class="form-control" placeholder="Rp" min="0" step="0.01">
      </div>
      <button type="submit" class="btn btn-nusa-outline w-100">
        <i class="bi bi-calculator me-1"></i> Simpan HPP
      </button>
    </form>
  </div>
@endsection

@push('scripts')
<script>
  document.getElementById('hppProduct')?.addEventListener('change', function () {
    const cost = this.options[this.selectedIndex]?.dataset?.cost || 0;
    document.getElementById('hppCost').value = cost;
  });
  document.getElementById('hppForm')?.addEventListener('submit', function (e) {
    const productId = document.getElementById('hppProduct').value;
    if (!productId) { e.preventDefault(); return; }
    this.action = this.action.replace('__product__', productId);
  });
</script>
@endpush
