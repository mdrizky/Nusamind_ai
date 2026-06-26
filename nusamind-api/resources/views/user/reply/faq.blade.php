@extends('layouts.user')

@section('title', 'FAQ - NusaReply')
@section('content')
  <div class="page-header">
    <div>
      <h5 class="fw-600 font-heading mb-0" style="color:var(--dark);">FAQ</h5>
      <p class="text-muted small mb-0">Pertanyaan yang sering diajukan</p>
    </div>
    <button class="btn btn-nusa btn-sm" data-bs-toggle="modal" data-bs-target="#faqModal">
      <i class="bi bi-plus-lg"></i> Tambah FAQ
    </button>
  </div>

  @if($faqs->count() > 0)
    @foreach($faqs as $faq)
      <div class="card-nusa mb-3">
        <div class="d-flex justify-content-between align-items-start">
          <div class="flex-grow-1">
            <h6 class="fw-600 mb-1" style="color:var(--dark);">{{ $faq->question }}</h6>
            <p class="small mb-1" style="color:#374151;">{{ $faq->answer }}</p>
            @if($faq->category)
              <span class="badge bg-tosca-light text-tosca small">{{ $faq->category }}</span>
            @endif
          </div>
          <form method="POST" action="{{ route('user.reply.faq.destroy', $faq->id) }}" class="ms-2"
                onsubmit="return confirm('Hapus FAQ ini?')">
            @csrf @method('DELETE')
            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
          </form>
        </div>
      </div>
    @endforeach
    <div class="mt-3 d-flex justify-content-center">
      {{ $faqs->links() }}
    </div>
  @else
    <div class="card-nusa">
      <div class="empty-state">
        <i class="bi bi-question-circle"></i>
        <p class="fw-500">Belum ada FAQ</p>
        <p class="small">Tambahkan pertanyaan yang sering diajukan pelanggan.</p>
      </div>
    </div>
  @endif

  <div class="modal fade" id="faqModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content" style="border-radius:16px;border:none;">
        <div class="modal-header border-0">
          <h6 class="fw-600 font-heading mb-0" style="color:var(--dark);">Tambah FAQ</h6>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form method="POST" action="{{ route('user.reply.faq.store') }}">
          @csrf
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label small fw-500">Pertanyaan</label>
              <input type="text" name="question" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label small fw-500">Jawaban</label>
              <textarea name="answer" class="form-control" rows="4" required></textarea>
            </div>
            <div class="mb-3">
              <label class="form-label small fw-500">Kategori <small class="text-muted">(opsional)</small></label>
              <input type="text" name="category" class="form-control" maxlength="50" placeholder="Misal: Pengiriman, Pembayaran">
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
