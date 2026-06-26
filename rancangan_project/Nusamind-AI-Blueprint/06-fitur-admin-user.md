# 06 — Rincian Fitur: Admin (Web/NiceAdmin) & User (Android + Web)

## A. Fitur Sisi USER (Android — utama, Web — pendamping)

### 1. Onboarding & Profil
- Register/Login (email + password, via Laravel Sanctum)
- Lengkapi profil usaha: nama usaha, kategori, kota, logo (opsional)
- Edit profil & ganti password

### 2. AI Catat Keuangan
- Input teks bebas (chat-like input box) → AI ekstrak transaksi
- Preview hasil ekstraksi sebelum disimpan (editable)
- Lihat saldo hari ini (pemasukan - pengeluaran)
- Riwayat transaksi (filter: hari ini / minggu ini / bulan ini, filter tipe)
- (Nice-to-have) Input via rekam suara

### 3. AI Konten & Copywriting
- Upload foto produk dari galeri/kamera
- Pilih gaya bahasa: Formal / Gaul / Hard Selling
- Lihat hasil: caption + hashtags + template broadcast WA
- Copy ke clipboard / share langsung ke WhatsApp/Instagram (via Android share intent)
- Riwayat konten yang pernah dibuat
- Tombol "Regenerate" (maks 5x/foto/hari)
- Tombol "Lapor konten tidak pantas" (untuk moderasi)

### 4. AI Business Briefing
- Lihat ringkasan mingguan (narasi AI) di kartu Home
- Lihat detail: produk terlaris, produk stok menipis
- Notifikasi saat briefing baru tersedia

### 5. Manajemen Produk
- CRUD produk milik usaha sendiri (nama, harga, stok, foto, deskripsi)
- Daftar produk untuk dipakai sebagai sumber foto AI Konten

### 6. (Nice-to-have) AI Lokalisasi & Ekspor
- Pilih produk → pilih bahasa tujuan (EN/ZH) → generate deskripsi ekspor
- Riwayat hasil terjemahan per produk

### 7. Notifikasi
- List notifikasi (briefing baru, stok menipis, pengumuman admin)
- Tandai sudah dibaca

---

## B. Fitur Sisi ADMIN (Web — NiceAdmin Dashboard)

### 1. Dashboard Overview
- Total user terdaftar, total user aktif 7 hari
- Total transaksi tercatat (real-time count dari DB — poin nilai tambah juri)
- Total pemakaian AI hari ini per fitur (grafik sederhana dari `ai_usage_logs`)
- Grafik pertumbuhan user per minggu

### 2. Manajemen User
- List semua user (search, filter status, filter kategori usaha)
- Lihat detail profil usaha user (read-only, demi privasi data keuangan user)
- Suspend / aktifkan kembali akun user
- Reset password user (jika diperlukan untuk support)

### 3. Monitoring AI Usage
- Tabel log pemakaian AI (`ai_usage_logs`): user, fitur, status (success/failed/timeout), waktu
- Filter berdasarkan fitur (finance/content/briefing/export) dan status
- Insight: fitur AI mana yang paling sering dipakai (untuk validasi produk ke depan)

### 4. Moderasi Konten
- List `content_reports` yang masuk (status: pending/reviewed/removed)
- Lihat detail konten yang dilaporkan (foto + hasil AI + alasan lapor)
- Aksi: tandai "reviewed" atau "removed" (otomatis sembunyikan dari histori user terkait)

### 5. Manajemen Kategori Usaha
- CRUD `categories` (tambah kategori baru jika dibutuhkan, mis. "Otomotif", "Kesehatan")

### 6. Pengumuman/Notifikasi Broadcast
- Admin bisa kirim notifikasi ke semua user (mis. "Fitur baru sudah hadir!") → insert ke tabel `notifications` untuk semua user aktif

### 7. (Opsional) Manajemen Kuota & Plan
- Atur limit kuota AI harian per tier (gratis/pro) dari satu halaman setting, tanpa perlu redeploy kode

---

## C. Matriks Akses (RBAC Singkat)

| Fitur | User | Admin | Superadmin |
|---|---|---|---|
| CRUD data sendiri (transaksi, produk, konten) | ✅ | ❌ (read-only agregat) | ❌ |
| Lihat dashboard monitoring | ❌ | ✅ | ✅ |
| Suspend/aktifkan user | ❌ | ✅ | ✅ |
| Moderasi konten | ❌ | ✅ | ✅ |
| Kelola akun admin lain | ❌ | ❌ | ✅ |
| Atur kuota AI global | ❌ | ❌ | ✅ |
