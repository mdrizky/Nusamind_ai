@extends('layouts.admin')

@section('title', 'Moderasi Konten')
@section('page-title', 'Moderasi Konten')

@section('content')
<div class="card">
  <div class="card-body">
    <h5 class="card-title">Laporan Konten</h5>

    <form method="GET" class="row g-3 mb-3">
      <div class="col-md-3">
        <select name="status" class="form-select">
          <option value="">Semua Status</option>
          <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu</option>
          <option value="reviewed" {{ request('status') == 'reviewed' ? 'selected' : '' }}>Sudah Direview</option>
          <option value="removed" {{ request('status') == 'removed' ? 'selected' : '' }}>Dihapus</option>
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
            <th>Konten</th>
            <th>Pelapor</th>
            <th>Alasan</th>
            <th>Status</th>
            <th>Tanggal</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($reports as $report)
          <tr>
            <td>
              <small>{{ Str::limit($report->contentGeneration?->caption_result ?? '-', 50) }}</small>
            </td>
            <td>{{ $report->reporter?->name ?? '-' }}</td>
            <td><small>{{ Str::limit($report->reason, 50) }}</small></td>
            <td>
              @if($report->status == 'pending')
                <span class="badge bg-warning">Menunggu</span>
              @elseif($report->status == 'reviewed')
                <span class="badge bg-info">Direview</span>
              @else
                <span class="badge bg-danger">Dihapus</span>
              @endif
            </td>
            <td>{{ $report->created_at->format('d/m/Y') }}</td>
            <td>
              @if($report->status == 'pending')
              <form method="POST" action="{{ route('admin.content-reports.resolve', $report->id) }}" class="d-inline">
                @csrf
                <input type="hidden" name="status" value="reviewed">
                <button type="submit" class="btn btn-sm btn-info"><i class="bi bi-check"></i> Tandai</button>
              </form>
              <form method="POST" action="{{ route('admin.content-reports.resolve', $report->id) }}" class="d-inline" onsubmit="return confirm('Yakin mau hapus konten ini?')">
                @csrf
                <input type="hidden" name="status" value="removed">
                <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i> Hapus</button>
              </form>
              @endif
            </td>
          </tr>
          @empty
          <tr><td colspan="6" class="text-center">Belum ada laporan konten</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{ $reports->links() }}
  </div>
</div>
@endsection
