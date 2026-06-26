<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>@yield('title', 'Beranda') - {{ config('app.name') }}</title>
  <meta content="" name="description">
  <meta content="" name="keywords">
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <link href="{{ asset('assets/vendors/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendors/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
  <style>
    :root {
      --primary: #0F9D8E;
      --primary-dark: #0c8578;
      --primary-light: #e8f5f3;
      --secondary: #F2B705;
      --secondary-light: #fef8e0;
      --dark: #1F2937;
      --gray: #6B7280;
      --light-bg: #F9FAFB;
      --border: #E5E7EB;
    }
    * { font-family: 'Inter', sans-serif; }
    h1, h2, h3, h4, h5, h6, .font-heading { font-family: 'Poppins', sans-serif; }
    body { background: var(--light-bg); min-height: 100vh; padding-bottom: 80px; }
    .top-bar {
      background: white;
      border-bottom: 1px solid var(--border);
      padding: 12px 20px;
      position: sticky;
      top: 0;
      z-index: 100;
    }
    .top-bar .brand {
      font-family: 'Poppins', sans-serif;
      font-weight: 700;
      font-size: 1.25rem;
      color: var(--primary);
      text-decoration: none;
    }
    .top-bar .user-badge {
      display: flex;
      align-items: center;
      gap: 8px;
      font-size: 0.875rem;
      color: var(--dark);
    }
    .top-bar .user-badge .avatar {
      width: 36px;
      height: 36px;
      border-radius: 50%;
      background: var(--primary-light);
      color: var(--primary);
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 600;
      font-size: 0.875rem;
    }
    .top-bar .role-badge {
      font-size: 0.7rem;
      background: var(--secondary-light);
      color: #b38400;
      padding: 2px 8px;
      border-radius: 10px;
      font-weight: 500;
    }
    .bottom-nav {
      position: fixed;
      bottom: 0;
      left: 0;
      right: 0;
      background: white;
      border-top: 1px solid var(--border);
      display: flex;
      justify-content: space-around;
      padding: 6px 0;
      z-index: 100;
    }
    .bottom-nav a {
      text-decoration: none;
      color: var(--gray);
      font-size: 0.65rem;
      text-align: center;
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 1px;
      padding: 4px 6px;
      border-radius: 8px;
    }
    .bottom-nav a i { font-size: 1.2rem; }
    .bottom-nav a.active, .bottom-nav a:hover { color: var(--primary); background: var(--primary-light); }
    .notif-badge {
      background: #ef4444; color: white; font-size: 0.6rem;
      min-width: 18px; height: 18px; border-radius: 9px;
      display: inline-flex; align-items: center; justify-content: center;
      font-weight: 700; position: absolute; top: -4px; right: -6px;
    }
    .notif-btn { position: relative; }
    .page-content { max-width: 768px; margin: 0 auto; padding: 20px; }
    .section-title {
      font-family: 'Poppins', sans-serif;
      font-weight: 600;
      font-size: 1.25rem;
      color: var(--dark);
      margin-bottom: 16px;
    }
    .card-nusa {
      background: white;
      border: none;
      border-radius: 12px;
      box-shadow: 0 1px 3px rgba(0,0,0,0.06);
      padding: 20px;
      margin-bottom: 16px;
    }
    .card-nusa .card-icon {
      width: 48px;
      height: 48px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.5rem;
    }
    .card-nusa .card-icon.tosca { background: var(--primary-light); color: var(--primary); }
    .card-nusa .card-icon.gold { background: var(--secondary-light); color: #b38400; }
    .stat-value { font-family: 'Poppins', sans-serif; font-weight: 700; font-size: 1.5rem; color: var(--dark); }
    .stat-label { font-size: 0.8rem; color: var(--gray); }
    .btn-nusa {
      background: var(--primary);
      color: white;
      border: none;
      border-radius: 10px;
      padding: 10px 24px;
      font-weight: 500;
      transition: all 0.2s;
    }
    .btn-nusa:hover { background: var(--primary-dark); color: white; }
    .btn-nusa-outline {
      background: transparent;
      color: var(--primary);
      border: 1.5px solid var(--primary);
      border-radius: 10px;
      padding: 10px 24px;
      font-weight: 500;
      transition: all 0.2s;
    }
    .btn-nusa-outline:hover { background: var(--primary-light); }
    .btn-nusa-gold {
      background: var(--secondary);
      color: #1F2937;
      border: none;
      border-radius: 10px;
      padding: 10px 24px;
      font-weight: 500;
    }
    .btn-nusa-gold:hover { background: #d9a500; color: #1F2937; }
    .table-nusa {
      font-size: 0.875rem;
    }
    .table-nusa thead th {
      border-bottom: 2px solid var(--border);
      color: var(--gray);
      font-weight: 600;
      font-size: 0.75rem;
      text-transform: uppercase;
      letter-spacing: 0.05em;
    }
    .badge-pemasukan { background: #dcfce7; color: #166534; }
    .badge-pengeluaran { background: #fee2e2; color: #991b1b; }
    .flash-message {
      position: fixed;
      top: 70px;
      left: 50%;
      transform: translateX(-50%);
      z-index: 999;
      min-width: 300px;
      max-width: 90%;
    }
    .form-control, .form-select {
      border-radius: 10px;
      border-color: var(--border);
      padding: 10px 14px;
      font-size: 0.9rem;
    }
    .form-control:focus, .form-select:focus {
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(15, 157, 142, 0.15);
    }
    .empty-state {
      text-align: center;
      padding: 48px 20px;
      color: var(--gray);
    }
    .empty-state i { font-size: 3rem; color: #d1d5db; margin-bottom: 16px; }
    .dropdown-user {
      min-width: 200px;
      border: none;
      border-radius: 12px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    }
    .dropdown-user .dropdown-item {
      padding: 10px 16px;
      font-size: 0.875rem;
    }
    .dropdown-user .dropdown-item:hover { background: var(--primary-light); color: var(--primary); }
    .dropdown-user .dropdown-item i { margin-right: 8px; width: 18px; }
    .page-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 20px;
    }
    @media (min-width: 768px) {
      body { padding-bottom: 0; }
      .bottom-nav { display: none; }
      .top-bar .desktop-nav { display: flex !important; }
    }
    @media (max-width: 767px) {
      .top-bar .desktop-nav { display: none !important; }
    }
  </style>
  @stack('styles')
</head>
<body>
  @php $unreadNotifCount = \App\Models\Notification::where('user_id', Auth::id())->where('is_read', false)->count(); @endphp

  <div class="top-bar d-flex align-items-center justify-content-between">
    <a href="{{ route('user.dashboard') }}" class="brand">
      <i class="bi bi-shield-check me-1"></i>{{ config('app.name') }}
    </a>
    <div class="desktop-nav d-flex align-items-center gap-3">
      <a href="{{ route('user.features') }}" class="text-decoration-none text-dark small {{ request()->routeIs('user.features*') ? 'fw-bold text-primary' : '' }}">Fitur</a>
      <a href="{{ route('user.content.index') }}" class="text-decoration-none text-dark small {{ request()->routeIs('user.content.*') ? 'fw-bold text-primary' : '' }}">Konten</a>
      <a href="{{ route('user.transactions') }}" class="text-decoration-none text-dark small {{ request()->routeIs('user.transactions') ? 'fw-bold text-primary' : '' }}">Transaksi</a>
      <a href="{{ route('user.reply.index') }}" class="text-decoration-none text-dark small {{ request()->routeIs('user.reply.*') ? 'fw-bold text-primary' : '' }}">Balas</a>
      <a href="{{ route('user.stock.index') }}" class="text-decoration-none text-dark small {{ request()->routeIs('user.stock.*') ? 'fw-bold text-primary' : '' }}">Stok</a>
      <a href="{{ route('user.coach.index') }}" class="text-decoration-none text-dark small {{ request()->routeIs('user.coach.*') ? 'fw-bold text-primary' : '' }}">Mentor</a>
    </div>
    <div class="d-flex align-items-center gap-2">
      <a href="{{ route('user.notifications.index') }}" class="text-decoration-none notif-btn">
        <i class="bi bi-bell fs-5" style="color:var(--gray);"></i>
        @if($unreadNotifCount > 0)
          <span class="notif-badge">{{ $unreadNotifCount > 99 ? '99+' : $unreadNotifCount }}</span>
        @endif
      </a>
      <div class="dropdown">
        <button class="btn p-0 border-0 bg-transparent" data-bs-toggle="dropdown">
          <div class="user-badge">
            <span class="d-none d-md-inline small">{{ Auth::user()->name }}</span>
            <div class="avatar">{{ substr(Auth::user()->name, 0, 1) }}</div>
          </div>
        </button>
        <ul class="dropdown-menu dropdown-menu-end dropdown-user">
          <li><span class="dropdown-item-text small text-muted">{{ Auth::user()->email }}</span></li>
          <li><hr class="dropdown-divider"></li>
          <li><a class="dropdown-item" href="{{ route('user.profile') }}"><i class="bi bi-person"></i>Profil Saya</a></li>
          @if(Auth::user()->isAdmin())
            <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}"><i class="bi bi-shield-lock"></i>Panel Admin</a></li>
          @endif
          <li><hr class="dropdown-divider"></li>
          <li>
            <form method="POST" action="{{ route('logout') }}">
              @csrf
              <button type="submit" class="dropdown-item"><i class="bi bi-box-arrow-right"></i>Keluar</button>
            </form>
          </li>
        </ul>
      </div>
    </div>
  </div>

  @if(session('success'))
    <div class="flash-message">
      <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
        <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    </div>
  @endif
  @if(session('error'))
    <div class="flash-message">
      <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
        <i class="bi bi-exclamation-triangle me-1"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    </div>
  @endif

  <div class="page-content">
    @yield('content')
  </div>

  <nav class="bottom-nav">
    <a href="{{ route('user.dashboard') }}" class="{{ request()->routeIs('user.dashboard') ? 'active' : '' }}">
      <i class="bi bi-house-door"></i>
      <span>Beranda</span>
    </a>
    <a href="{{ route('user.features') }}" class="{{ request()->routeIs('user.features*') ? 'active' : '' }}">
      <i class="bi bi-grid-3x3-gap"></i>
      <span>Fitur</span>
    </a>
    <a href="{{ route('user.transactions') }}" class="{{ request()->routeIs('user.transactions') ? 'active' : '' }}">
      <i class="bi bi-cash-stack"></i>
      <span>Transaksi</span>
    </a>
    <a href="{{ route('user.business') }}" class="{{ request()->routeIs('user.business') ? 'active' : '' }}">
      <i class="bi bi-shop"></i>
      <span>Usaha</span>
    </a>
    <a href="{{ route('user.profile') }}" class="{{ request()->routeIs('user.profile') ? 'active' : '' }}">
      <i class="bi bi-person"></i>
      <span>Profil</span>
    </a>
  </nav>

  <script src="{{ asset('assets/vendors/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
  @stack('scripts')
  <script>
    setTimeout(function() {
      document.querySelectorAll('.flash-message').forEach(function(el) {
        el.style.transition = 'opacity 0.5s';
        el.style.opacity = '0';
        setTimeout(function() { el.remove(); }, 500);
      });
    }, 4000);
  </script>
</body>
</html>
