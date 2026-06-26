@extends('layouts.user')

@section('title', $product ? 'Edit Produk' : 'Tambah Produk')
@section('content')
  <div class="page-header">
    <h5 class="fw-600 font-heading mb-0" style="color:var(--dark);">{{ $product ? 'Edit Produk' : 'Tambah Produk' }}</h5>
    <a href="{{ route('user.products.index') }}" class="btn btn-nusa-outline btn-sm">← Kembali</a>
  </div>

  <form method="POST" action="{{ $product ? route('user.products.update', $product->id) : route('user.products.store') }}">
    @csrf
    @if($product) @method('PUT') @endif

    <div class="card-nusa">
      <div class="mb-3">
        <label class="form-label fw-500">Nama Produk <span class="text-danger">*</span></label>
        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
               value="{{ old('name', $product->name ?? '') }}" required>
        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
      </div>

      <div class="mb-3">
        <label class="form-label fw-500">Harga (Rp) <span class="text-danger">*</span></label>
        <input type="number" name="price" class="form-control @error('price') is-invalid @enderror"
               value="{{ old('price', $product->price ?? '') }}" required min="0">
        @error('price') <div class="invalid-feedback">{{ $message }}</div> @enderror
      </div>

      <div class="mb-3">
        <label class="form-label fw-500">Stok</label>
        <input type="number" name="stock" class="form-control @error('stock') is-invalid @enderror"
               value="{{ old('stock', $product->stock ?? '') }}" min="0">
        @error('stock') <div class="invalid-feedback">{{ $message }}</div> @enderror
      </div>

      <div class="mb-3">
        <label class="form-label fw-500">Deskripsi</label>
        <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description', $product->description ?? '') }}</textarea>
        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
      </div>
    </div>

    <div class="d-grid gap-2">
      <button type="submit" class="btn btn-nusa">{{ $product ? 'Simpan' : 'Tambah Produk' }}</button>
    </div>
  </form>
@endsection
