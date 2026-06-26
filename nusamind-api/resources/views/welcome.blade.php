<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="Nusamind AI - Asisten digital AI untuk UMKM Indonesia. Catat keuangan, buat konten marketing, dan dapatkan insight bisnis dengan AI.">
  <title>Nusamind AI — Asisten Digital untuk UMKM</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Poppins:wght@500;600;700;800&display=swap" rel="stylesheet">
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
      font-family: 'Inter', -apple-system, sans-serif;
      background: linear-gradient(135deg, #f0fdfa 0%, #ffffff 50%, #fffbeb 100%);
      color: #1f2937;
      min-height: 100vh;
    }
    .font-heading { font-family: 'Poppins', 'Inter', sans-serif; }
    .container { max-width: 1100px; margin: 0 auto; padding: 0 24px; }
    nav {
      display: flex; align-items: center; justify-content: space-between;
      padding: 20px 0;
    }
    .logo {
      font-family: 'Poppins', sans-serif; font-weight: 700; font-size: 1.4rem;
      color: #0F9D8E; text-decoration: none;
    }
    .logo span { color: #F2B705; }
    .nav-links a {
      text-decoration: none; color: #4b5563; font-weight: 500; font-size: 0.9rem;
      margin-left: 24px; padding: 8px 20px; border-radius: 100px;
      transition: all 0.2s;
    }
    .nav-links a:hover { color: #0F9D8E; }
    .nav-links .btn-primary {
      background: #0F9D8E; color: white !important;
    }
    .nav-links .btn-primary:hover { background: #0c8578; }
    .hero {
      text-align: center; padding: 60px 0 50px;
    }
    .hero-badge {
      display: inline-block; background: #e6f7f5; color: #0F9D8E;
      font-size: 0.8rem; font-weight: 600; padding: 6px 16px;
      border-radius: 100px; margin-bottom: 20px;
    }
    .hero h1 {
      font-family: 'Poppins', sans-serif; font-size: 2.5rem; font-weight: 800;
      line-height: 1.2; margin-bottom: 16px; color: #111827;
    }
    .hero h1 span { color: #0F9D8E; }
    .hero p {
      font-size: 1.05rem; color: #6b7280; max-width: 600px;
      margin: 0 auto 32px; line-height: 1.6;
    }
    .hero-buttons a {
      display: inline-block; text-decoration: none; font-weight: 600;
      padding: 14px 32px; border-radius: 100px; font-size: 0.95rem;
      margin: 0 8px; transition: all 0.2s;
    }
    .btn-tosca { background: #0F9D8E; color: white; }
    .btn-tosca:hover { background: #0c8578; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(15,157,142,0.3); }
    .btn-outline { border: 2px solid #0F9D8E; color: #0F9D8E; }
    .btn-outline:hover { background: #0F9D8E; color: white; }
    .features {
      display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
      gap: 20px; padding: 40px 0 60px;
    }
    .feature-card {
      background: white; border-radius: 16px; padding: 28px 24px;
      box-shadow: 0 1px 3px rgba(0,0,0,0.06); border: 1px solid #e5e7eb;
      transition: all 0.2s;
    }
    .feature-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.08); transform: translateY(-2px); }
    .feature-icon {
      width: 48px; height: 48px; border-radius: 12px;
      display: flex; align-items: center; justify-content: center;
      font-size: 1.4rem; margin-bottom: 16px;
    }
    .icon-tosca { background: #e6f7f5; }
    .icon-gold { background: #fef3cd; }
    .feature-card h3 {
      font-family: 'Poppins', sans-serif; font-size: 1.1rem; font-weight: 600;
      margin-bottom: 8px; color: #111827;
    }
    .feature-card p { font-size: 0.9rem; color: #6b7280; line-height: 1.5; }
    footer {
      text-align: center; padding: 32px 0; border-top: 1px solid #e5e7eb;
      color: #9ca3af; font-size: 0.85rem;
    }
    @media (max-width: 640px) {
      .hero h1 { font-size: 1.8rem; }
      .hero p { font-size: 0.95rem; }
      .hero-buttons a { display: block; margin: 10px auto; max-width: 280px; }
      nav { flex-direction: column; gap: 12px; }
      .nav-links a { margin: 0 8px; }
    }
  </style>
</head>
<body>
  <div class="container">
    <nav>
      <a href="/" class="logo">Nusamind<span>AI</span></a>
      <div class="nav-links">
        <a href="{{ route('login') }}">Masuk</a>
        <a href="{{ route('register') }}" class="btn-primary">Daftar Gratis</a>
      </div>
    </nav>

    <section class="hero">
      <div class="hero-badge">🚀 Untuk UMKM Indonesia</div>
      <h1>Asisten AI <span>All-in-One</span> untuk Bisnismu</h1>
      <p>
        Catat keuangan otomatis, buat konten marketing menarik, dan dapatkan
        ringkasan bisnis mingguan — cukup dari HP!
      </p>
      <div class="hero-buttons">
        <a href="{{ route('register') }}" class="btn-tosca">Mulai Gratis</a>
        <a href="{{ route('login') }}" class="btn-outline">Masuk</a>
      </div>
    </section>

    <section class="features">
      <div class="feature-card">
        <div class="feature-icon icon-tosca">💰</div>
        <h3>AI Pencatatan Keuangan</h3>
        <p>Catat pemasukan & pengeluaran cukup dengan ngomong atau ngetik. AI otomatis merapikannya.</p>
      </div>
      <div class="feature-card">
        <div class="feature-icon icon-gold">📝</div>
        <h3>AI Konten & Copywriting</h3>
        <p>Upload foto produk, pilih gaya bahasa, dapatkan caption + hashtag siap pakai buat jualan.</p>
      </div>
      <div class="feature-card">
        <div class="feature-icon icon-tosca">📊</div>
        <h3>AI Business Briefing</h3>
        <p>Ringkasan bisnis mingguan dengan narasi AI yang mudah dipahami. Tahu produk paling laku & stok menipis.</p>
      </div>
      <div class="feature-card">
        <div class="feature-icon icon-gold">🌏</div>
        <h3>Ekspor Pasar Global</h3>
        <p>Terjemahkan deskripsi produk ke Inggris & Mandarin siap go internasional.</p>
      </div>
    </section>

    <footer>
      &copy; {{ date('Y') }} Nusamind AI — Dibuat untuk UMKM Indonesia
    </footer>
  </div>
</body>
</html>
