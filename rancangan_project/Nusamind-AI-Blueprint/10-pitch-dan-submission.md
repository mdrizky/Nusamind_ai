# 10 — Strategi Pitching & Checklist Submission Dicoding

## 1. Pemetaan ke Kriteria Penilaian (supaya tidak ada poin yang kebuang)

| Kriteria | Bobot | Cara Nusamind AI Menjawabnya |
|---|---|---|
| **Inovasi & Kebaruan** | 20% | Single platform multi-fungsi (keuangan + konten + analitik + ekspor), bukan tools terpisah seperti kompetitor; voice/text-to-ledger bahasa informal |
| **Kesesuaian Tema** | 30% | Langsung menjawab 3 pain point utama di brief: pencatatan manual, kesulitan iklan digital, produk lokal sulit ekspor — disebutkan eksplisit di Project Brief |
| **Manfaat untuk Masyarakat** | 25% | Dampak ekonomi langsung: hemat waktu pencatatan, tingkatkan kualitas promosi, buka akses pasar ekspor untuk usaha kecil |
| **Desain & Kemudahan Penggunaan** | 25% | Zero learning curve, mobile-first, microcopy santai, branding lokal hangat (lihat `01-product-overview.md`) |
| **Nilai Tambah** | bonus | Web live publik + data 100% real-time dari Supabase/MySQL (bukan dummy/local storage) + APK bisa diinstall langsung |

## 2. Struktur Project Brief (sesuai template Dicoding)

Isi minimal yang harus ada di dokumen Project Brief saat submission:

1. **Nama Aplikasi:** Nusamind AI
2. **Latar Belakang Masalah** — ambil dari `01-product-overview.md` bagian 1
3. **Solusi yang Ditawarkan** — ringkas 4 fitur inti dari `02-prd.md`
4. **Target Pengguna** — persona dari `01-product-overview.md` bagian 3
5. **Teknologi yang Digunakan** — Laravel 11, MySQL, NiceAdmin, Kotlin/Jetpack Compose, Gemini/Groq API
6. **Tangkapan Layar / Demo** — screenshot tiap fitur inti (Home, AI Keuangan, AI Konten, Briefing, Admin Dashboard)
7. **Link Aplikasi** — URL web live + link APK/Play Store (jika ada)
8. **(Jika ada) Testimoni Partner UMKM Riil** — cerita singkat + kalau bisa foto bersama partner (nilai tambah besar di mata juri)

## 3. Checklist Sebelum Submit

### Teknis
- [ ] Web sudah live di domain publik dan bisa diakses tanpa VPN
- [ ] Database terhubung real (bukan SQLite lokal/dummy data)
- [ ] Semua endpoint AI sudah dites dengan data nyata (bukan hardcoded response)
- [ ] APK Android sudah dibuild (debug minimal, signed kalau bisa) dan diuji install di HP fisik
- [ ] Tidak ada error 500 di flow utama (register → AI keuangan → AI konten → briefing)
- [ ] Rate limit AI berjalan (uji coba spam request, harus kena limit di percobaan ke-31)

### Konten Submission
- [ ] Nama Aplikasi diisi: "Nusamind AI"
- [ ] Link Aplikasi (web + link Drive APK jika belum publish ke Play Store)
- [ ] Project Brief dibuat di Google Docs, sharing diset "Anyone with the link"
- [ ] Komentar submission diisi dengan link Project Brief tersebut
- [ ] App ID diisi HANYA jika aplikasi sudah disubmit ke Play Store/App Store (opsional)

### Branding & Presentasi
- [ ] Logo & nama konsisten di semua halaman (web, Android, dokumen)
- [ ] Screenshot di Project Brief diambil dalam kondisi UI rapi (bukan layar error/loading kosong)
- [ ] Kalau ada waktu: buat video demo singkat 2-3 menit (selalu jadi nilai tambah, walau tidak diwajibkan brief)

## 4. Kalimat Pembuka Pitch (untuk Project Brief / video demo)

> "Dari 64 juta UMKM Indonesia, hanya 12% yang berhasil go-digital. Bukan karena mereka tidak mau — tapi karena solusi yang ada terlalu rumit untuk orang yang sehari-harinya jualan ayam geprek atau jahit baju. Nusamind AI hadir bukan sebagai aplikasi yang harus dipelajari, tapi sebagai partner yang langsung paham — cukup ngobrol atau kirim foto, sisanya biar AI yang urus."

## 5. Risiko & Mitigasi (baik untuk ditunjukkan ke juri sebagai bukti maturity produk)

| Risiko | Mitigasi yang Sudah Dirancang |
|---|---|
| AI salah tangkap nominal/transaksi | Wajib ada langkah konfirmasi sebelum data tersimpan |
| Biaya API AI membengkak | Rate limit harian + briefing dijadwalkan (bukan realtime tiap buka halaman) |
| Konten AI tidak pantas | Sistem moderasi & report dari user, dashboard admin untuk review |
| User non-teknis bingung pakai app | Filosofi Zero Learning Curve, microcopy santai, onboarding minim langkah |
