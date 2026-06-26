@extends('layouts.user')

@section('title', 'Global - NusaGlobal')

@section('content')
  <div class="page-header">
    <div>
      <h5 class="fw-600 font-heading mb-0" style="color:var(--dark);">NusaGlobal</h5>
      <small class="text-muted">Ekspor ke Pasar Global</small>
    </div>
  </div>

  <div class="card-nusa">
    <h6 class="fw-600 font-heading mb-3" style="color:var(--dark);">Terjemahkan Produk</h6>
    <form method="POST" action="{{ route('user.global.translate') }}">
      @csrf
      <div class="mb-3">
        <label class="form-label fw-500 small">Produk</label>
        <select name="product_id" class="form-select" required>
          <option value="">-- Pilih Produk --</option>
          @foreach($products as $p)
            <option value="{{ $p->id }}">{{ $p->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="mb-3">
        <label class="form-label fw-500 small">Bahasa Target</label>
        <select name="target_language" class="form-select" required>
          <option value="english">English</option>
          <option value="mandarin">Mandarin (中文)</option>
        </select>
      </div>
      <button type="submit" class="btn btn-nusa w-100">
        <i class="bi bi-translate me-1"></i> Translate
      </button>
    </form>
  </div>

  @if(session('translation'))
    @php $t = session('translation'); @endphp
    <div class="card-nusa" style="border-left:3px solid var(--primary);">
      <h6 class="fw-600 font-heading mb-3" style="color:var(--dark);">Hasil Terjemahan</h6>
      <div class="mb-3">
        <small class="text-muted d-block mb-1">Nama (Terjemahan)</small>
        <span class="fw-600" style="color:var(--primary);">{{ $t['translated_name'] ?? '' }}</span>
      </div>
      <div class="mb-3">
        <small class="text-muted d-block mb-1">Deskripsi (Terjemahan)</small>
        <p class="small mb-0" style="color:var(--dark);">{{ $t['translated_description'] ?? '' }}</p>
      </div>
      <div class="mb-3 p-3 rounded" style="background:var(--primary-light);">
        <small class="text-muted d-block mb-1">Export Pitch</small>
        <p class="small mb-0" style="color:var(--dark);">{{ $t['export_pitch'] ?? '' }}</p>
      </div>
      @if(isset($t['target_market_hints']) && count($t['target_market_hints']))
        <div>
          <small class="text-muted d-block mb-1">Target Market</small>
          <ul class="small mb-0" style="color:var(--dark);">
            @foreach($t['target_market_hints'] as $hint)
              <li>{{ $hint }}</li>
            @endforeach
          </ul>
        </div>
      @endif
    </div>
  @endif
@endsection
