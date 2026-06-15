# TASK DOMAIN REPORT

**Tanggal:** 13 Juni 2026  
**Sprint:** 4 - Task Domain Implementation  
**Status:** ✅ Completed

---

## Table of Contents

1. [Endpoint yang Dibuat](#1-endpoint-yang-dibuat)
2. [Service Method](#2-service-method)
3. [Validation Rules](#3-validation-rules)
4. [Status Transition Matrix](#4-status-transition-matrix)
5. [Ownership Validation](#5-ownership-validation)
6. [Security Consideration](#6-security-consideration)
7. [Testing Checklist](#7-testing-checklist)

---

## 1. Endpoint yang Dibuat

| Endpoint | Method | Description | Auth | Role |
|----------|--------|-------------|------|------|
| `POST /api/v1/tasks` | POST | Buat task baru | ✅ | user |
| `GET /api/v1/tasks` | GET | List semua tasks | ✅ | all |
| `GET /api/v1/tasks/{id}` | GET | Detail task | ✅ | all |
| `PUT /api/v1/tasks/{id}` | PUT | Update task | ✅ | owner |
| `DELETE /api/v1/tasks/{id}` | DELETE | Cancel task | ✅ | owner |
| `GET /api/v1/tasks/my` | GET | Tasks milik user | ✅ | all |
| `POST /api/v1/tasks/{id}/complete` | POST | Selesaikan task | ✅ | owner |

### Helper Endpoints (via HelperController)

| Endpoint | Method | Description | Auth | Role |
|----------|--------|-------------|------|------|
| `POST /api/v1/helpers/tasks/{id}/accept` | POST | Accept task | ✅ | helper |
| `POST /api/v1/helpers/tasks/{id}/start` | POST | Mulai pengerjaan | ✅ | helper |
| `POST /api/v1/helpers/{id}/submit` | POST | Submit hasil | ✅ | helper |

---

## 2. Service Method

### TaskService Methods

| Method | Description | Return |
|--------|-------------|--------|
| `createTask(userId, data)` | Buat task baru | array |
| `getTaskById(taskId)` | Ambil task + history | array |
| `getAllTasks(filters, page, perPage)` | List tasks dengan filter/search | array |
| `getUserTasks(userId, status, dateFrom, dateTo, page, perPage)` | Tasks milik user | array |
| `getHelperTasks(helperId, status, page, perPage)` | Tasks ditugaskan ke helper | array |
| `updateTask(taskId, userId, data)` | Update task | array |
| `cancelTask(taskId, userId, note)` | Cancel task | array |
| `acceptTask(taskId, helperId)` | Accept task oleh helper | array |
| `startTask(taskId, helperId)` | Mulai pengerjaan | array |
| `submitTask(taskId, helperId)` | Submit hasil | array |
| `completeTask(taskId, userId)` | Selesaikan task | array |
| `changeStatus(taskId, newStatus, userId, note)` | Ubah status dengan validasi | array |
| `createStatusHistory(taskId, status, createdBy, note)` | Buat record history | int |
| `getStatusHistory(taskId)` | Ambil status history | array |

### TaskStatusHistoryModel

| Method | Description | Return |
|--------|-------------|--------|
| `insert(data)` | Insert history record | int |
| `where(key, value)` | Filter query | Model |
| `get()` | Execute query | Result |

---

## 3. Validation Rules

### Create Task

| Field | Rule | Error Message |
|-------|------|---------------|
| `title` | required, 5-255 chars | Title is required / Title must be 5-255 characters |
| `description` | required | Description is required |
| `price` | required, numeric, > 0 | Price is required / Price must be positive |
| `category_id` | required, exists in categories | Category is required / Category is not valid |
| `deadline_start` | required, datetime, future | Deadline start is required / Must be in the future |
| `deadline_end` | required, datetime > deadline_start | Deadline end is required / Must be after deadline start |
| `location` | optional, string | - |

### Update Task

| Field | Rule | Error Message |
|-------|------|---------------|
| `title` | optional, 5-255 chars | Title must be 5-255 characters |
| `description` | optional, string | - |
| `price` | optional, numeric, > 0 | Price must be positive |
| `location` | optional, string | - |

### Business Rules

| Rule | Description |
|------|-------------|
| Role check | Only users (role=user) can create tasks |
| Ownership check | Only owner can update/cancel/complete task |
| Status check | Can only update task in DRAFT/OPEN status |
| Status check | Can only cancel task in OPEN/ACCEPTED status |
| Status check | Can only complete task in WAITING_APPROVAL status |

---

## 4. Status Transition Matrix

### Valid Transitions

```
OPEN ──────────┬──→ ACCEPTED ──────────┬──→ IN_PROGRESS ──→ WAITING_APPROVAL ──→ COMPLETED
               │                       │
               └──→ CANCELLED          └──→ CANCELLED
```

### Matrix

| From | To | Allowed | Actor |
|------|----|---------|-------|
| OPEN | ACCEPTED | ✅ | helper |
| OPEN | CANCELLED | ✅ | owner |
| ACCEPTED | IN_PROGRESS | ✅ | helper |
| ACCEPTED | CANCELLED | ✅ | owner |
| IN_PROGRESS | WAITING_APPROVAL | ✅ | helper |
| WAITING_APPROVAL | COMPLETED | ✅ | owner |
| COMPLETED | * | ❌ | - |

### Invalid Transitions (Examples)

| From | To | Allowed | Reason |
|------|----|---------|--------|
| OPEN | COMPLETED | ❌ | Must go through ACCEPTED → IN_PROGRESS → WAITING_APPROVAL |
| OPEN | IN_PROGRESS | ❌ | Must go through ACCEPTED first |
| IN_PROGRESS | COMPLETED | ❌ | Must go through WAITING_APPROVAL first |
| COMPLETED | OPEN | ❌ | Terminal state |

### Status History Record

Setiap perubahan status wajib membuat record di `task_status_histories`:

```php
[
    'task_id'    => 123,
    'status'     => 'accepted',
    'created_by' => 456, // user_id yang melakukan
    'note'       => 'Task accepted by helper',
]
```

---

## 5. Ownership Validation

### IDOR Prevention

| Endpoint | Validation | Code Location |
|----------|------------|---------------|
| `PUT /tasks/{id}` | `$task['user_id'] != $userId` | TaskService::updateTask() |
| `DELETE /tasks/{id}` | `$task['user_id'] != $userId` | TaskService::cancelTask() |
| `POST /tasks/{id}/complete` | `$task['user_id'] != $userId` | TaskService::completeTask() |
| `POST /helpers/tasks/{id}/accept` | `$task['user_id'] == $helperId` | TaskService::acceptTask() |
| `POST /helpers/tasks/{id}/start` | `$task['helper_id'] != $helperId` | TaskService::startTask() |
| `POST /helpers/{id}/submit` | `$task['helper_id'] != $helperId` | TaskService::submitTask() |

### Authorization Matrix

| Action | user | helper | admin |
|--------|------|--------|-------|
| Create task | ✅ | ❌ | ❌ |
| View tasks | ✅ | ✅ | ✅ |
| Update own task | ✅ (OPEN) | ❌ | ❌ |
| Cancel own task | ✅ | ❌ | ❌ |
| Complete own task | ✅ | ❌ | ❌ |
| Accept task | ❌ | ✅ | ❌ |
| Start task | ❌ | ✅ | ❌ |
| Submit task | ❌ | ✅ | ❌ |

---

## 6. Security Consideration

### Implemented Security

| Security | Status | Implementation |
|----------|--------|----------------|
| Authentication | ✅ | Shield tokens filter |
| Authorization | ✅ | Role check in service |
| Ownership validation | ✅ | user_id/helper_id check |
| Input validation | ✅ | BaseService validation |
| SQL Injection | ✅ | Query builder |
| Status transition validation | ✅ | validTransitions array |

### Protected Against

| Threat | Status | Protection |
|--------|--------|------------|
| IDOR | ✅ | Ownership check in service |
| Broken Authorization | ✅ | Role validation |
| Missing Ownership Validation | ✅ | user_id/helper_id check |
| Invalid Status Transition | ✅ | validTransitions array |
| Race Condition | ✅ | Transaction wrapper |

### Security Checklist

- [x] User tidak dapat mengakses task user lain untuk update
- [x] User tidak dapat mengakses task user lain untuk cancel
- [x] User tidak dapat mengakses task user lain untuk complete
- [x] Helper tidak dapat membuat task
- [x] Admin tidak dapat membuat task
- [x] Semua endpoint protected dengan token
- [x] Semua ownership diverifikasi di service
- [x] Status transition divalidasi
- [x] Status history selalu tercatat

---

## 7. Testing Checklist

### Create Task

- [ ] User dapat membuat task dengan data valid
- [ ] Task gagal dibuat tanpa title
- [ ] Task gagal dibuat tanpa category_id
- [ ] Task gagal dibuat tanpa description
- [ ] Task gagal dibuat tanpa price
- [ ] Task gagal dibuat tanpa deadline_start
- [ ] Task gagal dibuat tanpa deadline_end
- [ ] Task gagal dibuat dengan price <= 0
- [ ] Task gagal dibuat dengan deadline_end <= deadline_start
- [ ] Task gagal dibuat dengan category tidak valid
- [ ] Helper tidak dapat membuat task (403)
- [ ] Admin tidak dapat membuat task (403)

### List Tasks

- [ ] Dapat list semua tasks
- [ ] Dapat filter by status
- [ ] Dapat filter by category
- [ ] Dapat search by title
- [ ] Dapat search by description
- [ ] Dapat sort by created_at
- [ ] Dapat sort by price
- [ ] Pagination bekerja

### Detail Task

- [ ] Dapat lihat task detail
- [ ] Task tidak ditemukan (404)
- [ ] Status history tercantum

### Update Task

- [ ] Owner dapat update task OPEN
- [ ] Non-owner tidak dapat update (403)
- [ ] Task gagal diupdate selain OPEN/DRAFT (409)

### Cancel Task

- [ ] Owner dapat cancel task OPEN
- [ ] Owner dapat cancel task ACCEPTED
- [ ] Non-owner tidak dapat cancel (403)
- [ ] Task gagal dicancel selain OPEN/ACCEPTED (409)
- [ ] Status history tercatat

### Complete Task

- [ ] Owner dapat complete task WAITING_APPROVAL
- [ ] Non-owner tidak dapat complete (403)
- [ ] Task gagal dicomplete selain WAITING_APPROVAL (409)
- [ ] Status history tercatat

### Status History

- [ ] History tercatat saat task dibuat (status OPEN)
- [ ] History tercatat saat ACCEPTED
- [ ] History tercatat saat IN_PROGRESS
- [ ] History tercatat saat WAITING_APPROVAL
- [ ] History tercatat saat COMPLETED
- [ ] History tercatat saat CANCELLED
- [ ] History include user name

### Security

- [ ] Tidak ada IDOR
- [ ] Tidak ada direct model query di controller
- [ ] Semua logic berada di TaskService

---

**End of Report**
