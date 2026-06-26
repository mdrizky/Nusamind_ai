@extends('layouts.user')

@section('title', 'Katalog - NusaCatalog')

@section('content')
  <div class="page-header">
    <div>
      <h5 class="fw-600 font-heading mb-0" style="color:var(--dark);">NusaCatalog</h5>
      <small class="text-muted">Optimasi Produk dengan AI</small>
    </div>
  </div>

  <div class="card-nusa">
    <h6 class="fw-600 font-heading mb-3" style="color:var(--dark);">Optimasi Nama & Deskripsi</h6>
    <form method="POST" action="{{ route('user.catalog.enhance') }}">
      @csrf
      <div class="mb-3">
        <label class="form-label fw-500 small">Pilih Produk</label>
        <select name="product_id" class="form-select" required>
          <option value="">-- Pilih Produk --</option>
          @foreach($products as $p)
            <option value="{{ $p->id }}">{{ $p->name }}</option>
          @endforeach
        </select>
      </div>
      <button type="submit" class="btn btn-nusa w-100">
        <i class="bi bi-magic me-1"></i> Optimasi
      </button>
    </form>
  </div>

  @if(session('enhance_result'))
    @php $r = session('enhance_result'); $pid = session('enhance_product_id'); @endphp
    <div class="card-nusa" style="border-left:3px solid var(--primary);">
      <h6 class="fw-600 font-heading mb-3" style="color:var(--dark);">Hasil Optimasi</h6>
      <div class="mb-3">
        <small class="text-muted d-block mb-1">Nama Saat Ini</small>
        <span class="small text-decoration-line-through" style="color:#6b7280;">{{ $products->find($pid)?->name ?? '-' }}</span>
      </div>
      <div class="mb-3">
        <small class="text-muted d-block mb-1">Nama Optimasi</small>
        <span class="fw-600" style="color:var(--primary);">{{ $r['optimized_name'] ?? '' }}</span>
      </div>
      <div class="mb-3">
        <small class="text-muted d-block mb-1">Deskripsi Saat Ini</small>
        <p class="small text-decoration-line-through mb-0" style="color:#6b7280;">{{ Str::limit($products->find($pid)?->description ?? '-', 200) }}</p>
      </div>
      <div class="mb-3">
        <small class="text-muted d-block mb-1">Deskripsi Optimasi</small>
        <p class="small mb-0" style="color:var(--dark);">{{ $r['optimized_description'] ?? '' }}</p>
      </div>
      @if(isset($r['keywords']) && count($r['keywords']))
        <div class="mb-3">
          <small class="text-muted d-block mb-1">Kata Kunci</small>
          <div>
            @foreach($r['keywords'] as $kw)
              <span class="badge" style="background:var(--primary-light);color:var(--primary);margin:2px;">{{ $kw }}</span>
            @endforeach
          </div>
        </div>
      @endif
      <form method="POST" action="{{ route('user.catalog.apply', $pid) }}">
        @csrf
        <button type="submit" class="btn btn-nusa w-100">
          <i class="bi bi-check-lg me-1"></i> Terapkan Optimasi
        </button>
      </form>
    </div>
  @endif

  <h6 class="fw-600 font-heading mb-3" style="color:var(--dark);">Semua Produk</h6>
  @if($products->count() > 0)
    <div class="card-nusa p-0">
      <div class="table-responsive">
        <table class="table table-nusa mb-0">
          <thead>
            <tr>
              <th>Nama</th>
              <th>Harga</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            @foreach($products as $p)
              <tr>
                <td class="fw-500">{{ $p->name }}</td>
                <td>Rp{{ number_format($p->price, 0, ',', '.') }}</td>
                <td>
                  <form method="POST" action="{{ route('user.catalog.enhance') }}" class="d-inline">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $p->id }}">
                    <button class="btn btn-sm btn-nusa-outline">
                      <i class="bi bi-magic"></i> Optimasi
                    </button>
                  </form>
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
        <p class="small">Tambahkan produk terlebih dahulu untuk menggunakan fitur optimasi.</p>
      </div>
    </div>
  @endif
@endsection
