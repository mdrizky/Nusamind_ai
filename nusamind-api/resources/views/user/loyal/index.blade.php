@extends('layouts.user')

@section('title', 'Pelanggan - NusaLoyal')
@section('content')
  <div class="page-header">
    <h5 class="fw-600 font-heading mb-0" style="color:var(--dark);">NusaLoyal</h5>
    <p class="text-muted small mb-0">Kelola Pelanggan & Follow-up dengan AI</p>
  </div>

  <div class="row g-3 mb-4">
    <div class="col-4">
      <div class="card-nusa text-center">
        <div class="stat-value" style="font-size:1.25rem;">{{ $vipCount }}</div>
        <div class="stat-label">VIP</div>
      </div>
    </div>
    <div class="col-4">
      <div class="card-nusa text-center">
        <div class="stat-value" style="font-size:1.25rem;">{{ $regularCount }}</div>
        <div class="stat-label">Regular</div>
      </div>
    </div>
    <div class="col-4">
      <div class="card-nusa text-center">
        <div class="stat-value" style="font-size:1.25rem;">{{ $newCount }}</div>
        <div class="stat-label">Baru</div>
      </div>
    </div>
  </div>

  <div class="row g-3">
    <div class="col-lg-7">
      <div class="d-flex align-items-center justify-content-between mb-2">
        <h6 class="fw-600 font-heading mb-0" style="color:var(--dark);">Daftar Pelanggan</h6>
        <button class="btn btn-nusa btn-sm" data-bs-toggle="modal" data-bs-target="#addCustomerModal">
          <i class="bi bi-plus-lg"></i> Tambah
        </button>
      </div>

      @if($customers->count() > 0)
        <div class="card-nusa p-0">
          <div class="table-responsive">
            <table class="table table-nusa mb-0">
              <thead>
                <tr>
                  <th>Nama</th>
                  <th>Telepon</th>
                  <th>Segment</th>
                  <th>Pesanan</th>
                  <th>Total</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                @foreach($customers as $c)
                  <tr>
                    <td class="fw-500">{{ $c->name }}</td>
                    <td class="small">{{ $c->phone ?? '-' }}</td>
                    <td>
                      @php
                        $segmentColors = ['vip' => 'badge-pemasukan', 'regular' => 'bg-secondary text-white', 'new' => 'bg-info text-white'];
                        $segmentLabels = ['vip' => 'VIP', 'regular' => 'Regular', 'new' => 'Baru'];
                        $color = $segmentColors[$c->segment] ?? 'bg-light text-dark';
                        $label = $segmentLabels[$c->segment] ?? ucfirst($c->segment);
                      @endphp
                      <span class="badge {{ $color }} small">{{ $label }}</span>
                    </td>
                    <td>{{ $c->total_orders ?? 0 }}</td>
                    <td class="small">Rp{{ number_format($c->total_spent ?? 0, 0, ',', '.') }}</td>
                    <td>
                      <button class="btn btn-sm btn-nusa-outline me-1" data-bs-toggle="modal"
                              data-bs-target="#editCustomerModal"
                              data-id="{{ $c->id }}"
                              data-name="{{ $c->name }}"
                              data-phone="{{ $c->phone }}"
                              data-address="{{ $c->address }}"
                              data-notes="{{ $c->notes }}">
                        <i class="bi bi-pencil"></i>
                      </button>
                      <form method="POST" action="{{ route('user.loyal.customer.destroy', $c->id) }}" class="d-inline"
                            onsubmit="return confirm('Hapus pelanggan {{ $c->name }}?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                      </form>
                      <form method="POST" action="{{ route('user.loyal.follow-up') }}" class="d-inline">
                        @csrf
                        <input type="hidden" name="customer_id" value="{{ $c->id }}">
                        <button class="btn btn-sm btn-nusa-gold" title="Generate Follow-up">
                          <i class="bi bi-send"></i>
                        </button>
                      </form>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
        <div class="mt-3 d-flex justify-content-center">
          {{ $customers->links() }}
        </div>
      @else
        <div class="card-nusa">
          <div class="empty-state">
            <i class="bi bi-people"></i>
            <p class="fw-500">Belum ada pelanggan</p>
            <p class="small">Tambahkan pelanggan untuk mulai kelola hubungan dengan AI.</p>
          </div>
        </div>
      @endif
    </div>

    <div class="col-lg-5">
      <div class="card-nusa">
        <h6 class="fw-600 font-heading mb-3" style="color:var(--dark);">Generate Follow-up</h6>
        <form method="POST" action="{{ route('user.loyal.follow-up') }}">
          @csrf
          <div class="mb-3">
            <label class="form-label small fw-500">Pilih Pelanggan</label>
            <select name="customer_id" class="form-select" required>
              <option value="">Pilih pelanggan</option>
              @foreach($customers as $c)
                <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->segment ?? 'new' }})</option>
              @endforeach
            </select>
          </div>
          <button type="submit" class="btn btn-nusa w-100">
            <i class="bi bi-magic"></i> Generate Follow-up
          </button>
        </form>
      </div>

      @if(session('follow_up'))
        @php $fu = session('follow_up'); @endphp
        <div class="card-nusa" style="border:1px solid var(--primary);">
          <h6 class="fw-600 font-heading mb-3" style="color:var(--primary);">
            <i class="bi bi-check-circle-fill me-1"></i> Hasil Follow-up
          </h6>
          @if(isset($fu['subject']))
            <div class="mb-3">
              <small class="text-muted d-block">Subjek</small>
              <p class="fw-600 mb-0">{{ $fu['subject'] }}</p>
            </div>
          @endif
          @if(isset($fu['follow_up_message']))
            <div class="mb-3">
              <small class="text-muted d-block">Pesan</small>
              <p class="small mb-0">{{ $fu['follow_up_message'] }}</p>
            </div>
          @endif
          @if(isset($fu['segment_note']))
            <div class="mb-3">
              <small class="text-muted d-block">Catatan Segment</small>
              <p class="small mb-0">{{ $fu['segment_note'] }}</p>
            </div>
          @endif
          @if(isset($fu['next_action']))
            <div>
              <small class="text-muted d-block">Langkah Selanjutnya</small>
              <p class="small mb-0">{{ $fu['next_action'] }}</p>
            </div>
          @endif
        </div>
      @endif
    </div>
  </div>

  {{-- Modal Tambah Pelanggan --}}
  <div class="modal fade" id="addCustomerModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content" style="border-radius:12px;">
        <div class="modal-header border-0">
          <h6 class="fw-600 font-heading mb-0" style="color:var(--dark);">Tambah Pelanggan</h6>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form method="POST" action="{{ route('user.loyal.customer.store') }}">
          @csrf
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label small fw-500">Nama <span class="text-danger">*</span></label>
              <input type="text" name="name" class="form-control" maxlength="100" required>
            </div>
            <div class="mb-3">
              <label class="form-label small fw-500">Telepon</label>
              <input type="text" name="phone" class="form-control">
            </div>
            <div class="mb-3">
              <label class="form-label small fw-500">Alamat</label>
              <textarea name="address" class="form-control" rows="2"></textarea>
            </div>
            <div class="mb-3">
              <label class="form-label small fw-500">Catatan</label>
              <textarea name="notes" class="form-control" rows="2"></textarea>
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

  {{-- Modal Edit Pelanggan --}}
  <div class="modal fade" id="editCustomerModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content" style="border-radius:12px;">
        <div class="modal-header border-0">
          <h6 class="fw-600 font-heading mb-0" style="color:var(--dark);">Edit Pelanggan</h6>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form method="POST" action="">
          @csrf @method('PUT')
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label small fw-500">Nama <span class="text-danger">*</span></label>
              <input type="text" name="name" class="form-control" maxlength="100" required>
            </div>
            <div class="mb-3">
              <label class="form-label small fw-500">Telepon</label>
              <input type="text" name="phone" class="form-control">
            </div>
            <div class="mb-3">
              <label class="form-label small fw-500">Alamat</label>
              <textarea name="address" class="form-control" rows="2"></textarea>
            </div>
            <div class="mb-3">
              <label class="form-label small fw-500">Catatan</label>
              <textarea name="notes" class="form-control" rows="2"></textarea>
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
  document.addEventListener('DOMContentLoaded', function() {
    var editModal = document.getElementById('editCustomerModal');
    if (editModal) {
      editModal.addEventListener('show.bs.modal', function(event) {
        var btn = event.relatedTarget;
        var id = btn.getAttribute('data-id');
        var name = btn.getAttribute('data-name');
        var phone = btn.getAttribute('data-phone');
        var address = btn.getAttribute('data-address');
        var notes = btn.getAttribute('data-notes');

        var form = editModal.querySelector('form');
        form.action = '{{ route('user.loyal.customer.update', '') }}/' + id;
        form.querySelector('input[name="name"]').value = name || '';
        form.querySelector('input[name="phone"]').value = phone || '';
        form.querySelector('textarea[name="address"]').value = address || '';
        form.querySelector('textarea[name="notes"]').value = notes || '';
      });
    }
  });
</script>
@endpush
