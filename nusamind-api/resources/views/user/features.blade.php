@extends('layouts.user')
@section('title', 'Fitur Nusamind')
@section('content')
<div class="page-header">
  <div>
    <h5 class="section-title mb-1">Semua Fitur</h5>
    <p class="text-muted small mb-0">12 modul AI untuk bisnismu</p>
  </div>
</div>

<div class="row g-3">
  @foreach($modules as $mod)
  <div class="col-6 col-md-4 col-lg-3">
    <a href="{{ route($mod['route']) }}" class="text-decoration-none">
      <div class="card-nusa text-center h-100" style="cursor:pointer; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-4px)'" onmouseout="this.style.transform=''">
        <div class="card-icon mx-auto mb-3" style="background: {{ $mod['color'] }}15; color: {{ $mod['color'] }}; width: 56px; height: 56px; border-radius: 16px; display: flex; align-items: center; justify-content: center;">
          <i class="bi {{ $mod['icon'] }}" style="font-size: 1.6rem;"></i>
        </div>
        <h6 class="fw-semibold mb-1" style="color: var(--dark); font-size: 0.85rem;">{{ $mod['name'] }}</h6>
        <p class="small text-muted mb-0" style="font-size: 0.7rem;">{{ $mod['desc'] }}</p>
      </div>
    </a>
  </div>
  @endforeach
</div>
@endsection
