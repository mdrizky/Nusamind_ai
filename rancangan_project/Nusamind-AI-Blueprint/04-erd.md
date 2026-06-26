# 04 — Entity Relationship Diagram (ERD): Nusamind AI

## 1. Daftar Entity (Tabel)

| Entity | Fungsi |
|---|---|
| `users` | Akun pengguna (UMKM owner & admin) |
| `businesses` | Profil usaha milik user |
| `categories` | Kategori jenis usaha (Makanan, Fashion, Kerajinan, dst) |
| `products` | Produk/jasa yang dijual user, termasuk data stok |
| `transactions` | Catatan keuangan (pemasukan/pengeluaran) hasil AI atau manual |
| `content_generations` | Histori hasil AI caption & copywriting |
| `business_insights` | Hasil AI Business Briefing mingguan (cached) |
| `export_descriptions` | Histori hasil AI lokalisasi/ekspor produk |
| `ai_usage_logs` | Log pemakaian AI per user (untuk rate limit & monitoring admin) |
| `content_reports` | Laporan konten AI yang dianggap tidak pantas (moderasi) |
| `notifications` | Notifikasi sistem ke user (stok menipis, briefing baru, dll) |

## 2. Relasi Antar Tabel

```
users (1) ──────── (1) businesses
users (1) ──────── (N) transactions
users (1) ──────── (N) content_generations
users (1) ──────── (N) business_insights
users (1) ──────── (N) export_descriptions
users (1) ──────── (N) ai_usage_logs
users (1) ──────── (N) notifications
users (1) ──────── (N) content_reports   (sebagai pelapor)

businesses (1) ──── (N) products
categories (1) ──── (N) businesses

products (1) ──────  (N) transactions          (transaksi opsional terkait 1 produk)
products (1) ──────  (N) content_generations    (konten dibuat dari foto produk tertentu)
products (1) ──────  (N) export_descriptions

content_generations (1) ── (N) content_reports  (1 konten bisa dilaporkan beberapa kali)
```

## 3. Penjelasan Relasi

| Relasi | Tipe | Penjelasan |
|---|---|---|
| `users` → `businesses` | One-to-One | 1 user (role `user`) hanya punya 1 profil usaha di versi MVP (disederhanakan, belum multi-cabang) |
| `categories` → `businesses` | One-to-Many | 1 kategori dipakai banyak usaha (mis. banyak usaha kategori "Makanan & Minuman") |
| `businesses` → `products` | One-to-Many | 1 usaha bisa punya banyak produk |
| `users` → `transactions` | One-to-Many | 1 user punya banyak catatan transaksi |
| `products` → `transactions` | One-to-Many (nullable) | Transaksi bisa terkait produk spesifik (untuk hitung produk terlaris), atau `null` jika dicatat tanpa produk spesifik |
| `users` → `content_generations` | One-to-Many | 1 user punya banyak histori konten AI |
| `users` → `business_insights` | One-to-Many | 1 user punya banyak briefing (1 per minggu) |
| `content_generations` → `content_reports` | One-to-Many | 1 konten yang sama bisa dilaporkan lebih dari sekali sebelum dimoderasi |

## 4. Detail Entity & Atribut

### `users`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | BIGINT PK | |
| name | VARCHAR(150) | |
| email | VARCHAR(150) UNIQUE | |
| password | VARCHAR(255) | hashed |
| role | ENUM('user','admin','superadmin') | default `user` |
| status | ENUM('active','suspended') | default `active` |
| ai_quota_used_today | INT | reset harian via scheduler, atau dihitung dari `ai_usage_logs` |
| created_at / updated_at | TIMESTAMP | |

### `businesses`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | BIGINT PK | |
| user_id | BIGINT FK → users.id | |
| category_id | BIGINT FK → categories.id | |
| business_name | VARCHAR(150) | |
| city | VARCHAR(100) | |
| description | TEXT NULLABLE | |
| logo_path | VARCHAR(255) NULLABLE | |
| created_at / updated_at | TIMESTAMP | |

### `categories`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | BIGINT PK | |
| name | VARCHAR(100) | mis. "Makanan & Minuman", "Fashion", "Kerajinan" |
| icon | VARCHAR(100) NULLABLE | nama ikon untuk UI |

### `products`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | BIGINT PK | |
| business_id | BIGINT FK → businesses.id | |
| name | VARCHAR(150) | |
| price | BIGINT | dalam rupiah |
| stock | INT NULLABLE | nullable karena tidak semua usaha (jasa) punya stok |
| image_path | VARCHAR(255) NULLABLE | |
| description | TEXT NULLABLE | |
| created_at / updated_at | TIMESTAMP | |

### `transactions`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | BIGINT PK | |
| user_id | BIGINT FK → users.id | |
| product_id | BIGINT FK → products.id NULLABLE | |
| type | ENUM('pemasukan','pengeluaran') | |
| item_name | VARCHAR(150) | nama item dari hasil ekstraksi AI |
| quantity | INT NULLABLE | |
| amount | BIGINT | nominal rupiah |
| source | ENUM('ai_text','ai_voice','manual') | untuk tracking efektivitas fitur AI |
| raw_input | TEXT NULLABLE | kalimat asli user (audit trail AI) |
| transaction_date | DATE | |
| created_at / updated_at | TIMESTAMP | |

### `content_generations`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | BIGINT PK | |
| user_id | BIGINT FK → users.id | |
| product_id | BIGINT FK → products.id NULLABLE | |
| image_path | VARCHAR(255) | |
| style | ENUM('formal','gaul','hard_selling') | |
| caption_result | TEXT | |
| hashtags_result | JSON | array hashtag |
| whatsapp_template_result | TEXT | |
| created_at | TIMESTAMP | |

### `business_insights`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | BIGINT PK | |
| user_id | BIGINT FK → users.id | |
| period_start | DATE | |
| period_end | DATE | |
| narrative_text | TEXT | hasil narasi AI |
| top_product | VARCHAR(150) NULLABLE | |
| low_stock_product | VARCHAR(150) NULLABLE | |
| created_at | TIMESTAMP | |

### `export_descriptions`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | BIGINT PK | |
| user_id | BIGINT FK → users.id | |
| product_id | BIGINT FK → products.id | |
| target_language | ENUM('en','zh') | |
| original_text | TEXT | |
| translated_text | TEXT | |
| created_at | TIMESTAMP | |

### `ai_usage_logs`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | BIGINT PK | |
| user_id | BIGINT FK → users.id | |
| feature | ENUM('finance','content','briefing','export') | |
| tokens_used | INT NULLABLE | jika tersedia dari response provider |
| status | ENUM('success','failed','timeout') | |
| created_at | TIMESTAMP | |

### `content_reports`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | BIGINT PK | |
| content_generation_id | BIGINT FK → content_generations.id | |
| reported_by | BIGINT FK → users.id | |
| reason | TEXT | |
| status | ENUM('pending','reviewed','removed') | default `pending` |
| created_at | TIMESTAMP | |

### `notifications`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | BIGINT PK | |
| user_id | BIGINT FK → users.id | |
| title | VARCHAR(150) | |
| body | TEXT | |
| is_read | BOOLEAN | default false |
| created_at | TIMESTAMP | |
