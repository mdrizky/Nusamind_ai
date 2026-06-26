<aside id="sidebar" class="sidebar">
    <ul class="sidebar-nav" id="sidebar-nav">
      <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.dashboard') ? '' : 'collapsed' }}" href="{{ route('admin.dashboard') }}">
          <i class="bi bi-grid"></i>
          <span>Dashboard</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.users.*') ? '' : 'collapsed' }}" href="{{ route('admin.users.index') }}">
          <i class="bi bi-people"></i>
          <span>Kelola User</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.ai-usage.*') ? '' : 'collapsed' }}" href="{{ route('admin.ai-usage.index') }}">
          <i class="bi bi-cpu"></i>
          <span>Monitoring AI</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.content-reports.*') ? '' : 'collapsed' }}" href="{{ route('admin.content-reports.index') }}">
          <i class="bi bi-flag"></i>
          <span>Moderasi Konten</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.categories.*') ? '' : 'collapsed' }}" href="{{ route('admin.categories.index') }}">
          <i class="bi bi-tags"></i>
          <span>Kategori Usaha</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link collapsed" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
          <i class="bi bi-box-arrow-right"></i>
          <span>Keluar</span>
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
          @csrf
        </form>
      </li>
    </ul>
  </aside>
