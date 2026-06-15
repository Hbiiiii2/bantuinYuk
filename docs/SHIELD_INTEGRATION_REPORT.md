# SHIELD INTEGRATION REPORT

**Tanggal:** 13 Juni 2026  
**Sprint:** 3 - Authentication & Access Token  
**Status:** ✅ Completed

---

## Table of Contents

1. [Shield Installation](#1-shield-installation)
2. [Shield Configuration](#2-shield-configuration)
3. [AuthController Changes](#3-authcontroller-changes)
4. [AuthService Changes](#4-authservice-changes)
5. [Routes](#5-routes)
6. [Filters](#6-filters)
7. [Token Flow Diagram](#7-token-flow-diagram)
8. [Authorization Matrix](#8-authorization-matrix)
9. [Security Considerations](#9-security-considerations)
10. [Testing Checklist](#10-testing-checklist)

---

## 1. Shield Installation

Shield sudah terinstall via composer:

```
codeigniter4/shield: ^1.3
```

### Shield Tables

Tabel yang digunakan oleh Shield:

| Table | Purpose |
|-------|---------|
| `users` | User accounts (custom schema) |
| `auth_identities` | Email/password, access tokens |
| `auth_groups_users` | User-group assignments |
| `auth_permissions_users` | User-permission assignments |
| `auth_logins` | Login attempts |
| `auth_token_logins` | Token login attempts |
| `auth_remember_tokens` | Remember-me tokens |

---

## 2. Shield Configuration

### Auth.php (`app/Config/Auth.php`)

```php
// Default Authenticator
public string $defaultAuthenticator = 'tokens';

// Valid Login Fields
public array $validFields = [
    'email',
];

// Tables
public array $tables = [
    'users'             => 'users',
    'identities'        => 'auth_identities',
    'logins'            => 'auth_logins',
    'token_logins'      => 'auth_token_logins',
    'remember_tokens'   => 'auth_remember_tokens',
    'groups_users'      => 'auth_groups_users',
    'permissions_users' => 'auth_permissions_users',
];

// User Provider
public string $userProvider = \App\Models\UserModel::class;
```

### AuthGroups.php (`app/Config/AuthGroups.php`)

**Groups:**
| Group | Description |
|-------|-------------|
| superadmin | Complete control |
| admin | Administrators |
| developer | Programmers |
| user | General users |
| helper | Service providers |
| beta | Beta users |

**Permissions:**
| Permission | Description |
|------------|-------------|
| admin.access | Can access admin area |
| admin.settings | Can access settings |
| users.manage-admins | Can manage admins |
| users.create | Can create users |
| users.edit | Can edit users |
| users.delete | Can delete users |
| tasks.create | Can create tasks |
| tasks.manage | Can manage tasks |
| helpers.manage | Can manage helpers |
| helpers.verify | Can verify helpers |

---

## 3. AuthController Changes

### File: `app/Controllers/AuthController.php`

**Methods:**
| Method | Service | HTTP Method | Auth Required |
|--------|---------|-------------|---------------|
| `register()` | `AuthService::register()` | POST | ❌ |
| `login()` | `AuthService::login()` | POST | ❌ |
| `logout()` | `AuthService::logout()` | POST | ✅ |
| `me()` | `AuthService::getUserById()` | GET | ✅ |
| `updateProfile()` | `AuthService::updateProfile()` | PUT | ✅ |

**Key Changes:**
- Uses `auth()->id()` to get authenticated user ID
- Uses `AuthService` for all business logic
- Uses `ApiResponseTrait` for consistent responses
- Exception handling with try-catch

---

## 4. AuthService Changes

### File: `app/Services/AuthService.php`

**Methods:**
| Method | Description |
|--------|-------------|
| `register($data)` | Register new user with Shield |
| `login($data)` | Login and generate access token |
| `logout($userId)` | Revoke all tokens |
| `getUserById($userId)` | Get user profile |
| `updateProfile($userId, $data)` | Update user profile |
| `isEmailExists($email)` | Check email uniqueness |
| `hasGroup($userId, $group)` | Check user group |
| `hasPermission($userId, $permission)` | Check user permission |

**Register Flow:**
1. Validate input (name, email, password)
2. Check email uniqueness
3. Hash password using Shield's password hasher
4. Insert to `users` table
5. Insert to `auth_identities` table
6. Add to default group
7. Return user data

**Login Flow:**
1. Validate input (email, password)
2. Use Shield's Session authenticator
3. Verify credentials
4. Check account status
5. Generate access token using Shield
6. Return user data with token

---

## 5. Routes

### File: `app/Config/Routes.php`

**Route Groups:**

```
api/v1/auth/
├── POST /register          (Public)
├── POST /login             (Public)
├── POST /logout            (Protected: tokens)
├── GET  /me                (Protected: tokens)
└── PUT  /me                (Protected: tokens)

api/v1/user/
├── GET  /profile           (Protected: tokens)
└── PUT  /profile           (Protected: tokens)

api/v1/tasks/
├── GET    /                (Protected: tokens)
├── GET    /my              (Protected: tokens)
├── GET    /:id             (Protected: tokens)
├── POST   /                (Protected: tokens)
├── PUT    /:id             (Protected: tokens)
├── DELETE /:id             (Protected: tokens)
└── POST   /:id/complete    (Protected: tokens)

api/v1/helpers/
├── GET    /                (Protected: tokens + role:helper,admin)
├── GET    /available-tasks (Protected: tokens + role:helper,admin)
├── GET    /my-tasks        (Protected: tokens + role:helper,admin)
├── GET    /profile         (Protected: tokens + role:helper,admin)
├── PUT    /profile         (Protected: tokens + role:helper,admin)
├── PUT    /location        (Protected: tokens + role:helper,admin)
├── POST   /verification    (Protected: tokens + role:helper,admin)
├── GET    /stats           (Protected: tokens + role:helper,admin)
├── GET    /:id             (Protected: tokens + role:helper,admin)
├── POST   /tasks/:id/accept (Protected: tokens + role:helper,admin)
├── POST   /tasks/:id/start  (Protected: tokens + role:helper,admin)
└── POST   /:id/submit       (Protected: tokens + role:helper,admin)

api/v1/admin/
├── GET    /users           (Protected: tokens + role:admin)
├── PUT    /users/:id/status (Protected: tokens + role:admin)
├── GET    /tasks           (Protected: tokens + role:admin)
├── GET    /helpers         (Protected: tokens + role:admin)
├── PUT    /helpers/:id/verify (Protected: tokens + role:admin)
├── GET    /categories      (Protected: tokens + role:admin)
├── POST   /categories      (Protected: tokens + role:admin)
├── PUT    /categories/:id  (Protected: tokens + role:admin)
├── DELETE /categories/:id  (Protected: tokens + role:admin)
└── GET    /dashboard       (Protected: tokens + role:admin)
```

---

## 6. Filters

### Shield Filters (Built-in)

| Filter | Alias | Description |
|--------|-------|-------------|
| `TokenAuth` | `tokens` | Access token authentication |
| `SessionAuth` | `session` | Session authentication |
| `GroupFilter` | `group` | Group-based authorization |
| `PermissionFilter` | `permission` | Permission-based authorization |

### Custom Filters

| Filter | Alias | Description |
|--------|-------|-------------|
| `RoleFilter` | `role` | Role-based authorization (user role from DB) |
| `SimpleTokenAuth` | `simpleToken` | Alternative token auth (not used) |

### RoleFilter Usage

```php
// Single role
['filter' => 'role:admin']

// Multiple roles
['filter' => 'role:helper,admin']

// All authenticated users
['filter' => 'tokens']
```

---

## 7. Token Flow Diagram

### Register Flow

```
Client                          Server
  │                               │
  │  POST /auth/register          │
  │  { name, email, password }    │
  │ ─────────────────────────────>│
  │                               │
  │                    ┌──────────┴──────────┐
  │                    │ Validate Input      │
  │                    │ Hash Password       │
  │                    │ Insert User         │
  │                    │ Insert Identity     │
  │                    │ Add to Group        │
  │                    └──────────┬──────────┘
  │                               │
  │  201 Created                  │
  │  { user_id, name, email }     │
  │ <─────────────────────────────│
```

### Login Flow

```
Client                          Server
  │                               │
  │  POST /auth/login             │
  │  { email, password }          │
  │ ─────────────────────────────>│
  │                               │
  │                    ┌──────────┴──────────┐
  │                    │ Validate Input      │
  │                    │ Session::attempt()  │
  │                    │ Check Status        │
  │                    │ generateAccessToken()│
  │                    └──────────┬──────────┘
  │                               │
  │  200 OK                       │
  │  { user, token }              │
  │ <─────────────────────────────│
```

### Protected Request Flow

```
Client                          Server
  │                               │
  │  GET /auth/me                 │
  │  Authorization: Bearer TOKEN  │
  │ ─────────────────────────────>│
  │                               │
  │                    ┌──────────┴──────────┐
  │                    │ TokenAuth Filter    │
  │                    │ Verify Token        │
  │                    │ Load User           │
  │                    └──────────┬──────────┘
  │                               │
  │                    ┌──────────┴──────────┐
  │                    │ RoleFilter (if any) │
  │                    │ Check User Role     │
  │                    └──────────┬──────────┘
  │                               │
  │  200 OK                       │
  │  { user data }                │
  │ <─────────────────────────────│
```

---

## 8. Authorization Matrix

### Role-Based Access

| Endpoint | user | helper | admin |
|----------|------|--------|-------|
| `POST /auth/register` | ✅ | ✅ | ✅ |
| `POST /auth/login` | ✅ | ✅ | ✅ |
| `POST /auth/logout` | ✅ | ✅ | ✅ |
| `GET /auth/me` | ✅ | ✅ | ✅ |
| `PUT /auth/me` | ✅ | ✅ | ✅ |
| `GET /tasks` | ✅ | ✅ | ✅ |
| `POST /tasks` | ✅ | ✅ | ✅ |
| `GET /tasks/:id` | ✅ | ✅ | ✅ |
| `PUT /tasks/:id` | ✅ | ✅ | ✅ |
| `DELETE /tasks/:id` | ✅ | ✅ | ✅ |
| `POST /tasks/:id/complete` | ✅ | ✅ | ✅ |
| `GET /helpers` | ✅ | ✅ | ✅ |
| `GET /helpers/profile` | ❌ | ✅ | ✅ |
| `PUT /helpers/profile` | ❌ | ✅ | ✅ |
| `PUT /helpers/location` | ❌ | ✅ | ✅ |
| `POST /helpers/verification` | ❌ | ✅ | ✅ |
| `POST /helpers/tasks/:id/accept` | ❌ | ✅ | ✅ |
| `POST /helpers/tasks/:id/start` | ❌ | ✅ | ✅ |
| `POST /helpers/:id/submit` | ❌ | ✅ | ✅ |
| `GET /admin/users` | ❌ | ❌ | ✅ |
| `PUT /admin/users/:id/status` | ❌ | ❌ | ✅ |
| `GET /admin/tasks` | ❌ | ❌ | ✅ |
| `GET /admin/helpers` | ❌ | ❌ | ✅ |
| `PUT /admin/helpers/:id/verify` | ❌ | ❌ | ✅ |
| `GET /admin/categories` | ❌ | ❌ | ✅ |
| `POST /admin/categories` | ❌ | ❌ | ✅ |
| `PUT /admin/categories/:id` | ❌ | ❌ | ✅ |
| `DELETE /admin/categories/:id` | ❌ | ❌ | ✅ |
| `GET /admin/dashboard` | ❌ | ❌ | ✅ |

---

## 9. Security Considerations

### Implemented

| Security | Status | Implementation |
|----------|--------|----------------|
| Password Hashing | ✅ | Shield's password hasher |
| Token-based Auth | ✅ | Shield's AccessTokens |
| Role Authorization | ✅ | Custom RoleFilter |
| Input Validation | ✅ | AuthService validation |
| Error Handling | ✅ | Try-catch with exceptions |
| CORS | ✅ | Cors filter |

### Protected Against

| Threat | Status | Protection |
|--------|--------|------------|
| IDOR | ✅ | `auth()->id()` for user context |
| Broken Authentication | ✅ | Shield's token verification |
| Missing Authorization | ✅ | RoleFilter for admin/helper |
| Token Leakage | ✅ | Tokens not logged |
| Password in Response | ✅ | Never included in response |

---

## 10. Testing Checklist

### Register ✅
- [x] POST /api/v1/auth/register with valid data → 201
- [x] POST /api/v1/auth/register with duplicate email → 409
- [x] POST /api/v1/auth/register without name → 422
- [x] POST /api/v1/auth/register without email → 422
- [x] POST /api/v1/auth/register without password → 422
- [x] POST /api/v1/auth/register with short password → 422

### Login ✅
- [x] POST /api/v1/auth/login with valid credentials → 200 + token
- [x] POST /api/v1/auth/login with wrong email → 401
- [x] POST /api/v1/auth/login with wrong password → 401

### Logout ✅
- [x] POST /api/v1/auth/logout with valid token → 200
- [x] POST /api/v1/auth/logout without token → 401
- [x] POST /api/v1/auth/logout with invalid token → 401

### Current User ✅
- [x] GET /api/v1/auth/me with valid token → 200
- [x] GET /api/v1/auth/me without token → 401
- [x] PUT /api/v1/auth/me with valid data → 200

### Token Authentication ✅
- [x] Request with Bearer token valid → 200
- [x] Request without Authorization header → 401
- [x] Request with invalid token → 401

### Role Authorization ✅
- [x] User cannot access admin endpoints → 403
- [x] User cannot access helper endpoints → 403
- [x] Helper can access helper endpoints → 200
- [x] Admin can access admin endpoints → 200

### Security ✅
- [x] Password never appears in response
- [x] Token never appears in log
- [x] User cannot access other user's data

---

**End of Report**
