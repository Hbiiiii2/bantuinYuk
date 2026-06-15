# NOTIFICATION API Documentation

**Base URL:** `http://bantuinYuk.test/api/v1`

**Content-Type:** `application/json`

---

## Authentication

Semua endpoint memerlukan Bearer Token:

```
Authorization: Bearer <access_token>
```

---

## Notification Endpoints

### GET /notifications

Get user notifications dengan pagination.

**Query Parameters:**

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `page` | int | 1 | Page number |
| `per_page` | int | 20 | Items per page |
| `unread` | string | - | Filter: `1` atau `true` untuk unread saja |

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
                "type": "task_accepted",
                "title": "Task Accepted",
                "message": "Ahmad has accepted your task \"Renovasi Kamar Mandi\".",
                "data": "{\"task_id\":1,\"helper_id\":3}",
                "is_read": 0,
                "created_at": "2026-06-14 10:00:00"
            }
        ],
        "total": 5,
        "page": 1,
        "per_page": 20,
        "unread_count": 3
    }
}
```

---

### GET /notifications/unread-count

Get unread notification count.

**Response (200):**
```json
{
    "success": true,
    "message": "Success",
    "data": {
        "unread_count": 3
    }
}
```

---

### GET /notifications/{id}

Get notification detail.

**Response (200):**
```json
{
    "success": true,
    "message": "Success",
    "data": {
        "id": 1,
        "user_id": 2,
        "type": "task_accepted",
        "title": "Task Accepted",
        "message": "Ahmad has accepted your task \"Renovasi Kamar Mandi\".",
        "data": "{\"task_id\":1,\"helper_id\":3}",
        "is_read": 0,
        "created_at": "2026-06-14 10:00:00"
    }
}
```

**Error Response (404):**
```json
{
    "success": false,
    "message": "Notification not found"
}
```

---

### POST /notifications/{id}/read

Mark notification as read.

**Response (200):**
```json
{
    "success": true,
    "message": "Notification marked as read",
    "data": null
}
```

**Error Response (404):**
```json
{
    "success": false,
    "message": "Notification not found"
}
```

---

### POST /notifications/read-all

Mark all notifications as read.

**Response (200):**
```json
{
    "success": true,
    "message": "All notifications marked as read",
    "data": null
}
```

---

## Notification Types

| Type | Description | Example Message |
|------|-------------|-----------------|
| `task_created` | Task created | "Your task has been created successfully." |
| `task_accepted` | Helper accepted task | "Ahmad has accepted your task." |
| `task_started` | Helper started work | "Ahmad has started working on your task." |
| `task_progress` | Progress added | "Ahmad has added progress to your task." |
| `task_submitted` | Work submitted | "Ahmad has submitted work for your task." |
| `task_completed` | Task completed | "User has completed your task." |
| `task_cancelled` | Task cancelled | "User has cancelled the task." |
| `review_received` | Review received | "User left a 5-star review." |
| `payment_released` | Payment released | "You received Rp 500,000." |
| `withdraw_requested` | Withdrawal requested | "Your withdrawal request has been submitted." |
| `withdraw_approved` | Withdrawal approved | "Your withdrawal has been approved." |
| `withdraw_rejected` | Withdrawal rejected | "Your withdrawal has been rejected." |
| `dispute_created` | Dispute created | "A dispute has been created." |
| `dispute_resolved` | Dispute resolved | "The dispute has been resolved." |

---

## Notification Data Field

The `data` field contains JSON with additional context:

| Type | Data Fields |
|------|-------------|
| task_* | `task_id`, `helper_id` or `owner_id` |
| review_received | `task_id`, `user_id`, `rating` |
| payment_released | `task_id`, `amount` |
| withdraw_* | `transaction_id`, `amount`, `reason` (rejected only) |

---

## Error Responses

### 401 Unauthorized
```json
{
    "success": false,
    "message": "Unauthorized"
}
```

### 404 Not Found
```json
{
    "success": false,
    "message": "Notification not found"
}
```

---

## Business Rules

| Rule | Description |
|------|-------------|
| User isolation | Users can only see their own notifications |
| Ownership validation | Detail/mark operations check ownership |
| Pagination | All list endpoints use pagination |
| Sort order | Newest first (created_at DESC) |
| Unread filter | Optional filter for unread only |

---

## Security

| Security | Implementation |
|----------|----------------|
| Authentication | Shield tokens required |
| Authorization | Ownership validation (user_id) |
| IDOR Prevention | WHERE user_id in all queries |
| Data Isolation | User只能操作自己的通知 |

---

**End of Documentation**
