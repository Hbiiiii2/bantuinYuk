# DISPUTE DOMAIN REPORT

**Tanggal:** 14 Juni 2026  
**Sprint:** Dispute Domain Implementation  
**Status:** ✅ Completed

---

## Table of Contents

1. [File yang Dibuat/Diubah](#1-file-yang-dibuatdiubah)
2. [Endpoint Matrix](#2-endpoint-matrix)
3. [Status Transition Matrix](#3-status-transition-matrix)
4. [Ownership Rules](#4-ownership-rules)
5. [Authorization Matrix](#5-authorization-matrix)
6. [Notification Integration](#6-notification-integration)
7. [Security Review](#7-security-review)
8. [Performance Review](#8-performance-review)
9. [Testing Checklist](#9-testing-checklist)

---

## 1. File yang Dibuat/Diubah

### New Files

| File | Description |
|------|-------------|
| `app/Services/DisputeService.php` | Service untuk dispute management |
| `app/Controllers/DisputeController.php` | Controller untuk dispute operations |
| `docs/DISPUTE_DOMAIN_REPORT.md` | Dokumentasi ini |
| `docs/DISPUTE_API.md` | API documentation |

### Modified Files

| File | Changes |
|------|---------|
| `app/Models/DisputeModel.php` | Added constants, helper methods, queries |
| `app/Config/Routes.php` | Added dispute routes |

---

## 2. Endpoint Matrix

### User/Helper Endpoints

| Endpoint | Method | Description | Authorization |
|----------|--------|-------------|---------------|
| `/disputes` | GET | List my disputes | Auth required |
| `/disputes` | POST | Create dispute | Auth required |
| `/disputes/{id}` | GET | Get dispute detail | Involved parties |

### Admin Endpoints

| Endpoint | Method | Description | Authorization |
|----------|--------|-------------|---------------|
| `/admin/disputes` | GET | List all disputes | Admin only |
| `/admin/disputes/{id}/review` | POST | Review dispute | Admin only |
| `/admin/disputes/{id}/resolve` | POST | Resolve dispute | Admin only |
| `/admin/disputes/{id}/reject` | POST | Reject dispute | Admin only |

---

## 3. Status Transition Matrix

```
OPEN ──────────► UNDER_REVIEW ──────────► RESOLVED
 │                                          │
 │                                          │
 └────────────────────────────────────────► REJECTED
```

### Valid Transitions

| From | To | Trigger | Validation |
|------|----|---------|------------|
| OPEN | UNDER_REVIEW | Admin review | Status must be OPEN |
| UNDER_REVIEW | RESOLVED | Admin resolve | Status must be UNDER_REVIEW |
| UNDER_REVIEW | REJECTED | Admin reject | Status must be UNDER_REVIEW |

### Invalid Transitions (Rejected)

| From | To | Reason |
|------|----|--------|
| OPEN | RESOLVED | Must go through UNDER_REVIEW first |
| OPEN | REJECTED | Must go through UNDER_REVIEW first |
| RESOLVED | * | Terminal state |
| REJECTED | * | Terminal state |

---

## 4. Ownership Rules

### Who Can Create Dispute

| Role | Condition | Validation |
|------|-----------|------------|
| User | Task owner | `task.user_id == userId` |
| Helper | Assigned helper | `task.helper_id == userId` |

### Who Can View Dispute

| Role | Condition | Validation |
|------|-----------|------------|
| User | Dispute creator | `dispute.user_id == userId` |
| Helper | Involved helper | `dispute.helper_id == userId` |
| Admin | Any dispute | `user.role == 'admin'` |

### Who Can Modify Dispute

| Action | Role | Validation |
|--------|------|------------|
| Create | User/Helper | Task involvement check |
| Review | Admin only | Role check |
| Resolve | Admin only | Role check |
| Reject | Admin only | Role check |

---

## 5. Authorization Matrix

| Operation | User (Owner) | Helper (Involved) | Admin |
|-----------|--------------|-------------------|-------|
| Create Dispute | ✅ | ✅ | ❌ |
| List My Disputes | ✅ | ✅ | ❌ |
| View Dispute | ✅ | ✅ | ✅ |
| List All Disputes | ❌ | ❌ | ✅ |
| Review Dispute | ❌ | ❌ | ✅ |
| Resolve Dispute | ❌ | ❌ | ✅ |
| Reject Dispute | ❌ | ❌ | ✅ |

---

## 6. Notification Integration

### Notification Events

| Event | Recipients | Type | Message |
|-------|------------|------|---------|
| Dispute Created | Admin, Counterparty | `dispute_created` | "A new dispute has been created..." |
| Dispute Under Review | User, Helper | `dispute_under_review` | "Your dispute is now under review." |
| Dispute Resolved | User, Helper | `dispute_resolved` | "Your dispute has been resolved." |
| Dispute Rejected | User, Helper | `dispute_rejected` | "Your dispute has been rejected." |

### Notification Flow

```
┌─────────────────────────────────────────────────────────────────┐
│                    DISPUTE NOTIFICATION FLOW                    │
└─────────────────────────────────────────────────────────────────┘

User/Helper creates dispute
    │
    ├─→ Notify Admin (all admins)
    │
    └─→ Notify Counterparty (other party in task)

Admin reviews dispute
    │
    ├─→ Notify User (task owner)
    │
    └─→ Notify Helper (assigned helper)

Admin resolves/rejects dispute
    │
    ├─→ Notify User (task owner)
    │
    └─→ Notify Helper (assigned helper)
```

---

## 7. Security Review

### Implemented Security

| Security | Status | Implementation |
|----------|--------|----------------|
| Authentication | ✅ | Shield tokens filter |
| Authorization | ✅ | Role-based + ownership |
| IDOR Prevention | ✅ | Ownership validation |
| Task Involvement | ✅ | Check user_id or helper_id |
| Active Dispute Check | ✅ | Prevent duplicate disputes |
| Status Validation | ✅ | Valid status transitions |
| Admin Only Actions | ✅ | Role check in controller |

### Protected Against

| Threat | Status | Protection |
|--------|--------|------------|
| IDOR | ✅ | validateDisputeOwnership() |
| Cross-user Access | ✅ | isInvolved() check |
| Invalid Status Jump | ✅ | Status validation |
| Duplicate Dispute | ✅ | hasActiveDispute() check |
| Unauthorized Modify | ✅ | Admin role check |

### Validation Rules

| Rule | Validation | Error Message |
|------|------------|---------------|
| Task exists | taskId validation | "Task not found" |
| Task involvement | user_id or helper_id | "You are not involved in this task" |
| Task status | WAITING_APPROVAL or COMPLETED | "Dispute can only be created..." |
| No active dispute | hasActiveDispute() | "This task already has an active dispute" |
| Status transition | Valid from/to | "Only open disputes can be reviewed" |

---

## 8. Performance Review

### Database Indexes (Required)

| Table | Columns | Purpose |
|-------|---------|---------|
| disputes | task_id | Task lookup |
| disputes | user_id | User disputes |
| disputes | helper_id | Helper disputes |
| disputes | status | Status filter |
| disputes | created_at | Sort order |

### Query Optimization

| Query | Optimization | Status |
|-------|--------------|--------|
| GetUserDisputes | WHERE (user_id OR helper_id) + pagination | ✅ |
| GetAllDisputes | WHERE status + search + pagination | ✅ |
| HasActiveDispute | WHERE task_id + status IN | ✅ |
| GetDisputeById | JOIN users, tasks | ✅ |

### Anti-Patterns Avoided

| Anti-Pattern | Status | Implementation |
|--------------|--------|----------------|
| findAll() | ✅ Avoided | Always uses pagination |
| No LIMIT | ✅ Avoided | Always uses limit() |
| N+1 queries | ✅ Avoided | Single query with JOINs |

---

## 9. Testing Checklist

### Create Dispute

- [ ] User dapat membuat dispute untuk task sendiri
- [ ] Helper dapat membuat dispute untuk task yang di-assign
- [ ] User random tidak dapat membuat dispute task orang lain
- [ ] Helper random tidak dapat membuat dispute task orang lain
- [ ] Dispute gagal jika task status bukan WAITING_APPROVAL/COMPLETED
- [ ] Dispute gagal jika sudah ada dispute aktif
- [ ] Notification terkirim ke admin dan counterparty

### List Disputes

- [ ] User hanya melihat dispute sendiri
- [ ] Helper hanya melihat dispute sendiri
- [ ] Admin melihat semua dispute
- [ ] Pagination bekerja
- [ ] Search bekerja
- [ ] Filter status bekerja

### Dispute Detail

- [ ] User dapat melihat detail dispute sendiri
- [ ] Helper dapat melihat detail dispute sendiri
- [ ] Admin dapat melihat semua dispute detail
- [ ] Return 403 jika bukan involved party atau admin

### Admin Review

- [ ] Admin dapat review dispute (OPEN -> UNDER_REVIEW)
- [ ] Non-admin tidak dapat review dispute
- [ ] Review gagal jika status bukan OPEN
- [ ] Notification terkirim ke user dan helper

### Admin Resolve

- [ ] Admin dapat resolve dispute (UNDER_REVIEW -> RESOLVED)
- [ ] Non-admin tidak dapat resolve dispute
- [ ] Resolve gagal jika status bukan UNDER_REVIEW
- [ ] Resolution wajib diisi
- [ ] Notification terkirim ke user dan helper

### Admin Reject

- [ ] Admin dapat reject dispute (UNDER_REVIEW -> REJECTED)
- [ ] Non-admin tidak dapat reject dispute
- [ ] Reject gagal jika status bukan UNDER_REVIEW
- [ ] Notification terkirim ke user dan helper

### Security

- [ ] Tidak ada IDOR
- [ ] Ownership validation bekerja
- [ ] Hanya admin yang bisa review/resolve/reject
- [ ] Semua logic berada di Service Layer

---

**End of Report**
