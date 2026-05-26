# HafizPlus 2.0 — API v1 Error Response Standard

Dokumen ini menjelaskan format standar response error API HafizPlus v1.

Base URL lokal:

```text
http://127.0.0.1:8000/api/v1
```

---

# 1. Standard Error Shape

```json
{
  "success": false,
  "message": "Pesan error.",
  "errors": {},
  "status_code": 422,
  "meta": {
    "request_id": "uuid",
    "timestamp": "2026-05-26T00:00:00.000000Z"
  }
}
```

---

# 2. Field Definition

| Field | Type | Keterangan |
|---|---|---|
| `success` | boolean | Selalu `false` untuk error |
| `message` | string | Pesan utama yang bisa ditampilkan ke user |
| `errors` | object/array | Detail error field atau array kosong |
| `status_code` | integer | HTTP status code |
| `meta.request_id` | string | ID unik untuk tracing error |
| `meta.timestamp` | string | Waktu response dibuat dalam format ISO-8601 |

---

# 3. HTTP Status

| Status | Arti |
|---|---|
| `401` | Token tidak ada, token salah, atau token sudah expired/logout |
| `403` | User login tetapi tidak punya izin akses |
| `404` | Endpoint/resource tidak ditemukan |
| `405` | Method HTTP salah |
| `422` | Validasi gagal |
| `429` | Terlalu banyak request |
| `500` | Error server |

---

# 4. Error Examples

## 4.1 401 Unauthenticated

```json
{
  "success": false,
  "message": "Unauthenticated.",
  "errors": [],
  "status_code": 401,
  "meta": {
    "request_id": "uuid",
    "timestamp": "2026-05-26T00:00:00.000000Z"
  }
}
```

Penyebab umum:

- Header `Authorization` tidak dikirim
- Token salah
- Token sudah logout/revoked
- Format header salah

Format header yang benar:

```http
Authorization: Bearer {token}
Accept: application/json
```

---

## 4.2 403 Forbidden

```json
{
  "success": false,
  "message": "Anda tidak memiliki izin untuk mengakses resource ini.",
  "errors": [],
  "status_code": 403,
  "meta": {
    "request_id": "uuid",
    "timestamp": "2026-05-26T00:00:00.000000Z"
  }
}
```

Penyebab umum:

- Parent mencoba akses dashboard admin
- Santri mencoba akses dashboard guru
- Guru mencoba akses data santri yang bukan bimbingannya
- User login tetapi role tidak sesuai

---

## 4.3 404 Not Found

```json
{
  "success": false,
  "message": "Resource tidak ditemukan.",
  "errors": [],
  "status_code": 404,
  "meta": {
    "request_id": "uuid",
    "timestamp": "2026-05-26T00:00:00.000000Z"
  }
}
```

Penyebab umum:

- Endpoint salah
- ID resource tidak ada
- User tidak punya visibility ke resource tertentu dan API menyembunyikannya sebagai 404

---

## 4.4 405 Method Not Allowed

```json
{
  "success": false,
  "message": "Method HTTP tidak diizinkan untuk endpoint ini.",
  "errors": [],
  "status_code": 405,
  "meta": {
    "request_id": "uuid",
    "timestamp": "2026-05-26T00:00:00.000000Z"
  }
}
```

Contoh salah:

```http
POST /api/v1/surahs
```

Karena endpoint Surah saat ini hanya readonly:

```http
GET /api/v1/surahs
```

---

## 4.5 422 Validation Error

```json
{
  "success": false,
  "message": "Validasi gagal.",
  "errors": {
    "email": [
      "The email field is required."
    ],
    "password": [
      "The password field is required."
    ]
  },
  "status_code": 422,
  "meta": {
    "request_id": "uuid",
    "timestamp": "2026-05-26T00:00:00.000000Z"
  }
}
```

Contoh request invalid:

```http
GET /api/v1/surahs?juz=31
```

Karena `juz` valid hanya `1` sampai `30`.

---

## 4.6 429 Too Many Requests

```json
{
  "success": false,
  "message": "Terlalu banyak request. Silakan coba lagi nanti.",
  "errors": [],
  "status_code": 429,
  "meta": {
    "request_id": "uuid",
    "timestamp": "2026-05-26T00:00:00.000000Z"
  }
}
```

Penyebab:

- Client melewati rate limit `throttle:api`
- Default limit saat ini: 60 request per menit

---

## 4.7 500 Server Error

```json
{
  "success": false,
  "message": "Terjadi kesalahan pada server.",
  "errors": [],
  "status_code": 500,
  "meta": {
    "request_id": "uuid",
    "timestamp": "2026-05-26T00:00:00.000000Z"
  }
}
```

Di environment local/debug, response bisa menambahkan field debug:

```json
{
  "meta": {
    "request_id": "uuid",
    "timestamp": "2026-05-26T00:00:00.000000Z",
    "debug": {
      "exception": "ExceptionClass",
      "message": "Error message",
      "file": "path/to/file.php",
      "line": 123
    }
  }
}
```

Jangan tampilkan debug detail di production.

---

# 5. Mobile Client Handling

Pola handling sederhana:

```text
if success === true:
    render data
else:
    show message
    if errors exists:
        map field errors
```

Contoh pseudocode:

```js
if (response.success) {
  render(response.data);
} else {
  showToast(response.message);

  if (response.errors) {
    renderFieldErrors(response.errors);
  }
}
```

---

# 6. Debugging dengan Request ID

Jika terjadi error production:

1. Simpan `meta.request_id` dari response.
2. Cari `request_id` tersebut di log backend.
3. Cocokkan waktu error dengan `meta.timestamp`.
4. Jangan debugging berdasarkan screenshot saja.

---

# 7. Manual Error Test Checklist

| Test | Request | Expected |
|---|---|---|
| 401 | GET `/auth/me` tanpa token | `401 Unauthenticated` |
| 403 | Parent akses `/dashboard/admin` | `403 Forbidden` |
| 404 | GET `/endpoint-tidak-ada` | `404 Not Found` |
| 405 | POST `/surahs` | `405 Method Not Allowed` |
| 422 | GET `/surahs?juz=31` | `422 Validation Error` |
| 429 | Request berlebihan | `429 Too Many Requests` |