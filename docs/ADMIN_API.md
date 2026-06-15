# ADMIN API Documentation

**Base URL:** `http://bantuinYuk.test/api/v1`

**Content-Type:** `application/json`

---

## Authentication

Semua admin endpoint memerlukan Bearer Token dengan role admin:

```
Authorization: Bearer <admin_token>
```

---

## Dashboard

### GET /admin/dashboard

Get dashboard summary.

**Response (200):**
```json
{
    "success": true,
    "message": "Success",
    "data": {
        "users": 120,
        "helpers": 55,
        "tasks": 340,
        "open_tasks": 30,
        "completed_tasks": 260,
        "wallet_transactions": 1200,
        "disputes": 8,
        "pending_disputes": 2,
        "notifications": 1500
    }
}
```

---

### GET /admin/analytics

Get system analytics.

**Response (200):**
```json
{
    "success": true,
    "message": "Success",
    "data": {
        "total_users": 100,
        "total_helpers": 40,
        "verified_helpers": 35,
        "total_tasks": 500,
        "completed_tasks": 400,
        "completion_rate": 80,
        "total_disputes": 20,
        "resolved_disputes": 15,
        "dispute_rate": 4,
        "total_transaction_amount": 120000000
    }
}
```

---

## User Management

### GET /admin/users

List users with pagination and filters.

**Query Parameters:**

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `page` | int | 1 | Page number |
| `per_page` | int | 20 | Items per page |
| `search` | string | - | Search name, email, phone |
| `role` | string | - | Filter: user, helper, admin |
| `sort_by` | string | created_at | Sort: name, email, role, created_at |

**Response (200):**
```json
{
    "success": true,
    "message": "Success",
    "data": {
        "data": [
            {
                "id": 1,
                "name": "John Doe",
                "email": "john@example.com",
                "phone": "08123456789",
                "role": "user",
                "active": 1,
                "created_at": "2026-06-14 10:00:00"
            }
        ],
        "total": 120,
        "page": 1,
        "per_page": 20
    }
}
```

---

### GET /admin/users/{id}

Get user detail.

**Response (200):**
```json
{
    "success": true,
    "message": "Success",
    "data": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "phone": "08123456789",
        "role": "user",
        "active": 1,
        "created_at": "2026-06-14 10:00:00",
        "stats": {
            "total_tasks": 10,
            "completed_tasks": 8
        }
    }
}
```

---

### PUT /admin/users/{id}/status

Update user status.

**Request:**
```json
{
    "active": 0
}
```

**Response (200):**
```json
{
    "success": true,
    "message": "User status updated successfully",
    "data": {
        "id": 1,
        "name": "John Doe",
        "active": 0
    }
}
```

---

## Helper Management

### GET /admin/helpers

List helpers with pagination and filters.

**Query Parameters:**

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `page` | int | 1 | Page number |
| `per_page` | int | 20 | Items per page |
| `search` | string | - | Search name, email |
| `verification_status` | string | - | Filter: pending, verified, rejected |

**Response (200):**
```json
{
    "success": true,
    "message": "Success",
    "data": {
        "data": [
            {
                "id": 1,
                "user_id": 3,
                "name": "Ahmad Helper",
                "email": "ahmad@example.com",
                "phone": "08123456789",
                "rating": 4.5,
                "completed_tasks": 15,
                "verification_status": "verified",
                "created_at": "2026-06-14 10:00:00"
            }
        ],
        "total": 55,
        "page": 1,
        "per_page": 20
    }
}
```

---

### GET /admin/helpers/{id}

Get helper detail.

**Response (200):**
```json
{
    "success": true,
    "message": "Success",
    "data": {
        "id": 1,
        "user_id": 3,
        "name": "Ahmad Helper",
        "email": "ahmad@example.com",
        "rating": 4.5,
        "completed_tasks": 15,
        "verification_status": "verified",
        "stats": {
            "total_tasks": 20,
            "completed_tasks": 15
        }
    }
}
```

---

### POST /admin/helpers/{id}/verify

Verify helper.

**Response (200):**
```json
{
    "success": true,
    "message": "Helper verified successfully",
    "data": {
        "id": 1,
        "user_id": 3,
        "name": "Ahmad Helper",
        "verification_status": "verified"
    }
}
```

---

### POST /admin/helpers/{id}/reject

Reject helper.

**Request:**
```json
{
    "reason": "Invalid KTP photo"
}
```

**Response (200):**
```json
{
    "success": true,
    "message": "Helper rejected successfully",
    "data": {
        "id": 1,
        "user_id": 3,
        "name": "Ahmad Helper",
        "verification_status": "rejected",
        "rejection_reason": "Invalid KTP photo"
    }
}
```

---

## Task Management

### GET /admin/tasks

List tasks with pagination and filters.

**Query Parameters:**

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `page` | int | 1 | Page number |
| `per_page` | int | 20 | Items per page |
| `search` | string | - | Search title, user name |
| `status` | string | - | Filter by status |
| `category_id` | int | - | Filter by category |

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
                "price": 500000,
                "status": "open",
                "category_name": "Renovation",
                "user_name": "John Doe",
                "helper_name": null,
                "created_at": "2026-06-14 10:00:00"
            }
        ],
        "total": 340,
        "page": 1,
        "per_page": 20
    }
}
```

---

### GET /admin/tasks/{id}

Get task detail with all related data.

**Response (200):**
```json
{
    "success": true,
    "message": "Success",
    "data": {
        "id": 1,
        "title": "Renovasi Kamar Mandi",
        "description": "Detail task...",
        "price": 500000,
        "status": "completed",
        "category_name": "Renovation",
        "user_name": "John Doe",
        "helper_name": "Ahmad Helper",
        "status_history": [...],
        "attachments": [...],
        "progress": [...]
    }
}
```

---

## Transaction Management

### GET /admin/transactions

List transactions with pagination and filters.

**Query Parameters:**

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `page` | int | 1 | Page number |
| `per_page` | int | 20 | Items per page |
| `search` | string | - | Search reference_id, user name |
| `type` | string | - | Filter: task_payment, withdraw, refund, adjustment |
| `status` | string | - | Filter: pending, completed, cancelled |

**Response (200):**
```json
{
    "success": true,
    "message": "Success",
    "data": {
        "data": [
            {
                "id": 1,
                "user_id": 3,
                "amount": 500000,
                "type": "task_payment",
                "status": "completed",
                "reference_id": "PAY-20260614-A1B2C3D4",
                "user_name": "Ahmad Helper",
                "created_at": "2026-06-14 10:00:00"
            }
        ],
        "total": 1200,
        "page": 1,
        "per_page": 20
    }
}
```

---

### GET /admin/transactions/{id}

Get transaction detail.

**Response (200):**
```json
{
    "success": true,
    "message": "Success",
    "data": {
        "id": 1,
        "user_id": 3,
        "task_id": 1,
        "amount": 500000,
        "type": "task_payment",
        "status": "completed",
        "reference_id": "PAY-20260614-A1B2C3D4",
        "description": "Payment for task: Renovasi Kamar Mandi",
        "user_name": "Ahmad Helper",
        "created_at": "2026-06-14 10:00:00"
    }
}
```

---

## Wallet Monitoring

### GET /admin/wallets

List wallets with pagination.

**Query Parameters:**

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `page` | int | 1 | Page number |
| `per_page` | int | 20 | Items per page |
| `search` | string | - | Search user name, email |

**Response (200):**
```json
{
    "success": true,
    "message": "Success",
    "data": {
        "data": [
            {
                "id": 1,
                "user_id": 3,
                "balance": 500000,
                "user_name": "Ahmad Helper",
                "user_email": "ahmad@example.com",
                "created_at": "2026-06-14 10:00:00"
            }
        ],
        "total": 55,
        "page": 1,
        "per_page": 20
    }
}
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
    "message": "Forbidden"
}
```

### 404 Not Found
```json
{
    "success": false,
    "message": "Resource not found"
}
```

---

## Authorization Rules

| Endpoint | Role | Description |
|----------|------|-------------|
| All /admin/* | admin | Admin only |

---

**End of Documentation**
