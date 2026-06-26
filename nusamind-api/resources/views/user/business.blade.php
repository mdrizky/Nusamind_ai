@extends('layouts.user')

@section('title', 'Profil Usaha')
@section('content')
  <div class="page-header">
    <h5 class="fw-600 font-heading mb-0" style="color:var(--dark);">Profil Usaha</h5>
  </div>

  <form method="POST" action="{{ route('user.business.update') }}" enctype="multipart/form-data">
    @csrf
    @if($business) @method('PUT') @endif

    <div class="card-nusa">
      <h6 class="fw-600 mb-3" style="color:var(--dark);">Informasi Usaha</h6>

      <div class="mb-3">
        <label class="form-label fw-500">Nama Usaha <span class="text-danger">*</span></label>
        <input type="text" name="business_name" class="form-control @error('business_name') is-invalid @enderror"
               value="{{ old('business_name', $business->business_name ?? '') }}" required>
        @error('business_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
      </div>

      <div class="mb-3">
        <label class="form-label fw-500">Kategori Usaha <span class="text-danger">*</span></label>
        <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
          <option value="">-- Pilih Kategori --</option>
          @foreach($categories as $cat)
            <option value="{{ $cat->id }}" {{ old('category_id', $business->category_id ?? '') == $cat->id ? 'selected' : '' }}>
              {{ $cat->name }}
            </option>
          @endforeach
        </select>
        @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
      </div>

      <div class="mb-3">
        <label class="form-label fw-500">Kota <span class="text-danger">*</span></label>
        <input type="text" name="city" class="form-control @error('city') is-invalid @enderror"
               value="{{ old('city', $business->city ?? '') }}" required>
        @error('city') <div class="invalid-feedback">{{ $message }}</div> @enderror
      </div>

      <div class="mb-3">
        <label class="form-label fw-500">Deskripsi Usaha</label>
        <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description', $business->description ?? '') }}</textarea>
        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
      </div>

      <div class="mb-3">
        <label class="form-label fw-500">Logo Usaha</label>
        <input type="file" name="logo" class="form-control @error('logo') is-invalid @enderror" accept="image/*">
        @error('logo') <div class="invalid-feedback">{{ $message }}</div> @enderror
        @if($business && $business->logo_path)
          <div class="mt-2">
            <img src="{{ asset('storage/' . $business->logo_path) }}" alt="Logo" style="max-height:80px;border-radius:8px;">
          </div>
        @endif
      </div>
    </div>

    <div class="d-grid gap-2">
      <button type="submit" class="btn btn-nusa">
        {{ $business ? 'Simpan Perubahan' : 'Buat Profil Usaha' }}
      </button>
    </div>
  </form>
@endsection
