# AUTH HARDENING REPORT

**Tanggal:** 13 Juni 2026  
**Sprint:** Auth Hardening  
**Status:** ✅ Completed

---

## 1. File yang Diubah

| File | Perubahan |
|------|-----------|
| `app/Services/AuthService.php` | Login menggunakan pure AccessTokens, logout tanpa session |
| `app/Services/TaskService.php` | Role validation di createTask() |
| `app/Config/Routes.php` | Helper routes hanya helper, rate limiting login |
| `app/Config/Filters.php` | Tambah AuthRates filter alias |

---

## 2. Critical Issues yang Diperbaiki

### 🔴 Login menggunakan Session authenticator

**Sebelum:**
```php
$sessionAuth = auth('session')->getAuthenticator();
$result = $sessionAuth->attempt([...]);
```

**Sesudah:**
```php
$tokenAuth = auth('tokens');
$result = $tokenAuth->attempt([...]);
```

**Impact:** Login sekarang menggunakan pure AccessTokens - tidak ada session dependency.

### 🔴 POST /tasks tidak ada role restriction

**Sebelum:**
```php
// Tidak ada role check
$task = $this->taskService->createTask($userId, $data);
```

**Sesudah:**
```php
// Role validation: Only users can create tasks (not helpers, not admins)
if ($user->role !== 'user') {
    throw BusinessException::forbidden('Only users can create tasks');
}
```

**Impact:** Hanya user yang dapat membuat task.

### 🔴 Helper dapat membuat task

**Sebelum:**
```php
$routes->group('api/v1/tasks', ['filter' => 'tokens'], ...);
// Helper bisa akses POST /tasks
```

**Sesudah:**
```php
// Service layer block: role !== 'user' = forbidden
// Helper routes terpisah dengan role:helper filter
```

**Impact:** Helper tidak dapat membuat task lagi.

---

## 3. High Issues yang Diperbaiki

### 🟠 Admin dapat mengakses helper routes

**Sebelum:**
```php
$routes->group('api/v1/helpers', ['filter' => 'tokens', 'filter' => 'role:helper,admin'], ...);
```

**Sesudah:**
```php
$routes->group('api/v1/helpers', ['filter' => 'tokens', 'filter' => 'role:helper'], ...);
```

**Impact:** Hanya helper yang dapat mengakses helper routes.

### 🟠 Tidak ada rate limiting pada login

**Sebelum:**
```php
$routes->post('api/v1/auth/login', 'AuthController::login');
```

**Sesudah:**
```php
$routes->post('api/v1/auth/login', 'AuthController::login', ['filter' => 'authRateLimit']);
```

**Impact:** Login dibatasi 10 requests per menit per IP.

---

## 4. Route Matrix Final

| Route | Method | Filter | Role |
|-------|--------|--------|------|
| `/api/v1/auth/register` | POST | None | Public |
| `/api/v1/auth/login` | POST | authRateLimit | Public |
| `/api/v1/auth/logout` | POST | tokens | Any authenticated |
| `/api/v1/auth/me` | GET | tokens | Any authenticated |
| `/api/v1/auth/me` | PUT | tokens | Any authenticated |
| `/api/v1/user/profile` | GET | tokens | Any authenticated |
| `/api/v1/user/profile` | PUT | tokens | Any authenticated |
| `/api/v1/tasks/` | GET | tokens | Any authenticated |
| `/api/v1/tasks/my` | GET | tokens | Any authenticated |
| `/api/v1/tasks/{id}` | GET | tokens | Any authenticated |
| `/api/v1/tasks/` | POST | tokens | User only* |
| `/api/v1/tasks/{id}` | PUT | tokens | Owner only* |
| `/api/v1/tasks/{id}` | DELETE | tokens | Owner only* |
| `/api/v1/tasks/{id}/complete` | POST | tokens | Owner only* |
| `/api/v1/helpers/` | GET | tokens, role:helper | Helper only |
| `/api/v1/helpers/profile` | GET | tokens, role:helper | Helper only |
| `/api/v1/helpers/profile` | PUT | tokens, role:helper | Helper only |
| `/api/v1/helpers/location` | PUT | tokens, role:helper | Helper only |
| `/api/v1/helpers/tasks/{id}/accept` | POST | tokens, role:helper | Helper only |
| `/api/v1/helpers/tasks/{id}/start` | POST | tokens, role:helper | Helper only |
| `/api/v1/admin/users` | GET | tokens, role:admin | Admin only |
| `/api/v1/admin/tasks` | GET | tokens, role:admin | Admin only |
| `/api/v1/admin/helpers` | GET | tokens, role:admin | Admin only |
| `/api/v1/admin/categories` | POST | tokens, role:admin | Admin only |

*Role validation di service layer

---

## 5. Role Matrix Final

| Endpoint | user | helper | admin |
|----------|------|--------|-------|
| `POST /auth/register` | ✅ | ✅ | ✅ |
| `POST /auth/login` | ✅ | ✅ | ✅ |
| `POST /auth/logout` | ✅ | ✅ | ✅ |
| `GET /auth/me` | ✅ | ✅ | ✅ |
| `PUT /auth/me` | ✅ | ✅ | ✅ |
| `GET /tasks` | ✅ | ✅ | ✅ |
| `POST /tasks` | ✅ | ❌ | ❌ |
| `GET /tasks/{id}` | ✅ | ✅ | ✅ |
| `PUT /tasks/{id}` | Owner | ❌ | ❌ |
| `DELETE /tasks/{id}` | Owner | ❌ | ❌ |
| `POST /tasks/{id}/complete` | Owner | ❌ | ❌ |
| `GET /helpers` | ❌ | ✅ | ❌ |
| `GET /helpers/profile` | ❌ | ✅ | ❌ |
| `PUT /helpers/profile` | ❌ | ✅ | ❌ |
| `POST /helpers/tasks/{id}/accept` | ❌ | ✅ | ❌ |
| `GET /admin/users` | ❌ | ❌ | ✅ |
| `POST /admin/categories` | ❌ | ❌ | ✅ |

---

## 6. Authentication Flow Final

### Register Flow
```
Client → POST /api/v1/auth/register
       → AuthService::register()
       → Hash password dengan Shield's hasher
       → Insert ke users + auth_identities + auth_groups_users
       → Return user data
```

### Login Flow (Pure Token)
```
Client → POST /api/v1/auth/login
       → authRateLimit filter (10 req/min)
       → AuthService::login()
       → auth('tokens')->attempt() [BUKAN auth('session')]
       → Generate access token
       → Return user + token
```

### Protected Request Flow
```
Client → Request dengan Authorization: Bearer <token>
       → TokenAuth filter verifikasi token
       → RoleFilter (jika ada) cek role
       → Controller proses
       → Service layer owner check (jika ada)
```

### Logout Flow
```
Client → POST /api/v1/auth/logout (dengan token)
       → TokenAuth filter
       → AuthService::logout()
       → revokeAllAccessTokens()
       → Token tidak bisa digunakan lagi
```

---

## 7. Security Checklist

| # | Item | Status |
|---|------|--------|
| 1 | Login menggunakan pure token authentication | ✅ |
| 2 | Tidak ada auth('session') di login flow | ✅ |
| 3 | Helper tidak dapat membuat task | ✅ |
| 4 | Admin tidak dapat membuat task | ✅ |
| 5 | Hanya user dapat membuat task | ✅ |
| 6 | Admin tidak dapat mengakses helper-only routes | ✅ |
| 7 | Semua protected endpoint menggunakan token filter | ✅ |
| 8 | Rate limiting login aktif (10 req/min) | ✅ |
| 9 | Owner check untuk update/delete/complete task | ✅ |
| 10 | Token revoked saat logout | ✅ |
| 11 | Password tidak muncul di response | ✅ |
| 12 | Tidak ada session dependency | ✅ |

---

## 8. Verification Commands

### Test Login (Harus berhasil)
```powershell
$body = '{"email":"user@test.com","password":"password123"}'
Invoke-RestMethod -Uri "http://bantuinYuk.test/api/v1/auth/login" -Method POST -ContentType "application/json" -Body $body
```

### Test Helper Create Task (Harus 403)
```powershell
# Login sebagai helper
$helperLogin = Invoke-RestMethod -Uri "http://bantuinYuk.test/api/v1/auth/login" -Method POST -ContentType "application/json" -Body '{"email":"helper@test.com","password":"password123"}'
$helperToken = $helperLogin.data.token.access_token

# Coba buat task (harus gagal)
$headers = @{Authorization="Bearer $helperToken"}
$body = '{"title":"Test Task","description":"Desc","price":100000,"category_id":1,"deadline_start":"2026-06-20","deadline_end":"2026-06-21"}'
try { Invoke-RestMethod -Uri "http://bantuinYuk.test/api/v1/tasks" -Method POST -ContentType "application/json" -Body $body -Headers $headers } catch { $_.Exception.Response.StatusCode.value__; $_.ErrorDetails.Message }
```

### Test Admin Access Helper Routes (Harus 403)
```powershell
# Login sebagai admin
$adminLogin = Invoke-RestMethod -Uri "http://bantuinYuk.test/api/v1/auth/login" -Method POST -ContentType "application/json" -Body '{"email":"admin@test.com","password":"password123"}'
$adminToken = $adminLogin.data.token.access_token

# Coba akses helper routes (harus gagal)
$headers = @{Authorization="Bearer $adminToken"}
try { Invoke-RestMethod -Uri "http://bantuinYuk.test/api/v1/helpers/profile" -Headers $headers } catch { $_.Exception.Response.StatusCode.value__; $_.ErrorDetails.Message }
```

### Test Rate Limiting (Login 11x cepat)
```powershell
# Loop 11 kali login cepat
for ($i=1; $i -le 11; $i++) {
    try { 
        Invoke-RestMethod -Uri "http://bantuinYuk.test/api/v1/auth/login" -Method POST -ContentType "application/json" -Body '{"email":"test@test.com","password":"wrong"}'
    } catch { 
        Write-Host "Attempt $i`: $($_.Exception.Response.StatusCode.value__)"
    }
}
# Attempt 11+ harus 429 Too Many Requests
```

---

**End of Report**
