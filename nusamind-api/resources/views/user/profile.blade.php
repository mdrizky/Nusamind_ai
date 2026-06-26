@extends('layouts.user')

@section('title', 'Profil Saya')
@section('content')
  <div class="page-header">
    <h5 class="fw-600 font-heading mb-0" style="color:var(--dark);">Profil Saya</h5>
  </div>

  <div class="card-nusa">
    <div class="d-flex align-items-center gap-3 mb-4">
      <div style="width:64px;height:64px;border-radius:50%;background:var(--primary-light);color:var(--primary);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:1.5rem;">
        {{ substr(Auth::user()->name, 0, 1) }}
      </div>
      <div>
        <h5 class="mb-1 fw-600">{{ Auth::user()->name }}</h5>
        <span class="badge" style="background:var(--primary-light);color:var(--primary);">{{ ucfirst(Auth::user()->role) }}</span>
        <span class="badge {{ Auth::user()->status === 'active' ? 'badge-pemasukan' : 'badge-pengeluaran' }}">{{ Auth::user()->status }}</span>
      </div>
    </div>

    <table class="table table-nusa mb-0">
      <tr>
        <td class="text-muted">Email</td>
        <td class="fw-500">{{ Auth::user()->email }}</td>
      </tr>
      <tr>
        <td class="text-muted">Role</td>
        <td class="fw-500">{{ ucfirst(Auth::user()->role) }}</td>
      </tr>
      <tr>
        <td class="text-muted">Status</td>
        <td class="fw-500">{{ ucfirst(Auth::user()->status) }}</td>
      </tr>
      <tr>
        <td class="text-muted">Bergabung</td>
        <td class="fw-500">{{ Auth::user()->created_at->format('d M Y') }}</td>
      </tr>
    </table>
  </div>

  <div class="card-nusa">
    <h6 class="fw-600 mb-3" style="color:var(--dark);">Ubah Password</h6>
    <form method="POST" action="{{ route('user.profile.update-password') }}">
      @csrf
      <div class="mb-3">
        <label class="form-label fw-500">Password Baru</label>
        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required minlength="8">
        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
      </div>
      <div class="mb-3">
        <label class="form-label fw-500">Konfirmasi Password</label>
        <input type="password" name="password_confirmation" class="form-control" required>
      </div>
      <button type="submit" class="btn btn-nusa">Simpan Password</button>
    </form>
  </div>
@endsection
