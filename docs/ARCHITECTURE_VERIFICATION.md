# ARCHITECTURE VERIFICATION: BantuinYuk

**Tanggal:** 13 Juni 2026  
**Status:** Final Recommendations - Siap diikuti selama sisa project

---

## Table of Contents

1. [Namespace Controller](#1-namespace-controller)
2. [tasks.category Design](#2-taskscategory-design)
3. [locations.helper_id FK Target](#3-locationshelper_id-fk-target)
4. [wallets.user_id Unique Constraint](#4-walletsuser_id-unique-constraint)
5. [task_reviews Unique Constraint](#5-task_reviews-unique-constraint)
6. [Foreign Key Index Analysis](#6-foreign-key-index-analysis)
7. [Final Architecture Decisions](#7-final-architecture-decisions)

---

## 1. Namespace Controller

### Pertanyaan
Apakah `App\Controllers\Api` atau `App\Controllers` lebih tepat?

### Analisis

**Struktur folder saat ini:**
```
app/Controllers/
├── AdminController.php
├── AuthController.php
├── BaseController.php
├── DisputeController.php
├── HelperController.php
├── Home.php
├── ReviewController.php
├── TaskController.php
├── UserController.php
└── WalletController.php
```

**Temuan:**
- Tidak ada subdirectory `Api/` di dalam `app/Controllers/`
- Semua controller berada langsung di `app/Controllers/`
- CodeIgniter 4 default namespace: `App\Controllers`
- Routes CI4 menggunakan format `'Controller::method'` yang resolve ke `App\Controllers\{Controller}`
- Jika menggunakan `App\Controllers\Api`, route harus `'Api\AuthController::method'`

**Pertimbangan Alternatif:**

| Approach | Kelebihan | Kekurangan |
|----------|-----------|------------|
| `App\Controllers` | Konsisten dengan CI4 default, simple | Tidak ada namespace grouping |
| `App\Controllers\Api` + subdirectory | Grouping API controllers | Harus buat subdirectory, route lebih panjang |
| `App\Controllers\Api` tanpa subdirectory | Namespace grouping | Inconsistent dengan folder structure, autoloading error |

### Keputusan FINAL

**Gunakan `App\Controllers`**

Alasan:
1. Konsisten dengan struktur folder yang ada
2. Sesuai CI4 convention
3. Route resolution otomatis tanpa prefix
4. Tidak perlu buat subdirectory baru

### Action Items

- [x] AuthController sudah di-fix ke `App\Controllers`
- [x] TaskController sudah di-fix ke `App\Controllers`
- [ ] Semua controller baru wajib menggunakan `App\Controllers`

---

## 2. tasks.category Design

### Pertanyaan
Apakah `tasks.category` sebaiknya tetap VARCHAR atau diubah menjadi `category_id` FK ke `categories.id`?

### Analisis

**Current State:**
```php
// Migration tasks
'category' => [
    'type' => 'VARCHAR',
    'constraint' => 100,
]
```

```php
// Migration categories
'id' => ['type' => 'BIGINT', 'auto_increment' => true]
'name' => ['type' => 'VARCHAR', 'constraint' => 100]
'icon' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true]
'status' => ['type' => 'ENUM', 'constraint' => ['active', 'inactive']]
```

**Pertimbangan:**

| Aspek | VARCHAR (Current) | category_id FK |
|-------|-------------------|----------------|
| Data Integrity | ❌ Bisa input kategori tidak valid | ✅ FK enforce valid category |
| Storage | ❌ Redundant (nama category diulang) | ✅ Efficient (simpan ID saja) |
| Query Performance | ❌ Full text search | ✅ Index-based JOIN |
| Category Rename | ❌ Harus update semua tasks | ✅ Update 1 baris di categories |
| Category Deletion | ❌ Tidak bisa (data integrity issue) | ✅ CASCADE atau SET NULL |
| Reporting | ❌ GROUP BY string (case-sensitive) | ✅ GROUP BY ID |

**Use Cases:**
1. **Filter tasks by category** → Lebih efisien dengan FK + INDEX
2. **Display category name** → Perlu JOIN, tapi sangat cepat dengan FK
3. **Admin manage categories** → Lebih mudah jika FK (rename/delete otomatis)
4. **Search tasks** → Lebih fleksibel dengan FK

### Keputusan FINAL

**Ubah ke `category_id` BIGINT FK ke `categories.id`**

Alasan:
1. Data integrity terjamin
2. Query performance lebih baik
3. Maintenance lebih mudah
4. Industry best practice

### Action Items

- [ ] Buat migration baru: `AlterTasksChangeCategoryId.php`
  - Tambah kolom `category_id` BIGINT UNSIGNED
  - Copy data dari `category` ke `category_id` (lookup by name)
  - Drop kolom `category`
  - Add FK ke `categories.id`
- [ ] Update TaskModel: `category_id` sudah di-fix sebelumnya
- [ ] Update semua controller yang use `category` field

### Migration Reference

```php
// 2026-06-13-xxxxxx_AlterTasksChangeCategoryId.php
public function up()
{
    // 1. Tambah kolom category_id
    $this->forge->addColumn('tasks', [
        'category_id' => [
            'type' => 'BIGINT',
            'unsigned' => true,
            'null' => true,
        ],
    ]);

    // 2. Copy data (category name → category_id)
    $db = \Config\Database::connect();
    $categories = $db->table('categories')->get()->getResultArray();
    foreach ($categories as $cat) {
        $db->table('tasks')
           ->where('category', $cat['name'])
           ->update(['category_id' => $cat['id']]);
    }

    // 3. Drop kolom lama
    $this->forge->dropColumn('tasks', 'category');

    // 4. Rename category_id → category (optional, atau tetap category_id)
    $this->forge->modifyColumn('tasks', [
        'category_id' => [
            'type' => 'BIGINT',
            'unsigned' => true,
        ],
    ]);

    // 5. Add FK
    $this->forge->addForeignKey(
        'category_id',
        'categories',
        'id',
        'SET NULL',
        'CASCADE'
    );
}
```

---

## 3. locations.helper_id FK Target

### Pertanyaan
Apakah `locations.helper_id` sebaiknya FK ke `users.id` atau `helper_profiles.id`?

### Analisis

**Current State:**
```php
// Migration locations
$this->forge->addForeignKey(
    'helper_id',
    'helper_profiles',  // FK ke helper_profiles.id
    'id',
    'CASCADE',
    'CASCADE'
);
```

**Relationship Chain:**
```
locations.helper_id → helper_profiles.id (CURRENT)
locations.helper_id → users.id (ALTERNATIVE)

helper_profiles.user_id → users.id (1:1, UNIQUE)
```

**Query Patterns:**

| Query | Current (FK → helper_profiles) | Alternative (FK → users) |
|-------|-------------------------------|--------------------------|
| Lokasi by user_id | `locations JOIN helper_profiles ON helper_profiles.id = locations.helper_id WHERE helper_profiles.user_id = ?` | `locations WHERE helper_id = ?` |
| Lokasi by helper_profile_id | `locations WHERE helper_id = ?` | `locations JOIN helper_profiles ON helper_profiles.user_id = locations.helper_id WHERE helper_profiles.id = ?` |
| Semua lokasi helper | `locations JOIN helper_profiles` | `locations` (langsung) |
| Helper profile + lokasi | `helper_profiles JOIN locations ON locations.helper_id = helper_profiles.id` | `helper_profiles JOIN locations ON locations.helper_id = helper_profiles.user_id` |

**Pertimbangan:**
1. **Naming**: Kolom `helper_id` secara natural merujuk ke "helper" (user), bukan "helper profile"
2. **Consistency**: Tabel lain (`tasks`, `transactions`, `disputes`) langsung FK ke `users.id`
3. **Simplicity**: Kurangi 1 JOIN untuk query paling umum (lokasi by user)
4. **Semantic**: `helper_profiles.id` adalah auto-increment ID profile, bukan ID user

### Keputusan FINAL

**Ubah FK ke `users.id`**

Alasan:
1. Konsisten dengan tabel lain
2. `helper_id` secara natural = user ID
3. Query lokasi by user_id lebih simpel
4. Helper adalah "user" yang punya role helper

### Action Items

- [ ] Buat migration baru: `AlterLocationsChangeHelperId.php`
  - Rename `helper_id` column (atau drop & recreate FK)
  - Change FK reference dari `helper_profiles.id` ke `users.id`

### Migration Reference

```php
// 2026-06-13-xxxxxx_AlterLocationsChangeHelperId.php
public function up()
{
    $db = \Config\Database::connect();

    // Drop existing FK
    $db->query('ALTER TABLE `locations` DROP FOREIGN KEY `locations_helper_id_foreign`');

    // Add new FK ke users
    $this->forge->addForeignKey(
        'helper_id',
        'users',
        'id',
        'CASCADE',
        'CASCADE'
    );
}
```

---

## 4. wallets.user_id Unique Constraint

### Pertanyaan
Apakah `wallets.user_id` perlu unique constraint?

### Analisis

**Current State:**
```php
// Migration wallets
$this->forge->addKey('id', true);  // PK only
$this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
// TIDAK ada unique constraint
```

**Business Rule:**
> "Satu user memiliki satu wallet"

**Risiko tanpa UNIQUE:**

| Scenario | Impact |
|----------|--------|
| User register | Auto-create wallet → bisa double insert |
| Race condition | 2 request bersamaan → 2 wallet |
| Bug di code | Loop create wallet → multiple entries |
| Query `wallet balance` | `WHERE user_id = ?` bisa return multiple rows |

**Contoh Bug:**
```php
// Jika tidak ada UNIQUE, ini bisa terjadi:
$wallet1 = $walletModel->where('user_id', $userId)->first(); // balance 100
$wallet2 = $walletModel->where('user_id', $userId)->first(); // balance 0 (baru dibuat)

// User punya 2 wallet dengan saldo berbeda!
```

### Keputusan FINAL

**WAJIB tambah UNIQUE constraint pada `wallets.user_id`**

Alasan:
1. Enforce business rule di database level
2. Prevent race condition
3. Prevent data inconsistency
4. Application-level check saja tidak cukup

### Action Items

- [ ] Buat migration baru: `AlterWalletsAddUniqueConstraint.php`
- [ ] Update WalletModel jika perlu

### Migration Reference

```php
// 2026-06-13-xxxxxx_AlterWalletsAddUniqueConstraint.php
public function up()
{
    $this->forge->addUniqueKey('user_id');
}
```

---

## 5. task_reviews Unique Constraint

### Pertanyaan
Apakah `task_reviews` perlu unique constraint `(task_id, user_id)`?

### Analisis

**Current State:**
```php
// Migration task_reviews
$this->forge->addKey('id', true);
$this->forge->addKey('task_id');
$this->forge->addKey('helper_id');
// TIDAK ada unique constraint
```

**Business Rule:**
> "User hanya bisa review 1x per task"

**Risiko tanpa UNIQUE:**

| Scenario | Impact |
|----------|--------|
| Double submit | User klik review 2x → 2 review |
| Bug di code | Loop create review → multiple reviews |
| Rating calculation | Avg rating terpengaruh duplikat |
| UI confusion | Menampilkan review yang sama berkali-kali |

**Contoh Bug:**
```php
// Tanpa UNIQUE, user bisa submit review 2x:
// Review 1: rating 5
// Review 2: rating 3 (user berubah pikiran)

// Average = (5+3)/2 = 4 (seharusnya 3)
```

### Keputusan FINAL

**WAJIB tambah UNIQUE constraint pada `(task_id, user_id)`**

Alasan:
1. One review per user per task (business rule)
2. Prevent double submit
3. Accurate rating calculation
4. Clean data

### Action Items

- [ ] Buat migration baru: `AlterTaskReviewsAddUniqueConstraint.php`
- [ ] Update TaskReviewModel jika perlu

### Migration Reference

```php
// 2026-06-13-xxxxxx_AlterTaskReviewsAddUniqueConstraint.php
public function up()
{
    $this->forge->addUniqueKey(['task_id', 'user_id']);
}
```

---

## 6. Foreign Key Index Analysis

### Apakah FK perlu explicit index?

**MySQL/InnoDB Behavior:**
- FK columns **otomatis di-index** oleh InnoDB
- Primary Key sudah di-index
- UNIQUE constraint sudah di-index

**Kapan perlu explicit index?**
1. Composite index untuk query tertentu
2. Covering index untuk query spesifik
3. Index untuk columns yang sering di-WHERE tapi bukan FK

### Analisis Per Tabel

#### tasks

| Column | Type | Index Status | Need Explicit Index? |
|--------|------|--------------|---------------------|
| `id` | PK | ✅ Auto | ❌ |
| `user_id` | FK | ✅ Auto | ⚠️ Opsional (sering WHERE) |
| `helper_id` | FK | ✅ Auto | ⚠️ Opsional (sering WHERE) |
| `status` | ENUM | ❌ | ✅ **WAJIB** (sering filter) |
| `category_id` | FK (future) | ✅ Auto | ⚠️ Opsional (sering WHERE) |

**Recommendation:**
```php
// Tambah index untuk status
$this->forge->addKey('status');
```

#### task_progress

| Column | Type | Index Status | Need Explicit Index? |
|--------|------|--------------|---------------------|
| `id` | PK | ✅ Auto | ❌ |
| `task_id` | FK | ✅ Auto | ❌ |
| `helper_id` | FK | ✅ Auto | ❌ |

**Recommendation:** Tidak perlu tambahan

#### transactions

| Column | Type | Index Status | Need Explicit Index? |
|--------|------|--------------|---------------------|
| `id` | PK | ✅ Auto | ❌ |
| `user_id` | FK | ✅ Auto | ⚠️ Opsional |
| `task_id` | FK | ✅ Auto | ⚠️ Opsional |
| `status` | ENUM | ❌ | ✅ **Disarankan** |
| `type` | ENUM | ❌ | ✅ **Disarankan** |

**Recommendation:**
```php
// Composite index untuk common query
$this->forge->addKey(['user_id', 'status']);
$this->forge->addKey(['user_id', 'type']);
```

#### notifications

| Column | Type | Index Status | Need Explicit Index? |
|--------|------|--------------|---------------------|
| `id` | PK | ✅ Auto | ❌ |
| `user_id` | FK | ✅ Auto | ⚠️ Opsional |
| `is_read` | BOOLEAN | ❌ | ✅ **Disarankan** |

**Recommendation:**
```php
// Composite index
$this->forge->addKey(['user_id', 'is_read']);
```

#### task_reviews

| Column | Type | Index Status | Need Explicit Index? |
|--------|------|--------------|---------------------|
| `id` | PK | ✅ Auto | ❌ |
| `task_id` | FK | ✅ Auto | ❌ |
| `user_id` | FK | ✅ Auto | ❌ (akan di-UNIQUE) |
| `helper_id` | FK | ✅ Auto | ❌ |

**Recommendation:** UNIQUE constraint sudah cukup

#### disputes

| Column | Type | Index Status | Need Explicit Index? |
|--------|------|--------------|---------------------|
| `id` | PK | ✅ Auto | ❌ |
| `task_id` | FK | ✅ Auto | ❌ |
| `user_id` | FK | ✅ Auto | ❌ |
| `helper_id` | FK | ✅ Auto | ❌ |
| `status` | ENUM | ❌ | ✅ **Disarankan** |

**Recommendation:**
```php
$this->forge->addKey('status');
```

#### task_status_histories

| Column | Type | Index Status | Need Explicit Index? |
|--------|------|--------------|---------------------|
| `id` | PK | ✅ Auto | ❌ |
| `task_id` | FK | ✅ Auto | ❌ |
| `created_by` | FK | ✅ Auto | ❌ |

**Recommendation:** Tidak perlu tambahan

#### locations

| Column | Type | Index Status | Need Explicit Index? |
|--------|------|--------------|---------------------|
| `id` | PK | ✅ Auto | ❌ |
| `helper_id` | FK | ✅ Auto | ⚠️ Opsional (akan di-FK ke users) |

**Recommendation:** FK index sudah cukup

### Keputusan FINAL

**Index yang WAJIB ditambahkan:**

| Tabel | Kolom | Tipe Index | Alasan |
|-------|-------|------------|--------|
| `tasks` | `status` | Single | Sering filter by status |
| `transactions` | `user_id, status` | Composite | Query riwayat transaksi |
| `transactions` | `user_id, type` | Composite | Filter by tipe transaksi |
| `notifications` | `user_id, is_read` | Composite | Unread notifications |
| `disputes` | `status` | Single | Admin filter disputes |

### Action Items

- [ ] Buat migration: `AddMissingIndexes.php`

---

## 7. Final Architecture Decisions

### Ringkasan Keputusan

| # | Issue | Keputusan | Status |
|---|-------|-----------|--------|
| 1 | Namespace | `App\Controllers` (tanpa \Api) | ✅ Fixed |
| 2 | tasks.category | Ubah ke `category_id` FK | ⚠️ Perlu migration |
| 3 | locations.helper_id | Ubah FK ke `users.id` | ⚠️ Perlu migration |
| 4 | wallets.user_id | Tambah UNIQUE constraint | ⚠️ Perlu migration |
| 5 | task_reviews | Tambah UNIQUE (task_id, user_id) | ⚠️ Perlu migration |
| 6 | Indexes | Tambah 5 indexes | ⚠️ Perlu migration |

### Migration Roadmap

Buat 2 migration files baru:

#### Migration 1: `2026-06-14-xxxxxx_FixDatabaseConstraints.php`

```php
public function up()
{
    // 1. Fix tasks.category → category_id
    // 2. Fix locations.helper_id FK
    // 3. Add wallets.user_id UNIQUE
    // 4. Add task_reviews UNIQUE (task_id, user_id)
}
```

#### Migration 2: `2026-06-14-xxxxxx_AddPerformanceIndexes.php`

```php
public function up()
{
    // 1. tasks.status index
    // 2. transactions composite indexes
    // 3. notifications composite index
    // 4. disputes.status index
}
```

### Checklist Final

**Phase 1 - Database (SELESAI):**
- [x] Database schema
- [x] Basic migrations
- [x] Models
- [ ] Fix tasks.category → category_id
- [ ] Fix locations.helper_id FK
- [ ] Add wallets.user_id UNIQUE
- [ ] Add task_reviews UNIQUE
- [ ] Add performance indexes

**Phase 2 - Authentication (BELUM):**
- [ ] Shield setup
- [ ] Access tokens
- [ ] AuthController (sudah fixed)
- [ ] Middleware

**Architecture Principles (UNTUK DIIKUTI):**
1. ✅ Semua controller: `namespace App\Controllers;`
2. ✅ Selalu gunakan `auth()->id()` untuk user_id
3. ✅ Selalu gunakan `ApiResponseTrait` untuk response
4. ✅ Service Layer Pattern: `Controller → Service → Model`
5. ✅ Transaction untuk operasi kritis
6. ✅ Input validation sebelum insert/update
7. ✅ Error handling dengan try-catch

---

**End of Architecture Verification**
