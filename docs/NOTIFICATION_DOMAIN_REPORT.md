# NOTIFICATION DOMAIN REPORT

**Tanggal:** 14 Juni 2026  
**Sprint:** Notification Domain Implementation  
**Status:** ✅ Completed

---

## Table of Contents

1. [File yang Dibuat/Diubah](#1-file-yang-dibuatdiubah)
2. [Event Matrix](#2-event-matrix)
3. [Notification Types](#3-notification-types)
4. [Ownership Validation](#4-ownership-validation)
5. [Security Review](#5-security-review)
6. [Performance Review](#6-performance-review)
7. [Testing Checklist](#7-testing-checklist)

---

## 1. File yang Dibuat/Diubah

### New Files

| File | Description |
|------|-------------|
| `app/Services/NotificationService.php` | Service untuk notification management |
| `app/Controllers/NotificationController.php` | Controller untuk user notification operations |
| `docs/NOTIFICATION_DOMAIN_REPORT.md` | Dokumentasi ini |
| `docs/NOTIFICATION_API.md` | API documentation |

### Modified Files

| File | Changes |
|------|---------|
| `app/Models/NotificationModel.php` | Added constants, helper methods, ownership check |
| `app/Services/TaskService.php` | Integrated notifications for task events |
| `app/Services/ReviewService.php` | Integrated notification for review events |
| `app/Services/WalletService.php` | Integrated notifications for wallet events |
| `app/Config/Routes.php` | Added notification routes |

---

## 2. Event Matrix

### Task Events

| Event | Recipient | Type | Trigger Location |
|-------|-----------|------|------------------|
| Task Created | Task Owner | `task_created` | TaskService::createTask() |
| Task Accepted | Task Owner | `task_accepted` | TaskService::acceptTask() |
| Task Started | Task Owner | `task_started` | TaskService::startTask() |
| Progress Added | Task Owner | `task_progress` | HelperController::createProgress() |
| Task Submitted | Task Owner | `task_submitted` | TaskService::submitTask() |
| Task Completed | Helper | `task_completed` | TaskService::completeTask() |
| Task Cancelled | Helper | `task_cancelled` | TaskService::cancelTask() |

### Review Events

| Event | Recipient | Type | Trigger Location |
|-------|-----------|------|------------------|
| Review Received | Helper | `review_received` | ReviewService::createReview() |

### Wallet Events

| Event | Recipient | Type | Trigger Location |
|-------|-----------|------|------------------|
| Payment Released | Helper | `payment_released` | WalletService::releasePayment() |
| Withdraw Requested | User | `withdraw_requested` | WalletService::requestWithdraw() |
| Withdraw Approved | User | `withdraw_approved` | WalletService::approveWithdraw() |
| Withdraw Rejected | User | `withdraw_rejected` | WalletService::rejectWithdraw() |

### Dispute Events (Future Ready)

| Event | Recipient | Type | Status |
|-------|-----------|------|--------|
| Dispute Created | User/Admin | `dispute_created` | Placeholder |
| Dispute Resolved | User/Admin | `dispute_resolved` | Placeholder |

---

## 3. Notification Types

```php
const TYPE_TASK_CREATED       = 'task_created';
const TYPE_TASK_ACCEPTED      = 'task_accepted';
const TYPE_TASK_STARTED       = 'task_started';
const TYPE_TASK_PROGRESS      = 'task_progress';
const TYPE_TASK_SUBMITTED     = 'task_submitted';
const TYPE_TASK_COMPLETED     = 'task_completed';
const TYPE_TASK_CANCELLED     = 'task_cancelled';
const TYPE_REVIEW_RECEIVED    = 'review_received';
const TYPE_PAYMENT_RELEASED   = 'payment_released';
const TYPE_WITHDRAW_REQUESTED = 'withdraw_requested';
const TYPE_WITHDRAW_APPROVED  = 'withdraw_approved';
const TYPE_WITHDRAW_REJECTED  = 'withdraw_rejected';
const TYPE_DISPUTE_CREATED    = 'dispute_created';
const TYPE_DISPUTE_RESOLVED   = 'dispute_resolved';
```

---

## 4. Ownership Validation

### User Can Only See Own Notifications

```php
// NotificationModel
public function getByUserId(int $userId, int $page = 1, int $perPage = 20): array
{
    $builder->where('user_id', $userId);  // Ownership enforced
    ...
}

// NotificationService
public function getNotificationById(int $notificationId, int $userId): array
{
    if (!$this->notificationModel->belongsToUser($notificationId, $userId)) {
        throw BusinessException::notFound('Notification not found');  // Ownership check
    }
    ...
}
```

### Mark Read Ownership

```php
// NotificationModel
public function markAsRead(int $notificationId, int $userId): bool
{
    return $this->where('id', $notificationId)
        ->where('user_id', $userId)  // Ownership enforced
        ->update(['is_read' => 1]);
}
```

---

## 5. Security Review

### Implemented Security

| Security | Status | Implementation |
|----------|--------|----------------|
| Authentication | ✅ | Shield tokens filter |
| Authorization | ✅ | Ownership validation |
| IDOR Prevention | ✅ | user_id check in all queries |
| Data Isolation | ✅ | User只能看到自己的通知 |
| Mark Read Security | ✅ | Ownership check before update |

### Protected Against

| Threat | Status | Protection |
|--------|--------|------------|
| IDOR | ✅ | Ownership validation (belongsToUser) |
| Unauthorized Access | ✅ | Token authentication required |
| Data Leakage | ✅ | WHERE user_id in all queries |
| Cross-user Mark Read | ✅ | Ownership check in markAsRead |

---

## 6. Performance Review

### Database Indexes (Required)

| Table | Columns | Purpose |
|-------|---------|---------|
| notifications | user_id, created_at | User notifications query |
| notifications | user_id, is_read | Unread count query |
| notifications | id, user_id | Ownership check |

### Query Optimization

| Query | Optimization | Status |
|-------|--------------|--------|
| GetUserNotifications | WHERE user_id + pagination | ✅ |
| GetUnreadCount | WHERE user_id + is_read | ✅ |
| MarkAsRead | WHERE id + user_id | ✅ |
| MarkAllAsRead | WHERE user_id + is_read | ✅ |

### Anti-Patterns Avoided

| Anti-Pattern | Status | Implementation |
|--------------|--------|----------------|
| findAll() | ✅ Avoided | Always uses pagination |
| No LIMIT | ✅ Avoided | Always uses limit() |
| N+1 queries | ✅ Avoided | Single query with conditions |

---

## 7. Testing Checklist

### Notification List

- [ ] User dapat melihat notifications
- [ ] Pagination bekerja
- [ ] Filter unread bekerja
- [ ] Sort terbaru (DESC) bekerja
- [ ] Hanya menampilkan notifikasi sendiri

### Notification Detail

- [ ] User dapat melihat notification detail
- [ ] Return 404 jika notifikasi bukan milik sendiri
- [ ] Return 404 jika notifikasi tidak ada

### Mark As Read

- [ ] Mark as read bekerja
- [ ] is_read berubah ke 1
- [ ] Return 404 jika notifikasi bukan milik sendiri

### Mark All As Read

- [ ] Mark all as read bekerja
- [ ] Semua is_read berubah ke 1
- [ ] Hanya untuk notifikasi sendiri

### Unread Count

- [ ] Unread count benar
- [ ] Update setelah mark as read

### Task Events

- [ ] Task Created menghasilkan notifikasi ke owner
- [ ] Task Accepted menghasilkan notifikasi ke owner
- [ ] Task Started menghasilkan notifikasi ke owner
- [ ] Progress menghasilkan notifikasi ke owner
- [ ] Task Submitted menghasilkan notifikasi ke owner
- [ ] Task Completed menghasilkan notifikasi ke helper
- [ ] Task Cancelled menghasilkan notifikasi ke helper

### Review Events

- [ ] Review Received menghasilkan notifikasi ke helper

### Wallet Events

- [ ] Payment Released menghasilkan notifikasi ke helper
- [ ] Withdraw Requested menghasilkan notifikasi ke user
- [ ] Withdraw Approved menghasilkan notifikasi ke user
- [ ] Withdraw Rejected menghasilkan notifikasi ke user

### Security

- [ ] Tidak ada IDOR
- [ ] User hanya melihat notifikasi sendiri
- [ ] Mark read hanya untuk notifikasi sendiri
- [ ] Semua logic berada di Service

---

**End of Report**
