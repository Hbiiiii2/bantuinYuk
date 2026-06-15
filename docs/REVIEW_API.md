# REVIEW & RATING API Documentation

**Base URL:** `http://bantuinYuk.test/api/v1`

**Content-Type:** `application/json`

---

## Authentication

Semua endpoint memerlukan Bearer Token:

```
Authorization: Bearer <access_token>
```

---

## Review Endpoints

### POST /tasks/{id}/review

Buat review untuk task.

**Authorization:** Task owner only, task status must be COMPLETED

**Request:**
```json
{
    "rating": 5,
    "review": "Helper sangat profesional dan tepat waktu. Hasil kerja memuaskan!"
}
```

**Response (201):**
```json
{
    "success": true,
    "message": "Review created successfully",
    "data": {
        "id": 1,
        "task_id": 1,
        "user_id": 1,
        "helper_id": 2,
        "rating": 5,
        "review": "Helper sangat profesional dan tepat waktu. Hasil kerja memuaskan!",
        "user_name": "John Doe",
        "task_title": "Renovasi Kamar Mandi",
        "created_at": "2026-06-14 10:00:00"
    }
}
```

**Error Response (403):**
```json
{
    "success": false,
    "message": "You can only review your own tasks"
}
```

**Error Response (409):**
```json
{
    "success": false,
    "message": "Only completed tasks can be reviewed"
}
```

**Error Response (409):**
```json
{
    "success": false,
    "message": "This task already has a review"
}
```

**Error Response (422):**
```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "rating": "Rating must be between 1 and 5"
    }
}
```

---

### GET /tasks/{id}/review

Lihat review untuk task.

**Authorization:** Task owner atau assigned helper

**Response (200):**
```json
{
    "success": true,
    "message": "Success",
    "data": {
        "id": 1,
        "task_id": 1,
        "user_id": 1,
        "helper_id": 2,
        "rating": 5,
        "review": "Helper sangat profesional dan tepat waktu!",
        "user_name": "John Doe",
        "task_title": "Renovasi Kamar Mandi",
        "created_at": "2026-06-14 10:00:00"
    }
}
```

**Error Response (404):**
```json
{
    "success": false,
    "message": "No review found for this task"
}
```

---

### GET /helpers/reviews

Lihat semua reviews untuk helper.

**Authorization:** Helper only (own reviews)

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
                "task_id": 1,
                "user_id": 1,
                "helper_id": 2,
                "rating": 5,
                "review": "Sangat profesional!",
                "user_name": "John Doe",
                "task_title": "Renovasi Kamar Mandi",
                "created_at": "2026-06-14 10:00:00"
            },
            {
                "id": 2,
                "task_id": 2,
                "user_id": 3,
                "helper_id": 2,
                "rating": 4,
                "review": "Kerja bagus, sedikit telat.",
                "user_name": "Jane Smith",
                "task_title": "Pemasangan AC",
                "created_at": "2026-06-13 15:00:00"
            }
        ],
        "total": 2,
        "page": 1,
        "per_page": 20
    }
}
```

---

### GET /helpers/rating-summary

Lihat rating summary untuk helper.

**Authorization:** Helper only (own summary)

**Response (200):**
```json
{
    "success": true,
    "message": "Success",
    "data": {
        "average_rating": 4.50,
        "total_reviews": 10,
        "completed_tasks": 15,
        "distribution": {
            "1": 0,
            "2": 1,
            "3": 1,
            "4": 3,
            "5": 5
        }
    }
}
```

---

### GET /admin/reviews

Lihat semua reviews (admin only).

**Authorization:** Admin only

**Query Parameters:**

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `page` | int | 1 | Page number |
| `per_page` | int | 20 | Items per page |
| `helper_id` | int | - | Filter by helper ID |
| `rating` | int | - | Filter by rating |

**Response (200):**
```json
{
    "success": true,
    "message": "Success",
    "data": {
        "data": [
            {
                "id": 1,
                "task_id": 1,
                "user_id": 1,
                "helper_id": 2,
                "rating": 5,
                "review": "Sangat profesional!",
                "user_name": "John Doe",
                "task_title": "Renovasi Kamar Mandi",
                "helper_name": "Ahmad Helper",
                "created_at": "2026-06-14 10:00:00"
            }
        ],
        "total": 1,
        "page": 1,
        "per_page": 20
    }
}
```

---

## Rating Calculation

### Average Rating Formula

```
Average Rating = SUM(all ratings) / COUNT(all reviews)
```

**Example:**
- Review 1: rating = 5
- Review 2: rating = 4
- Review 3: rating = 3

```
Average Rating = (5 + 4 + 3) / 3 = 4.00
```

### Rating Updates

When a review is created:

1. **users.rating** - Updated with new average rating
2. **helper_profiles.completed_tasks** - Recalculated

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
    "message": "You can only review your own tasks"
}
```

### 404 Not Found
```json
{
    "success": false,
    "message": "No review found for this task"
}
```

### 409 Conflict
```json
{
    "success": false,
    "message": "This task already has a review"
}
```

### 422 Validation Failed
```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "rating": "Rating must be between 1 and 5"
    }
}
```

---

## Business Rules

| Rule | Description |
|------|-------------|
| Task status | Only COMPLETED tasks can be reviewed |
| Ownership | Only task owner can create review |
| Duplicate | One task can only have one review |
| Rating | Must be between 1 and 5 |
| Comment | Optional, max 2000 characters |

---

## Authorization Rules

| Endpoint | Role | Owner Check | Description |
|----------|------|-------------|-------------|
| POST /tasks/{id}/review | user | ✅ | Create review |
| GET /tasks/{id}/review | user/helper | ✅ | View task review |
| GET /helpers/reviews | helper | ✅ | View own reviews |
| GET /helpers/rating-summary | helper | ✅ | View own summary |
| GET /admin/reviews | admin | ❌ | View all reviews (with filters) |

---

**End of Documentation**
