# HafizPlus 2.0 — API v1 Browser Testing Guide

Dokumen ini menjelaskan cara mengetes API v1 HafizPlus lewat browser menggunakan halaman development API Tester.

---

# 1. API Tester URL

Jalankan Laravel:

```powershell
php artisan serve
```

Buka browser:

```text
http://127.0.0.1:8000/dev/api-tester
```

---

# 2. Kenapa Tidak Bisa Langsung dari Address Bar?

Endpoint seperti ini:

```text
http://127.0.0.1:8000/api/v1/auth/me
```

tidak bisa dites langsung dari address bar jika butuh token, karena browser address bar tidak mengirim header:

```http
Authorization: Bearer {token}
```

Testing lewat browser harus memakai:

- Halaman API Tester
- DevTools Console dengan `fetch()`
- Postman/Insomnia sebagai alternatif

---

# 3. Urutan Test Manual

## 3.1 Login

Pilih akun:

```text
superadmin@hafizplus.test
```

Password:

```text
password123
```

Klik:

```text
Login
```

Jika sukses, token akan tampil di kotak token.

---

## 3.2 Test Auth Me

Klik:

```text
GET /auth/me
```

Expected:

```json
{
  "success": true,
  "message": "Data user aktif berhasil diambil.",
  "data": {},
  "status_code": 200
}
```

---

# 4. Test Matrix by Role

## 4.1 Super Admin

Login sebagai:

```text
superadmin@hafizplus.test
```

Test endpoint:

```text
GET /auth/me
GET /dashboard/admin
GET /students
GET /hafalan-records
GET /murajaah-records
GET /hafalan-targets
GET /surahs
GET /surahs/1/ayahs
```

Expected:

- Semua endpoint sukses
- Status `200`
- `success: true`
- Bisa melihat semua data akademik

---

## 4.2 Admin

Login sebagai:

```text
admin@hafizplus.test
```

Test endpoint:

```text
GET /auth/me
GET /dashboard/admin
GET /students
GET /hafalan-records
GET /murajaah-records
GET /hafalan-targets
GET /surahs
```

Expected:

- Bisa melihat semua data akademik
- Status `200`
- `success: true`

---

## 4.3 Guru

Login sebagai:

```text
guru@hafizplus.test
```

Test endpoint:

```text
GET /auth/me
GET /dashboard/teacher
GET /students
GET /hafalan-records
GET /murajaah-records
GET /hafalan-targets
```

Expected:

- Hanya melihat santri bimbingannya
- Tidak melihat santri guru lain
- Status `200`

Test forbidden:

```text
GET /dashboard/admin
GET /dashboard/parent
GET /dashboard/student
```

Expected:

```text
403
```

---

## 4.4 Orangtua

Login sebagai:

```text
orangtua@hafizplus.test
```

Test endpoint:

```text
GET /auth/me
GET /dashboard/parent
GET /students
GET /hafalan-records
GET /murajaah-records
GET /hafalan-targets
```

Expected:

- Hanya melihat anak yang terhubung
- Tidak melihat anak parent lain
- Status `200`

Test forbidden:

```text
GET /dashboard/admin
GET /dashboard/teacher
GET /dashboard/student
```

Expected:

```text
403
```

---

## 4.5 Santri

Login sebagai:

```text
santri@hafizplus.test
```

Test endpoint:

```text
GET /auth/me
GET /dashboard/student
GET /students
GET /hafalan-records
GET /murajaah-records
GET /hafalan-targets
```

Expected:

- Hanya melihat dirinya sendiri
- Tidak melihat santri lain
- Status `200`

Test forbidden:

```text
GET /dashboard/admin
GET /dashboard/teacher
GET /dashboard/parent
```

Expected:

```text
403
```

---

# 5. Error Testing

## 5.1 401 Unauthenticated

Klik:

```text
Clear Token
```

Lalu klik:

```text
GET /auth/me
```

Expected:

```json
{
  "success": false,
  "message": "Unauthenticated.",
  "errors": [],
  "status_code": 401
}
```

---

## 5.2 403 Forbidden

Login sebagai parent:

```text
orangtua@hafizplus.test
```

Lalu akses:

```text
GET /dashboard/admin
```

Expected:

```json
{
  "success": false,
  "message": "Anda tidak memiliki akses ke dashboard ini.",
  "errors": [],
  "status_code": 403
}
```

---

## 5.3 404 Not Found

Custom request:

```text
GET /api/v1/endpoint-tidak-ada
```

Expected:

```json
{
  "success": false,
  "message": "Resource tidak ditemukan.",
  "errors": [],
  "status_code": 404
}
```

---

## 5.4 405 Method Not Allowed

Custom request:

```text
POST /api/v1/surahs
```

Expected:

```json
{
  "success": false,
  "message": "Method HTTP tidak diizinkan untuk endpoint ini.",
  "errors": [],
  "status_code": 405
}
```

---

## 5.5 422 Validation Error

Custom request:

```text
GET /api/v1/surahs?juz=31
```

Expected:

```json
{
  "success": false,
  "message": "Validasi gagal.",
  "errors": {
    "juz": [
      "The juz field must not be greater than 30."
    ]
  },
  "status_code": 422
}
```

---

# 6. Manual Testing Checklist

| Test | Status |
|---|---|
| Login superadmin berhasil | ⬜ |
| Login admin berhasil | ⬜ |
| Login guru berhasil | ⬜ |
| Login orangtua berhasil | ⬜ |
| Login santri berhasil | ⬜ |
| `/auth/me` sukses setelah login | ⬜ |
| `/dashboard/admin` hanya admin/superadmin | ⬜ |
| `/dashboard/teacher` hanya guru | ⬜ |
| `/dashboard/parent` hanya orangtua | ⬜ |
| `/dashboard/student` hanya santri | ⬜ |
| Parent hanya melihat anaknya | ⬜ |
| Guru hanya melihat santri bimbingannya | ⬜ |
| Santri hanya melihat dirinya | ⬜ |
| Error 401 standar | ⬜ |
| Error 403 standar | ⬜ |
| Error 404 standar | ⬜ |
| Error 405 standar | ⬜ |
| Error 422 standar | ⬜ |

---

# 7. Catatan Keamanan

Halaman browser tester hanya untuk local development.

Route tester harus dibatasi seperti ini:

```php
if (app()->environment('local')) {
    Route::view('/dev/api-tester', 'dev.api-tester')
        ->name('dev.api-tester');
}
```

Jangan jadikan `/dev/api-tester` sebagai halaman production publik.