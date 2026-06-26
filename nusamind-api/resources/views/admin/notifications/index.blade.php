@extends('layouts.admin')

@section('title', 'Broadcast Notifikasi')
@section('page-title', 'Broadcast Notifikasi')

@section('content')
<div class="card">
  <div class="card-body">
    <h5 class="card-title">Kirim Notifikasi ke Semua User</h5>

    <form method="POST" action="{{ route('admin.notifications.broadcast') }}">
      @csrf
      <div class="mb-3">
        <label class="form-label">Judul Notifikasi</label>
        <input type="text" name="title" class="form-control" required maxlength="150" placeholder="Contoh: Fitur baru sudah hadir!">
      </div>
      <div class="mb-3">
        <label class="form-label">Isi Notifikasi</label>
        <textarea name="body" class="form-control" rows="4" required placeholder="Tulis pesan untuk semua user..."></textarea>
      </div>
      <button type="submit" class="btn btn-primary"><i class="bi bi-send"></i> Kirim ke Semua User Aktif</button>
    </form>
  </div>
</div>
@endsection
