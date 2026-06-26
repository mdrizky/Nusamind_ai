@extends('layouts.admin')

@section('title', 'Kategori Usaha')
@section('page-title', 'Kategori Usaha')

@section('content')
<div class="row">
  <div class="col-md-4">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">Tambah Kategori</h5>
        <form method="POST" action="{{ route('admin.categories.store') }}">
          @csrf
          <div class="mb-3">
            <label class="form-label">Nama Kategori</label>
            <input type="text" name="name" class="form-control" required maxlength="100">
          </div>
          <div class="mb-3">
            <label class="form-label">Ikon (opsional)</label>
            <input type="text" name="icon" class="form-control" maxlength="100" placeholder="contoh: utensils">
          </div>
          <button type="submit" class="btn btn-primary">Simpan</button>
        </form>
      </div>
    </div>
  </div>

  <div class="col-md-8">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">Daftar Kategori</h5>
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Nama</th>
                <th>Ikon</th>
                <th>Jumlah Usaha</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              @forelse($categories as $category)
              <tr>
                <td>{{ $category->name }}</td>
                <td><i class="bi bi-{{ $category->icon ?? 'tag' }}"></i> {{ $category->icon ?? '-' }}</td>
                <td>{{ $category->businesses->count() }}</td>
                <td>
                  <form method="POST" action="{{ route('admin.categories.destroy', $category->id) }}" class="d-inline" onsubmit="return confirm('Yakin mau hapus?')">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                  </form>
                </td>
              </tr>
              @empty
              <tr><td colspan="4" class="text-center">Belum ada kategori</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
