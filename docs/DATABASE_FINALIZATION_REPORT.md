# DATABASE FINALIZATION REPORT

**Tanggal:** 14 Juni 2026  
**Status:** Semua database decisions sudah diimplementasi

---

## Ringkasan Perubahan

| # | Perubahan | File | Status |
|---|-----------|------|--------|
| 1 | tasks.category → category_id FK | FixDatabaseConstraints.php | ✅ |
| 2 | locations.helper_id FK → users.id | FixDatabaseConstraints.php | ✅ |
| 3 | wallets.user_id UNIQUE | FixDatabaseConstraints.php | ✅ |
| 4 | task_reviews UNIQUE (task_id, user_id) | FixDatabaseConstraints.php | ✅ |
| 5 | Performance indexes (5 indexes) | AddPerformanceIndexes.php | ✅ |
| 6 | TaskModel update | TaskModel.php | ✅ |
| 7 | LocationModel create | LocationModel.php | ✅ |

---

## Migration Files

### Migration 1: FixDatabaseConstraints

**File:** `app/Database\Migrations\2026-06-14-000001_FixDatabaseConstraints.php`

**Aksi:**

| # | Table | Perubahan | Detail |
|---|-------|-----------|--------|
| 1 | tasks | Ubah category → category_id | Tambah kolom, copy data, drop kolom lama, add FK |
| 2 | locations | Ubah FK target | Drop FK ke helper_profiles, add FK ke users |
| 3 | wallets | Tambah UNIQUE | Add unique constraint pada user_id |
| 4 | task_reviews | Tambah UNIQUE | Add unique constraint pada (task_id, user_id) |

**Data Safety:**
- ✅ Kolom baru ditambahkan dengan NULL dulu
- ✅ Data dicopy menggunakan lookup dari categories table
- ✅ Kolom lama di-drop SETELAH data tercopy
- ✅ Menggunakan cek existense sebelum alter (idempotent)
- ✅ Rollback path tersedia di method down()

**Rollback Instructions:**
```bash
php spark migrate:rollback -g default
```

---

### Migration 2: AddPerformanceIndexes

**File:** `app\Database\Migrations\2026-06-14-000002_AddPerformanceIndexes.php`

**Aksi:**

| # | Table | Index Name | Columns | Alasan |
|---|-------|------------|---------|--------|
| 1 | tasks | idx_tasks_status | status | Sering filter by status |
| 2 | transactions | idx_transactions_user_status | user_id, status | Query riwayat transaksi |
| 3 | transactions | idx_transactions_user_type | user_id, type | Filter by tipe transaksi |
| 4 | notifications | idx_notifications_user_read | user_id, is_read | Unread notifications |
| 5 | disputes | idx_disputes_status | status | Admin filter disputes |

**Data Safety:**
- ✅ Menggunakan cek existense sebelum add index (idempotent)
- ✅ Index naming konsisten (idx_table_column)
- ✅ Rollback path tersedia

---

## Updated Models

### TaskModel

**File:** `app/Models/TaskModel.php`

**Perubahan:**

```php
// SEBELUM
protected $allowedFields = [
    'category',   // ❌ VARCHAR
    ...
];

// SESUDAH
protected $allowedFields = [
    'category_id',  // ✅ FK ke categories.id
    ...
];
```

**Penambahan:**
- Status constants
- Relationship methods (getCategory, getUser, getHelper)

### LocationModel (BARU)

**File:** `app/Models/LocationModel.php`

**Fungsi:**
- `updateLocation(int $helperId, float $latitude, float $longitude)` - Upsert lokasi
- `getLocationByHelper(int $helperId)` - Ambil lokasi helper
- Relationship: `getHelper()` → belongsTo UserModel

---

## Database Schema Final

### tasks (UPDATED)

```
tasks
├── id (PK, BIGINT UNSIGNED, AUTO_INCREMENT)
├── user_id (FK → users.id, CASCADE)
├── helper_id (FK → users.id, SET NULL)
├── category_id (FK → categories.id, SET NULL) ← BARU
├── title (VARCHAR 255)
├── description (TEXT)
├── price (DECIMAL 15,2)
├── location (TEXT, NULLABLE)
├── deadline_start (DATETIME)
├── deadline_end (DATETIME)
├── status (ENUM: draft, open, accepted, in_progress, waiting_approval, completed, cancelled, disputed)
├── created_at (DATETIME)
├── updated_at (DATETIME)
└── INDEX: idx_tasks_status (status)
```

### locations (UPDATED)

```
locations
├── id (PK, BIGINT UNSIGNED, AUTO_INCREMENT)
├── helper_id (FK → users.id, CASCADE) ← DIUBAH dari helper_profiles.id
├── latitude (DECIMAL 10,8)
├── longitude (DECIMAL 11,8)
└── updated_at (DATETIME)
```

### wallets (UPDATED)

```
wallets
├── id (PK, BIGINT UNSIGNED, AUTO_INCREMENT)
├── user_id (FK → users.id, CASCADE, UNIQUE) ← UNIQUE DITAMBAH
├── balance (DECIMAL 15,2)
├── created_at (DATETIME)
└── updated_at (DATETIME)
```

### task_reviews (UPDATED)

```
task_reviews
├── id (PK, BIGINT UNSIGNED, AUTO_INCREMENT)
├── task_id (FK → tasks.id, CASCADE)
├── user_id (FK → users.id, CASCADE)
├── helper_id (FK → users.id, CASCADE)
├── rating (INT 1)
├── review (TEXT, NULLABLE)
├── created_at (DATETIME)
└── UNIQUE: (task_id, user_id) ← DITAMBAH
```

### transactions (UPDATED - indexes)

```
transactions
├── id (PK, BIGINT UNSIGNED, AUTO_INCREMENT)
├── user_id (FK → users.id, CASCADE)
├── task_id (FK → tasks.id, SET NULL)
├── amount (DECIMAL 15,2)
├── type (ENUM: topup, payment, withdraw, refund)
├── status (ENUM: pending, success, failed, cancelled)
├── reference_id (VARCHAR 100)
├── description (TEXT)
├── created_at (DATETIME)
├── updated_at (DATETIME)
├── INDEX: idx_transactions_user_status (user_id, status) ← BARU
└── INDEX: idx_transactions_user_type (user_id, type) ← BARU
```

### notifications (UPDATED - indexes)

```
notifications
├── id (PK, BIGINT UNSIGNED, AUTO_INCREMENT)
├── user_id (FK → users.id, CASCADE)
├── title (VARCHAR 255)
├── message (TEXT)
├── type (ENUM: task, payment, system, dispute)
├── is_read (BOOLEAN)
├── created_at (DATETIME)
└── INDEX: idx_notifications_user_read (user_id, is_read) ← BARU
```

### disputes (UPDATED - indexes)

```
disputes
├── id (PK, BIGINT UNSIGNED, AUTO_INCREMENT)
├── task_id (FK → tasks.id, CASCADE)
├── user_id (FK → users.id, CASCADE)
├── helper_id (FK → users.id, CASCADE)
├── reason (TEXT)
├── evidence_file (VARCHAR 255)
├── admin_note (TEXT)
├── status (ENUM: open, investigating, resolved, rejected)
├── resolved_by (FK → users.id, SET NULL)
├── resolved_at (DATETIME)
├── created_at (DATETIME)
├── updated_at (DATETIME)
└── INDEX: idx_disputes_status (status) ← BARU
```

---

## Cara Jalankan Migration

### Fresh Database (Recommended)

```bash
# Drop semua tables, jalankan semua migration
php spark migrate:fresh
```

### Database dengan Data

```bash
# Jalankan migration baru saja
php spark migrate
```

**Catatan:** 
- Migration menggunakan idempotent checks (cek dulu sebelum alter)
- Aman dijalankan berulang kali
- Data existing tidak akan hilang

### Rollback

```bash
# Rollback migration terakhir
php spark migrate:rollback

# Rollback 2 migration terakhir
php spark migrate:rollback -n 2
```

---

## Verification Checklist

- [x] tasks.category_id FK ke categories.id
- [x] locations.helper_id FK ke users.id
- [x] wallets.user_id UNIQUE constraint
- [x] task_reviews UNIQUE (task_id, user_id)
- [x] tasks.status index
- [x] transactions composite indexes
- [x] notifications composite index
- [x] disputes.status index
- [x] TaskModel updated dengan category_id
- [x] LocationModel created
- [x] Semua migration idempotent (aman dijalankan berulang)
- [x] Rollback path tersedia

---

## Architecture Compliance

| Principle | Status |
|-----------|--------|
| Data Integrity (FK) | ✅ Semua FK sudah benar |
| Business Rules (UNIQUE) | ✅ 1 wallet/user, 1 review/task |
| Performance (INDEX) | ✅ 5 indexes ditambahkan |
| Consistency | ✅ locations FK ke users.id (konsisten) |
| Maintainability | ✅ Migration terstruktur, rollback tersedia |

---

**End of Report**
