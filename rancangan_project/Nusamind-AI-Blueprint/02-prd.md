# 02 — Product Requirements Document (PRD): Nusamind AI

## A. Ringkasan Produk

| Item | Detail |
|---|---|
| Nama Produk | Nusamind AI |
| Platform | Website (Laravel, akses publik) + Android App (Kotlin, konsumsi API yang sama) |
| Kategori | Generative AI untuk UMKM (Finansial, Marketing, Analitik, Ekspor) |
| Target Rilis MVP | Sebelum 22 Juli 2026 (deadline submission) |
| Model Bisnis (opsional, nilai tambah) | Freemium — fitur dasar gratis, kuota AI generation harian terbatas, upgrade Pro untuk kuota lebih besar |

## B. Problem Statement

Pelaku UMKM Indonesia kehilangan waktu, peluang, dan pendapatan karena tiga hal:
1. Pencatatan keuangan manual yang rawan human error dan tidak real-time.
2. Tidak punya kemampuan/waktu membuat konten pemasaran yang menarik secara konsisten.
3. Tidak memahami data penjualan sendiri sehingga sulit mengambil keputusan stok/produksi, serta terkendala bahasa untuk menjangkau pasar ekspor.

## C. Target User

1. **End User (UMKM Owner)** — pengguna utama, akses via Android App & Web.
2. **Admin Platform** — tim internal Nusamind yang mengelola user, memonitor pemakaian AI, dan moderasi konten, akses via Web (NiceAdmin dashboard).

## D. Goals & Success Metrics

| Goal | Metrik Sukses |
|---|---|
| UMKM bisa mencatat transaksi tanpa friksi | Rata-rata waktu input transaksi < 15 detik |
| Konten promosi dibuat lebih cepat | Caption + hashtag dihasilkan < 10 detik per request |
| User memahami kondisi bisnisnya | 1 ringkasan AI (briefing) tersedia tiap minggu otomatis |
| Produk siap pakai & live (nilai tambah juri) | Web di-deploy publik, data 100% real-time dari database (no dummy/local storage) |
| Engagement | Target ≥ 70% fitur inti (keuangan, konten, briefing) digunakan minimal 1x oleh setiap user aktif dalam 7 hari |

## E. Fitur Utama (Feature Set)

### 🟢 MVP Scope (WAJIB ada untuk submission)

| # | Fitur | Platform | Deskripsi Singkat |
|---|---|---|---|
| 1 | **Autentikasi & Profil Usaha** | Web + Android | Register/login, lengkapi profil usaha (nama, kategori, kota) |
| 2 | **AI Pencatatan Keuangan (Text-to-Ledger)** | Android (utama) + Web | Ketik kalimat natural → AI ekstrak jadi transaksi terstruktur (income/expense) |
| 3 | **AI Konten & Copywriting (Foto-to-Caption)** | Android (utama) + Web | Upload foto produk → AI hasilkan caption, hashtag, & template broadcast WA |
| 4 | **AI Business Briefing** | Android + Web | Ringkasan mingguan otomatis dari data transaksi & stok, dalam bahasa naratif |
| 5 | **Dashboard Admin (NiceAdmin)** | Web | Kelola user, monitor pemakaian AI, moderasi konten yang dihasilkan |
| 6 | **Riwayat & Laporan Sederhana** | Android + Web | Lihat histori transaksi, grafik sederhana pemasukan/pengeluaran |

### 🟡 Nice-to-Have (kalau waktu cukup, menambah nilai Inovasi)

| # | Fitur | Deskripsi |
|---|---|---|
| 7 | AI Asisten Lokalisasi & Ekspor | Generate deskripsi produk multi-bahasa (EN/ZH) dari deskripsi Bahasa Indonesia |
| 8 | Voice-to-Ledger (input suara) | Rekam suara → transkrip → ekstrak jadi transaksi (pakai Whisper/Gemini Audio) |
| 9 | Chatbot WhatsApp/Instagram (auto-reply dasar) | Webhook sederhana yang membalas FAQ dasar pelanggan UMKM |
| 10 | Notifikasi proaktif | Push notification "Stok keripik singkong tinggal 5, mau restock?" |

### 🔴 Out of Scope (tidak dikerjakan untuk versi ini)

- Integrasi pembayaran/payment gateway penuh.
- Marketplace bawaan (jual-beli langsung di Nusamind).
- Rekomendasi diet/nutrisi (tidak relevan dengan tema).
- Multi-tenant enterprise (banyak cabang per usaha) — disederhanakan jadi 1 usaha per user dulu.

## F. User Story & Acceptance Criteria

### Modul Autentikasi
- **US-01:** Sebagai UMKM owner, saya ingin mendaftar dengan email & password agar data bisnis saya tersimpan aman.
  - AC: Email harus unik & valid; password min. 8 karakter; setelah register, user diarahkan ke onboarding lengkapi profil usaha.
- **US-02:** Sebagai UMKM owner, saya ingin login dan tetap logged-in di Android tanpa harus login ulang setiap buka app.
  - AC: Token (Sanctum) disimpan aman di Android (EncryptedSharedPreferences); auto-refresh/redirect ke login jika token expired.

### Modul AI Keuangan
- **US-03:** Sebagai UMKM owner, saya ingin mengetik "hari ini laku 5 ayam geprek 75rb, beli minyak 20rb" dan sistem otomatis mencatatnya sebagai 2 transaksi terpisah.
  - AC: AI mengembalikan JSON terstruktur (tipe, item, jumlah, nominal); jika AI gagal parsing, sistem menampilkan form manual sebagai fallback; data tersimpan ke tabel `transactions` real-time.
- **US-04:** Sebagai UMKM owner, saya ingin melihat ringkasan saldo (pemasukan - pengeluaran) hari ini di halaman utama.
  - AC: Saldo dihitung real-time dari `transactions` milik user yang login; update otomatis tanpa refresh manual (atau minimal refresh on-resume).

### Modul AI Konten
- **US-05:** Sebagai UMKM owner, saya ingin upload foto produk dan memilih gaya bahasa (Formal/Gaul/Hard Selling), lalu mendapat caption siap pakai.
  - AC: Upload mendukung JPG/PNG max 5MB; hasil AI muncul < 10 detik (loading state jelas); user bisa copy/edit/regenerate hasil; histori tersimpan di `content_generations`.

### Modul AI Business Briefing
- **US-06:** Sebagai UMKM owner, saya ingin menerima ringkasan otomatis tiap minggu tentang produk terlaris dan stok menipis.
  - AC: Briefing digenerate otomatis (scheduled job Laravel) setiap Senin pagi berdasarkan data transaksi 7 hari terakhir; jika data kosong, tampilkan pesan motivasi alih-alih error.

### Modul Admin
- **US-07:** Sebagai admin, saya ingin melihat daftar semua user, jumlah pemakaian AI, dan dapat menangguhkan akun yang melanggar.
  - AC: Tabel user dengan filter status aktif/nonaktif; tombol suspend mengubah kolom `status` user dan langsung memblok akses API (cek di middleware).
- **US-08:** Sebagai admin, saya ingin memoderasi konten AI yang dianggap tidak pantas (laporan dari user).
  - AC: Konten yang dilaporkan masuk ke tabel `content_reports`, admin bisa hapus/sembunyikan dari histori user.

## G. Non-Functional Requirements

| Kategori | Requirement |
|---|---|
| Performance | Respon API non-AI < 1 detik; respon AI generation < 10 detik |
| Security | Password di-hash bcrypt; API pakai Laravel Sanctum token; rate limit endpoint AI (anti-abuse) |
| Usability | Mobile responsive, microcopy Bahasa Indonesia santai |
| Reliability | Jika AI provider gagal/timeout, sistem fallback ke pesan error ramah + opsi input manual |
| Scalability | Skema DB mendukung penambahan kategori bisnis tanpa migration besar (pakai tabel `categories` bukan enum hardcode) |
| Availability | Web di-hosting publik (live URL) selama periode penjurian |

## H. User Flow Singkat (Ringkasan)

```
[Splash] → [Login/Register] → [Onboarding Profil Usaha] → [Home Dashboard]
                                                              ├─ [AI Catat Keuangan] → [Konfirmasi Transaksi] → [Tersimpan]
                                                              ├─ [AI Konten] → [Upload Foto] → [Pilih Gaya] → [Hasil Caption]
                                                              ├─ [Business Briefing] → [Lihat Ringkasan Mingguan]
                                                              └─ [Riwayat & Laporan] → [Detail Transaksi]

[Admin Login] → [Dashboard Admin] → [Kelola User] / [Monitor AI Usage] / [Moderasi Konten]
```
