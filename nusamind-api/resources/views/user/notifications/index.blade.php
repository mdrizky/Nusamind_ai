@extends('layouts.user')

@section('title', 'Notifikasi')
@section('content')
  <div class="page-header">
    <h5 class="fw-600 font-heading mb-0" style="color:var(--dark);">Notifikasi</h5>
  </div>

  @if(session('success'))
    <div class="alert alert-success alert-sm">{{ session('success') }}</div>
  @endif

  @if($notifications->count() > 0)
    @foreach($notifications as $n)
      <div class="card-nusa mb-2 d-flex justify-content-between align-items-start {{ $n->is_read ? '' : 'border-start border-tosca' }}" style="{{ $n->is_read ? '' : 'border-left:3px solid var(--primary);' }}">
        <div class="flex-grow-1">
          <h6 class="mb-1 fw-600 small {{ $n->is_read ? '' : 'text-dark' }}" style="{{ $n->is_read ? 'color:#6b7280;' : '' }}">{{ $n->title }}</h6>
          <p class="small mb-1" style="color:#6b7280;">{{ $n->body }}</p>
          <small class="text-muted">{{ $n->created_at->diffForHumans() }}</small>
        </div>
        @unless($n->is_read)
          <form action="{{ route('user.notifications.read', $n->id) }}" method="POST" class="ms-2">
            @csrf
            @method('PUT')
            <button class="btn btn-sm btn-tosca-light rounded-pill px-2 py-0 small">Tandai</button>
          </form>
        @endunless
      </div>
    @endforeach
    <div class="mt-3 d-flex justify-content-center">
      {{ $notifications->links() }}
    </div>
  @else
    <div class="card-nusa">
      <div class="empty-state">
        <i class="bi bi-bell"></i>
        <p class="fw-500">Belum ada notifikasi</p>
        <p class="small">Notifikasi akan muncul di sini saat ada briefing baru atau pengumuman.</p>
      </div>
    </div>
  @endif
@endsection
