# TECH_DEBT.md

# BANTUIN YUK - TECHNICAL DEBT REGISTER

## Status

Project masih dalam fase MVP.

Debt berikut sengaja ditunda untuk mempercepat delivery MVP.

Debt ini wajib direview sebelum production deployment.

---

# TD-001

## Withdraw Balance Hold

### Priority

HIGH

### Current Implementation

Ketika user melakukan withdraw:

Withdraw Request
↓
Balance langsung dikurangi
↓
Transaction status = pending
↓
Menunggu approval admin

### Problem

Jika admin terlambat melakukan approval:

* Saldo user terlihat berkurang
* Dana sebenarnya belum keluar
* Sulit membedakan available balance dan pending balance

### Current Risk

Medium

### Recommended Solution

Tambahkan:

wallets.available_balance

wallets.pending_balance

Flow:

Withdraw Request
↓
available_balance -= amount
pending_balance += amount

Admin Approve
↓
pending_balance -= amount

Admin Reject
↓
available_balance += amount
pending_balance -= amount

### Target Sprint

Post-MVP

---

# TD-002

## Payment Release Idempotency

### Priority

CRITICAL

### Current Implementation

Check:

hasPaymentForTask(task_id)

↓

Create Transaction

### Problem

Race condition dapat terjadi:

Request A
↓

Check = false

Request B
↓

Check = false

Request A create payment

Request B create payment

Double payment mungkin terjadi.

### Current Risk

High

### Recommended Solution

Database Constraint:

UNIQUE(task_id, type)

atau

Database Locking

atau

SELECT FOR UPDATE

### Target Sprint

Before Production

---

# TD-003

## Automatic Payment Release

### Priority

MEDIUM

### Current Implementation

Task Completed
↓

POST /wallet/release-payment/{taskId}

### Problem

User dapat lupa melakukan release payment.

Flow bisnis tidak sepenuhnya otomatis.

### Recommended Solution

TaskService::completeTask()

↓

WalletService::releasePayment()

dalam transaction yang sama.

### Target Sprint

Post-MVP

---

# TD-004

## Attachment Storage

### Priority

LOW

### Current Implementation

File disimpan pada local filesystem.

uploads/tasks/

### Problem

Tidak scalable untuk multiple server.

### Recommended Solution

Gunakan:

* MinIO
* AWS S3
* Cloudflare R2

### Target Sprint

Production Scaling

---

# TD-005

## Rating Recalculation

### Priority

LOW

### Current Implementation

AVG rating dihitung ulang setiap review baru.

### Problem

Semakin banyak review semakin mahal query.

### Recommended Solution

Simpan:

users.rating

users.total_reviews

Update incremental.

### Target Sprint

Production Scaling

---

# TD-006

## Notification Queue

### Priority

LOW

### Current Implementation

Belum diimplementasikan.

### Recommended Solution

Gunakan:

* Queue Job
* Event Driven Notification

### Target Sprint

Notification Refactor

---

# Debt Review Schedule

Review seluruh debt:

* Sebelum Production Deployment
* Sebelum Scaling > 1.000 User
* Sebelum Mobile App Release

END
