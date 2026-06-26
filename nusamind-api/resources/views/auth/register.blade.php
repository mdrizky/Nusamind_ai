<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Daftar - {{ config('app.name') }}</title>

  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

  <link href="{{ asset('assets/vendors/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendors/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendors/boxicons/css/boxicons.min.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">

  <style>
    :root { --nusamind-primary: #0F9D8E; --nusamind-secondary: #F2B705; }
    .btn-primary { background-color: var(--nusamind-primary) !important; border-color: var(--nusamind-primary) !important; }
    .btn-primary:hover { background-color: #0c8578 !important; border-color: #0c8578 !important; }
    .text-primary { color: var(--nusamind-primary) !important; }
    .brand-text { color: var(--nusamind-primary); font-weight: 700; }
  </style>
</head>

<body>
  <main>
    <div class="container">
      <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
        <div class="container">
          <div class="row justify-content-center">
            <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">
              <div class="d-flex justify-content-center py-4">
                <a href="/" class="logo d-flex align-items-center w-auto">
                  <span class="brand-text" style="font-size:1.5rem;">{{ config('app.name') }}</span>
                </a>
              </div>

              <div class="card mb-3">
                <div class="card-body">
                  <div class="pt-4 pb-2">
                    <h5 class="card-title text-center pb-0 fs-4">Daftar Akun Baru</h5>
                    <p class="text-center small">Mulai catat jualan kamu!</p>
                  </div>

                  @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                      {{ $errors->first() }}
                      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                  @endif

                  <form class="row g-3" method="POST" action="/register">
                    @csrf
                    <div class="col-12">
                      <label for="name" class="form-label">Nama Lengkap</label>
                      <input type="text" name="name" class="form-control" id="name" required value="{{ old('name') }}">
                    </div>

                    <div class="col-12">
                      <label for="email" class="form-label">Email</label>
                      <input type="email" name="email" class="form-control" id="email" required value="{{ old('email') }}">
                    </div>

                    <div class="col-12">
                      <label for="password" class="form-label">Password</label>
                      <input type="password" name="password" class="form-control" id="password" required minlength="8">
                    </div>

                    <div class="col-12">
                      <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                      <input type="password" name="password_confirmation" class="form-control" id="password_confirmation" required>
                    </div>

                    <div class="col-12">
                      <button class="btn btn-primary w-100" type="submit">Daftar</button>
                    </div>
                    <div class="col-12">
                      <p class="small mb-0">Sudah punya akun? <a href="{{ route('login') }}">Masuk</a></p>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>
  </main>
</body>
</html>
