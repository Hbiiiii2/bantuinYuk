# HELPER API Documentation

**Base URL:** `http://bantuinYuk.test/api/v1`

**Content-Type:** `application/json`

---

## Authentication

Semua endpoint memerlukan Bearer Token dengan role **helper**:

```
Authorization: Bearer <access_token>
```

---

## Endpoints

### GET /helpers

List semua helpers.

**Authorization:** Helper only

**Query Parameters:**

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `page` | int | 1 | Page number |
| `per_page` | int | 20 | Items per page |
| `verification_status` | string | - | Filter by status |
| `search` | string | - | Search by name/skills |

**Response (200):**
```json
{
    "success": true,
    "message": "Success",
    "data": {
        "data": [
            {
                "id": 1,
                "user_id": 2,
                "bio": "Tukang bangunan berpengalaman",
                "skills": "Bangunan, Renovasi, Plumbing",
                "verification_status": "verified",
                "completed_tasks": 15,
                "name": "Jane Smith",
                "email": "jane@example.com",
                "photo": "photo.jpg",
                "rating": 4.5
            }
        ],
        "total": 50,
        "page": 1,
        "per_page": 20
    }
}
```

---

### GET /helpers/{id}

Detail profile helper.

**Authorization:** Helper only

**Response (200):**
```json
{
    "success": true,
    "message": "Success",
    "data": {
        "id": 1,
        "user_id": 2,
        "bio": "Tukang bangunan berpengalaman",
        "skills": "Bangunan, Renovasi, Plumbing",
        "ktp_number": "3201234567890001",
        "verification_status": "verified",
        "completed_tasks": 15,
        "user": {
            "id": 2,
            "name": "Jane Smith",
            "email": "jane@example.com",
            "phone": "081234567890",
            "role": "helper",
            "photo": "photo.jpg",
            "rating": 4.5
        },
        "location": {
            "id": 1,
            "helper_id": 2,
            "latitude": -6.2088,
            "longitude": 106.8456,
            "updated_at": "2026-06-13 12:00:00"
        }
    }
}
```

**Error Response (404):**
```json
{
    "success": false,
    "message": "Helper profile not found"
}
```

---

### GET /helpers/profile

Profile sendiri.

**Authorization:** Helper only

**Response (200):**
```json
{
    "success": true,
    "message": "Success",
    "data": {
        "id": 1,
        "user_id": 2,
        "bio": "Tukang bangunan berpengalaman",
        "skills": "Bangunan, Renovasi, Plumbing",
        "ktp_number": "3201234567890001",
        "verification_status": "verified",
        "completed_tasks": 15,
        "user": { ... },
        "location": { ... }
    }
}
```

---

### PUT /helpers/profile

Update profile sendiri.

**Authorization:** Helper only

**Request:**
```json
{
    "bio": "Tukang bangunan berpengalaman 10 tahun",
    "skills": "Bangunan, Renovasi, Plumbing, Electrical"
}
```

**Response (200):**
```json
{
    "success": true,
    "message": "Profile updated successfully",
    "data": { ... }
}
```

**Error Response (422):**
```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "bio": "Bio must not exceed 1000 characters"
    }
}
```

---

### PUT /helpers/location

Update lokasi.

**Authorization:** Helper only

**Request:**
```json
{
    "latitude": -6.2088,
    "longitude": 106.8456
}
```

**Response (200):**
```json
{
    "success": true,
    "message": "Location updated successfully",
    "data": {
        "id": 1,
        "helper_id": 2,
        "latitude": -6.2088,
        "longitude": 106.8456,
        "updated_at": "2026-06-13 12:00:00"
    }
}
```

**Error Response (422):**
```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "latitude": "Latitude must be between -90 and 90"
    }
}
```

---

### POST /helpers/verification

Submit KTP untuk verifikasi.

**Authorization:** Helper only

**Request:**
```json
{
    "ktp_number": "3201234567890001",
    "ktp_photo": "https://storage.example.com/ktp.jpg"
}
```

**Response (200):**
```json
{
    "success": true,
    "message": "Verification submitted successfully",
    "data": {
        "id": 1,
        "user_id": 2,
        "ktp_number": "3201234567890001",
        "verification_status": "pending",
        ...
    }
}
```

**Error Response (409):**
```json
{
    "success": false,
    "message": "Account is already verified"
}
```

**Error Response (409):**
```json
{
    "success": false,
    "message": "Verification is already being reviewed"
}
```

---

### GET /helpers/stats

Statistik helper.

**Authorization:** Helper only

**Response (200):**
```json
{
    "success": true,
    "message": "Success",
    "data": {
        "total_tasks": 25,
        "completed_tasks": 15,
        "in_progress_tasks": 3,
        "completed_count": 15,
        "verification_status": "verified"
    }
}
```

---

### GET /helpers/available-tasks

Tasks tersedia untuk di-accept.

**Authorization:** Helper only

**Query Parameters:**

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `page` | int | 1 | Page number |
| `per_page` | int | 20 | Items per page |

**Response (200):**
```json
{
    "success": true,
    "message": "Success",
    "data": {
        "data": [
            {
                "id": 1,
                "title": "Renovasi Kamar Mandi",
                "description": "...",
                "price": 500000,
                "status": "open",
                "category_name": "Bangunan",
                "user_name": "John Doe",
                "deadline_start": "2026-06-20 08:00:00",
                "deadline_end": "2026-06-25 17:00:00"
            }
        ],
        "total": 10,
        "page": 1,
        "per_page": 20
    }
}
```

---

### GET /helpers/my-tasks

Tasks yang ditugaskan ke helper.

**Authorization:** Helper only

**Query Parameters:**

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `page` | int | 1 | Page number |
| `per_page` | int | 20 | Items per page |
| `statuses` | string | - | Comma-separated statuses |

**Example:**
```
GET /api/v1/helpers/my-tasks?statuses=accepted,in_progress
```

**Response (200):**
```json
{
    "success": true,
    "message": "Success",
    "data": {
        "data": [
            {
                "id": 1,
                "title": "Renovasi Kamar Mandi",
                "status": "accepted",
                "helper_id": 2,
                ...
            }
        ],
        "total": 5,
        "page": 1,
        "per_page": 20
    }
}
```

---

### POST /helpers/tasks/{id}/accept

Accept task.

**Authorization:** Helper only

**Response (200):**
```json
{
    "success": true,
    "message": "Task accepted successfully",
    "data": {
        "id": 1,
        "helper_id": 2,
        "status": "accepted",
        "status_history": [
            {
                "status": "open",
                "created_at": "2026-06-13 12:00:00"
            },
            {
                "status": "accepted",
                "note": "Task accepted by helper",
                "created_by_name": "Jane Smith",
                "created_at": "2026-06-13 12:05:00"
            }
        ]
    }
}
```

**Error Response (409):**
```json
{
    "success": false,
    "message": "Task was just accepted by another helper"
}
```

**Error Response (409):**
```json
{
    "success": false,
    "message": "You cannot accept your own task"
}
```

---

### POST /helpers/tasks/{id}/start

Mulai pengerjaan task.

**Authorization:** Helper only (assigned helper)

**Response (200):**
```json
{
    "success": true,
    "message": "Task started successfully",
    "data": {
        "id": 1,
        "status": "in_progress",
        ...
    }
}
```

**Error Response (403):**
```json
{
    "success": false,
    "message": "You are not assigned to this task"
}
```

**Error Response (409):**
```json
{
    "success": false,
    "message": "Cannot start task in current status"
}
```

---

### POST /helpers/{id}/submit

Submit hasil pengerjaan task.

**Authorization:** Helper only (assigned helper)

**Response (200):**
```json
{
    "success": true,
    "message": "Task submitted successfully",
    "data": {
        "id": 1,
        "status": "waiting_approval",
        ...
    }
}
```

**Error Response (403):**
```json
{
    "success": false,
    "message": "You are not assigned to this task"
}
```

---

## Status Flow

### Task Status (Helper Perspective)

```
OPEN → ACCEPTED → IN_PROGRESS → WAITING_APPROVAL → COMPLETED
```

### Helper Actions on Task

| Status | Action | Result |
|--------|--------|--------|
| OPEN | accept | ACCEPTED |
| ACCEPTED | start | IN_PROGRESS |
| IN_PROGRESS | submit | WAITING_APPROVAL |

### Verification Status

| Status | Description | Action |
|--------|-------------|--------|
| null/empty | Belum submit | Can submit |
| pending | Sedang review | Cannot submit |
| verified | Sudah diverifikasi | Cannot submit |
| rejected | Ditolak | Can resubmit |

---

## Error Responses

### 401 Unauthorized
```json
{
    "success": false,
    "message": "Unauthorized"
}
```

### 403 Forbidden
```json
{
    "success": false,
    "message": "You are not assigned to this task"
}
```

### 404 Not Found
```json
{
    "success": false,
    "message": "Helper profile not found"
}
```

### 409 Conflict
```json
{
    "success": false,
    "message": "Task was just accepted by another helper"
}
```

### 422 Validation Failed
```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "latitude": "Latitude must be between -90 and 90"
    }
}
```

---

## Security Notes

1. **Race Condition Protection:** Accept task menggunakan atomic update untuk mencegah dua helper accept task yang sama
2. **Ownership Validation:** Semua action hanya bisa dilakukan pada task yang di-assign
3. **Status Validation:** Setiap action memiliki validasi status yang ketat
4. **Location Validation:** Latitude dan longitude divalidasi sebelum disimpan
5. **Verification Guard:** Tidak bisa submit verifikasi jika sudah verified atau sedang pending

---

**End of Documentation**
