# AUTH AUDIT REPORT

**Tanggal:** 13 Juni 2026  
**Auditor:** Automated Code Review  
**Scope:** Shield Authentication & Authorization Implementation

---

## Ringkasan Eksekusi

| Total | 🔴 Critical | 🟠 High | 🟡 Medium | 🟢 Improvement |
|-------|-------------|---------|-----------|----------------|
| 8 | 3 | 2 | 2 | 1 |

---

## Hasil Audit

### 1. Shield menggunakan token authenticator murni

| Item | Status |
|------|--------|
| `$defaultAuthenticator = 'tokens'` | ✅ |
| Protected routes use `'filter' => 'tokens'` | ✅ |
| Login menggunakan Session authenticator | 🔴 |

**Detail:**

`app/Config/Auth.php:135` - Config benar:
```php
public string $defaultAuthenticator = 'tokens';
```

`app/Services/AuthService.php:127` - Login Salah:
```php
$sessionAuth = auth('session')->getAuthenticator();
$result = $sessionAuth->attempt([...]);
```

**Masalah:** Login menggunakan `Session::attempt()`, bukan `AccessTokens::attempt()`. Ini hybrid approach - session authentication lalu generate token.

**Impact:** Token authentication tidak murni. Login flow bergantung pada session, bukan token-based authentication.

---

### 2. POST /tasks hanya dapat dilakukan role user

| Item | Status |
|------|--------|
| Route filter | 🔴 |
| Service layer check | 🔴 |

**Detail:**

`app/Config/Routes.php:36-44`:
```php
$routes->group('api/v1/tasks', ['filter' => 'tokens'], function ($routes) {
    $routes->post('/', 'TaskController::store');  // ← Hanya 'tokens', tanpa role filter
});
```

`app/Services/AuthService.php:112-164` - `createTask()` tidak ada check role.

**Masalah:** Tidak ada role restriction. Helper dan Admin bisa membuat task.

**Impact:** Pelanggaran bisnis rule - hanya user yang boleh membuat task.

---

### 3. PUT /tasks/{id} hanya dapat dilakukan owner task

| Item | Status |
|------|--------|
| Route filter | ✅ |
| Service layer check | ✅ |

**Detail:**

`app/Services/TaskService.php:183`:
```php
if ($task['user_id'] != $userId) {
    throw BusinessException::forbidden('You can only update your own tasks');
}
```

**Verifikasi:** Owner check ada di service layer. ✅

---

### 4. DELETE /tasks/{id} hanya dapat dilakukan owner task

| Item | Status |
|------|--------|
| Route filter | ✅ |
| Service layer check | ✅ |

**Detail:**

`app/Services/TaskService.php:231`:
```php
if ($task['user_id'] != $userId) {
    throw BusinessException::forbidden('You can only cancel your own tasks');
}
```

**Verifikasi:** Owner check ada di service layer. ✅

---

### 5. POST /tasks/{id}/complete hanya dapat dilakukan owner task

| Item | Status |
|------|--------|
| Route filter | ✅ |
| Service layer check | ✅ |

**Detail:**

`app/Services/TaskService.php:386`:
```php
if ($task['user_id'] != $userId) {
    throw BusinessException::forbidden('Only task owner can complete the task');
}
```

**Verifikasi:** Owner check ada di service layer. ✅

---

### 6. Helper tidak dapat membuat task

| Item | Status |
|------|--------|
| Route filter | 🔴 |
| Service layer check | 🔴 |

**Detail:**

`app/Config/Routes.php:36` - POST /tasks tidak ada role restriction:
```php
$routes->post('/', 'TaskController::store');  // ← Hanya 'tokens'
```

Helper bisa mengakses POST /tasks karena tidak ada filter role.

**Impact:** Helper bisa membuat task - melanggar bisnis rule.

---

### 7. Admin tidak dapat bertindak sebagai helper atau user

| Item | Status |
|------|--------|
| Helper routes filter | 🟠 |
| Service layer check | 🟠 |

**Detail:**

`app/Config/Routes.php:49`:
```php
$routes->group('api/v1/helpers', ['filter' => 'tokens', 'filter' => 'role:helper,admin'], ...);
```

Admin bisa mengakses helper routes karena ada di allowed roles.

`app/Services/TaskService.php:275`:
```php
if (!$helper || $helper['role'] !== 'helper') {
    throw BusinessException::forbidden('Only helpers can accept tasks');
}
```

**Masalah:** 
1. Route filter mengizinkan admin akses helper routes
2. Service layer block non-helper roles di acceptTask

**Impact:** Inconsistent - route filter izinkan, service layer block.

---

### 8. Semua protected routes menggunakan Shield token filter

| Item | Status |
|------|--------|
| Auth protected routes | ✅ |
| User routes | ✅ |
| Task routes | ✅ |
| Helper routes | ✅ |
| Admin routes | ✅ |

**Detail:**

| Route Group | Filter |
|-------------|--------|
| `api/v1/auth` (protected) | `'filter' => 'tokens'` ✅ |
| `api/v1/user` | `'filter' => 'tokens'` ✅ |
| `api/v1/tasks` | `'filter' => 'tokens'` ✅ |
| `api/v1/helpers` | `'filter' => 'tokens'` + `'filter' => 'role:helper,admin'` ✅ |
| `api/v1/admin` | `'filter' => 'tokens'` + `'filter' => 'role:admin'` ✅ |

**Verifikasi:** Semua protected routes menggunakan Shield token filter. ✅

---

## Rekomendasi Perbaikan

### 🔴 Critical: Fix Login Flow

`app/Services/AuthService.php:119-169` - Ganti login method:

```php
public function login(array $data): array
{
    $this->validateRequired($data, [
        'email'    => 'Email',
        'password' => 'Password',
    ]);

    // Gunakan AccessTokens authenticator, bukan Session
    $tokenAuth = auth('tokens');
    
    $result = $tokenAuth->attempt([
        'email'    => $data['email'],
        'password' => $data['password'],
    ]);

    if (!$result->isOK()) {
        throw BusinessException::unauthorized('Email or password is incorrect');
    }

    $user = $result->extraInfo();

    if ($user->status === 'suspended') {
        throw BusinessException::forbidden('Account is suspended');
    }

    // Generate access token
    $token = $user->generateAccessToken('bantuin-yuk-' . date('Y-m-d'));

    return [
        'user'  => [...],
        'token' => [
            'access_token' => $token->raw_token,
            ...
        ],
    ];
}
```

### 🔴 Critical: Add Role Check to Task Creation

`app/Config/Routes.php:36` - Tambah role filter:

```php
$routes->group('api/v1/tasks', ['filter' => 'tokens'], function ($routes) {
    // Public endpoints (any authenticated user)
    $routes->get('/', 'TaskController::index');
    $routes->get('(:num)', 'TaskController::show/$1');
    $routes->get('my', 'TaskController::myTasks');
    
    // User-only endpoints
    $routes->post('/', 'TaskController::store');  // ← Perlu role check
    $routes->put('(:num)', 'TaskController::update/$1');  // ← Perlu owner check
    $routes->delete('(:num)', 'TaskController::delete/$1');  // ← Perlu owner check
    $routes->post('(:num)/complete', 'TaskController::complete/$1');  // ← Perlu owner check
});
```

**Alternatif:** Tambah role check di TaskService::createTask():

```php
public function createTask(int $userId, array $data): array
{
    // Check if user is allowed to create tasks
    $user = $this->userModel->find($userId);
    if (!$user || $user->role !== 'user') {
        throw BusinessException::forbidden('Only users can create tasks');
    }
    // ... rest of method
}
```

### 🔴 Critical: Prevent Helper from Creating Task

`app/Services/TaskService.php:112` - Tambah role check:

```php
public function createTask(int $userId, array $data): array
{
    // Check user role
    $user = $this->userModel->find($userId);
    if (!$user) {
        throw BusinessException::notFound('User not found');
    }
    
    if ($user->role !== 'user') {
        throw BusinessException::forbidden('Only users can create tasks');
    }
    
    // ... rest of method
}
```

### 🟠 High: Fix Admin Access to Helper Routes

`app/Config/Routes.php:49` - Hapus admin dari helper routes:

```php
$routes->group('api/v1/helpers', ['filter' => 'tokens', 'filter' => 'role:helper'], function ($routes) {
    // ... helper endpoints
});
```

Atau tambah logic di service layer untuk handle admin access.

### 🟠 High: Add Role Check in TaskService Methods

Tambahkan role check di semua task methods yang memerlukan role spesifik:

```php
// Di createTask()
if ($user->role !== 'user') {
    throw BusinessException::forbidden('Only users can create tasks');
}

// Di acceptTask()
if ($helper['role'] !== 'helper') {
    throw BusinessException::forbidden('Only helpers can accept tasks');
}

// Di completeTask()
// Owner check sudah ada ✅
```

### 🟡 Medium: Add Owner Check for Update/Delete

`app/Services/TaskService.php:175` - Sudah ada owner check ✅  
`app/Services/TaskService.php:223` - Sudah ada owner check ✅

### 🟡 Medium: Add Owner Check for Complete

`app/Services/TaskService.php:378` - Sudah ada owner check ✅

### 🟢 Improvement: Add Rate Limiting

Tambahkan rate limiting untuk login endpoint untuk mencegah brute force:

```php
// app/Config/Filters.php
public array $filters = [
    'before' => [
        'login' => [
            'before' => ['maxRequests' => 5, 'time' => 60],
        ],
    ],
];
```

---

## Compliance Matrix

| # | Requirement | Status | Severity |
|---|-------------|--------|----------|
| 1 | Shield uses pure token authenticator | ❌ | 🔴 Critical |
| 2 | POST /tasks only for user role | ❌ | 🔴 Critical |
| 3 | PUT /tasks/{id} only for owner | ✅ | - |
| 4 | DELETE /tasks/{id} only for owner | ✅ | - |
| 5 | POST /tasks/{id}/complete only for owner | ✅ | - |
| 6 | Helper cannot create task | ❌ | 🔴 Critical |
| 7 | Admin cannot act as helper/user | ⚠️ | 🟠 High |
| 8 | All protected routes use token filter | ✅ | - |

---

## Kesimpulan

**3 Critical Issues** yang harus diperbaiki sebelum production:

1. **Login Flow** - Menggunakan Session authenticator, bukan AccessTokens
2. **Task Creation** - Tidak ada role restriction (helper/admin bisa create)
3. **Helper Task Creation** - Helper bisa membuat task

**2 High Issues** yang perlu diperbaiki:

1. **Admin Access** - Admin bisa akses helper routes
2. **Service Layer Role Check** - Perlu konsistensi antara route filter dan service layer

---

**End of Report**
