@extends('layouts.admin')

@section('title', 'Kelola User')
@section('page-title', 'Kelola User')

@section('content')
<div class="card">
  <div class="card-body">
    <h5 class="card-title">Daftar User</h5>

    <form method="GET" class="row g-3 mb-3">
      <div class="col-md-4">
        <input type="text" name="search" class="form-control" placeholder="Cari nama/email..." value="{{ request('search') }}">
      </div>
      <div class="col-md-3">
        <select name="status" class="form-select">
          <option value="">Semua Status</option>
          <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
          <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Dinonaktifkan</option>
        </select>
      </div>
      <div class="col-md-2">
        <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> Filter</button>
      </div>
    </form>

    <div class="table-responsive">
      <table class="table table-hover">
        <thead>
          <tr>
            <th>Nama</th>
            <th>Email</th>
            <th>Role</th>
            <th>Usaha</th>
            <th>Status</th>
            <th>Daftar</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($users as $user)
          <tr>
            <td>{{ $user->name }}</td>
            <td>{{ $user->email }}</td>
            <td><span class="badge bg-{{ $user->role == 'superadmin' ? 'warning' : ($user->role == 'admin' ? 'info' : 'secondary') }}">{{ $user->role }}</span></td>
            <td>{{ $user->business?->business_name ?? '-' }}</td>
            <td>
              @if($user->status == 'active')
                <span class="badge bg-success">Aktif</span>
              @else
                <span class="badge bg-danger">Dinonaktifkan</span>
              @endif
            </td>
            <td>{{ $user->created_at->format('d/m/Y') }}</td>
            <td>
              @if($user->role == 'user')
                @if($user->status == 'active')
                  <form method="POST" action="{{ route('admin.users.suspend', $user->id) }}" class="d-inline" onsubmit="return confirm('Yakin mau nonaktifkan user ini?')">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-warning"><i class="bi bi-pause-circle"></i> Nonaktifkan</button>
                  </form>
                @else
                  <form method="POST" action="{{ route('admin.users.activate', $user->id) }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-success"><i class="bi bi-check-circle"></i> Aktifkan</button>
                  </form>
                @endif
              @endif
            </td>
          </tr>
          @empty
          <tr><td colspan="7" class="text-center">Belum ada user</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{ $users->links() }}
  </div>
</div>
@endsection
