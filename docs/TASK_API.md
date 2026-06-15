# TASK API Documentation

**Base URL:** `http://bantuinYuk.test/api/v1`

**Content-Type:** `application/json`

---

## Authentication

Semua endpoint memerlukan Bearer Token:

```
Authorization: Bearer <access_token>
```

---

## Endpoints

### POST /tasks

Buat task baru.

**Authorization:** User only

**Request:**
```json
{
    "title": "Butuh tukang bangunan",
    "description": "Butuh tukang bangunan untuk renovasi kamar mandi",
    "price": 500000,
    "category_id": 1,
    "deadline_start": "2026-06-20 08:00:00",
    "deadline_end": "2026-06-25 17:00:00",
    "location": "Jl. Sudirman No. 123"
}
```

**Response (201):**
```json
{
    "success": true,
    "message": "Task created successfully",
    "data": {
        "id": 1,
        "user_id": 1,
        "category_id": 1,
        "title": "Butuh tukang bangunan",
        "description": "Butuh tukang bangunan untuk renovasi kamar mandi",
        "price": 500000,
        "location": "Jl. Sudirman No. 123",
        "deadline_start": "2026-06-20 08:00:00",
        "deadline_end": "2026-06-25 17:00:00",
        "status": "open",
        "category_name": "Bangunan",
        "user_name": "John Doe",
        "status_history": [
            {
                "id": 1,
                "task_id": 1,
                "status": "open",
                "note": null,
                "created_by": 1,
                "created_by_name": "John Doe",
                "created_at": "2026-06-13 12:00:00"
            }
        ],
        "created_at": "2026-06-13 12:00:00",
        "updated_at": "2026-06-13 12:00:00"
    }
}
```

**Error Response (403):**
```json
{
    "success": false,
    "message": "Only users can create tasks"
}
```

**Error Response (422):**
```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "title": "Title must be 5-255 characters"
    }
}
```

---

### GET /tasks

List semua tasks dengan filter, search, dan pagination.

**Authorization:** All authenticated users

**Query Parameters:**

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `page` | int | 1 | Page number |
| `per_page` | int | 20 | Items per page |
| `status` | string | - | Filter by status |
| `category_id` | int | - | Filter by category |
| `search` | string | - | Search in title/description |
| `sort_by` | string | created_at | Sort field (created_at, price, deadline_end, status) |
| `sort_order` | string | DESC | Sort direction (ASC/DESC) |

**Example:**
```
GET /api/v1/tasks?page=1&per_page=10&status=open&search=tukang&sort_by=price&sort_order=ASC
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
                "title": "Butuh tukang bangunan",
                "description": "...",
                "price": 500000,
                "status": "open",
                "category_name": "Bangunan",
                "user_name": "John Doe",
                "created_at": "2026-06-13 12:00:00"
            }
        ],
        "total": 50,
        "page": 1,
        "per_page": 10,
        "total_pages": 5
    }
}
```

---

### GET /tasks/{id}

Detail task lengkap dengan status history.

**Authorization:** All authenticated users

**Response (200):**
```json
{
    "success": true,
    "message": "Success",
    "data": {
        "id": 1,
        "user_id": 1,
        "helper_id": 2,
        "category_id": 1,
        "title": "Butuh tukang bangunan",
        "description": "...",
        "price": 500000,
        "location": "Jl. Sudirman No. 123",
        "deadline_start": "2026-06-20 08:00:00",
        "deadline_end": "2026-06-25 17:00:00",
        "status": "in_progress",
        "category_name": "Bangunan",
        "user_name": "John Doe",
        "user_email": "john@example.com",
        "helper_name": "Jane Smith",
        "helper_email": "jane@example.com",
        "status_history": [
            {
                "id": 1,
                "task_id": 1,
                "status": "open",
                "note": null,
                "created_by": 1,
                "created_by_name": "John Doe",
                "created_at": "2026-06-13 12:00:00"
            },
            {
                "id": 2,
                "task_id": 1,
                "status": "accepted",
                "note": "Task accepted by helper",
                "created_by": 2,
                "created_by_name": "Jane Smith",
                "created_at": "2026-06-13 12:05:00"
            },
            {
                "id": 3,
                "task_id": 1,
                "status": "in_progress",
                "note": "Work started",
                "created_by": 2,
                "created_by_name": "Jane Smith",
                "created_at": "2026-06-14 08:00:00"
            }
        ],
        "created_at": "2026-06-13 12:00:00",
        "updated_at": "2026-06-14 08:00:00"
    }
}
```

**Error Response (404):**
```json
{
    "success": false,
    "message": "Task not found"
}
```

---

### PUT /tasks/{id}

Update task.

**Authorization:** Owner only, status OPEN/DRAFT

**Request:**
```json
{
    "title": "Judul baru",
    "description": "Deskripsi baru",
    "price": 600000,
    "location": " Lokasi baru"
}
```

**Response (200):**
```json
{
    "success": true,
    "message": "Task updated successfully",
    "data": { ... }
}
```

**Error Response (403):**
```json
{
    "success": false,
    "message": "You can only update your own tasks"
}
```

**Error Response (409):**
```json
{
    "success": false,
    "message": "Cannot update task in current status"
}
```

---

### DELETE /tasks/{id}

Cancel task.

**Authorization:** Owner only, status OPEN/ACCEPTED

**Response (200):**
```json
{
    "success": true,
    "message": "Task cancelled successfully",
    "data": {
        "id": 1,
        "status": "cancelled",
        ...
    }
}
```

**Error Response (403):**
```json
{
    "success": false,
    "message": "You can only cancel your own tasks"
}
```

**Error Response (409):**
```json
{
    "success": false,
    "message": "Cannot cancel task in current status"
}
```

---

### GET /tasks/my

Tasks milik user yang sedang login.

**Authorization:** All authenticated users

**Query Parameters:**

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `page` | int | 1 | Page number |
| `per_page` | int | 20 | Items per page |
| `status` | string | - | Filter by status |
| `date_from` | string | - | Filter start date (Y-m-d) |
| `date_to` | string | - | Filter end date (Y-m-d) |

**Example:**
```
GET /api/v1/tasks/my?status=open&date_from=2026-06-01&date_to=2026-06-30
```

**Response (200):**
```json
{
    "success": true,
    "message": "Success",
    "data": {
        "data": [...],
        "total": 5,
        "page": 1,
        "per_page": 20,
        "total_pages": 1
    }
}
```

---

### POST /tasks/{id}/complete

Selesaikan task.

**Authorization:** Owner only, status WAITING_APPROVAL

**Response (200):**
```json
{
    "success": true,
    "message": "Task completed successfully",
    "data": {
        "id": 1,
        "status": "completed",
        ...
    }
}
```

**Error Response (403):**
```json
{
    "success": false,
    "message": "Only task owner can complete the task"
}
```

**Error Response (409):**
```json
{
    "success": false,
    "message": "Cannot complete task in current status"
}
```

---

## Helper Task Endpoints

### POST /helpers/tasks/{id}/accept

Accept task oleh helper.

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
        ...
    }
}
```

**Error Response (403):**
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

---

## Status Flow

```
OPEN → ACCEPTED → IN_PROGRESS → WAITING_APPROVAL → COMPLETED
  ↓         ↓
CANCELLED  CANCELLED
```

### Status Transition Rules

| From | To | Actor |
|------|----|-------|
| OPEN | ACCEPTED | helper |
| OPEN | CANCELLED | owner |
| ACCEPTED | IN_PROGRESS | helper |
| ACCEPTED | CANCELLED | owner |
| IN_PROGRESS | WAITING_APPROVAL | helper |
| WAITING_APPROVAL | COMPLETED | owner |

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
    "message": "You can only update your own tasks"
}
```

### 404 Not Found
```json
{
    "success": false,
    "message": "Task not found"
}
```

### 409 Conflict
```json
{
    "success": false,
    "message": "Cannot update task in current status"
}
```

### 422 Validation Failed
```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "field": "Error message"
    }
}
```

### 500 Server Error
```json
{
    "success": false,
    "message": "An unexpected error occurred"
}
```

---

## Authorization Rules

| Endpoint | Role | Owner Check | Status Check |
|----------|------|-------------|--------------|
| POST /tasks | user | - | - |
| GET /tasks | all | - | - |
| GET /tasks/{id} | all | - | - |
| PUT /tasks/{id} | owner | ✅ | OPEN/DRAFT |
| DELETE /tasks/{id} | owner | ✅ | OPEN/ACCEPTED |
| GET /tasks/my | all | ✅ | - |
| POST /tasks/{id}/complete | owner | ✅ | WAITING_APPROVAL |
| POST /helpers/tasks/{id}/accept | helper | ✅ (not own task) | OPEN |
| POST /helpers/tasks/{id}/start | helper | ✅ (assigned) | ACCEPTED |
| POST /helpers/{id}/submit | helper | ✅ (assigned) | IN_PROGRESS |

---

**End of Documentation**
