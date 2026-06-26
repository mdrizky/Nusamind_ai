@extends('layouts.user')

@section('title', 'Mentor - NusaCoach')

@push('styles')
<style>
  .chat-container {
    max-height: 60vh;
    overflow-y: auto;
    padding: 16px;
    display: flex;
    flex-direction: column;
    gap: 12px;
  }
  .chat-container::-webkit-scrollbar {
    width: 4px;
  }
  .chat-container::-webkit-scrollbar-thumb {
    background: #d1d5db;
    border-radius: 2px;
  }
  .msg-user {
    align-self: flex-end;
    background: #0F9D8E;
    color: white;
    border-radius: 18px 18px 4px 18px;
    padding: 10px 16px;
    max-width: 80%;
    word-wrap: break-word;
    font-size: 0.9rem;
  }
  .msg-assistant {
    align-self: flex-start;
    background: #f3f4f6;
    color: #1F2937;
    border-radius: 18px 18px 18px 4px;
    padding: 10px 16px;
    max-width: 80%;
    word-wrap: break-word;
    font-size: 0.9rem;
  }
  .msg-assistant p {
    margin-bottom: 4px;
  }
  .msg-assistant p:last-child {
    margin-bottom: 0;
  }
  .chat-input-area {
    position: sticky;
    bottom: 0;
    background: white;
    padding: 12px 0;
    border-top: 1px solid var(--border);
  }
  .typing-indicator {
    align-self: flex-start;
    background: #f3f4f6;
    border-radius: 18px 18px 18px 4px;
    padding: 12px 20px;
    display: flex;
    gap: 4px;
  }
  .typing-indicator span {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #9ca3af;
    animation: typing 1.4s infinite ease-in-out;
  }
  .typing-indicator span:nth-child(2) { animation-delay: 0.2s; }
  .typing-indicator span:nth-child(3) { animation-delay: 0.4s; }
  @keyframes typing {
    0%, 60%, 100% { opacity: 0.3; transform: translateY(0); }
    30% { opacity: 1; transform: translateY(-4px); }
  }
  .welcome-msg {
    text-align: center;
    padding: 32px 16px;
    color: var(--gray);
  }
  .welcome-msg i {
    font-size: 3rem;
    color: var(--primary);
    margin-bottom: 12px;
    display: block;
  }
</style>
@endpush

@section('content')
  <div class="page-header">
    <div>
      <h4 class="fw-700 font-heading mb-0" style="color:var(--dark);">NusaCoach</h4>
      <small class="text-muted">Tanya Mentor Bisnis AI</small>
    </div>
    <form method="POST" action="{{ route('user.coach.clear') }}" class="d-inline">
      @csrf
      <button type="submit" class="btn btn-nusa-outline btn-sm">
        <i class="bi bi-trash me-1"></i>Hapus Percakapan
      </button>
    </form>
  </div>

  <div class="card-nusa mb-0 p-0" style="display:flex;flex-direction:column;">
    <div class="chat-container" id="chatContainer">
      @if(count($messages) > 0)
        @foreach($messages as $msg)
          <div class="msg-{{ $msg['role'] }}">
            {!! nl2br(e($msg['content'])) !!}
          </div>
        @endforeach
      @else
        <div class="welcome-msg">
          <i class="bi bi-robot"></i>
          <h6 class="fw-600 font-heading" style="color:var(--dark);">Halo! Aku Coach Nusamind</h6>
          <p class="small mb-0">Tanya apa aja tentang bisnismu ya!</p>
        </div>
      @endif
    </div>

    <div class="chat-input-area">
      <form method="POST" action="{{ route('user.coach.chat') }}">
        @csrf
        <div class="input-group">
          <input type="text" name="message" class="form-control" placeholder="Tanya sesuatu..." maxlength="1000" required autocomplete="off">
          <button type="submit" class="btn btn-nusa">
            <i class="bi bi-send"></i>
          </button>
        </div>
        @error('message')
          <small class="text-danger mt-1 d-block">{{ $message }}</small>
        @enderror
      </form>
    </div>
  </div>
@endsection

@push('scripts')
<script>
  var container = document.getElementById('chatContainer');
  if (container) {
    container.scrollTop = container.scrollHeight;
  }
</script>
@endpush
