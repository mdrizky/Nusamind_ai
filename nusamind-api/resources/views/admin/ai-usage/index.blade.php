@extends('layouts.admin')

@section('title', 'Monitoring AI')
@section('page-title', 'Monitoring AI Usage')

@section('content')
<div class="card">
  <div class="card-body">
    <h5 class="card-title">Log Pemakaian AI</h5>

    <form method="GET" class="row g-3 mb-3">
      <div class="col-md-3">
        <select name="feature" class="form-select">
          <option value="">Semua Fitur</option>
          <option value="finance" {{ request('feature') == 'finance' ? 'selected' : '' }}>Keuangan</option>
          <option value="content" {{ request('feature') == 'content' ? 'selected' : '' }}>Konten</option>
          <option value="briefing" {{ request('feature') == 'briefing' ? 'selected' : '' }}>Briefing</option>
          <option value="export" {{ request('feature') == 'export' ? 'selected' : '' }}>Ekspor</option>
        </select>
      </div>
      <div class="col-md-3">
        <select name="status" class="form-select">
          <option value="">Semua Status</option>
          <option value="success" {{ request('status') == 'success' ? 'selected' : '' }}>Sukses</option>
          <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Gagal</option>
          <option value="timeout" {{ request('status') == 'timeout' ? 'selected' : '' }}>Timeout</option>
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
            <th>User</th>
            <th>Fitur</th>
            <th>Status</th>
            <th>Token</th>
            <th>Waktu</th>
          </tr>
        </thead>
        <tbody>
          @forelse($logs as $log)
          <tr>
            <td>{{ $log->user?->name ?? '-' }}</td>
            <td><span class="badge bg-primary">{{ $log->feature }}</span></td>
            <td>
              @if($log->status == 'success')
                <span class="badge bg-success">Sukses</span>
              @elseif($log->status == 'failed')
                <span class="badge bg-danger">Gagal</span>
              @else
                <span class="badge bg-warning">Timeout</span>
              @endif
            </td>
            <td>{{ $log->tokens_used ?? '-' }}</td>
            <td>{{ $log->created_at->format('d/m/Y H:i') }}</td>
          </tr>
          @empty
          <tr><td colspan="5" class="text-center">Belum ada log pemakaian AI</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{ $logs->links() }}
  </div>
</div>
@endsection
