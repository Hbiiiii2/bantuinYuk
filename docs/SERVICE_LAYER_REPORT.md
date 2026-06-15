# SERVICE LAYER REPORT: BantuinYuk

**Tanggal:** 13 Juni 2026  
**Sprint:** Sprint 2 - Service Layer Foundation  
**Status:** ✅ Completed

---

## Table of Contents

1. [Struktur Service Layer](#1-struktur-service-layer)
2. [Dependency Diagram](#2-dependency-diagram)
3. [File yang Dibuat](#3-file-yang-dibuat)
4. [Method yang Tersedia](#4-method-yang-tersedia)
5. [Business Rules yang Dipindahkan](#5-business-rules-yang-dipindahkan)
6. [Usage Examples](#6-usage-examples)

---

## 1. Struktur Service Layer

```
app/
├── Exceptions/
│   ├── BusinessException.php      # Error business logic
│   └── ValidationException.php    # Error validasi
├── Services/
│   ├── BaseService.php            # Base class untuk semua service
│   ├── AuthService.php            # Autentikasi user
│   ├── TaskService.php            # Manajemen task
│   └── HelperService.php          # Manajemen helper
└── Traits/
    └── ApiResponseTrait.php       # Response format (sudah ada)
```

### Arsitektur Alur

```
Controller
    │
    ├─ Validation (input)
    ├─ Authorization (akses)
    ├─ Response (format)
    │
    ▼
Service
    │
    ├─ Business Logic
    ├─ Transaction Management
    ├─ Exception Handling
    │
    ▼
Model
    │
    ├─ Data Access
    ├─ Query Builder
    │
    ▼
Database
```

---

## 2. Dependency Diagram

```
┌─────────────────────────────────────────────────────────────┐
│                        Controller                           │
│  (AuthController, TaskController, HelperController)         │
└─────────────────────────────┬───────────────────────────────┘
                              │ use ApiResponseTrait
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                      Exceptions                             │
│  ┌─────────────────┐  ┌─────────────────────────────────┐  │
│  │ BusinessException│  │    ValidationException          │  │
│  └─────────────────┘  └─────────────────────────────────┘  │
└─────────────────────────────┬───────────────────────────────┘
                              │ throw
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                        Service                              │
│  ┌──────────────┐ ┌──────────────┐ ┌────────────────────┐  │
│  │  BaseService  │ │  AuthService │ │   TaskService      │  │
│  └──────────────┘ └──────────────┘ └────────────────────┘  │
│                       ┌────────────────────┐                │
│                       │   HelperService    │                │
│                       └────────────────────┘                │
└─────────────────────────────┬───────────────────────────────┘
                              │ use Models
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                         Model                               │
│  ┌────────────┐ ┌────────────┐ ┌────────────┐ ┌─────────┐ │
│  │ UserModel  │ │ TaskModel  │ │CategoryModel│ │Location │ │
│  └────────────┘ └────────────┘ └────────────┘ └─────────┘ │
│  ┌─────────────────┐  ┌─────────────────┐                  │
│  │HelperProfileModel│  │TaskStatusHistory│                  │
│  └─────────────────┘  └─────────────────┘                  │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                       Database                              │
│                     (MySQL Tables)                          │
└─────────────────────────────────────────────────────────────┘
```

### Service Dependencies

```
BaseService
    ├── CodeIgniter\Database\BaseConnection (db)
    └── Methods: transaction(), validateRequired(), validateEmail(), etc.

AuthService extends BaseService
    ├── UserModel
    └── Methods: register(), login(), getUserById(), updateProfile()

TaskService extends BaseService
    ├── TaskModel
    ├── CategoryModel
    ├── UserModel
    └── Methods: createTask(), acceptTask(), startTask(), submitTask(), completeTask(), etc.

HelperService extends BaseService
    ├── UserModel
    ├── HelperProfileModel
    ├── LocationModel
    ├── TaskModel
    └── Methods: getOrCreateProfile(), updateProfile(), submitVerification(), updateLocation(), etc.
```

---

## 3. File yang Dibuat

| File | Deskripsi | Lines |
|------|-----------|-------|
| `app/Exceptions/BusinessException.php` | Exception untuk business logic error | ~80 |
| `app/Exceptions/ValidationException.php` | Exception untuk validasi error | ~50 |
| `app/Services/BaseService.php` | Base class dengan utility methods | ~140 |
| `app/Services/AuthService.php` | Service untuk autentikasi | ~160 |
| `app/Services/TaskService.php` | Service untuk manajemen task | ~350 |
| `app/Services/HelperService.php` | Service untuk manajemen helper | ~300 |

---

## 4. Method yang Tersedia

### Exceptions

#### BusinessException

| Method | Return | Deskripsi |
|--------|--------|-----------|
| `__construct(message, statusCode, errors)` | - | Constructor |
| `getStatusCode()` | int | Ambil HTTP status code |
| `getErrors()` | mixed | Ambil error details |
| `toArray()` | array | Konversi ke array untuk response |
| `notFound(message)` | self | Factory: 404 Not Found |
| `alreadyExists(message)` | self | Factory: 409 Conflict |
| `unauthorized(message)` | self | Factory: 401 Unauthorized |
| `forbidden(message)` | self | Factory: 403 Forbidden |
| `conflict(message)` | self | Factory: 409 Conflict |
| `failed(message)` | self | Factory: 500 Server Error |

#### ValidationException

| Method | Return | Deskripsi |
|--------|--------|-----------|
| `__construct(errors, message)` | - | Constructor |
| `getErrors()` | array | Ambil validation errors |
| `getStatusCode()` | int | Selalu 422 |
| `toArray()` | array | Konversi ke array untuk response |
| `withErrors(errors)` | self | Factory dengan multiple errors |
| `single(field, message)` | self | Factory untuk single error |

---

### BaseService

| Method | Return | Deskripsi |
|--------|--------|-----------|
| `transaction(callback)` | mixed | Jalankan callback dalam DB transaction |
| `validateRequired(data, rules)` | void | Validasi field wajib ada |
| `validateEmail(email)` | bool | Validasi format email |
| `validateNumeric(value, field)` | void | Validasi format number |
| `validatePositive(value, field)` | void | Validasi value positif |
| `validateLength(value, field, min, max)` | void | Validasi panjang string |
| `generateReferenceId(prefix)` | string | Generate unique reference ID |
| `getData(data, key, default)` | mixed | Ambil data dengan default value |
| `filterData(data, allowedFields)` | array | Filter data hanya dengan field yang diizinkan |

---

### AuthService

| Method | Return | Deskripsi |
|--------|--------|-----------|
| `register(data)` | array | Register user baru |
| `login(data)` | array | Login dan return user data |
| `getUserById(userId)` | array | Ambil user berdasarkan ID |
| `updateProfile(userId, data)` | array | Update profile user |
| `isEmailExists(email)` | bool | Cek apakah email sudah terdaftar |

**Register Parameters:**
```php
$data = [
    'name'     => 'required|string(2-150)',
    'email'    => 'required|email|unique',
    'phone'    => 'optional|string',
    'password' => 'required|string(min:8)',
];
```

**Login Parameters:**
```php
$data = [
    'email'    => 'required|email',
    'password' => 'required',
];
```

---

### TaskService

| Method | Return | Deskripsi |
|--------|--------|-----------|
| `getAllTasks(filters, page, perPage)` | array | Ambil semua tasks dengan filter |
| `getTaskById(taskId)` | array | Ambil task berdasarkan ID |
| `createTask(userId, data)` | array | Buat task baru |
| `updateTask(taskId, userId, data)` | array | Update task |
| `cancelTask(taskId, userId)` | array | Cancel task |
| `acceptTask(taskId, helperId)` | array | Accept task oleh helper |
| `startTask(taskId, helperId)` | array | Mulai pengerjaan task |
| `submitTask(taskId, helperId)` | array | Submit hasil pengerjaan |
| `completeTask(taskId, userId)` | array | Selesaikan task |
| `getTasksByStatus(status, page, perPage)` | array | Filter by status |
| `getUserTasks(userId, status, page, perPage)` | array | Tasks milik user |
| `getHelperTasks(helperId, status, page, perPage)` | array | Tasks ditugaskan ke helper |
| `getUserTaskStats(userId)` | array | Statistik task user |

**Create Task Parameters:**
```php
$data = [
    'title'          => 'required|string(5-255)',
    'description'    => 'required|string',
    'price'          => 'required|numeric|positive',
    'category_id'    => 'required|exists:categories',
    'deadline_start' => 'required|datetime|future',
    'deadline_end'   => 'required|datetime|>deadline_start',
    'location'       => 'optional|string',
];
```

---

### HelperService

| Method | Return | Deskripsi |
|--------|--------|-----------|
| `getOrCreateProfile(userId)` | array | Ambil atau buat profile |
| `updateProfile(userId, data)` | array | Update profile |
| `getHelperProfile(userId)` | array | Ambil profile lengkap |
| `submitVerification(userId, data)` | array | Submit KTP untuk verifikasi |
| `updateLocation(userId, lat, lng)` | array | Update lokasi |
| `getLocation(userId)` | array\|null | Ambil lokasi |
| `getAvailableTasks(page, perPage)` | array | Tasks tersedia untuk helper |
| `getMyTasks(helperId, statuses, page, perPage)` | array | Tasks ditugaskan ke helper |
| `getHelperStats(helperId)` | array | Statistik helper |
| `getAllHelpers(filters, page, perPage)` | array | Semua helpers (admin) |

---

## 5. Business Rules yang Dipindahkan

### Dari AuthController → AuthService

| Rule | Sebelum (Controller) | Sesudah (Service) |
|------|---------------------|-------------------|
| Validasi input | Manual check | `validateRequired()` |
| Cek email exists | Manual query | `isEmailExists()` |
| Hash password | Manual (removed) | Handled by UserModel |
| Error response | Inline response | Throw exception |
| Return user data | Manual unset password | Service handle |

### Dari TaskController → TaskService

| Rule | Sebelum (Controller) | Sesudah (Service) |
|------|---------------------|-------------------|
| Validasi required fields | Manual check | `validateRequired()` |
| Validasi category exists | Tidak ada | Query CategoryModel |
| Validasi deadline | Tidak ada | Cek temporal logic |
| Cek task ownership | Tidak ada | Cek `user_id == userId` |
| Status transition check | Tidak ada | Validasi status flow |
| Response format | Inline | Throw exception |

### Dari HelperController → HelperService

| Rule | Sebelum (Controller) | Sesudah (Service) |
|------|---------------------|-------------------|
| Cek task exists | Manual find | Service method |
| Cek task status | Manual check | Validasi status |
| Cek helper role | Tidak ada | Cek `role == helper` |
| Cek self-accept | Tidak ada | Cek `user_id != helperId` |
| Update task | Direct model | Transaction wrapper |

---

## 6. Usage Examples

### Controller dengan Service (Pattern yang Benar)

```php
<?php

namespace App\Controllers;

use App\Services\TaskService;
use App\Traits\ApiResponseTrait;
use App\Exceptions\BusinessException;
use App\Exceptions\ValidationException;

class TaskController extends BaseController
{
    use ApiResponseTrait;

    protected TaskService $taskService;

    public function __construct()
    {
        $this->taskService = new TaskService();
    }

    public function store()
    {
        try {
            $data = $this->request->getJSON(true);
            $userId = auth()->id();

            $task = $this->taskService->createTask($userId, $data);

            return $this->createdResponse($task, 'Task created successfully');

        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->getErrors(), $e->getMessage());

        } catch (BusinessException $e) {
            return $this->errorResponse($e->getMessage(), $e->getStatusCode());

        } catch (\Exception $e) {
            return $this->errorResponse('An unexpected error occurred', 500);
        }
    }

    public function acceptTask($taskId)
    {
        try {
            $helperId = auth()->id();

            $task = $this->taskService->acceptTask($taskId, $helperId);

            return $this->successResponse($task, 'Task accepted successfully');

        } catch (BusinessException $e) {
            return $this->errorResponse($e->getMessage(), $e->getStatusCode());

        } catch (\Exception $e) {
            return $this->errorResponse('An unexpected error occurred', 500);
        }
    }
}
```

### Service dengan Transaction (Pattern yang Benar)

```php
public function acceptTask(int $taskId, int $helperId): array
{
    // 1. Validasi
    $task = $this->taskModel->find($taskId);
    if (!$task) {
        throw BusinessException::notFound('Task not found');
    }

    if ($task['status'] !== TaskModel::STATUS_OPEN) {
        throw BusinessException::conflict('Task is no longer available');
    }

    // 2. Business Rules
    if ($task['user_id'] == $helperId) {
        throw BusinessException::conflict('You cannot accept your own task');
    }

    // 3. Transaction
    $result = $this->transaction(function () use ($taskId, $helperId) {
        $this->taskModel->update($taskId, [
            'helper_id' => $helperId,
            'status'    => TaskModel::STATUS_ACCEPTED,
        ]);

        return $this->getTaskById($taskId);
    });

    return $result;
}
```

---

## 7. Exception Handling Flow

```
Service Method
    │
    ├─ Validasi gagal → throw ValidationException
    │                       │
    │                       ▼
    │                   Controller catch
    │                       │
    │                       ▼
    │                   return validationErrorResponse()
    │
    ├─ Business rule gagal → throw BusinessException
    │                           │
    │                           ▼
    │                       Controller catch
    │                           │
    │                           ▼
    │                       return errorResponse()
    │
    ├─ Transaction gagal → throw BusinessException
    │                          │
    │                          ▼
    │                      DB transRollback()
    │                          │
    │                          ▼
    │                      Controller catch
    │
    └─ Success → return data
                    │
                    ▼
                Controller
                    │
                    ▼
                return successResponse() / createdResponse()
```

---

## 8. Checklist

- [x] BaseService.php - Base class dengan utility methods
- [x] AuthService.php - Register, login, profile management
- [x] TaskService.php - Full task lifecycle management
- [x] HelperService.php - Helper profile, location, task matching
- [x] BusinessException.php - Business error handling
- [x] ValidationException.php - Validation error handling
- [x] Transaction support untuk operasi kritikal
- [x] Consistent exception handling
- [x] Input validation di service layer
- [x] Business rules dipindahkan dari controller

---

**End of Report**
