# 07 — API Contract Laravel: Nusamind AI

**Base URL (contoh):** `https://nusamind-ai.com/api`
**Auth:** Laravel Sanctum (Bearer Token) — semua endpoint kecuali register/login wajib header:
```
Authorization: Bearer {token}
```

---

## 1. Auth

### POST `/auth/register`
**Request:**
```json
{
  "name": "Sari Wulandari",
  "email": "sari@mail.com",
  "password": "rahasia123",
  "password_confirmation": "rahasia123"
}
```
**Response 201:**
```json
{
  "message": "Registrasi berhasil",
  "user": { "id": 1, "name": "Sari Wulandari", "email": "sari@mail.com", "role": "user" },
  "token": "1|xxxxxxxxxxxx"
}
```

### POST `/auth/login`
**Request:** `{ "email": "sari@mail.com", "password": "rahasia123" }`
**Response 200:** sama seperti register (user + token)
**Response 401:** `{ "message": "Email atau password salah" }`

### POST `/auth/logout`
**Response 200:** `{ "message": "Berhasil logout" }`

### GET `/auth/me`
**Response 200:** data user yang sedang login

---

## 2. Profil Usaha

### POST `/business` (lengkapi onboarding)
**Request:**
```json
{ "business_name": "Warung Sari Rasa", "category_id": 1, "city": "Pekanbaru", "description": "Warung makan rumahan" }
```
**Response 201:** data business yang dibuat

### GET `/business/me`
**Response 200:** profil usaha milik user yang login

### PUT `/business/me`
**Request:** field yang ingin diupdate (partial update)

---

## 3. AI — Pencatatan Keuangan

### POST `/ai/finance/extract`
**Request:**
```json
{ "input_text": "Hari ini laku 5 porsi ayam geprek total 75 ribu, terus beli minyak goreng 20 ribu" }
```
**Response 200:**
```json
{
  "transactions": [
    { "type": "pemasukan", "item_name": "ayam geprek", "quantity": 5, "amount": 75000 },
    { "type": "pengeluaran", "item_name": "minyak goreng", "quantity": 1, "amount": 20000 }
  ],
  "note": "Silakan konfirmasi sebelum disimpan"
}
```
**Response 422 (gagal parsing):** `{ "message": "Nusamind belum yakin dengan inputmu, coba tulis ulang ya" }`

### POST `/transactions` (simpan setelah dikonfirmasi user)
**Request:**
```json
{
  "transactions": [
    { "type": "pemasukan", "item_name": "ayam geprek", "quantity": 5, "amount": 75000, "product_id": null, "source": "ai_text", "raw_input": "Hari ini laku 5 porsi ayam geprek..." }
  ]
}
```
**Response 201:** `{ "message": "Transaksi tersimpan", "count": 1 }`

### GET `/transactions?filter=today|week|month&type=pemasukan|pengeluaran`
**Response 200:** list transaksi + `summary: { total_income, total_expense, balance }`

### GET `/transactions/{id}` | PUT `/transactions/{id}` | DELETE `/transactions/{id}`
CRUD standar untuk koreksi manual.

---

## 4. AI — Konten & Copywriting

### POST `/ai/content/generate`
**Request:** `multipart/form-data`
| Field | Tipe | Keterangan |
|---|---|---|
| image | file | wajib, max 5MB |
| style | string | `formal` \| `gaul` \| `hard_selling` |
| product_id | int (nullable) | |

**Response 200:**
```json
{
  "caption_result": "Ayam geprek juara, pedasnya nampol! 🔥",
  "hashtags_result": ["#ayamgeprek", "#kulinerpekanbaru", "#pedasnampol"],
  "whatsapp_template_result": "Halo kak! Ayam Geprek Sari Rasa lagi promo nih, yuk order sekarang 😋",
  "content_id": 12
}
```

### POST `/ai/content/{id}/regenerate`
Sama strukturnya dengan generate, tapi memakai `image_path` yang sudah tersimpan.

### GET `/content-generations`
List histori konten user.

### POST `/content-reports`
**Request:** `{ "content_generation_id": 12, "reason": "Hasil tidak relevan dengan produk" }`

---

## 5. AI — Business Briefing

### GET `/business-insights/latest`
**Response 200:**
```json
{
  "period_start": "2026-06-15",
  "period_end": "2026-06-21",
  "narrative_text": "Halo Kak! Minggu ini ayam geprek paling laku, terjual 40 porsi...",
  "top_product": "Ayam Geprek",
  "low_stock_product": "Keripik Singkong"
}
```
*(Catatan: endpoint ini hanya membaca data hasil scheduled job, tidak memicu generate AI baru — sesuai business rule efisiensi biaya.)*

### GET `/business-insights/history`
List briefing-briefing sebelumnya.

---

## 6. Produk

### GET `/products` | POST `/products` | GET `/products/{id}` | PUT `/products/{id}` | DELETE `/products/{id}`
CRUD standar produk milik usaha user yang login (`business_id` diambil otomatis dari token, tidak dari input client — keamanan).

---

## 7. AI — Lokalisasi & Ekspor (Nice-to-have)

### POST `/ai/export/translate`
**Request:** `{ "product_id": 5, "target_language": "en" }`
**Response 200:**
```json
{ "original_text": "Keripik singkong renyah pedas manis", "translated_text": "Crispy cassava chips with a sweet and spicy flavor", "target_language": "en" }
```

---

## 8. Admin — Khusus Role `admin`/`superadmin` (prefix `/admin`)

### GET `/admin/dashboard/summary`
Statistik agregat (total user, total transaksi, pemakaian AI hari ini, dst).

### GET `/admin/users?status=active|suspended&search=`
List user dengan filter.

### PUT `/admin/users/{id}/suspend`
Set status user jadi `suspended`.

### PUT `/admin/users/{id}/activate`
Set status user jadi `active`.

### GET `/admin/ai-usage-logs?feature=&status=`
List log pemakaian AI.

### GET `/admin/content-reports?status=pending`
List laporan konten.

### PUT `/admin/content-reports/{id}/resolve`
**Request:** `{ "status": "reviewed" }` atau `{ "status": "removed" }`

### POST `/admin/notifications/broadcast`
**Request:** `{ "title": "Fitur baru!", "body": "Sekarang ada fitur ekspor multi-bahasa" }`

---

## 9. Format Error Standar

Semua error mengikuti format konsisten supaya mudah ditangani di Android:
```json
{ "message": "Pesan error yang ramah dibaca user", "errors": { "field": ["detail validasi"] } }
```

| Kode | Arti |
|---|---|
| 401 | Token tidak valid/expired |
| 403 | Tidak punya izin akses (role salah / akun suspended) |
| 422 | Validasi input gagal / AI gagal parsing |
| 429 | Rate limit AI harian tercapai |
| 503 | AI provider timeout |
