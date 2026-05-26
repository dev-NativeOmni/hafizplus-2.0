# HafizPlus 2.0 — API v1 Reference

Dokumen ini berisi referensi endpoint API v1 HafizPlus 2.0.

API ini disiapkan untuk kebutuhan:

- Mobile app
- Frontend client terpisah
- Integrasi internal
- Development/testing tool

Base URL lokal:

```text
http://127.0.0.1:8000/api/v1
```

---

# 1. Authentication

API menggunakan Laravel Sanctum Personal Access Token.

Setelah login berhasil, client menerima token:

```json
{
  "access_token": "1|xxxxxxxxxxxxxxxxxxxxxxxx",
  "token_type": "Bearer"
}
```

Untuk endpoint yang membutuhkan login, kirim header:

```http
Authorization: Bearer {access_token}
Accept: application/json
```

---

# 2. Standard Success Response

```json
{
  "success": true,
  "message": "Pesan sukses.",
  "data": {},
  "status_code": 200
}
```

Contoh response dengan pagination:

```json
{
  "success": true,
  "message": "Data berhasil diambil.",
  "data": {},
  "status_code": 200,
  "meta": {
    "pagination": {
      "current_page": 1,
      "from": 1,
      "last_page": 1,
      "per_page": 15,
      "to": 15,
      "total": 100
    }
  }
}
```

---

# 3. Standard Error Response

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

## Common HTTP Status

| Status | Arti |
|---|---|
| `200` | Request sukses |
| `401` | Belum login, token tidak ada, atau token tidak valid |
| `403` | User login tetapi tidak punya izin |
| `404` | Resource atau endpoint tidak ditemukan |
| `405` | Method HTTP tidak valid |
| `422` | Validasi gagal |
| `429` | Terlalu banyak request |
| `500` | Server error |

---

# 4. Auth Endpoints

## 4.1 Login

```http
POST /auth/login
```

### Body

```json
{
  "email": "superadmin@hafizplus.test",
  "password": "password123",
  "device_name": "Windows API Test"
}
```

### Success Response

```json
{
  "success": true,
  "message": "Login berhasil.",
  "data": {
    "access_token": "1|xxxxxxxxxxxxxxxxxxxxxxxx",
    "token_type": "Bearer",
    "user": {
      "id": 1,
      "name": "Super Admin",
      "email": "superadmin@hafizplus.test",
      "role": "super_admin"
    }
  },
  "status_code": 200
}
```

---

## 4.2 Current User

```http
GET /auth/me
```

### Headers

```http
Authorization: Bearer {token}
Accept: application/json
```

### Success Response

```json
{
  "success": true,
  "message": "Data user aktif berhasil diambil.",
  "data": {
    "user": {
      "id": 1,
      "name": "Super Admin",
      "email": "superadmin@hafizplus.test",
      "role": {
        "id": 1,
        "name": "super_admin",
        "display_name": "Super Admin"
      }
    }
  },
  "status_code": 200
}
```

---

## 4.3 Logout

```http
POST /auth/logout
```

### Headers

```http
Authorization: Bearer {token}
Accept: application/json
```

### Success Response

```json
{
  "success": true,
  "message": "Logout berhasil.",
  "data": null,
  "status_code": 200
}
```

---

# 5. Dashboard Endpoints

Semua dashboard endpoint membutuhkan token.

## 5.1 Admin Dashboard

```http
GET /dashboard/admin
```

Role yang boleh akses:

- `super_admin`
- `admin`

### Response Data

```json
{
  "dashboard": "admin",
  "role": "super_admin",
  "generated_at": "2026-05-26T00:00:00.000000Z",
  "summary": {
    "total_students": 10,
    "active_students": 10,
    "total_programs": 2,
    "total_class_rooms": 3,
    "total_teachers": 5,
    "total_parents": 8,
    "total_users": 30,
    "total_hafalan_records": 100,
    "passed_hafalan_records": 90,
    "repeat_hafalan_records": 10,
    "average_hafalan_score": 86.5,
    "total_murajaah_records": 80,
    "passed_murajaah_records": 70,
    "repeat_murajaah_records": 10,
    "average_murajaah_score": 84.2,
    "total_targets": 20,
    "active_targets": 5,
    "completed_targets": 10,
    "missed_targets": 5,
    "unread_notifications": 0
  },
  "today": {
    "date": "2026-05-26",
    "hafalan_submitted_today": 0,
    "murajaah_reviewed_today": 0,
    "targets_due_today": 0,
    "targets_completed_today": 0
  },
  "progress": {
    "hafalan_pass_rate": 90,
    "murajaah_pass_rate": 87.5,
    "target_completion_rate": 50
  },
  "recent": {
    "latest_hafalan_records": [],
    "latest_murajaah_records": [],
    "latest_targets": []
  },
  "alerts": {
    "overdue_targets": 0,
    "hafalan_needs_improvement": 0,
    "murajaah_needs_improvement": 0
  }
}
```

---

## 5.2 Teacher Dashboard

```http
GET /dashboard/teacher
```

Role yang boleh akses:

- `teacher`

Catatan:

Dashboard ini hanya menghitung data santri yang dibimbing guru login.

---

## 5.3 Parent Dashboard

```http
GET /dashboard/parent
```

Role yang boleh akses:

- `parent`

Catatan:

Dashboard ini hanya menghitung data anak yang terhubung ke parent login melalui relasi `parent_student`.

---

## 5.4 Student Dashboard

```http
GET /dashboard/student
```

Role yang boleh akses:

- `student`

Catatan:

Dashboard ini hanya menghitung data milik santri login.

---

# 6. Surah & Ayah Endpoints

Semua endpoint Surah/Ayah membutuhkan token pada fase ini.

## 6.1 List Surah

```http
GET /surahs
```

### Query Parameters

| Parameter | Type | Required | Keterangan |
|---|---|---|---|
| `search` | string | no | Cari berdasarkan nama latin, arab, atau nomor |
| `juz` | integer | no | Filter juz 1–30 |
| `per_page` | integer | no | Default 114, max 114 |

### Example

```http
GET /surahs?search=Baqarah
```

### Success Response

```json
{
  "success": true,
  "message": "Data surah berhasil diambil.",
  "data": {
    "surahs": [
      {
        "id": 1,
        "number": 1,
        "name_ar": "الفاتحة",
        "name_latin": "Al-Fatihah",
        "total_ayah": 7,
        "juz_start": 1,
        "juz_end": 1,
        "ayahs_count": 7
      }
    ]
  },
  "status_code": 200,
  "meta": {
    "pagination": {
      "current_page": 1,
      "from": 1,
      "last_page": 1,
      "per_page": 114,
      "to": 114,
      "total": 114
    },
    "filters": {
      "search": "",
      "juz": null
    }
  }
}
```

---

## 6.2 Detail Surah

```http
GET /surahs/{surah}
```

### Example

```http
GET /surahs/1
```

---

## 6.3 Ayahs by Surah

```http
GET /surahs/{surah}/ayahs
```

### Example

```http
GET /surahs/1/ayahs
```

### Success Response

```json
{
  "success": true,
  "message": "Data ayat surah berhasil diambil.",
  "data": {
    "surah": {
      "id": 1,
      "number": 1,
      "name_ar": "الفاتحة",
      "name_latin": "Al-Fatihah",
      "total_ayah": 7,
      "juz_start": 1,
      "juz_end": 1
    },
    "ayahs": [
      {
        "id": 1,
        "surah_id": 1,
        "ayah_number": 1,
        "juz": 1,
        "text_ar": "...",
        "translation_id": "..."
      }
    ]
  },
  "status_code": 200,
  "meta": {
    "total": 7
  }
}
```

---

# 7. Student Endpoints

## 7.1 List Students

```http
GET /students
```

### Access Rules

| Role | Data yang terlihat |
|---|---|
| `super_admin` | Semua santri |
| `admin` | Semua santri |
| `teacher` | Santri bimbingannya |
| `parent` | Anak yang terhubung |
| `student` | Dirinya sendiri |

### Query Parameters

| Parameter | Type | Required | Keterangan |
|---|---|---|---|
| `search` | string | no | Cari nama, nomor santri, email |
| `status` | string | no | Filter status |
| `class_room_id` | integer | no | Filter kelas |
| `teacher_id` | integer | no | Filter guru |
| `per_page` | integer | no | Default 15, max 50 |

### Example

```http
GET /students?search=Ahmad&per_page=10
```

---

## 7.2 Detail Student

```http
GET /students/{student}
```

### Example

```http
GET /students/1
```

Jika user tidak punya akses ke santri tersebut, response bisa `404`.

---

## 7.3 Student Progress

```http
GET /students/{student}/progress
```

### Example

```http
GET /students/1/progress
```

### Response Data

```json
{
  "student": {},
  "quran": {
    "total_ayahs": 6236,
    "memorized_ayahs": 20,
    "progress_percentage": 0.32
  },
  "hafalan": {
    "total_records": 10,
    "passed_records": 8,
    "repeat_records": 2,
    "average_score": 86.5,
    "latest": {}
  },
  "murajaah": {
    "total_records": 8,
    "passed_records": 7,
    "repeat_records": 1,
    "average_score": 84.2,
    "latest": {}
  },
  "targets": {
    "total_targets": 5,
    "active_targets": 2,
    "completed_targets": 2,
    "missed_targets": 1,
    "latest": {}
  }
}
```

---

# 8. Hafalan Record Endpoints

## 8.1 List Hafalan Records

```http
GET /hafalan-records
```

### Access Rules

Mengikuti visibility santri:

| Role | Data yang terlihat |
|---|---|
| `super_admin` | Semua record |
| `admin` | Semua record |
| `teacher` | Record santri bimbingannya |
| `parent` | Record anaknya |
| `student` | Record miliknya sendiri |

### Query Parameters

| Parameter | Type | Required | Keterangan |
|---|---|---|---|
| `student_id` | integer | no | Filter santri |
| `teacher_id` | integer | no | Filter guru |
| `surah_id` | integer | no | Filter surah |
| `status` | string | no | `passed`, `repeat`, `needs_improvement` |
| `submission_type` | string | no | `new`, `continuation`, `revision` |
| `from` | date | no | Filter tanggal mulai |
| `to` | date | no | Filter tanggal akhir |
| `search` | string | no | Cari catatan, santri, surah, guru |
| `per_page` | integer | no | Default 15, max 50 |

### Example

```http
GET /hafalan-records?status=passed&from=2026-05-01&to=2026-05-31
```

---

## 8.2 Detail Hafalan Record

```http
GET /hafalan-records/{hafalanRecord}
```

### Example

```http
GET /hafalan-records/1
```

---

# 9. Murajaah Record Endpoints

## 9.1 List Murajaah Records

```http
GET /murajaah-records
```

### Access Rules

Mengikuti visibility santri.

### Query Parameters

| Parameter | Type | Required | Keterangan |
|---|---|---|---|
| `student_id` | integer | no | Filter santri |
| `teacher_id` | integer | no | Filter guru |
| `surah_id` | integer | no | Filter surah |
| `status` | string | no | `passed`, `repeat`, `needs_improvement` |
| `from` | date | no | Filter tanggal mulai berdasarkan `reviewed_at` |
| `to` | date | no | Filter tanggal akhir berdasarkan `reviewed_at` |
| `search` | string | no | Cari catatan, santri, surah, guru |
| `per_page` | integer | no | Default 15, max 50 |

### Example

```http
GET /murajaah-records?status=repeat&per_page=10
```

---

## 9.2 Detail Murajaah Record

```http
GET /murajaah-records/{murajaahRecord}
```

### Example

```http
GET /murajaah-records/1
```

---

# 10. Hafalan Target Endpoints

## 10.1 List Hafalan Targets

```http
GET /hafalan-targets
```

### Access Rules

Mengikuti visibility santri.

### Query Parameters

| Parameter | Type | Required | Keterangan |
|---|---|---|---|
| `student_id` | integer | no | Filter santri |
| `teacher_id` | integer | no | Filter guru |
| `surah_id` | integer | no | Filter surah |
| `status` | string | no | `active`, `planned`, `in_progress`, `completed`, `missed`, `cancelled` |
| `from` | date | no | Filter `target_date` mulai |
| `to` | date | no | Filter `target_date` akhir |
| `search` | string | no | Cari catatan, santri, surah, guru |
| `per_page` | integer | no | Default 15, max 50 |

### Example

```http
GET /hafalan-targets?status=active
```

---

## 10.2 Detail Hafalan Target

```http
GET /hafalan-targets/{hafalanTarget}
```

### Example

```http
GET /hafalan-targets/1
```

---

# 11. PowerShell Examples

## Login

```powershell
$body = @{
    email = "superadmin@hafizplus.test"
    password = "password123"
    device_name = "Windows API Test"
} | ConvertTo-Json

$login = Invoke-RestMethod `
    -Uri "http://127.0.0.1:8000/api/v1/auth/login" `
    -Method POST `
    -Headers @{ Accept = "application/json" } `
    -ContentType "application/json" `
    -Body $body

$token = $login.data.access_token
```

## GET Auth Me

```powershell
Invoke-RestMethod `
    -Uri "http://127.0.0.1:8000/api/v1/auth/me" `
    -Method GET `
    -Headers @{
        Accept = "application/json"
        Authorization = "Bearer $token"
    }
```

## GET Students

```powershell
Invoke-RestMethod `
    -Uri "http://127.0.0.1:8000/api/v1/students" `
    -Method GET `
    -Headers @{
        Accept = "application/json"
        Authorization = "Bearer $token"
    }
```

---

# 12. JavaScript Fetch Examples

## Login

```js
const response = await fetch('/api/v1/auth/login', {
  method: 'POST',
  headers: {
    Accept: 'application/json',
    'Content-Type': 'application/json'
  },
  body: JSON.stringify({
    email: 'superadmin@hafizplus.test',
    password: 'password123',
    device_name: 'Browser Client'
  })
});

const data = await response.json();
const token = data.data.access_token;
```

## Authenticated GET

```js
const response = await fetch('/api/v1/students', {
  method: 'GET',
  headers: {
    Accept: 'application/json',
    Authorization: `Bearer ${token}`
  }
});

const data = await response.json();
```

---

# 13. Security Notes

## Token Handling

Client harus menyimpan token secara aman.

Untuk mobile:

- Gunakan secure storage
- Jangan simpan token di plain text
- Hapus token saat logout

Untuk browser development:

- Boleh simpan di localStorage untuk testing
- Jangan pakai pola ini untuk production app sensitif

## Role-Based Data Visibility

API HafizPlus membatasi data berdasarkan role:

| Role | Visibility |
|---|---|
| `super_admin` | Semua data |
| `admin` | Semua data akademik |
| `teacher` | Santri bimbingannya |
| `parent` | Anak yang terhubung |
| `student` | Dirinya sendiri |

Jika user mencoba membuka resource di luar haknya, response harus `403` atau `404`.

## Rate Limiting

API memakai middleware:

```text
throttle:api
```

Default limiter:

```text
60 request per minute
```

---

# 14. Current API v1 Endpoint List

```text
POST /api/v1/auth/login
GET  /api/v1/auth/me
POST /api/v1/auth/logout

GET  /api/v1/dashboard/admin
GET  /api/v1/dashboard/teacher
GET  /api/v1/dashboard/parent
GET  /api/v1/dashboard/student

GET  /api/v1/surahs
GET  /api/v1/surahs/{surah}
GET  /api/v1/surahs/{surah}/ayahs

GET  /api/v1/students
GET  /api/v1/students/{student}
GET  /api/v1/students/{student}/progress

GET  /api/v1/hafalan-records
GET  /api/v1/hafalan-records/{hafalanRecord}

GET  /api/v1/murajaah-records
GET  /api/v1/murajaah-records/{murajaahRecord}

GET  /api/v1/hafalan-targets
GET  /api/v1/hafalan-targets/{hafalanTarget}
```