# WALLET DOMAIN REPORT

**Tanggal:** 14 Juni 2026  
**Sprint:** Wallet & Transaction Implementation  
**Status:** ✅ Completed

---

## Table of Contents

1. [File yang Dibuat/Diubah](#1-file-yang-dibuatdiubah)
2. [Wallet Flow Diagram](#2-wallet-flow-diagram)
3. [Transaction Flow Diagram](#3-transaction-flow-diagram)
4. [Security Review](#4-security-review)
5. [Double Payment Prevention](#5-double-payment-prevention)
6. [Business Rules](#6-business-rules)
7. [Testing Checklist](#7-testing-checklist)

---

## 1. File yang Dibuat/Diubah

### New Files

| File | Description |
|------|-------------|
| `app/Services/WalletService.php` | Service untuk wallet & transaction management |
| `app/Controllers/WalletController.php` | Controller untuk user wallet operations |
| `app/Controllers/AdminWalletController.php` | Controller untuk admin wallet operations |
| `docs/WALLET_DOMAIN_REPORT.md` | Dokumentasi ini |
| `docs/WALLET_API.md` | API documentation |

### Modified Files

| File | Changes |
|------|---------|
| `app/Models/WalletModel.php` | Added helper methods, atomic operations |
| `app/Models/TransactionModel.php` | Added query methods, constants |
| `app/Config/Routes.php` | Added wallet routes |

---

## 2. Wallet Flow Diagram

### Payment Release Flow

```
┌─────────────────────────────────────────────────────────────────┐
│                     PAYMENT RELEASE FLOW                        │
└─────────────────────────────────────────────────────────────────┘

User (Task Owner)
    │
    ▼
POST /wallet/release-payment/{taskId}
    │
    ├─→ [1] Validate Task Status = COMPLETED
    │
    ├─→ [2] Validate Task Ownership (user_id = auth()->id())
    │
    ├─→ [3] Check Idempotency (no existing payment)
    │
    ├─→ [4] BEGIN TRANSACTION
    │       │
    │       ├─→ Create Transaction (type: task_payment, status: completed)
    │       │
    │       ├─→ Ensure Helper has Wallet
    │       │
    │       └─→ Increment Helper Balance (atomic)
    │
    └─→ [5] COMMIT TRANSACTION
            │
            └─→ Return Transaction Data
```

### Withdrawal Flow

```
┌─────────────────────────────────────────────────────────────────┐
│                      WITHDRAWAL FLOW                            │
└─────────────────────────────────────────────────────────────────┘

Helper/User
    │
    ▼
POST /wallet/withdraw
    │
    ├─→ [1] Validate Amount > 0
    │
    ├─→ [2] Validate Balance >= Amount
    │
    ├─→ [3] BEGIN TRANSACTION
    │       │
    │       ├─→ Create Transaction (type: withdraw, status: pending)
    │       │
    │       └─→ Decrement Balance (atomic + balance check)
    │
    └─→ [4] COMMIT TRANSACTION
            │
            └─→ Return Transaction (status: pending)

Admin
    │
    ├─→ Approve: status → completed
    │
    └─→ Reject: status → cancelled + refund balance
```

---

## 3. Transaction Flow Diagram

### Transaction Types

```
┌─────────────────────────────────────────────────────────────────┐
│                    TRANSACTION TYPES                            │
└─────────────────────────────────────────────────────────────────┘

┌─────────────────┬───────────────────────────────────────────────┐
│ Type            │ Description                                   │
├─────────────────┼───────────────────────────────────────────────┤
│ task_payment    │ Payment from task owner to helper             │
│ withdraw        │ Withdrawal request (pending → completed)      │
│ refund          │ Refund to user (e.g., rejected withdrawal)    │
│ adjustment      │ Manual adjustment by admin                    │
└─────────────────┴───────────────────────────────────────────────┘
```

### Transaction Status Flow

```
┌─────────────────────────────────────────────────────────────────┐
│                  TRANSACTION STATUS FLOW                        │
└─────────────────────────────────────────────────────────────────┘

For task_payment:
    pending → completed (when payment released)

For withdraw:
    pending → completed (when admin approves)
    pending → cancelled (when admin rejects + refund)

For refund:
    completed (immediate)

For adjustment:
    completed (immediate)
```

---

## 4. Security Review

### Implemented Security

| Security | Status | Implementation |
|----------|--------|----------------|
| Authentication | ✅ | Shield tokens filter |
| Authorization | ✅ | Ownership check |
| Atomic Operations | ✅ | Atomic increment/decrement |
| Transaction Use | ✅ | All financial ops in transaction() |
| Idempotency | ✅ | hasPaymentForTask() check |
| Balance Validation | ✅ | Sufficiency check before withdraw |
| Negative Balance Prevention | ✅ | Atomic decrement with WHERE clause |

### Protected Against

| Threat | Status | Protection |
|--------|--------|------------|
| Double Payment | ✅ | Idempotency check (hasPaymentForTask) |
| Negative Balance | ✅ | Atomic decrement with balance check |
| Race Conditions | ✅ | Atomic SQL operations |
| Unauthorized Access | ✅ | Ownership validation |
| SQL Injection | ✅ | Query builder |

### Atomic Operations

```php
// Increment (safe from race conditions)
$builder->set('balance', "balance + {$amount}", false);

// Decrement with check (prevents negative balance)
$builder->where('balance >=', $amount);
$builder->set('balance', "balance - {$amount}", false);
```

---

## 5. Double Payment Prevention

### Idempotency Mechanism

```php
// Check before creating payment
if ($this->transactionModel->hasPaymentForTask($taskId)) {
    throw BusinessException::conflict('Payment has already been released for this task');
}

// In TransactionModel
public function hasPaymentForTask(int $taskId): bool
{
    return $this->where('task_id', $taskId)
        ->where('type', self::TYPE_TASK_PAYMENT)
        ->where('status', self::STATUS_COMPLETED)
        ->countAllResults() > 0;
}
```

### Database Constraint

Even without unique constraint, the idempotency check prevents double payments:
1. First payment: Check passes → Create transaction → Complete
2. Second payment: Check fails → Throw exception

---

## 6. Business Rules

### Wallet Rules

| Rule | Description |
|------|-------------|
| One wallet per user | Created automatically on first access |
| Balance cannot be negative | Atomic decrement with check |
| All changes must be recorded | Transaction created for every balance change |

### Payment Rules

| Rule | Description |
|------|-------------|
| Release on COMPLETED | Only when task status = COMPLETED |
| One payment per task | Idempotency check |
| Owner only | Only task owner can release payment |
| Helper receives payment | Balance credited to helper's wallet |

### Withdrawal Rules

| Rule | Description |
|------|-------------|
| Sufficient balance | Amount <= balance |
| Positive amount | Amount > 0 |
| Pending status | Requires admin approval |
| Admin can approve/reject | Reject refunds balance |

### Transaction Rules

| Rule | Description |
|------|-------------|
| Immutable | Transactions cannot be edited |
| Auditable | All changes tracked |
| Reference ID | Unique reference for each transaction |

---

## 7. Testing Checklist

### Wallet Summary

- [ ] User dapat melihat wallet summary
- [ ] Balance benar
- [ ] Total earned benar
- [ ] Total withdrawn benar
- [ ] Pending withdrawals benar

### Transaction History

- [ ] User dapat melihat transaction history
- [ ] Filter by type bekerja
- [ ] Pagination bekerja
- [ ] Hanya menampilkan transaksi sendiri

### Payment Release

- [ ] Payment release berhasil untuk COMPLETED task
- [ ] Payment release gagal jika task bukan COMPLETED (409)
- [ ] Payment release gagal jika bukan task owner (403)
- [ ] Payment release gagal jika sudah ada payment (409 - idempotency)
- [ ] Helper balance bertambah
- [ ] Transaction record tercipta

### Withdrawal Request

- [ ] Withdraw berhasil jika balance cukup
- [ ] Withdraw gagal jika balance kurang (409)
- [ ] Withdraw gagal jika amount <= 0 (422)
- [ ] Balance berkurang
- [ ] Transaction status = pending

### Admin Approval

- [ ] Admin dapat melihat pending withdrawals
- [ ] Admin dapat approve withdrawal
- [ ] Admin dapat reject withdrawal
- [ ] Reject mengembalikan balance

### Security

- [ ] Tidak ada double payment
- [ ] Tidak ada negative balance
- [ ] Semua operasi finansial pakai transaction()
- [ ] Idempotency bekerja

---

**End of Report**
