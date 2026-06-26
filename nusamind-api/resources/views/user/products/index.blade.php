@extends('layouts.user')

@section('title', 'Produk Saya')
@section('content')
  <div class="page-header">
    <h5 class="fw-600 font-heading mb-0" style="color:var(--dark);">Produk Saya</h5>
    <a href="{{ route('user.products.create') }}" class="btn btn-nusa btn-sm">
      <i class="bi bi-plus-lg"></i> Tambah
    </a>
  </div>

  @if($products->count() > 0)
    <div class="card-nusa p-0">
      <div class="table-responsive">
        <table class="table table-nusa mb-0">
          <thead>
            <tr>
              <th>Nama</th>
              <th>Harga</th>
              <th>Stok</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            @foreach($products as $p)
              <tr>
                <td class="fw-500">{{ $p->name }}</td>
                <td>Rp{{ number_format($p->price, 0, ',', '.') }}</td>
                <td>{{ $p->stock ?? '-' }}</td>
                <td>
                  <a href="{{ route('user.products.edit', $p->id) }}" class="btn btn-sm btn-nusa-outline me-1">
                    <i class="bi bi-pencil"></i>
                  </a>
                  <form method="POST" action="{{ route('user.products.destroy', $p->id) }}" class="d-inline"
                        onsubmit="return confirm('Hapus produk {{ $p->name }}?')">
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
  @else
    <div class="card-nusa">
      <div class="empty-state">
        <i class="bi bi-box"></i>
        <p class="fw-500">Belum ada produk</p>
        <p class="small">Tambahkan produk usahamu agar lebih mudah dicatat transaksinya.</p>
        <a href="{{ route('user.products.create') }}" class="btn btn-nusa btn-sm mt-2">Tambah Produk</a>
      </div>
    </div>
  @endif
@endsection
