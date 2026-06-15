# ATTACHMENT & PROGRESS REPORT

**Tanggal:** 13 Juni 2026  
**Sprint:** Attachment & Progress Implementation  
**Status:** ✅ Completed

---

## Table of Contents

1. [File yang Dibuat/Diubah](#1-file-yang-dibuatdiubah)
2. [Security Review](#2-security-review)
3. [Upload Validation Matrix](#3-upload-validation-matrix)
4. [Ownership Matrix](#4-ownership-matrix)
5. [State Transition Matrix](#5-state-transition-matrix)
6. [Testing Checklist](#6-testing-checklist)

---

## 1. File yang Dibuat/Diubah

### New Files

| File | Description |
|------|-------------|
| `app/Models/TaskAttachmentModel.php` | Model untuk task_attachments |
| `app/Models/TaskProgressModel.php` | Model untuk task_progress |
| `app/Services/AttachmentService.php` | Service untuk attachment management |
| `app/Services/ProgressService.php` | Service untuk progress management |

### Modified Files

| File | Changes |
|------|---------|
| `app/Controllers/TaskController.php` | Added uploadAttachment(), getAttachments(), deleteAttachment() |
| `app/Controllers/HelperController.php` | Added createProgress(), getProgress(), uploadAttachment() |
| `app/Config/Routes.php` | Added attachment and progress routes |

---

## 2. Security Review

### Implemented Security

| Security | Status | Implementation |
|----------|--------|----------------|
| Authentication | ✅ | Shield tokens filter |
| Authorization | ✅ | Role-based (user/helper) |
| Ownership validation | ✅ | Task ownership check |
| MIME type validation | ✅ | finfo_file() check |
| File size validation | ✅ | MAX_FILE_SIZE limit |
| File extension validation | ✅ | Whitelist check |
| Path traversal prevention | ✅ | Unique filename generation |
| SQL Injection | ✅ | Query builder |

### Protected Against

| Threat | Status | Protection |
|--------|--------|------------|
| IDOR | ✅ | Ownership check before upload/delete |
| Malicious file upload | ✅ | MIME type + extension whitelist |
| File system attack | ✅ | Unique filename, no user input in path |
| Oversized file | ✅ | 10MB limit |
| Unauthorized access | ✅ | Token + role validation |

---

## 3. Upload Validation Matrix

### Allowed MIME Types

| Category | MIME Types |
|----------|------------|
| Images | image/jpeg, image/png, image/gif, image/webp |
| Videos | video/mp4, video/mpeg, video/webm |
| Documents | application/pdf, application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/vnd.ms-excel, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, text/plain |

### Allowed Extensions

| Category | Extensions |
|----------|------------|
| Images | jpg, jpeg, png, gif, webp |
| Videos | mp4, mpeg, webm |
| Documents | pdf, doc, docx, xls, xlsx, txt |

### Validation Rules

| Rule | Value | Error Message |
|------|-------|---------------|
| Max file size | 10MB | File size must not exceed 10MB |
| MIME type | Whitelist | File type not allowed |
| Extension | Whitelist | File extension not allowed |
| Upload error | UPLOAD_ERR_OK | File upload failed |

### Upload Flow

```
Client
  │
  ├─ POST /api/v1/tasks/{id}/attachments
  │  (multipart/form-data, field: file)
  │
  ▼
Controller
  │
  ├─ Get file from request
  ├─ Convert to array format
  │
  ▼
AttachmentService
  │
  ├─ Validate task exists
  ├─ Validate ownership (owner or helper)
  ├─ Validate file:
  │   ├─ Upload error check
  │   ├─ File size check
  │   ├─ MIME type check
  │   └─ Extension check
  ├─ Generate unique filename
  ├─ Move file to uploads/tasks/
  ├─ Save to database
  │
  ▼
Response
  │
  └─ 201 Created with attachment data
```

---

## 4. Ownership Matrix

### Attachment Operations

| Operation | user (owner) | helper (assigned) | helper (other) | admin |
|-----------|--------------|-------------------|----------------|-------|
| Upload to task | ✅ | ✅ | ❌ | ❌ |
| View attachments | ✅ | ✅ | ❌ | ❌ |
| Delete own attachment | ✅ | ✅ | ❌ | ❌ |
| Delete other's attachment | ✅ (as owner) | ❌ | ❌ | ❌ |

### Progress Operations

| Operation | user (owner) | helper (assigned) | helper (other) | admin |
|-----------|--------------|-------------------|----------------|-------|
| Create progress | ❌ | ✅ | ❌ | ❌ |
| View progress | ✅ | ✅ | ❌ | ❌ |
| Delete progress | ❌ | ✅ (own) | ❌ | ❌ |

### Ownership Validation Code

```php
// AttachmentService::uploadAttachment()
$isOwner = $task['user_id'] == $userId;
$isHelper = $task['helper_id'] == $userId && $user->role === 'helper';

if (!$isOwner && !$isHelper) {
    throw BusinessException::forbidden('You can only upload attachments to your own tasks');
}

// ProgressService::createProgress()
if ($task['helper_id'] != $helperId) {
    throw BusinessException::forbidden('You are not assigned to this task');
}
```

---

## 5. State Transition Matrix

### Task Status Impact

| Action | Task Status | Status History |
|--------|-------------|----------------|
| Create progress (on ACCEPTED) | ACCEPTED → IN_PROGRESS | ✅ Created |
| Create progress (on IN_PROGRESS) | No change | ❌ Not created |
| Upload attachment | No change | ❌ Not created |

### Progress Status

| Status | Description |
|--------|-------------|
| active | Progress visible |
| deleted | Soft deleted |

---

## 6. Testing Checklist

### Attachment Upload

- [ ] User dapat upload attachment ke task sendiri
- [ ] Helper dapat upload attachment ke task yang di-assign
- [ ] User lain tidak dapat upload ke task orang lain (403)
- [ ] Helper lain tidak dapat upload ke task yang bukan miliknya (403)
- [ ] File size > 10MB ditolak (422)
- [ ] MIME type tidak valid ditolak (422)
- [ ] Extension tidak valid ditolak (422)
- [ ] Multiple upload bekerja
- [ ] File tersimpan di uploads/tasks/
- [ ] Record tersimpan di database

### Attachment View

- [ ] User dapat melihat attachments task sendiri
- [ ] Helper dapat melihat attachments task yang di-assign
- [ ] Attachments terurut berdasarkan created_at

### Attachment Delete

- [ ] Uploader dapat delete attachment sendiri
- [ ] Task owner dapat delete attachment apapun di tasknya
- [ ] User lain tidak dapat delete (403)
- [ ] File terhapus dari filesystem

### Progress Create

- [ ] Helper dapat create progress untuk task yang di-assign
- [ ] User tidak dapat create progress (403)
- [ ] Helper lain tidak dapat create progress (403)
- [ ] Progress dengan attachment_ids bekerja
- [ ] Attachment ids divalidasi (harus milik task yang sama)
- [ ] Status ACCEPTED → IN_PROGRESS saat progress pertama
- [ ] Status history tercatat

### Progress View

- [ ] User dapat melihat progress task sendiri
- [ ] Helper dapat melihat progress task yang di-assign
- [ ] Attachments pada progress ditampilkan
- [ ] Helper name ditampilkan

### Progress Delete

- [ ] Creator dapat delete progress sendiri
- [ ] User tidak dapat delete progress (403)
- [ ] Helper lain tidak dapat delete (403)
- [ ] Soft delete (status = deleted)

### Security

- [ ] Tidak ada IDOR
- [ ] Semua logic berada di Service
- [ ] Ownership selalu divalidasi
- [ ] File validation lengkap

---

**End of Report**
