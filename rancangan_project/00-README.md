# 📁 Nusamind AI — Blueprint Dokumentasi Project

**Submission untuk:** IDCamp Developer Challenge #2 — *Digitalization & Acceleration of MSMEs with Generative AI*
**Tagline Produk:** *"Asisten Digital All-in-One Berbasis AI untuk UMKM Indonesia"*
**Stack:** Laravel 11 (Backend + Admin Panel NiceAdmin) · MySQL · Kotlin (Android, Jetpack Compose) · Gemini/Groq AI Engine

---

## 🗂️ Daftar Isi Folder

| No | File | Isi |
|----|------|-----|
| 00 | `00-README.md` | Index & cara pakai folder ini |
| 01 | `01-product-overview.md` | Ringkasan produk, masalah, value proposition, branding & tema visual |
| 02 | `02-prd.md` | Product Requirements Document lengkap (goals, fitur, user story, acceptance criteria, scope) |
| 03 | `03-business-rules.md` | Aturan logika bisnis tiap fitur AI (wajib biar AI & developer nggak salah tafsir) |
| 04 | `04-erd.md` | Entity Relationship Diagram + penjelasan relasi antar tabel |
| 05 | `05-database-schema.sql` | Schema SQL siap pakai (bisa langsung jadi migration Laravel) |
| 06 | `06-fitur-admin-user.md` | Rincian fitur sisi User (Android+Web) dan Admin (NiceAdmin) |
| 07 | `07-api-laravel.md` | API contract lengkap (endpoint, request, response, auth) |
| 08 | `08-roadmap-web-android.md` | Roadmap pengerjaan 8 minggu (Laravel + Android), siap jadi sprint planning |
| 09 | `09-ai-prompts-per-fitur.md` | System prompt & prompt template siap pakai untuk tiap fitur AI |
| 10 | `10-pitch-dan-submission.md` | Strategi pitching, isi Project Brief, dan checklist submission Dicoding |

---

## 🚀 Cara Pakai Folder Ini (Urutan Kerja yang Disarankan)

1. Baca `01` dan `02` dulu → biar lo dan tim (kalau ada) satu pemahaman soal produk.
2. Baca `03` → ini fondasi logika AI, **jangan skip**, karena ini yang bikin output AI konsisten.
3. Pakai `04` + `05` → langsung jalankan migration Laravel.
4. Pakai `06` → breakdown kerjaan jadi checklist fitur per role.
5. Pakai `07` → kontrak API, biar kerja backend (Laravel) dan Android bisa paralel tanpa nunggu-nungguan.
6. Pakai `08` → ikuti roadmap mingguan biar nggak overscope (ingat: MVP dulu, fitur canggih belakangan).
7. Pakai `09` → copy-paste prompt AI ke kode (Gemini/Groq API call).
8. Pakai `10` pas mau submit ke Dicoding.

---

## ⚠️ Prinsip Penting

> **Jangan coba bikin 10 fitur sekaligus.** Challenge ini dinilai dari MVP yang *jalan, real-time, dan solutif* — bukan dari jumlah fitur. Fokus ke 4 fitur inti dulu (lihat `02-prd.md` bagian MVP Scope), baru tambah yang lain kalau waktu masih ada sampai deadline **22 Juli 2026**.
