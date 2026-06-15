# DISPUTE API Documentation

**Base URL:** `http://bantuinYuk.test/api/v1`

**Content-Type:** `application/json`

---

## Authentication

Semua endpoint memerlukan Bearer Token:

```
Authorization: Bearer <access_token>
```

---

## User/Helper Endpoints

### POST /disputes

Create a new dispute.

**Request:**
```json
{
    "task_id": 1,
    "reason": "Work quality issue",
    "description": "The work done does not meet the agreed standards..."
}
```

**Response (201):**
```json
{
    "success": true,
    "message": "Dispute created successfully",
    "data": {
        "id": 1,
        "task_id": 1,
        "user_id": 2,
        "helper_id": 3,
        "reason": "Work quality issue",
        "evidence_file": "The work done does not meet the agreed standards...",
        "admin_note": null,
        "status": "open",
        "resolved_by": null,
        "resolved_at": null,
        "task_title": "Renovasi Kamar Mandi",
        "creator_name": "John Doe",
        "helper_name": "Ahmad Helper",
        "created_at": "2026-06-14 10:00:00"
    }
}
```

**Error Response (403):**
```json
{
    "success": false,
    "message": "You are not involved in this task"
}
```

**Error Response (409):**
```json
{
    "success": false,
    "message": "Dispute can only be created for tasks with status waiting_approval or completed"
}
```

**Error Response (409):**
```json
{
    "success": false,
    "message": "This task already has an active dispute"
}
```

---

### GET /disputes

List disputes for current user.

**Query Parameters:**

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `page` | int | 1 | Page number |
| `per_page` | int | 20 | Items per page |
| `status` | string | - | Filter: open, under_review, resolved, rejected |
| `search` | string | - | Search in task title or reason |

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
                "user_id": 2,
                "helper_id": 3,
                "reason": "Work quality issue",
                "status": "open",
                "task_title": "Renovasi Kamar Mandi",
                "creator_name": "John Doe",
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

### GET /disputes/{id}

Get dispute detail.

**Response (200):**
```json
{
    "success": true,
    "message": "Success",
    "data": {
        "id": 1,
        "task_id": 1,
        "user_id": 2,
        "helper_id": 3,
        "reason": "Work quality issue",
        "evidence_file": "The work done does not meet the agreed standards...",
        "admin_note": null,
        "status": "open",
        "resolved_by": null,
        "resolved_at": null,
        "task_title": "Renovasi Kamar Mandi",
        "task_status": "waiting_approval",
        "creator_name": "John Doe",
        "helper_name": "Ahmad Helper",
        "resolved_by_name": null,
        "created_at": "2026-06-14 10:00:00"
    }
}
```

**Error Response (403):**
```json
{
    "success": false,
    "message": "Forbidden"
}
```

---

## Admin Endpoints

### GET /admin/disputes

List all disputes (admin only).

**Query Parameters:**

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `page` | int | 1 | Page number |
| `per_page` | int | 20 | Items per page |
| `status` | string | - | Filter: open, under_review, resolved, rejected |
| `search` | string | - | Search in task title or reason |

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
                "user_id": 2,
                "helper_id": 3,
                "reason": "Work quality issue",
                "status": "open",
                "task_title": "Renovasi Kamar Mandi",
                "creator_name": "John Doe",
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

### POST /admin/disputes/{id}/review

Review dispute (OPEN -> UNDER_REVIEW).

**Response (200):**
```json
{
    "success": true,
    "message": "Dispute is now under review",
    "data": {
        "id": 1,
        "task_id": 1,
        "user_id": 2,
        "helper_id": 3,
        "reason": "Work quality issue",
        "status": "under_review",
        "task_title": "Renovasi Kamar Mandi",
        "creator_name": "John Doe",
        "helper_name": "Ahmad Helper",
        "created_at": "2026-06-14 10:00:00"
    }
}
```

**Error Response (409):**
```json
{
    "success": false,
    "message": "Only open disputes can be reviewed"
}
```

---

### POST /admin/disputes/{id}/resolve

Resolve dispute (UNDER_REVIEW -> RESOLVED).

**Request:**
```json
{
    "resolution": "After review, the helper is required to redo the work according to specifications."
}
```

**Response (200):**
```json
{
    "success": true,
    "message": "Dispute resolved successfully",
    "data": {
        "id": 1,
        "task_id": 1,
        "user_id": 2,
        "helper_id": 3,
        "reason": "Work quality issue",
        "admin_note": "After review, the helper is required to redo the work according to specifications.",
        "status": "resolved",
        "resolved_by": 1,
        "resolved_at": "2026-06-14 12:00:00",
        "task_title": "Renovasi Kamar Mandi",
        "creator_name": "John Doe",
        "helper_name": "Ahmad Helper",
        "resolved_by_name": "Admin",
        "created_at": "2026-06-14 10:00:00"
    }
}
```

**Error Response (422):**
```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "resolution": "Resolution is required"
    }
}
```

**Error Response (409):**
```json
{
    "success": false,
    "message": "Only under review disputes can be resolved"
}
```

---

### POST /admin/disputes/{id}/reject

Reject dispute (UNDER_REVIEW -> REJECTED).

**Request:**
```json
{
    "resolution": "Dispute rejected due to insufficient evidence."
}
```

**Response (200):**
```json
{
    "success": true,
    "message": "Dispute rejected successfully",
    "data": {
        "id": 1,
        "task_id": 1,
        "user_id": 2,
        "helper_id": 3,
        "reason": "Work quality issue",
        "admin_note": "Dispute rejected due to insufficient evidence.",
        "status": "rejected",
        "resolved_by": 1,
        "resolved_at": "2026-06-14 12:00:00",
        "task_title": "Renovasi Kamar Mandi",
        "creator_name": "John Doe",
        "helper_name": "Ahmad Helper",
        "resolved_by_name": "Admin",
        "created_at": "2026-06-14 10:00:00"
    }
}
```

**Error Response (409):**
```json
{
    "success": false,
    "message": "Only under review disputes can be rejected"
}
```

---

## Dispute Status Flow

```
OPEN ──────────► UNDER_REVIEW ──────────► RESOLVED
 │                                          │
 │                                          │
 └────────────────────────────────────────► REJECTED
```

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
    "message": "You are not involved in this task"
}
```

### 404 Not Found
```json
{
    "success": false,
    "message": "Dispute not found"
}
```

### 409 Conflict
```json
{
    "success": false,
    "message": "This task already has an active dispute"
}
```

### 422 Validation Failed
```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "task_id": "Task ID is required",
        "reason": "Reason is required"
    }
}
```

---

## Business Rules

| Rule | Description |
|------|-------------|
| Task Status | Dispute only for WAITING_APPROVAL or COMPLETED tasks |
| Task Involvement | Only task owner or assigned helper can create |
| Active Dispute | One active dispute per task (OPEN or UNDER_REVIEW) |
| Status Transition | Must follow: OPEN -> UNDER_REVIEW -> RESOLVED/REJECTED |
| Admin Only | Only admin can review, resolve, or reject |

---

## Authorization Rules

| Endpoint | Role | Owner Check | Description |
|----------|------|-------------|-------------|
| POST /disputes | user/helper | Task involvement | Create dispute |
| GET /disputes | user/helper | Dispute involvement | List my disputes |
| GET /disputes/{id} | user/helper/admin | Dispute involvement | View dispute |
| GET /admin/disputes | admin | ❌ | List all disputes |
| POST /admin/disputes/{id}/review | admin | ❌ | Review dispute |
| POST /admin/disputes/{id}/resolve | admin | ❌ | Resolve dispute |
| POST /admin/disputes/{id}/reject | admin | ❌ | Reject dispute |

---

**End of Documentation**
