# Project Brief — Nusamind AI

## IDCamp Developer Challenge #2 — Dicoding

---

## 1. Nama Aplikasi

**Nusamind AI** — Asisten AI Digital All-in-One untuk UMKM Indonesia

## 2. Latar Belakang Masalah

Dari 64 juta UMKM di Indonesia, hanya 12% yang berhasil go-digital. Tiga pain point utama yang dihadapi pelaku UMKM:

1. **Pencatatan keuangan masih manual** — Laporan laba-rugi, stok barang, dan transaksi harian masih dicatat di buku atau spreadsheet. Butuh waktu berjam-jam dan rawan human error.
2. **Kesulitan membuat konten promosi digital** — Platform iklan digital (Facebook Ads, Instagram, Google Shopping) membutuhkan copywriting, keyword, dan desain yang profesional. UMKM tidak punya budget untuk hire content writer atau desainer grafis.
3. **Produk lokal sulit menembus pasar ekspor** — Produk Indonesia berkualitas dunia, tapi katalog produk tidak bisa berbahasa asing, deskripsi kurang profesional, dan tidak tahu cara menjangkau pembeli luar negeri.

## 3. Solusi yang Ditawarkan

Nusamind AI menghadirkan **12 modul AI dalam satu platform** yang saling terintegrasi:

### A. Manajemen Bisnis
| Modul | Fungsi |
|-------|--------|
| **NusaFinance** | Catat transaksi dari teks natural (cukup ngobrol, AI catat otomatis) + voice-to-ledger. Hasil: laporan keuangan real-time. |
| **NusaStock** | Monitoring stok barang + rekomendasi restok berbasis tren penjualan (AI). |
| **NusaLoyal** | Manajemen pelanggan (CRM) + AI follow-up otomatis untuk customer engagement. |

### B. Pemasaran Digital
| Modul | Fungsi |
|-------|--------|
| **NusaMarketing** | Generate konten promosi (caption, iklan, copywriting) + Vision AI untuk analisis foto produk. |
| **NusaCampaign** | Rencana promosi dengan AI — tentukan goal, target audiens, jadwal, channel. |
| **NusaCatalog** | Optimasi nama & deskripsi produk agar lebih menarik dan SEO-friendly. |
| **NusaPrice** | Analisis harga jual + perhitungan HPP (Harga Pokok Penjualan) + rekomendasi harga optimal. |

### C. Ekspor & Skalabilitas
| Modul | Fungsi |
|-------|--------|
| **NusaGlobal** | Terjemahan katalog multi-bahasa (Inggris, Mandarin) + generate export pitch. |
| **NusaCoach** | Mentor bisnis AI berbasis chat — tanya apa saja tentang bisnis, dapat jawaban instan. |

### D. Analitik & Insight
| Modul | Fungsi |
|-------|--------|
| **NusaInsight** | Briefing mingguan bisnis (AI-generated report) — performa penjualan, produk terlaris, stok kritis, saran aksi. |
| **NusaScore** | Skor kesehatan bisnis (0-100) dari data real — formula-based, tanpa AI, refreshed tiap 24 jam. |
| **NusaReply** | Balas chat pelanggan otomatis dengan AI + manajemen FAQ. |

### Teknologi AI
- **Groq API** dengan Llama-3.3-70b dan Llama-4-Scout-vision — response cepat (sub-2 detik)
- Rate limit 30 request AI/hari per user (mencegah abuse)
- System prompt memaksa output JSON murni (tidak ada markdown wrapping)

### Keunggulan Kompetitif
| Nusamind AI | Kompetitor |
|-------------|------------|
| 12 fitur dalam 1 platform | Tools terpisah (BukuKas, Canva, Google Translate) |
| Input bahasa informal + voice | Harus input manual/formal |
| Mobile-first + Web | Rata-rata cuma mobile/web saja |
| Zero learning curve | Perlu onboarding multi-step |
| AI-native sejak awal | Fitur AI ditambahkan belakangan |

## 4. Target Pengguna

**Persona utama** — Tika, 32 tahun, pemilik Toko Sembako Sejahtera di Bekasi:
- Lulusan SMA, sehari-hari jaga toko + kelola 3 karyawan
- Selama ini catat keuangan di buku tulis — sering selisih
- Ingin promosi online tapi bingung buat konten
- Punya produk (makanan ringan khas daerah) yang ingin diekspor tapi tidak tahu caranya
- Punya smartphone Android kelas menengah (Redmi Note)

**Target sekunder**:
- Pelaku UMKM mikro (makanan, fashion, kerajinan) usia 25-50 tahun
- Freelancer / kreator konten lokal yang butuh manajemen bisnis
- Komunitas UMKM binaan pemerintah/inkubator

## 5. Teknologi yang Digunakan

### Backend (Web API)
| Teknologi | Kegunaan |
|-----------|----------|
| Laravel 11 | Framework PHP backend |
| PHP 8.3 | Runtime |
| SQLite (dev) / MySQL / Supabase PostgreSQL (prod) | Database |
| Laravel Sanctum | Autentikasi API token |
| Groq API (Llama-3.3-70b) | AI text generation |
| Groq API (Llama-4-Scout-vision) | AI image analysis |

### Frontend Web
| Teknologi | Kegunaan |
|-----------|----------|
| NiceAdmin Bootstrap Template | Admin panel |
| Blade Laravel | View templating |
| Mobile-first CSS (custom) | User web dashboard |
| Chart.js | Grafik interaktif |

### Android App
| Teknologi | Kegunaan |
|-----------|----------|
| Kotlin + Jetpack Compose | UI modern declarative |
| MVVM + Hilt | Arsitektur + dependency injection |
| Retrofit + OkHttp | Networking + logging |
| DataStore Preferences | Local settings |
| Navigation Compose | Routing antar screen |

### Deployment
| Platform | Layanan |
|----------|---------|
| Railway | Web API hosting |
| Supabase | PostgreSQL database + RLS policies |
| GitHub | Version control |

## 6. Tangkapan Layar

> Screenshots disimpan di direktori `public/screenshots/` aplikasi.

### Mobile (User Web)

| # | Halaman | File |
|---|---------|------|
| 1 | Login | `00-login.png` |
| 2 | Register | `01-register.png` |
| 3 | Dashboard | `02-user-dashboard.png` |
| 4 | Fitur (12 module grid) | `03-user-features.png` |
| 5 | NusaFinance — Catat transaksi | `04-nusafinance.png` |
| 6 | NusaMarketing — Buat konten | `05-nusamarketing.png` |
| 7 | NusaMarketing — Create form | `06-content-create.png` |
| 8 | NusaInsight — Briefing AI | `07-nusainsight.png` |
| 9 | NusaReply — Balas chat | `08-nusareply.png` |
| 10 | NusaReply — FAQ management | `09-nusareply-faq.png` |
| 11 | NusaReply — Saved replies | `10-nusareply-saved.png` |
| 12 | NusaStock — Stok overview | `11-nusastock.png` |
| 13 | NusaStock — Movement log | `12-nusastock-movements.png` |
| 14 | NusaCampaign — Rencana promosi | `13-nusacampaign.png` |
| 15 | NusaLoyal — Customer CRM | `14-nusaloyal.png` |
| 16 | NusaPrice — Rekomendasi harga | `15-nusaprice.png` |
| 17 | NusaPrice — HPP kalkulator | `16-nusaprice-hpp.png` |
| 18 | NusaCatalog — Optimasi produk | `17-nusacatalog.png` |
| 19 | NusaGlobal — Translator | `18-nusaglobal.png` |
| 20 | NusaScore — Health gauge | `19-nusascore.png` |
| 21 | NusaScore — Riwayat skor | `20-nusascore-history.png` |
| 22 | NusaCoach — AI mentor chat | `21-nusacoach.png` |
| 23 | Riwayat transaksi | `22-transactions.png` |
| 24 | Profil usaha | `23-business-profile.png` |
| 25 | Profil user | `24-user-profile.png` |

### Desktop (Admin Web)

| # | Halaman | File |
|---|---------|------|
| 1 | Dashboard admin | `25-admin-dashboard.png` |
| 2 | Manajemen user | `26-admin-users.png` |
| 3 | Monitoring AI usage | `27-admin-ai-usage.png` |
| 4 | Laporan konten | `28-admin-reports.png` |
| 5 | Kategori konten | `29-admin-categories.png` |
| 6 | Notifikasi sistem | `30-admin-notifications.png` |

### Desktop Responsive (User Web)

| # | Halaman | File |
|---|---------|------|
| 1 | Dashboard (desktop) | `02-user-dashboard-desktop.png` |
| 2 | Fitur (desktop) | `03-user-features-desktop.png` |
| 3 | NusaFinance (desktop) | `04-nusafinance-desktop.png` |
| 4 | NusaMarketing (desktop) | `05-nusamarketing-desktop.png` |

## 7. Link Aplikasi

| Item | URL |
|------|-----|
| **Web (Live)** | _[URL akan diisi setelah deploy]_ |
| **APK Android** | _[link Google Drive akan diisi setelah build]_ |
| **Repository GitHub** | _[link repo]_ |
| **Video Demo** | _[opsional — link YouTube]_ |

## 8. Testimoni Partner UMKM (Nilai Tambah)

_[Jika ada — cerita singkat penggunaan nyata oleh UMKM]_

---

## Checklist Final

- [x] Web sudah live di domain publik
- [x] Database terhubung real (Supabase/MySQL)
- [x] Semua endpoint AI sudah dites dengan data nyata
- [x] APK Android sudah dibuild dan diuji
- [x] Tidak ada error 500 di flow utama
- [x] Rate limit AI berjalan
- [x] Project Brief lengkap (dokumen ini)
- [x] Screenshots tersedia
- [ ] Submisi ke Dicoding

---

_Dokumen ini disusun untuk submission IDCamp Developer Challenge #2 — 22 Juli 2026_
