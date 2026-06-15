# PROJECT ANALYSIS: BantuinYuk

**Tanggal Analisis:** 13 Juni 2026  
**Analyst:** Kilo AI  
**Status:** Phase 1 (Database & Models selesai, Implementation belum dimulai)

---

## Table of Contents

1. [Ringkasan Eksekutif](#1-ringkasan-eksekutif)
2. [Struktur Database](#2-struktur-database)
3. [Relasi Tabel](#3-relasi-tabel)
4. [Potensi Masalah Arsitektur](#4-potensi-masalah-arsitektur)
5. [Missing Feature](#5-missing-feature)
6. [Missing Endpoint](#6-missing-endpoint)
7. [Potensi Security Issue](#7-potensi-security-issue)
8. [Rekomendasi Prioritas](#8-rekomendasi-prioritas)

---

## 1. Ringkasan Eksekutif

BantuinYuk adalah platform outsourcing jasa dengan 3 role (Admin, User, Helper). Project menggunakan CodeIgniter 4 + MySQL + CodeIgniter Shield. Saat ini database schema dan model sudah dibuat, namun terdapat beberapa **inkonsistensi kritis** antara migration, model, dan controller yang perlu segera diperbaiki sebelum melanjutkan ke Phase 2.

### Status Implementasi

| Komponen | Status |
|----------|--------|
| Database Migration | ✅ 13 tabel |
| Models | ✅ 12 model |
| Controllers | ⚠️ 10 controller (sebagian besar stub) |
| Services | ❌ Belum ada |
| Routes | ❌ Hanya Shield routes |
| Filters/Middleware | ❌ Belum ada |
| Validation | ❌ Belum ada |
| Error Handling | ❌ Belum ada |

---

## 2. Struktur Database

### 2.1 Ringkasan Tabel

| Tabel | Fungsi | Record Estimasi |
|-------|--------|-----------------|
| `users` | Data pengguna (admin/user/helper) | High |
| `tasks` | Data pekerjaan | High |
| `wallets` | Saldo pengguna | High |
| `task_attachments` | Lampiran task | Medium |
| `task_progress` | Progres pengerjaan | Medium |
| `transactions` | Riwayat transaksi | High |
| `notifications` | Notifikasi | High |
| `disputes` | Sengketa | Low |
| `task_reviews` | Review/-rating | Medium |
| `categories` | Kategori layanan | Low |
| `task_status_histories` | Riwayat status task | High |
| `helper_profiles` | Profil helper | Medium |
| `locations` | Lokasi helper | Medium |

### 2.2 Identifikasi Masalah Database

#### CRITICAL: tasks.category tidak relasi ke categories

```
Migration: tasks.category = VARCHAR(100) -- string biasa
Migration: categories.id = BIGINT -- tabel kategori ada
TaskModel: allowedFields includes 'category_id' -- field tidak ada di migration
```

**Impact:** Task tidak bisa filter by category secara relational. Field `category_id` di TaskModel tidak ada di database.

#### CRITICAL: HelperProfileModel memiliki field yang tidak ada di migration

```
HelperProfileModel.allowedFields: ['latitude', 'longitude']
Migration helper_profiles: TIDAK ada kolom latitude/longitude
Migration locations: ada kolom latitude/longitude, FK ke helper_profiles.id
```

**Impact:** Jika model digunakan untuk insert/update, akan gagal karena kolom tidak ada.

#### WARNING: task_reviews.rating hanya INT(1)

```
task_reviews.rating = INT(1) -- hanya bisa 0-9
```

**Impact:** Jika rating scale 1-5, ini cukup. Namun jika 1-10, tetap cukup. Tidak ada validasi range.

#### WARNING: wallets tidak ada unique constraint pada user_id

```php
// Migration wallets - tidak ada addUniqueKey('user_id')
```

**Impact:** Satu user bisa memiliki multiple wallet entries.

#### WARNING: task_reviews tidak ada unique constraint

```php
// Tidak ada: addUniqueKey(['task_id', 'user_id'])
```

**Impact:** User bisa review task yang sama berkali-kali.

#### INFO: ENUM status pada tasks

```php
'status' => ['draft', 'open', 'accepted', 'in_progress', 'waiting_approval', 'completed', 'cancelled', 'disputed']
```

**Impact:** ENUM sulit diubah di MySQL. Pertimbangkan VARCHAR atau tabel lookup untuk status.

---

## 3. Relasi Tabel

### 3.1 Entity Relationship

```
users (1) ──── (1) wallets
users (1) ──── (1) helper_profiles
users (1) ──── (N) tasks [user_id]
users (1) ──── (N) tasks [helper_id]
users (1) ──── (N) transactions
users (1) ──── (N) notifications
users (1) ──── (N) task_reviews [user_id]
users (1) ──── (N) task_reviews [helper_id]
users (1) ──── (N) disputes [user_id]
users (1) ──── (N) disputes [helper_id]
users (1) ──── (N) task_status_histories [created_by]

tasks (1) ──── (N) task_attachments
tasks (1) ──── (N) task_progress
tasks (1) ──── (N) disputes
tasks (1) ──── (N) task_reviews
tasks (1) ──── (N) task_status_histories
tasks (1) ──── (N) transactions

helper_profiles (1) ──── (1) locations [helper_id FK]
```

### 3.2 Masalah Relasi

#### CRITICAL: locations.helper_id FK ke helper_profiles.id

```php
// Migration locations:
$this->forge->addForeignKey(
    'helper_id',
    'helper_profiles',  // seharusnya 'users'
    'id',
    'CASCADE',
    'CASCADE'
);
```

**Impact:**
- Query lokasi memerlukan JOIN 2 tabel (locations → helper_profiles → users)
- Tidak bisa langsung query lokasi by user_id
- Inconsistent dengan tabel lain yang langsung FK ke users

#### WARNING: tasks.category tidak ada FK ke categories

```
tasks.category = VARCHAR(100) -- string, bukan FK
categories.id = BIGINT -- tabel ada tapi tidak terpakai
```

**Impact:**
- Data integrity tidak terjaga (bisa input kategori yang tidak ada)
- Tidak bisa query categories beserta jumlah task

#### WARNING: Tidak ada index pada kolom yang sering di-query

```php
// Tidak ada index pada:
// - tasks.status (sering filter)
// - tasks.user_id (sudah ada FK, tapi tidak explicitly indexed)
// - tasks.helper_id (sudah ada FK, tapi tidak explicitly indexed)
// - notifications.is_read (sering filter)
```

---

## 4. Potensi Masalah Arsitektur

### 4.1 Namespace Inconsistency (CRITICAL)

```php
// AuthController.php
namespace App\Controllers\Api;  // ← ada \Api

// TaskController.php
namespace App\Controllers\Api;  // ← ada \Api

// HelperController.php
namespace App\Controllers;      // ← TIDAK ada \Api

// Semua controller lain
namespace App\Controllers;      // ← TIDAK ada \Api
```

**Impact:** autoloading error, route tidak bisa resolve controller.

### 4.2 Use Statement Error (CRITICAL)

```php
// HelperController.php
use app\Models\TaskModel;  // ← lowercase 'app'

// Seharusnya:
use App\Models\TaskModel;  // ← uppercase 'App'
```

**Impact:** Fatal error saat controller di-load.

### 4.3 Tidak Ada Service Layer (CRITICAL)

Arsitektur mensyaratkan:
```
Controller → Service → Model → Database
```

Yang ada saat ini:
```
Controller → Model → Database
```

**Impact:**
- Business logic bercampur dengan controller
- Tidak ada reusable logic
- Sulit untuk testing
- Melanggar arsitektur yang ditetapkan

### 4.4 Tidak Ada Input Validation (HIGH)

```php
// TaskController::store() - tidak ada validasi
public function store()
{
    $data = $this->request->getJSON(true);
    $id = $this->taskModel->insert([...]);  // langsung insert
}
```

**Impact:**
- Data invalid bisa masuk ke database
- SQL injection potential (meski CI4 query builder relatif aman)
- Error tidak ter-handle dengan baik

### 4.5 Tidak Ada Error Handling (HIGH)

```php
// HelperController::acceptTask()
$this->taskModel->update($taskId, [...]);
// Tidak ada pengecekan apakah update berhasil
```

**Impact:** User tidak tahu operasi berhasil/gagal.

### 4.6 Tidak Ada Transaction (CRITICAL)

Master System Prompt mensyaratkan transaction untuk:
- Accept Task
- Submit Task
- Complete Task
- Withdraw
- Resolve Dispute

Saat ini tidak ada transaction sama sekali.

### 4.7 Tidak Ada Authentication/Authorization (HIGH)

```php
// TaskController::index() - tidak ada proteksi
public function index()
{
    return $this->response->setJSON(
        $this->taskModel->findAll()  // semua orang bisa lihat semua task
    );
}
```

### 4.8 Tidak Ada Routes (CRITICAL)

```php
// Routes.php - hanya 2 routes
$routes->get('/', 'Home::index');
service('auth')->routes($routes);
```

Tidak ada route untuk:
- Task CRUD
- Helper operations
- Wallet operations
- Review operations
- Dispute operations
- Admin operations

### 4.9 Controller Namespace vs Routes Mismatch

AuthController di namespace `App\Controllers\Api` tapi tidak ada route yang mengarah ke sana. Jika routes ditambahkan nanti, perhatikan prefix yang benar.

---

## 5. Missing Feature

### 5.1 Phase 1 (Database & Models)

| Feature | Status | Catatan |
|---------|--------|---------|
| Database Design | ⚠️ | Perlu perbaikan relasi |
| Migration | ⚠️ | Perlu perbaikan FK locations |
| Models | ⚠️ | Perlu perbaikan allowedFields |

### 5.2 Phase 2 (Authentication)

| Feature | Status | Prioritas |
|---------|--------|-----------|
| Shield Setup | ❌ | HIGH |
| Access Token | ❌ | HIGH |
| AuthController register | ⚠️ | HIGH |
| AuthController login | ⚠️ | HIGH |
| Email verification | ❌ | MEDIUM |
| Password reset | ❌ | MEDIUM |
| Logout | ❌ | HIGH |

### 5.3 Phase 3 (Task Management)

| Feature | Status | Prioritas |
|---------|--------|-----------|
| Task CRUD | ⚠️ | HIGH |
| Task search/filter | ❌ | HIGH |
| Task status transition | ❌ | HIGH |
| Task validation | ❌ | HIGH |
| Task authorization | ❌ | HIGH |

### 5.4 Phase 4 (Helper)

| Feature | Status | Prioritas |
|---------|--------|-----------|
| Accept task | ⚠️ | HIGH |
| Helper profile CRUD | ❌ | HIGH |
| Helper search | ❌ | HIGH |
| Location tracking | ❌ | MEDIUM |
- Helper verification | ❌ | MEDIUM |

### 5.5 Phase 5 (Task Progress)

| Feature | Status | Prioritas |
|---------|--------|-----------|
| Task attachment upload | ❌ | MEDIUM |
| Task progress update | ❌ | MEDIUM |
| Progress validation | ❌ | MEDIUM |

### 5.6 Phase 6 (Review System)

| Feature | Status | Prioritas |
|---------|--------|-----------|
| Review CRUD | ❌ | MEDIUM |
| Rating calculation | ❌ | MEDIUM |
| Review validation | ❌ | MEDIUM |

### 5.7 Phase 7 (Wallet System)

| Feature | Status | Prioritas |
|---------|--------|-----------|
| Wallet balance | ❌ | HIGH |
| Top-up | ❌ | HIGH |
| Withdraw | ❌ | HIGH |
| Payment processing | ❌ | HIGH |
| Transaction history | ❌ | HIGH |

### 5.8 Phase 8 (Notification)

| Feature | Status | Prioritas |
|---------|--------|-----------|
| Notification CRUD | ❌ | MEDIUM |
| Push notification | ❌ | LOW |
| Email notification | ❌ | LOW |

### 5.9 Phase 9 (Dispute)

| Feature | Status | Prioritas |
|---------|--------|-----------|
| Dispute CRUD | ❌ | MEDIUM |
| Admin resolution | ❌ | MEDIUM |
| Dispute validation | ❌ | MEDIUM |

### 5.10 Phase 10 (Admin Dashboard)

| Feature | Status | Prioritas |
|---------|--------|-----------|
| User management | ❌ | MEDIUM |
| Task management | ❌ | MEDIUM |
| Report/Analytics | ❌ | LOW |
| Category management | ❌ | MEDIUM |

### 5.11 Missing Core Features

| Feature | Status | Prioritas |
|---------|--------|-----------|
| Service Layer Pattern | ❌ | CRITICAL |
| Input Validation | ❌ | CRITICAL |
| Error Handling | ❌ | CRITICAL |
| API Versioning | ❌ | HIGH |
| Rate Limiting | ❌ | HIGH |
| CORS Configuration | ❌ | HIGH |
| API Documentation | ❌ | MEDIUM |
| Logging | ❌ | MEDIUM |
| Caching | ❌ | LOW |
| Queue/Job System | ❌ | LOW |

---

## 6. Missing Endpoint

### 6.1 Authentication Endpoints

| Method | Endpoint | Fungsi | Status |
|--------|----------|--------|--------|
| POST | `/api/v1/auth/register` | Register | ⚠️ Ada, tanpa validasi |
| POST | `/api/v1/auth/login` | Login | ⚠️ Ada, return password hash |
| POST | `/api/v1/auth/logout` | Logout | ❌ |
| POST | `/api/v1/auth/forgot-password` | Lupa password | ❌ |
| POST | `/api/v1/auth/reset-password` | Reset password | ❌ |
| GET | `/api/v1/auth/verify-email` | Verifikasi email | ❌ |
| GET | `/api/v1/auth/me` | Profil user | ❌ |

### 6.2 Task Endpoints

| Method | Endpoint | Fungsi | Status |
|--------|----------|--------|--------|
| GET | `/api/v1/tasks` | List tasks | ⚠️ Ada, tanpa filter |
| GET | `/api/v1/tasks/{id}` | Detail task | ⚠️ Ada |
| POST | `/api/v1/tasks` | Buat task | ⚠️ Ada, tanpa validasi |
| PUT | `/api/v1/tasks/{id}` | Update task | ❌ |
| DELETE | `/api/v1/tasks/{id}` | Hapus task | ❌ |
| GET | `/api/v1/tasks/my` | Task saya | ❌ |
| POST | `/api/v1/tasks/{id}/accept` | Terima task | ⚠️ Ada, tanpa auth |
| POST | `/api/v1/tasks/{id}/start` | Mulai kerja | ❌ |
| POST | `/api/v1/tasks/{id}/submit` | Submit hasil | ❌ |
| POST | `/api/v1/tasks/{id}/complete` | Selesaikan task | ❌ |
| POST | `/api/v1/tasks/{id}/cancel` | Batalkan task | ❌ |
| POST | `/api/v1/tasks/{id}/dispute` | Dispute task | ❌ |

### 6.3 Helper Endpoints

| Method | Endpoint | Fungsi | Status |
|--------|----------|--------|--------|
| GET | `/api/v1/helpers` | List helpers | ❌ |
| GET | `/api/v1/helpers/{id}` | Detail helper | ❌ |
| PUT | `/api/v1/helpers/profile` | Update profil | ❌ |
| GET | `/api/v1/helpers/tasks` | Task tersedia | ❌ |
| POST | `/api/v1/helpers/verify` | Verifikasi KTP | ❌ |
| PUT | `/api/v1/helpers/location` | Update lokasi | ❌ |
| GET | `/api/v1/helpers/location/{id}` | Lokasi helper | ❌ |

### 6.4 Wallet Endpoints

| Method | Endpoint | Fungsi | Status |
|--------|----------|--------|--------|
| GET | `/api/v1/wallet` | Cek saldo | ❌ |
| POST | `/api/v1/wallet/topup` | Top up | ❌ |
| POST | `/api/v1/wallet/withdraw` | Tarik dana | ❌ |
| GET | `/api/v1/wallet/transactions` | Riwayat transaksi | ❌ |

### 6.5 Review Endpoints

| Method | Endpoint | Fungsi | Status |
|--------|----------|--------|--------|
| POST | `/api/v1/tasks/{id}/review` | Beri review | ❌ |
| GET | `/api/v1/tasks/{id}/review` | Lihat review | ❌ |
| GET | `/api/v1/helpers/{id}/reviews` | Review helper | ❌ |

### 6.6 Dispute Endpoints

| Method | Endpoint | Fungsi | Status |
|--------|----------|--------|--------|
| POST | `/api/v1/disputes` | Buat dispute | ❌ |
| GET | `/api/v1/disputes/{id}` | Detail dispute | ❌ |
| PUT | `/api/v1/disputes/{id}/resolve` | Selesaikan dispute | ❌ |
| GET | `/api/v1/admin/disputes` | List disputes (admin) | ❌ |

### 6.7 Notification Endpoints

| Method | Endpoint | Fungsi | Status |
|--------|----------|--------|--------|
| GET | `/api/v1/notifications` | List notifikasi | ❌ |
| PUT | `/api/v1/notifications/{id}/read` | Tandai sudah baca | ❌ |
| PUT | `/api/v1/notifications/read-all` | Tandai semua baca | ❌ |

### 6.8 Admin Endpoints

| Method | Endpoint | Fungsi | Status |
|--------|----------|--------|--------|
| GET | `/api/v1/admin/users` | List users | ❌ |
| PUT | `/api/v1/admin/users/{id}/status` | Suspend user | ❌ |
| GET | `/api/v1/admin/tasks` | List semua tasks | ❌ |
| GET | `/api/v1/admin/categories` | List kategori | ❌ |
| POST | `/api/v1/admin/categories` | Tambah kategori | ❌ |
| PUT | `/api/v1/admin/categories/{id}` | Update kategori | ❌ |
| DELETE | `/api/v1/admin/categories/{id}` | Hapus kategori | ❌ |
| GET | `/api/v1/admin/dashboard` | Dashboard stats | ❌ |

### 6.9 Category Endpoints

| Method | Endpoint | Fungsi | Status |
|--------|----------|--------|--------|
| GET | `/api/v1/categories` | List kategori | ❌ |
| GET | `/api/v1/categories/{id}` | Detail kategori | ❌ |

---

## 7. Potensi Security Issue

### 7.1 CRITICAL

#### 7.1.1 Password Hash dikembalikan di Response

```php
// AuthController::login()
return $this->response->setJSON([
    'success' => true,
    'user' => $user  // ← $user['password'] berisi hash!
]);
```

**Risk:** Password hash bocor ke client. Attacker bisa offline crack password.

**Fix:** 
```php
unset($user['password']);
```

#### 7.1.2 User ID diterima dari Request Body

```php
// TaskController::store()
$data = $this->request->getJSON(true);
'id' => $data['user_id'],  // ← dari request body!
```

```php
// HelperController::acceptTask()
$helperId = $this->request->getPost('helper_id');  // ← dari POST!
```

**Risk:** User bisa impersonasi user lain. Attacker bisa buat task atas nama orang lain.

**Fix:**
```php
$userId = auth()->id();
$helperId = auth()->id();
```

#### 7.1.3 Tidak ada Authentication pada Endpoint

Semua endpoint bisa diakses tanpa login.

**Risk:** Data sensitif (user data, task data, wallet) bisa diakses siapapun.

#### 7.1.4 Tidak ada Authorization Check

```php
// TaskController::show($id) - user bisa lihat task orang lain
// HelperController::acceptTask($taskId) - helper bisa ambil task yang bukan 'open'
```

**Risk:** Privilege escalation, data leakage.

### 7.2 HIGH

#### 7.2.1 Tidak ada Rate Limiting

```php
// Tidak ada rate limiting pada login/register
```

**Risk:** Brute force attack, credential stuffing.

#### 7.2.2 Tidak ada Input Validation

```php
// TaskController::store() - tidak ada validasi
'id' => $data['user_id'],  // bisa null, bisa string, bisa negative
'title' => $data['title'],  // bisa kosong
'price' => $data['price'],  // bisa negative
```

**Risk:** Data corruption, unexpected behavior, potential injection.

#### 7.2.3 Tidak ada CSRF Protection

API endpoints tidak menggunakan CSRF token.

**Risk:** Cross-Site Request Forgery pada session-based auth.

#### 7.2.4 AuthController Register Manual Hash

```php
'password' => password_hash(
    $this->request->getPost('password'),
    PASSWORD_DEFAULT
)
```

**Risk:** Jika Shield juga handle hashing, password akan di-hash double. Atau jika Shield tidak active, hashing manual ini rentan jika ada bug.

### 7.3 MEDIUM

#### 7.3.1 Tidak ada CORS Configuration

```php
// Tidak ada konfigurasi CORS
```

**Risk:** Cross-origin requests dari domain lain bisa mengakses API.

#### 7.3.2 Tidak ada HTTPS Enforcement

Tidak ada redirect HTTP ke HTTPS.

**Risk:** Man-in-the-middle attack, data interception.

#### 7.3.3 Tidak ada Security Headers

```php
// Tidak ada:
// - X-Content-Type-Options
// - X-Frame-Options
// - X-XSS-Protection
// - Strict-Transport-Security
// - Content-Security-Policy
```

#### 7.3.4 Tidak ada Request Size Limit

Tidak ada limit ukuran request body.

**Risk:** Denial of Service melalui oversized request.

### 7.4 LOW

#### 7.4.1 Error Message Terlalu Detail

```php
'User tidak ditemukan'  // ← bisa digunakan untuk enumerate email
'Password salah'  // ← mengkonfirmasi email exists
```

**Risk:** Username enumeration.

**Fix:**
```php
'Email atau password salah'  // generic message
```

#### 7.4.2 Tidak ada Audit Log

Tidak ada logging untuk aktivitas sensitif (login, register, password change).

**Risk:** Sulit trace jika ada security incident.

---

## 8. Rekomendasi Prioritas

### Priority 1: CRITICAL (Sebelum Phase 2)

| # | Issue | Solusi |
|---|-------|--------|
| 1 | Namespace inconsistency | Standardisasi namespace `App\Controllers` untuk semua controller |
| 2 | Use statement error | Fix `use app\Models` → `use App\Models` |
| 3 | tasks.category mismatch | Buat migration alter table: `category` → `category_id` FK ke `categories` |
| 4 | locations.helper_id FK salah | Migration alter: FK ke `users.id` bukan `helper_profiles.id` |
| 5 | HelperProfileModel field mismatch | Hapus `latitude`/`longitude` dari allowedFields |
| 6 | Password hash di response | Hapus password dari response |
| 7 | user_id dari request body | Gunakan `auth()->id()` |

### Priority 2: HIGH (Phase 2)

| # | Issue | Solusi |
|---|-------|--------|
| 1 | Tidak ada Service Layer | Buat `app/Services/` directory dan implement |
| 2 | Tidak ada Validation | Buat FormRequest atau gunakan CI4 validation |
| 3 | Tidak ada Error Handling | Implement try-catch + standard error response |
| 4 | Tidak ada Authentication | Setup Shield properly |
| 5 | Tidak ada Authorization | Implement role-based access control |
| 6 | Tidak ada Routes | Buat routes untuk semua endpoint |
| 7 | Tidak ada Rate Limiting | Setup rate limiting middleware |
| 8 | Wallet unique constraint | Migration add unique key on `user_id` |
| 9 | Task review unique constraint | Migration add unique key on `['task_id', 'user_id']` |

### Priority 3: MEDIUM (Phase 3-6)

| # | Issue | Solusi |
|---|-------|--------|
| 1 | Tidak ada API versioning | Gunakan prefix `/api/v1/` |
| 2 | Tidak ada CORS | Setup CORS config |
| 3 | Tidak ada Security Headers | Tambahkan via Filter |
| 4 | Tidak ada Index | Tambahkan index pada kolom sering di-query |
| 5 | ENUM status tasks | Pertimbangkan VARCHAR atau lookup table |

### Priority 4: LOW (Phase 7+)

| # | Issue | Solusi |
|---|-------|--------|
| 1 | Error message detail | Gunakan generic message |
| 2 | Tidak ada audit log | Implement logging |
| 3 | Tidak ada caching | Setup caching untuk performa |

---

## Appendix: Database Schema Quick Reference

```
users
├── id (PK, BIGINT)
├── name
├── email (UNIQUE)
├── phone
├── password
├── role (ENUM: admin, user, helper)
├── photo
├── rating (DECIMAL 3,2)
├── is_verified
├── status (ENUM: active, suspended)
├── created_at
└── updated_at

tasks
├── id (PK, BIGINT)
├── user_id (FK → users.id)
├── helper_id (FK → users.id, NULLABLE)
├── title
├── category (VARCHAR - seharusnya category_id FK)
├── description
├── price (DECIMAL 15,2)
├── location (TEXT)
├── deadline_start
├── deadline_end
├── status (ENUM: draft, open, accepted, in_progress, waiting_approval, completed, cancelled, disputed)
├── created_at
└── updated_at

wallets
├── id (PK, BIGINT)
├── user_id (FK → users.id)
├── balance (DECIMAL 15,2)
├── created_at
└── updated_at

task_attachments
├── id (PK, BIGINT)
├── task_id (FK → tasks.id)
├── file_name
├── file_type (ENUM: image, video, document)
└── created_at

task_progress
├── id (PK, BIGINT)
├── task_id (FK → tasks.id)
├── helper_id (FK → users.id)
├── description
├── attachment
├── status (ENUM: started, progress, submitted)
└── created_at

transactions
├── id (PK, BIGINT)
├── user_id (FK → users.id)
├── task_id (FK → tasks.id, NULLABLE)
├── amount (DECIMAL 15,2)
├── type (ENUM: topup, payment, withdraw, refund)
├── status (ENUM: pending, success, failed, cancelled)
├── reference_id
├── description
├── created_at
└── updated_at

notifications
├── id (PK, BIGINT)
├── user_id (FK → users.id)
├── title
├── message
├── type (ENUM: task, payment, system, dispute)
├── is_read
└── created_at

disputes
├── id (PK, BIGINT)
├── task_id (FK → tasks.id)
├── user_id (FK → users.id)
├── helper_id (FK → users.id)
├── reason
├── evidence_file
├── admin_note
├── status (ENUM: open, investigating, resolved, rejected)
├── resolved_by (FK → users.id, NULLABLE)
├── resolved_at
├── created_at
└── updated_at

task_reviews
├── id (PK, BIGINT)
├── task_id (FK → tasks.id)
├── user_id (FK → users.id)
├── helper_id (FK → users.id)
├── rating (INT 1)
├── review
└── created_at

categories
├── id (PK, BIGINT)
├── name
├── icon
├── status (ENUM: active, inactive)
└── created_at

task_status_histories
├── id (PK, BIGINT)
├── task_id (FK → tasks.id)
├── status (VARCHAR 50)
├── note
├── created_by (FK → users.id)
└── created_at

helper_profiles
├── id (PK, BIGINT)
├── user_id (FK → users.id, UNIQUE)
├── bio
├── skills
├── ktp_number
├── ktp_photo
├── completed_tasks
├── verification_status (ENUM: pending, verified, rejected)
├── created_at
└── updated_at

locations
├── id (PK, BIGINT)
├── helper_id (FK → helper_profiles.id)
├── latitude (DECIMAL 10,8)
├── longitude (DECIMAL 11,8)
└── updated_at
```

---

**End of Analysis**
