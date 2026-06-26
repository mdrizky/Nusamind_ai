@extends('layouts.user')

@section('title', 'Riwayat Konten')
@section('content')
  <div class="page-header">
    <h5 class="fw-600 font-heading mb-0" style="color:var(--dark);">Riwayat Konten</h5>
    <p class="text-muted small mb-0">Konten yang pernah dibuat dengan AI Nusamind</p>
  </div>

  @if($contents->count() > 0)
    @foreach($contents as $c)
      <div class="card-nusa mb-3">
        <div class="d-flex gap-3">
          @if($c->image_path)
            <img src="{{ Storage::url($c->image_path) }}" alt="Foto produk" style="width:80px;height:80px;object-fit:cover;border-radius:8px;">
          @endif
          <div class="flex-grow-1 min-w-0">
            <div class="d-flex justify-content-between align-items-start mb-1">
              <span class="badge bg-tosca-light text-tosca small">
                {{ $c->style === 'formal' ? 'Formal' : ($c->style === 'gaul' ? 'Gaul' : 'Hard Selling') }}
              </span>
              <small class="text-muted">{{ $c->created_at->diffForHumans() }}</small>
            </div>
            @if($c->product)
              <small class="text-muted d-block mb-1">Produk: {{ $c->product->name }}</small>
            @endif
            <p class="small mb-1 text-truncate-2">{{ $c->caption_result ?? 'Tidak ada caption' }}</p>
            @if($c->hashtags_result && count($c->hashtags_result) > 0)
              <div class="small text-tosca">
                {{ implode(' ', array_map(fn($h) => '#'.$h, $c->hashtags_result)) }}
              </div>
            @endif
          </div>
        </div>
      </div>
    @endforeach
    <div class="mt-3 d-flex justify-content-center">
      {{ $contents->links() }}
    </div>
  @else
    <div class="card-nusa">
      <div class="empty-state">
        <i class="bi bi-file-earmark-text"></i>
        <p class="fw-500">Belum ada konten</p>
        <p class="small">Gunakan AI Content untuk mulai membuat caption produk.</p>
      </div>
    </div>
  @endif
@endsection
