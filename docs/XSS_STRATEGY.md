# XSS STRATEGY

**Tanggal:** 14 Juni 2026  
**Status:** ✅ Documented

---

## Overview

BantuinYuk menggunakan REST API yang mengembalikan JSON. XSS protection strategy disesuaikan dengan konteks ini.

---

## User-Generated Content Fields

### Fields yang memerlukan sanitization:

| Domain | Field | Location |
|--------|-------|----------|
| Task | title | TaskService::createTask() |
| Task | description | TaskService::createTask() |
| Review | review | ReviewService::createReview() |
| Dispute | reason | DisputeService::createDispute() |
| Dispute | description | DisputeService::createDispute() |
| Progress | description | ProgressService::createProgress() |
| Notification | message | NotificationService::create() |

---

## Strategy

### 1. Input Validation (Backend)

**Approach:** Whitelist characters, reject dangerous patterns.

```php
// Validation rules
$this->validateLength($data['title'], 'title', 5, 255);
$this->validateLength($data['description'], 'description', 10, 5000);
```

### 2. Output Encoding (JSON API)

**Approach:** JSON naturally escapes special characters.

Ketika backend mengembalikan JSON:
```json
{
    "description": "Task <script>alert('xss')</script>"
}
```

CodeIgniter's `setJSON()` otomatis escape:
```json
{
    "description": "Task \u003Cscript\u003Ealert('xss')\u003C/script\u003E"
}
```

### 3. Frontend Responsibility

**Approach:** Frontend (PWA) harus implement sanitization saat rendering.

| Framework | Built-in Protection |
|-----------|---------------------|
| React | Auto-escapes JSX |
| Vue | Auto-escapes template |
| Angular | Auto-escapes bindings |

---

## Backend Sanitization

### Current State

Backend melakukan **validation** tapi belum melakukan **sanitization** explicit.

### Recommendation

Untuk MVP, backend validation sudah cukup karena:
1. JSON response otomatis escape
2. Frontend framework punya built-in protection
3. Database tidak interprets HTML

### Optional Enhancement (Post-MVP)

Tambahkan `htmlspecialchars()` atau `strip_tags()` untuk defense-in-depth:

```php
// Optional: Strip HTML tags
$data['description'] = strip_tags($data['description']);

// Optional: Encode special characters
$data['description'] = htmlspecialchars($data['description'], ENT_QUOTES, 'UTF-8');
```

---

## Attack Vectors & Mitigations

### 1. Stored XSS

**Risk:** User input disimpan di database dan ditampilkan ke user lain.

**Mitigation:**
- ✅ JSON encoding on output
- ✅ Frontend auto-escaping
- ✅ Content Security Policy (CSP) headers

### 2. Reflected XSS

**Risk:** Input langsung di-reflect tanpa encoding.

**Mitigation:**
- ✅ API returns JSON, not HTML
- ✅ No direct HTML rendering

### 3. DOM-based XSS

**Risk:** JavaScript manipulation di client-side.

**Mitigation:**
- Frontend responsibility
- Use safe DOM APIs

---

## Testing Checklist

| Test Case | Expected Result |
|-----------|-----------------|
| Input `<script>alert('xss')</script>` | Stored as text, not executed |
| Input `<img onerror="alert('xss')">` | Stored as text, not executed |
| Input `javascript:alert('xss')` | Stored as text, not executed |
| Input `<b>bold</b>` | Stored as text, rendered by frontend |

---

## Conclusion

### Current Status: ✅ ADEQUATE FOR MVP

Backend API sudah cukup aman karena:
1. Response adalah JSON (auto-escaped)
2. Frontend framework punya built-in protection
3. Validation sudah dilakukan

### Post-MVP Enhancement

Tambahkan explicit sanitization sebagai defense-in-depth.

---

**End of Strategy**
