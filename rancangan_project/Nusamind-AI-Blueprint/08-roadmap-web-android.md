# 08 — Roadmap Pengerjaan: Nusamind AI

**Periode tersedia:** sekarang → 22 Juli 2026 (≈4-8 minggu tergantung kapan mulai)
**Prinsip:** Backend Laravel jalan duluan 1 minggu lebih awal supaya Android bisa langsung konsumsi API real (bukan dummy), sesuai kriteria nilai tambah juri.

---

## Minggu 1 — Setup & Fondasi
**Laravel (Web):**
- Setup project Laravel 11 + install NiceAdmin template (admin panel)
- Setup Sanctum untuk API auth
- Jalankan migration dari `05-database-schema.sql`
- Buat seeder kategori
- Implementasi endpoint Auth (register, login, logout, me)
- Deploy awal ke server (Railway/VPS) — biar dari minggu 1 sudah ada live URL

**Android:**
- Setup project Kotlin + Jetpack Compose + Hilt + Retrofit
- Buat struktur folder (data/domain/presentation, MVVM)
- Buat layar Splash, Login, Register (UI dulu, integrasi API menyusul)

---

## Minggu 2 — Profil Usaha & Produk
**Laravel:**
- Endpoint `/business` (CRUD profil usaha)
- Endpoint `/products` (CRUD produk)
- Setup storage untuk upload gambar (logo & foto produk)

**Android:**
- Integrasi API Auth (token tersimpan aman)
- Layar Onboarding profil usaha
- Layar CRUD produk (list, tambah, edit)

---

## Minggu 3 — AI Pencatatan Keuangan (Fitur Inti #1)
**Laravel:**
- Integrasi Gemini/Groq API di backend (service class `AiFinanceService`)
- Implementasi prompt extraction (lihat `09-ai-prompts-per-fitur.md`)
- Endpoint `/ai/finance/extract` + `/transactions` (CRUD)
- Implementasi business rules (validasi nominal, array transaksi, dst)

**Android:**
- Layar input teks (chat-like input)
- Layar preview & konfirmasi transaksi sebelum simpan
- Layar Home dengan ringkasan saldo hari ini
- Layar riwayat transaksi + filter

---

## Minggu 4 — AI Konten & Copywriting (Fitur Inti #2)
**Laravel:**
- Service `AiContentService` (Vision LLM via Gemini)
- Endpoint `/ai/content/generate`, regenerate, histori
- Validasi upload foto (ukuran, format)
- Endpoint `/content-reports`

**Android:**
- Layar upload foto (kamera/galeri) + pilih gaya bahasa
- Layar hasil (caption, hashtag, template WA) dengan tombol copy/share
- Layar histori konten
- Tombol lapor konten

---

## Minggu 5 — AI Business Briefing (Fitur Inti #3) + Admin Panel Dasar
**Laravel:**
- Scheduled job (`php artisan schedule:run`) generate briefing tiap Senin
- Service `AiBriefingService`
- Endpoint `/business-insights/latest` & `/history`
- Admin: Dashboard overview (NiceAdmin) — total user, total transaksi, grafik
- Admin: Manajemen user (list, suspend/aktifkan)

**Android:**
- Layar Business Briefing di Home (kartu narasi mingguan)
- Notifikasi lokal saat briefing baru tersedia

---

## Minggu 6 — Polish, Moderasi, & Fitur Nice-to-have (jika waktu cukup)
**Laravel:**
- Admin: Monitoring AI usage logs
- Admin: Moderasi konten (resolve content reports)
- Admin: Broadcast notifikasi
- (Opsional) Endpoint `/ai/export/translate`

**Android:**
- Layar notifikasi
- (Opsional) Layar AI Lokalisasi & Ekspor
- Polishing UI (loading state, empty state, error state) — penting untuk skor Desain 25%

---

## Minggu 7 — Testing & Bug Fixing
- Uji semua user flow end-to-end (register → onboarding → AI keuangan → AI konten → briefing)
- Uji edge case sesuai `acceptance criteria` di PRD (input AI gagal parsing, foto invalid, dst)
- Uji role admin vs user (RBAC)
- Perbaikan bug dari hasil testing
- Pastikan deploy production stabil (web live + API live untuk Android)

---

## Minggu 8 — Submission
- Build APK/AAB final Android
- Pastikan web public URL aktif (cek `TERJAMIN` — uptime selama masa penjurian 23 Juli - 5 Agustus)
- Siapkan Project Brief (lihat `10-pitch-dan-submission.md`)
- Submit ke Dicoding sebelum **22 Juli 2026**

---

## Catatan Jika Waktu Terbatas (Versi Kompresi 4 Minggu)

| Minggu | Fokus |
|---|---|
| 1 | Setup + Auth + Profil + Produk (gabung) |
| 2 | AI Keuangan (full, ini fitur paling wajib & paling relevan ke tema "mempermudah pencatatan") |
| 3 | AI Konten + Admin dashboard dasar |
| 4 | AI Briefing (versi sederhana) + Testing + Submission |

> Kalau benar-benar mepet waktu: **AI Keuangan + AI Konten + Admin Panel** adalah kombinasi minimum yang masih solid untuk submission — briefing & ekspor boleh jadi "Coming Soon" di UI asal tidak di-klaim sebagai fitur jadi ke juri.
