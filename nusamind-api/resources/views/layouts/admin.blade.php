<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>@yield('title', 'Dashboard') - {{ config('app.name') }}</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

  <link href="{{ asset('assets/vendors/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendors/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendors/boxicons/css/boxicons.min.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendors/quill/quill.snow.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendors/quill/quill.bubble.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendors/remixicon/remixicon.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendors/simple-datatables/style.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">

  <style>
    :root {
      --nusamind-primary: #0F9D8E;
      --nusamind-secondary: #F2B705;
      --nusamind-dark: #1F2937;
    }
    .navbar, .sidebar, .btn-primary, .bg-primary {
      background-color: var(--nusamind-primary) !important;
    }
    .btn-primary {
      border-color: var(--nusamind-primary);
    }
    .btn-primary:hover {
      background-color: #0c8578 !important;
      border-color: #0c8578 !important;
    }
    .text-primary {
      color: var(--nusamind-primary) !important;
    }
    .badge-primary {
      background-color: var(--nusamind-primary) !important;
    }
    .btn-warning, .bg-warning {
      background-color: var(--nusamind-secondary) !important;
      border-color: var(--nusamind-secondary) !important;
    }
    .brand-text {
      color: var(--nusamind-primary);
      font-weight: 700;
    }
    .card {
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0,0,0,0.05);
    }
  </style>

  @stack('styles')
</head>

<body>

  @include('layouts.partials.admin-header')

  @include('layouts.partials.admin-sidebar')

  <main id="main" class="main">
    <div class="pagetitle">
      <h1>@yield('page-title', 'Dashboard')</h1>
      @hasSection('breadcrumb')
        @yield('breadcrumb')
      @endif
    </div>

    @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    @endif

    @if(session('error'))
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle me-1"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    @endif

    @yield('content')
  </main>

  <footer id="footer" class="footer">
    <div class="copyright">
      &copy; {{ date('Y') }} <strong><span>{{ config('app.name') }}</span></strong>. Dibangun untuk UMKM Indonesia
    </div>
  </footer>

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <script src="{{ asset('assets/vendors/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('assets/vendors/apexcharts/apexcharts.min.js') }}"></script>
  <script src="{{ asset('assets/vendors/quill/quill.min.js') }}"></script>
  <script src="{{ asset('assets/vendors/simple-datatables/simple-datatables.js') }}"></script>
  <script src="{{ asset('assets/vendors/tinymce/tinymce.min.js') }}"></script>
  <script src="{{ asset('assets/js/main.js') }}"></script>
  @stack('scripts')
</body>
</html>
