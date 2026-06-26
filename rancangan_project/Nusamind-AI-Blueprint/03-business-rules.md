# 03 — Business Rules: Nusamind AI

> Dokumen ini wajib dibaca sebelum coding fitur AI. Tujuannya supaya logika yang ditulis AI (lewat prompt) dan logika yang ditulis developer (lewat kode) **konsisten** — nggak ada celah "AI mikirnya beda sama sistem".

## 1. Aturan Umum AI Engine

- Semua request ke AI **wajib** melewati backend Laravel (bukan langsung dari Android ke Gemini/Groq) — alasan: keamanan API key + bisa di-rate-limit + bisa dicatat di `ai_usage_logs`.
- Setiap response AI yang berbentuk data terstruktur **wajib** dalam format JSON ketat (system prompt mengatur ini), supaya backend bisa langsung `json_decode()` tanpa regex rapuh.
- Jika AI mengembalikan format yang tidak valid (gagal parse JSON) → sistem **tidak boleh** menyimpan data setengah jadi. Tampilkan pesan: *"Nusamind belum yakin dengan inputmu, coba tulis ulang ya"* + opsi input manual.
- Rate limit: maksimal **30 request AI generation per user per hari** untuk tier gratis (mencegah abuse & kontrol biaya API). Disimpan & dicek dari `ai_usage_logs` (hitung jumlah baris hari ini per `user_id`).

## 2. Business Rules — AI Pencatatan Keuangan

- Tipe transaksi hanya dua: `pemasukan` atau `pengeluaran`. Tidak ada tipe lain.
- Jika kalimat user mengandung kata seperti "laku", "jual", "terima", "dapat" → cenderung `pemasukan`.
- Jika mengandung "beli", "bayar", "keluar", "belanja" → cenderung `pengeluaran`.
- Satu kalimat input **bisa menghasilkan lebih dari satu transaksi** (contoh: "laku 5 ayam geprek 75rb, beli minyak 20rb" = 2 baris transaksi). AI wajib mengembalikan **array**, bukan objek tunggal.
- Nominal wajib dikonversi ke angka murni (contoh: "75rb" → `75000`, "1,5jt" → `1500000`). Jika AI tidak yakin dengan nominal → field `nominal` diisi `null` dan sistem meminta konfirmasi manual sebelum simpan ke database (jangan simpan otomatis kalau ada field penting kosong).
- Setiap transaksi yang berhasil diekstrak **wajib dikonfirmasi user** (tampil preview dulu) sebelum benar-benar tersimpan ke `transactions` — mencegah salah catat karena AI salah tangkap konteks.
- Saldo bisnis = `SUM(nominal WHERE tipe='pemasukan') - SUM(nominal WHERE tipe='pengeluaran')`, dihitung query real-time, **tidak disimpan sebagai kolom statis** (hindari data basi).

## 3. Business Rules — AI Konten & Copywriting

- Gaya bahasa hanya 3 pilihan tetap: `formal`, `gaul`, `hard_selling`. Tidak boleh user input gaya bebas teks (supaya prompt AI konsisten dan hasil terprediksi).
- Foto yang diupload wajib divalidasi: format JPG/PNG, ukuran maksimal 5MB, dimensi minimal 300x300px. Foto di luar syarat ini ditolak sebelum dikirim ke AI (hemat biaya API).
- Hasil AI selalu berisi 3 bagian wajib: `caption`, `hashtags` (array, maksimal 10), `whatsapp_broadcast_template`. Jika salah satu bagian gagal digenerate, tampilkan bagian yang berhasil saja — jangan gagal total.
- Histori hasil generate disimpan permanen di `content_generations` (bukan local storage Android) supaya bisa diakses ulang dan dihitung sebagai data real-time oleh juri.
- User bisa menekan "Regenerate" maksimal 5x per foto per hari (mencegah spam ke AI Vision yang lebih mahal dari teks).

## 4. Business Rules — AI Business Briefing

- Briefing dihitung dari data **transaksi 7 hari terakhir** milik user yang sama (`user_id` match).
- Jika user belum punya transaksi sama sekali dalam 7 hari → AI tidak dipanggil sama sekali (hemat biaya), sistem langsung tampilkan pesan template motivasi statis: *"Belum ada transaksi minggu ini. Yuk mulai catat jualanmu dari sekarang!"*
- Produk "paling laku" dihitung dari `SUM(jumlah) GROUP BY item` pada transaksi tipe `pemasukan`, diurutkan descending, diambil 1 teratas.
- "Stok menipis" hanya berlaku jika user mengisi data stok di modul produk (`products.stock`). Jika user tidak pernah mengisi stok produk, bagian itu **tidak ditampilkan** di briefing (jangan AI mengarang data stok).
- Briefing digenerate otomatis via **scheduled job Laravel** (`php artisan schedule:run`, cron tiap Senin 06:00 WIB) dan disimpan ke tabel `business_insights` — bukan digenerate ulang setiap kali user membuka halaman (efisiensi biaya & konsisten).

## 5. Business Rules — AI Lokalisasi & Ekspor (Nice-to-have)

- Bahasa tujuan terbatas pada pilihan tetap: `en` (Inggris), `zh` (Mandarin) — sesuai kebutuhan ekspor yang disebut di brief tantangan.
- Hasil terjemahan wajib menyertakan disclaimer kecil di UI: *"Hasil AI, disarankan dicek ulang sebelum dipublikasikan resmi"* — transparansi ke user bahwa ini bantuan, bukan sertifikasi profesional.

## 6. Business Rules — Role & Akses (RBAC)

| Role | Akses |
|---|---|
| `user` | Hanya bisa CRUD data miliknya sendiri (transaksi, produk, konten). Tidak bisa lihat data user lain. |
| `admin` | Bisa lihat semua user (read-only untuk data transaksi user demi privasi), bisa suspend/aktifkan akun, bisa moderasi `content_generations` yang dilaporkan, bisa lihat agregat `ai_usage_logs`. |
| `superadmin` (opsional) | Tambahan: bisa kelola akun admin lain, ubah limit kuota AI global. |

- Middleware Laravel wajib mengecek `role` di setiap route group `/admin/*`. User dengan role `user` yang mencoba akses endpoint admin → response `403 Forbidden`, bukan redirect diam-diam (transparan untuk debugging).
- Akun dengan `status = 'suspended'` ditolak saat login (pesan: "Akun Anda telah dinonaktifkan, hubungi admin") meskipun token masih valid secara teknis — dicek ulang di middleware tiap request.

## 7. Business Rules — Validasi & Error Handling Umum

- Semua endpoint yang menerima input AI wajib punya **timeout 15 detik** ke provider AI. Jika timeout → response `503` dengan pesan ramah, dicatat di log sebagai `ai_timeout`.
- Semua nominal uang disimpan sebagai `BIGINT` (rupiah, tanpa desimal) — bukan `FLOAT`, untuk menghindari floating point error pada uang.
- Semua timestamp disimpan dalam UTC di database, dikonversi ke WIB hanya di layer presentasi (Blade/Android).
