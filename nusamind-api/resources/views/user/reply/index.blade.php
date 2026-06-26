@extends('layouts.user')

@section('title', 'Balas Pelanggan - NusaReply')
@section('content')
  <div class="page-header">
    <h5 class="fw-600 font-heading mb-0" style="color:var(--dark);">NusaReply</h5>
    <p class="text-muted small mb-0">Balas Chat Pelanggan dengan AI</p>
  </div>

  <div class="card-nusa">
    <h6 class="fw-600 font-heading mb-3" style="color:var(--dark);">Generate Balasan</h6>
    <form method="POST" action="{{ route('user.reply.generate') }}">
      @csrf
      <div class="mb-3">
        <label class="form-label small fw-500">Pesan Pelanggan</label>
        <textarea name="customer_message" class="form-control" rows="4" placeholder="Tulis pesan pelanggan di sini..." required>{{ old('customer_message') }}</textarea>
      </div>
      <div class="row g-3 mb-3">
        <div class="col-6">
          <label class="form-label small fw-500">Intent</label>
          <select name="intent" class="form-select">
            <option value="">Pilih intent</option>
            <option value="pertanyaan" {{ old('intent') === 'pertanyaan' ? 'selected' : '' }}>Pertanyaan</option>
            <option value="keluhan" {{ old('intent') === 'keluhan' ? 'selected' : '' }}>Keluhan</option>
            <option value="pemesanan" {{ old('intent') === 'pemesanan' ? 'selected' : '' }}>Pemesanan</option>
            <option value="lainnya" {{ old('intent') === 'lainnya' ? 'selected' : '' }}>Lainnya</option>
          </select>
        </div>
        <div class="col-6">
          <label class="form-label small fw-500">Tone</label>
          <select name="tone" class="form-select">
            <option value="">Pilih tone</option>
            <option value="ramah" {{ old('tone') === 'ramah' ? 'selected' : '' }}>Ramah</option>
            <option value="profesional" {{ old('tone') === 'profesional' ? 'selected' : '' }}>Profesional</option>
            <option value="santai" {{ old('tone') === 'santai' ? 'selected' : '' }}>Santai</option>
          </select>
        </div>
      </div>
      <button type="submit" class="btn btn-nusa w-100">Generate Balasan</button>
    </form>
  </div>

  @if(session('reply'))
    <div class="card-nusa" style="background:#f0fdfa;border:1px solid #b8e5e0;">
      <h6 class="fw-600 font-heading mb-2" style="color:var(--dark);">Hasil Balasan</h6>
      <p class="mb-0" style="color:#374151;white-space:pre-wrap;">{{ session('reply') }}</p>
    </div>
  @endif

  <h6 class="fw-600 font-heading mb-3 mt-4" style="color:var(--dark);">Riwayat Balasan</h6>
  @if($replies->count() > 0)
    @foreach($replies as $r)
      <div class="card-nusa mb-3">
        <div class="d-flex justify-content-between align-items-start mb-2">
          <small class="text-muted">{{ $r->created_at->diffForHumans() }}</small>
          <form method="POST" action="{{ route('user.reply.save', $r->id) }}" class="d-inline">
            @csrf
            <button class="btn btn-sm {{ $r->is_saved ? 'btn-nusa-gold' : 'btn-nusa-outline' }} rounded-pill px-3">
              <i class="bi {{ $r->is_saved ? 'bi-bookmark-fill' : 'bi-bookmark' }}"></i>
            </button>
          </form>
        </div>
        <p class="small mb-2 text-muted"><strong>Pesan:</strong> {{ Str::limit($r->customer_message, 120) }}</p>
        <p class="small mb-0" style="color:#374151;white-space:pre-wrap;">{{ Str::limit($r->generated_reply, 200) }}</p>
        @if($r->intent)
          <span class="badge bg-tosca-light text-tosca small mt-2">{{ $r->intent }}</span>
        @endif
      </div>
    @endforeach
    <div class="mt-3 d-flex justify-content-center">
      {{ $replies->links() }}
    </div>
  @else
    <div class="card-nusa">
      <div class="empty-state">
        <i class="bi bi-chat-dots"></i>
        <p class="fw-500">Belum ada balasan</p>
        <p class="small">Generate balasan chat pelanggan dengan AI di atas.</p>
      </div>
    </div>
  @endif
@endsection
