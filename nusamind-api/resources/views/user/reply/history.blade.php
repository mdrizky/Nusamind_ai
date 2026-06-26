@extends('layouts.user')

@section('title', 'Tersimpan - NusaReply')
@section('content')
  <div class="page-header">
    <h5 class="fw-600 font-heading mb-0" style="color:var(--dark);">Balasan Tersimpan</h5>
    <p class="text-muted small mb-0">Balasan yang kamu bookmark untuk digunakan lagi</p>
  </div>

  @if($replies->count() > 0)
    @foreach($replies as $r)
      <div class="card-nusa mb-3">
        <div class="d-flex justify-content-between align-items-start mb-2">
          <small class="text-muted">{{ $r->created_at->diffForHumans() }}</small>
          <form method="POST" action="{{ route('user.reply.save', $r->id) }}" class="d-inline">
            @csrf
            <button class="btn btn-sm btn-nusa-gold rounded-pill px-3">
              <i class="bi bi-bookmark-fill"></i>
            </button>
          </form>
        </div>
        <p class="small mb-2 text-muted"><strong>Pesan:</strong> {{ Str::limit($r->customer_message, 120) }}</p>
        <p class="small mb-0" style="color:#374151;white-space:pre-wrap;">{{ $r->generated_reply }}</p>
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
        <i class="bi bi-bookmark"></i>
        <p class="fw-500">Belum ada balasan tersimpan</p>
        <p class="small">Simpan balasan dengan mengklik ikon bookmark pada riwayat balasan.</p>
      </div>
    </div>
  @endif
@endsection
