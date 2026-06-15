# AUTH API Documentation

**Base URL:** `http://bantuinYuk.test/api/v1`

**Content-Type:** `application/json`

---

## Authentication

Semua endpoint yang dilindungi memerlukan Bearer Token di header:

```
Authorization: Bearer <access_token>
```

---

## Endpoints

### POST /auth/register

Register user baru.

**Request:**
```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "phone": "081234567890",
    "password": "password123"
}
```

**Response (201):**
```json
{
    "success": true,
    "message": "User registered successfully",
    "data": {
        "user_id": 1,
        "name": "John Doe",
        "email": "john@example.com"
    }
}
```

**Error Response (422):**
```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "email": "Email format is invalid"
    }
}
```

**Error Response (409):**
```json
{
    "success": false,
    "message": "Email already registered"
}
```

---

### POST /auth/login

Login dan dapatkan access token.

**Request:**
```json
{
    "email": "john@example.com",
    "password": "password123"
}
```

**Response (200):**
```json
{
    "success": true,
    "message": "Login successful",
    "data": {
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com",
            "phone": "081234567890",
            "role": "user",
            "photo": null,
            "groups": ["user"]
        },
        "token": {
            "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
            "type": "Bearer",
            "expires_in": 2592000
        }
    }
}
```

**Error Response (401):**
```json
{
    "success": false,
    "message": "Email or password is incorrect"
}
```

**Error Response (403):**
```json
{
    "success": false,
    "message": "Account is suspended"
}
```

---

### POST /auth/logout

Logout dan revoke token.

**Headers:**
```
Authorization: Bearer <access_token>
```

**Response (200):**
```json
{
    "success": true,
    "message": "Logged out successfully",
    "data": null
}
```

**Error Response (401):**
```json
{
    "success": false,
    "message": "Unauthorized"
}
```

---

### GET /auth/me

Dapatkan data user yang sedang login.

**Headers:**
```
Authorization: Bearer <access_token>
```

**Response (200):**
```json
{
    "success": true,
    "message": "Success",
    "data": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "phone": "081234567890",
        "role": "user",
        "photo": null,
        "is_verified": false,
        "status": "active",
        "groups": ["user"],
        "created_at": "2026-06-13 12:00:00"
    }
}
```

**Error Response (401):**
```json
{
    "success": false,
    "message": "Unauthorized"
}
```

---

### PUT /auth/me

Update profile user yang sedang login.

**Headers:**
```
Authorization: Bearer <access_token>
```

**Request:**
```json
{
    "name": "John Updated",
    "phone": "081234567891",
    "photo": "photo-url.jpg"
}
```

**Response (200):**
```json
{
    "success": true,
    "message": "Profile updated successfully",
    "data": {
        "id": 1,
        "name": "John Updated",
        "email": "john@example.com",
        "phone": "081234567891",
        "role": "user",
        "photo": "photo-url.jpg",
        "is_verified": false,
        "status": "active",
        "groups": ["user"],
        "created_at": "2026-06-13 12:00:00"
    }
}
```

**Error Response (422):**
```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "name": "name must be at least 2 characters"
    }
}
```

---

## Bearer Token Usage

### Cara Mendapatkan Token

1. Register akun baru via `POST /auth/register`
2. Login via `POST /auth/login`
3. Simpan `access_token` dari response login

### Cara Menggunakan Token

Tambahkan header `Authorization` di setiap request:

```
Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...
```

### Contoh Request dengan Token

```bash
curl -X GET http://bantuinYuk.test/api/v1/auth/me \
  -H "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..." \
  -H "Content-Type: application/json"
```

### Token Lifetime

- Default: 30 hari (2,592,000 detik)
- Token bisa di-revoke saat logout
- Setelah logout, token tidak bisa digunakan lagi

### Error Jika Token Tidak Valid

**Response (401):**
```json
{
    "success": false,
    "message": "The access token is invalid."
}
```

Kemungkinan penyebab:
- Token tidak dikirim
- Token sudah di-revoke (logout)
- Token expired
- Token salah/tidak terdaftar

---

## Role-Based Access Control

### Roles

| Role | Description |
|------|-------------|
| `user` | Regular user (can create tasks) |
| `helper` | Service provider (can accept tasks) |
| `admin` | Administrator (full access) |

### Endpoint Access by Role

| Endpoint | user | helper | admin |
|----------|------|--------|-------|
| `POST /auth/register` | ✅ | ✅ | ✅ |
| `POST /auth/login` | ✅ | ✅ | ✅ |
| `POST /auth/logout` | ✅ | ✅ | ✅ |
| `GET /auth/me` | ✅ | ✅ | ✅ |
| `PUT /auth/me` | ✅ | ✅ | ✅ |
| `GET /tasks` | ✅ | ✅ | ✅ |
| `POST /tasks` | ✅ | ✅ | ✅ |
| `GET /helpers/profile` | ❌ | ✅ | ✅ |
| `PUT /helpers/profile` | ❌ | ✅ | ✅ |
| `POST /helpers/tasks/:id/accept` | ❌ | ✅ | ✅ |
| `GET /admin/users` | ❌ | ❌ | ✅ |
| `POST /admin/categories` | ❌ | ❌ | ✅ |

### Error Response for Insufficient Role

**Response (403):**
```json
{
    "success": false,
    "message": "Insufficient permissions. Required role: admin"
}
```

---

## HTTP Status Codes

| Code | Description |
|------|-------------|
| 200 | Success |
| 201 | Created |
| 400 | Bad Request |
| 401 | Unauthorized (token tidak valid/tidak ada) |
| 403 | Forbidden (tidak punya akses/role) |
| 404 | Not Found |
| 422 | Validation Failed |
| 500 | Server Error |

---

## Testing dengan PowerShell

### Register
```powershell
$body = '{"name":"Test User","email":"test@test.com","password":"password123"}'
Invoke-RestMethod -Uri "http://bantuinYuk.test/api/v1/auth/register" -Method POST -ContentType "application/json" -Body $body
```

### Login
```powershell
$body = '{"email":"test@test.com","password":"password123"}'
$login = Invoke-RestMethod -Uri "http://bantuinYuk.test/api/v1/auth/login" -Method POST -ContentType "application/json" -Body $body
$token = $login.data.token.access_token
$token
```

### Get Profile
```powershell
$headers = @{Authorization="Bearer $token"}
Invoke-RestMethod -Uri "http://bantuinYuk.test/api/v1/auth/me" -Headers $headers
```

### Update Profile
```powershell
$body = '{"name":"Nama Baru","phone":"08123456789"}'
$headers = @{Authorization="Bearer $token"}
Invoke-RestMethod -Uri "http://bantuinYuk.test/api/v1/auth/me" -Method PUT -ContentType "application/json" -Body $body -Headers $headers
```

### Logout
```powershell
$headers = @{Authorization="Bearer $token"}
Invoke-RestMethod -Uri "http://bantuinYuk.test/api/v1/auth/logout" -Method POST -Headers $headers
```

### Test Admin Endpoint (as User - should fail)
```powershell
$headers = @{Authorization="Bearer $token"}
try { Invoke-RestMethod -Uri "http://bantuinYuk.test/api/v1/admin/users" -Headers $headers } catch { $_.Exception.Response.StatusCode.value__; $_.ErrorDetails.Message }
```

---

## Flow Lengkap

```
┌─────────────────────────────────────────────────────────────────┐
│                     FULL AUTH FLOW                              │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  1. Register                                                    │
│     POST /api/v1/auth/register                                  │
│     → User created, email + password stored                     │
│                                                                 │
│  2. Login                                                       │
│     POST /api/v1/auth/login                                     │
│     → Access token generated                                    │
│                                                                 │
│  3. Access Protected Endpoint                                   │
│     GET /api/v1/auth/me                                         │
│     Header: Authorization: Bearer <token>                       │
│     → User data returned                                        │
│                                                                 │
│  4. Role-Based Access                                           │
│     GET /api/v1/admin/users                                     │
│     Header: Authorization: Bearer <token>                       │
│     → 403 if not admin role                                     │
│                                                                 │
│  5. Logout                                                      │
│     POST /api/v1/auth/logout                                    │
│     Header: Authorization: Bearer <token>                       │
│     → Token revoked                                             │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

---

**End of Documentation**
