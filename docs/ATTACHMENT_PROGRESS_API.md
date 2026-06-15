# ATTACHMENT & PROGRESS API Documentation

**Base URL:** `http://bantuinYuk.test/api/v1`

**Content-Type:** `application/json` (except file upload: `multipart/form-data`)

---

## Authentication

Semua endpoint memerlukan Bearer Token:

```
Authorization: Bearer <access_token>
```

---

## Attachment Endpoints

### POST /tasks/{id}/attachments

Upload attachment ke task.

**Authorization:** Task owner atau assigned helper

**Content-Type:** `multipart/form-data`

**Form Data:**

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `file` | file | ✅ | File to upload |

**Allowed File Types:**

| Category | Extensions |
|----------|------------|
| Images | jpg, jpeg, png, gif, webp |
| Videos | mp4, mpeg, webm |
| Documents | pdf, doc, docx, xls, xlsx, txt |

**Max File Size:** 10MB

**Response (201):**
```json
{
    "success": true,
    "message": "Attachment uploaded successfully",
    "data": {
        "id": 1,
        "task_id": 1,
        "user_id": 1,
        "file_name": "photo.jpg",
        "file_path": "uploads/tasks/task_1_1718312345_abc123.jpg",
        "file_type": "image/jpeg",
        "file_size": 1024000,
        "created_at": "2026-06-13 12:00:00"
    }
}
```

**Error Response (403):**
```json
{
    "success": false,
    "message": "You can only upload attachments to your own tasks"
}
```

**Error Response (422):**
```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "file": "File size must not exceed 10MB"
    }
}
```

**cURL Example:**
```bash
curl -X POST http://bantuinYuk.test/api/v1/tasks/1/attachments \
  -H "Authorization: Bearer <token>" \
  -F "file=@/path/to/photo.jpg"
```

---

### GET /tasks/{id}/attachments

Lihat semua attachments untuk task.

**Authorization:** Task owner atau assigned helper

**Response (200):**
```json
{
    "success": true,
    "message": "Success",
    "data": [
        {
            "id": 1,
            "task_id": 1,
            "user_id": 1,
            "file_name": "photo.jpg",
            "file_path": "uploads/tasks/task_1_1718312345_abc123.jpg",
            "file_type": "image/jpeg",
            "file_size": 1024000,
            "created_at": "2026-06-13 12:00:00"
        },
        {
            "id": 2,
            "task_id": 1,
            "user_id": 2,
            "file_name": "document.pdf",
            "file_path": "uploads/tasks/task_1_1718312456_def456.pdf",
            "file_type": "application/pdf",
            "file_size": 2048000,
            "created_at": "2026-06-13 12:05:00"
        }
    ]
}
```

---

### DELETE /tasks/{id}/attachments/{attachmentId}

Hapus attachment.

**Authorization:** Uploader atau task owner

**Response (200):**
```json
{
    "success": true,
    "message": "Attachment deleted successfully",
    "data": true
}
```

**Error Response (403):**
```json
{
    "success": false,
    "message": "You can only delete your own attachments"
}
```

---

## Progress Endpoints

### POST /helpers/tasks/{id}/progress

Buat progress baru untuk task.

**Authorization:** Assigned helper only

**Request:**
```json
{
    "description": "Sudah selesai pemasangan keramik untuk kamar mandi. Menunggu pengeringan.",
    "attachment_ids": [1, 2]
}
```

**Response (201):**
```json
{
    "success": true,
    "message": "Progress created successfully",
    "data": {
        "id": 1,
        "task_id": 1,
        "helper_id": 2,
        "description": "Sudah selesai pemasangan keramik untuk kamar mandi. Menunggu pengeringan.",
        "attachment": "[1,2]",
        "attachment_ids": [1, 2],
        "status": "active",
        "helper_name": "Jane Smith",
        "attachments": [
            {
                "id": 1,
                "file_name": "progress1.jpg",
                "file_path": "uploads/tasks/task_1_1718312345_abc123.jpg",
                "file_type": "image/jpeg"
            },
            {
                "id": 2,
                "file_name": "progress2.jpg",
                "file_path": "uploads/tasks/task_1_1718312456_def456.jpg",
                "file_type": "image/jpeg"
            }
        ],
        "created_at": "2026-06-13 12:00:00"
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
    "message": "Cannot add progress in current task status"
}
```

**Notes:**
- Task status ACCEPTED → IN_PROGRESS when first progress is created
- Status history is automatically created

---

### GET /helpers/tasks/{id}/progress

Lihat semua progress untuk task.

**Authorization:** Task owner atau assigned helper

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
                "helper_id": 2,
                "description": "Sudah selesai pemasangan keramik.",
                "attachment_ids": [1, 2],
                "status": "active",
                "helper_name": "Jane Smith",
                "attachments": [
                    {
                        "id": 1,
                        "file_name": "progress1.jpg",
                        "file_type": "image/jpeg"
                    }
                ],
                "created_at": "2026-06-13 12:00:00"
            },
            {
                "id": 2,
                "task_id": 1,
                "helper_id": 2,
                "description": "Sedang proses pengeringan.",
                "attachment_ids": [],
                "status": "active",
                "helper_name": "Jane Smith",
                "attachments": [],
                "created_at": "2026-06-14 12:00:00"
            }
        ],
        "total": 2,
        "page": 1,
        "per_page": 20
    }
}
```

---

### POST /helpers/tasks/{id}/attachments

Upload attachment ke task (helper version).

**Authorization:** Assigned helper only

**Content-Type:** `multipart/form-data`

**Response (201):**
```json
{
    "success": true,
    "message": "Attachment uploaded successfully",
    "data": {
        "id": 3,
        "task_id": 1,
        "user_id": 2,
        "file_name": "helper_photo.jpg",
        "file_path": "uploads/tasks/task_1_1718312789_ghi789.jpg",
        "file_type": "image/jpeg",
        "file_size": 512000,
        "created_at": "2026-06-13 12:10:00"
    }
}
```

---

## Error Responses

### 400 Bad Request
```json
{
    "success": false,
    "message": "No file uploaded"
}
```

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
    "message": "You can only upload attachments to your own tasks"
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
    "message": "Cannot add progress in current task status"
}
```

### 422 Validation Failed
```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "file": "File type not allowed. Allowed: images, videos, documents"
    }
}
```

---

## File Upload Notes

### Supported Formats

| Format | MIME Type | Extension |
|--------|-----------|-----------|
| JPEG | image/jpeg | jpg, jpeg |
| PNG | image/png | png |
| GIF | image/gif | gif |
| WebP | image/webp | webp |
| MP4 | video/mp4 | mp4 |
| MPEG | video/mpeg | mpeg |
| WebM | video/webm | webm |
| PDF | application/pdf | pdf |
| Word | application/msword | doc |
| Word (OOXML) | application/vnd.openxmlformats-officedocument.wordprocessingml.document | docx |
| Excel | application/vnd.ms-excel | xls |
| Excel (OOXML) | application/vnd.openxmlformats-officedocument.spreadsheetml.sheet | xlsx |
| Text | text/plain | txt |

### File Storage

- **Path:** `public/uploads/tasks/`
- **Naming:** `task_{taskId}_{timestamp}_{random}.{ext}`
- **Example:** `task_1_1718312345_a1b2c3d4.jpg`

### Security

- MIME type verified using `finfo_file()`
- Extension checked against whitelist
- File size limited to 10MB
- Unique filename prevents path traversal
- Ownership validated before upload/delete

---

**End of Documentation**
