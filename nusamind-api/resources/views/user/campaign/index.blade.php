@extends('layouts.user')

@section('title', 'Campaign - NusaCampaign')
@section('content')
  <div class="page-header">
    <h5 class="fw-600 font-heading mb-0" style="color:var(--dark);">NusaCampaign</h5>
    <p class="text-muted small mb-0">Rencanakan Promosi dengan AI</p>
  </div>

  <div class="card-nusa">
    <h6 class="fw-600 font-heading mb-3" style="color:var(--dark);">Buat Campaign Baru</h6>
    <form method="POST" action="{{ route('user.campaign.generate') }}">
      @csrf
      <div class="mb-3">
        <label class="form-label small fw-500">Tujuan Campaign</label>
        <select name="campaign_goal" class="form-select" required>
          <option value="">Pilih tujuan</option>
          <option value="Meningkatkan penjualan">Meningkatkan penjualan</option>
          <option value="Brand awareness">Brand awareness</option>
          <option value="Diskon musiman">Diskon musiman</option>
          <option value="Launch produk baru">Launch produk baru</option>
          <option value="Acara khusus">Acara khusus</option>
        </select>
      </div>
      <div class="mb-3">
        <label class="form-label small fw-500">Produk (opsional)</label>
        <select name="target_product_id" class="form-select">
          <option value="">Semua produk</option>
          @foreach($products as $p)
            <option value="{{ $p->id }}">{{ $p->name }}</option>
          @endforeach
        </select>
      </div>
      <button type="submit" class="btn btn-nusa w-100">
        <i class="bi bi-magic"></i> Generate Campaign
      </button>
    </form>
  </div>

  @if(session('plan'))
    @php $plan = session('plan'); @endphp
    <div class="card-nusa" style="border:1px solid var(--primary);">
      <h6 class="fw-600 font-heading mb-3" style="color:var(--primary);">
        <i class="bi bi-check-circle-fill me-1"></i> Hasil Campaign
      </h6>
      <div class="mb-3">
        <small class="text-muted d-block">Nama Campaign</small>
        <p class="fw-600 mb-0">{{ $plan['campaign_name'] ?? '-' }}</p>
      </div>
      <div class="mb-3">
        <small class="text-muted d-block">Caption</small>
        <p class="small mb-0">{{ $plan['caption'] ?? '-' }}</p>
      </div>
      <div class="mb-3">
        <small class="text-muted d-block">Pesan Broadcast</small>
        <p class="small mb-0">{{ $plan['broadcast_message'] ?? '-' }}</p>
      </div>
      @if(isset($plan['tips']) && count($plan['tips']) > 0)
        <div class="mb-3">
          <small class="text-muted d-block">Tips</small>
          <ul class="small mb-0">
            @foreach($plan['tips'] as $tip)
              <li>{{ $tip }}</li>
            @endforeach
          </ul>
        </div>
      @endif
      @if(isset($plan['hashtags']) && count($plan['hashtags']) > 0)
        <div>
          <small class="text-muted d-block">Hashtags</small>
          <div class="small" style="color:var(--primary);">
            {{ implode(' ', array_map(fn($h) => '#'.$h, $plan['hashtags'])) }}
          </div>
        </div>
      @endif
    </div>
  @endif

  <h6 class="fw-600 font-heading mb-3" style="color:var(--dark);">Riwayat Campaign</h6>

  @if($campaigns->count() > 0)
    <div class="card-nusa p-0">
      <div class="table-responsive">
        <table class="table table-nusa mb-0">
          <thead>
            <tr>
              <th>Nama</th>
              <th>Tujuan</th>
              <th>Tanggal</th>
              <th>Status</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            @foreach($campaigns as $c)
              <tr>
                <td class="fw-500">{{ $c->campaign_name }}</td>
                <td><span class="badge bg-tosca-light text-tosca small">{{ $c->campaign_goal }}</span></td>
                <td class="small text-muted">{{ $c->created_at ? $c->created_at->format('d M Y') : '-' }}</td>
                <td>
                  <form method="POST" action="{{ route('user.campaign.activate', $c->id) }}" class="d-inline">
                    @csrf
                    <div class="form-check form-switch d-inline-block mb-0">
                      <input type="checkbox" class="form-check-input" role="switch"
                             onchange="this.form.submit()"
                             {{ $c->is_active ? 'checked' : '' }}
                             style="cursor:pointer;">
                    </div>
                  </form>
                </td>
                <td>
                  <form method="POST" action="{{ route('user.campaign.delete', $c->id) }}" class="d-inline"
                        onsubmit="return confirm('Hapus campaign {{ $c->campaign_name }}?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                  </form>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
    <div class="mt-3 d-flex justify-content-center">
      {{ $campaigns->links() }}
    </div>
  @else
    <div class="card-nusa">
      <div class="empty-state">
        <i class="bi bi-megaphone"></i>
        <p class="fw-500">Belum ada campaign</p>
        <p class="small">Gunakan AI untuk membuat rencana campaign promosi usahamu.</p>
      </div>
    </div>
  @endif
@endsection
