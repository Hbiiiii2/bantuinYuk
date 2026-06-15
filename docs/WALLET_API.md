# WALLET & TRANSACTION API Documentation

**Base URL:** `http://bantuinYuk.test/api/v1`

**Content-Type:** `application/json`

---

## Authentication

Semua endpoint memerlukan Bearer Token:

```
Authorization: Bearer <access_token>
```

---

## User Wallet Endpoints

### GET /wallet

Get wallet summary untuk user yang sedang login.

**Response (200):**
```json
{
    "success": true,
    "message": "Success",
    "data": {
        "balance": 500000,
        "total_earned": 1500000,
        "total_withdrawn": 800000,
        "total_refunded": 0,
        "pending_withdrawals": 200000
    }
}
```

---

### GET /wallet/transactions

Get transaction history untuk user.

**Query Parameters:**

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `page` | int | 1 | Page number |
| `per_page` | int | 20 | Items per page |
| `type` | string | - | Filter: task_payment, withdraw, refund, adjustment |

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
                "task_id": 1,
                "amount": 500000,
                "type": "task_payment",
                "status": "completed",
                "reference_id": "PAY-20260614-A1B2C3D4",
                "description": "Payment for task: Renovasi Kamar Mandi",
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

### GET /wallet/transactions/{id}

Get transaction detail.

**Response (200):**
```json
{
    "success": true,
    "message": "Success",
    "data": {
        "id": 1,
        "user_id": 2,
        "task_id": 1,
        "amount": 500000,
        "type": "task_payment",
        "status": "completed",
        "reference_id": "PAY-20260614-A1B2C3D4",
        "description": "Payment for task: Renovasi Kamar Mandi",
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

### POST /wallet/release-payment/{taskId}

Release payment ke helper untuk task yang COMPLETED.

**Authorization:** Task owner only

**Response (200):**
```json
{
    "success": true,
    "message": "Payment released successfully",
    "data": {
        "id": 1,
        "user_id": 3,
        "task_id": 1,
        "amount": 500000,
        "type": "task_payment",
        "status": "completed",
        "reference_id": "PAY-20260614-A1B2C3D4",
        "description": "Payment for task: Renovasi Kamar Mandi",
        "created_at": "2026-06-14 10:00:00"
    }
}
```

**Error Response (409):**
```json
{
    "success": false,
    "message": "Only completed tasks can be released for payment"
}
```

**Error Response (409):**
```json
{
    "success": false,
    "message": "Payment has already been released for this task"
}
```

---

### POST /wallet/withdraw

Request withdrawal.

**Request:**
```json
{
    "amount": 200000,
    "description": "Withdraw for June"
}
```

**Response (201):**
```json
{
    "success": true,
    "message": "Withdrawal request submitted successfully",
    "data": {
        "id": 2,
        "user_id": 2,
        "task_id": null,
        "amount": 200000,
        "type": "withdraw",
        "status": "pending",
        "reference_id": "WD-20260614-E5F6G7H8",
        "description": "Withdraw for June",
        "created_at": "2026-06-14 11:00:00"
    }
}
```

**Error Response (409):**
```json
{
    "success": false,
    "message": "Insufficient balance"
}
```

**Error Response (422):**
```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "amount": "amount must be positive"
    }
}
```

---

## Admin Wallet Endpoints

### GET /admin/withdrawals

Get pending withdrawals.

**Authorization:** Admin only

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
                "id": 2,
                "user_id": 2,
                "task_id": null,
                "amount": 200000,
                "type": "withdraw",
                "status": "pending",
                "reference_id": "WD-20260614-E5F6G7H8",
                "description": "Withdraw for June",
                "user_name": "John Doe",
                "created_at": "2026-06-14 11:00:00"
            }
        ],
        "total": 1,
        "page": 1,
        "per_page": 20
    }
}
```

---

### POST /admin/withdrawals/{id}/approve

Approve withdrawal request.

**Authorization:** Admin only

**Response (200):**
```json
{
    "success": true,
    "message": "Withdrawal approved successfully",
    "data": {
        "id": 2,
        "user_id": 2,
        "task_id": null,
        "amount": 200000,
        "type": "withdraw",
        "status": "completed",
        "reference_id": "WD-20260614-E5F6G7H8",
        "description": "Withdraw for June",
        "created_at": "2026-06-14 11:00:00"
    }
}
```

**Error Response (409):**
```json
{
    "success": false,
    "message": "Withdrawal is not pending"
}
```

---

### POST /admin/withdrawals/{id}/reject

Reject withdrawal request.

**Authorization:** Admin only

**Request:**
```json
{
    "reason": "Invalid account details"
}
```

**Response (200):**
```json
{
    "success": true,
    "message": "Withdrawal rejected successfully",
    "data": {
        "id": 2,
        "user_id": 2,
        "task_id": null,
        "amount": 200000,
        "type": "withdraw",
        "status": "cancelled",
        "reference_id": "WD-20260614-E5F6G7H8",
        "description": "Invalid account details",
        "created_at": "2026-06-14 11:00:00"
    }
}
```

---

### GET /admin/transactions

Get all transactions with filters.

**Authorization:** Admin only

**Query Parameters:**

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `page` | int | 1 | Page number |
| `per_page` | int | 20 | Items per page |
| `user_id` | int | - | Filter by user ID |
| `type` | string | - | Filter by type |
| `status` | string | - | Filter by status |

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
                "task_id": 1,
                "amount": 500000,
                "type": "task_payment",
                "status": "completed",
                "reference_id": "PAY-20260614-A1B2C3D4",
                "description": "Payment for task: Renovasi Kamar Mandi",
                "user_name": "John Doe",
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

## Transaction Types

| Type | Description | Status Flow |
|------|-------------|-------------|
| `task_payment` | Payment from task owner to helper | pending → completed |
| `withdraw` | Withdrawal request | pending → completed/cancelled |
| `refund` | Refund to user | completed (immediate) |
| `adjustment` | Manual adjustment by admin | completed (immediate) |

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
    "message": "Transaction not found"
}
```

### 409 Conflict
```json
{
    "success": false,
    "message": "Insufficient balance"
}
```

### 422 Validation Failed
```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "amount": "amount must be positive"
    }
}
```

---

## Business Rules

| Rule | Description |
|------|-------------|
| One wallet per user | Created automatically on first access |
| Balance cannot be negative | Atomic decrement with check |
| Payment release on COMPLETED | Only for completed tasks |
| One payment per task | Idempotency check |
| Withdraw requires approval | Pending until admin approves |
| Reject refunds balance | Balance restored on rejection |

---

## Security

| Security | Implementation |
|----------|----------------|
| Double Payment Prevention | Idempotency check |
| Negative Balance Prevention | Atomic decrement with WHERE clause |
| Race Condition Prevention | Atomic SQL operations |
| Transaction Safety | All financial ops in transaction() |

---

**End of Documentation**
