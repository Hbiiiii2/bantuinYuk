# TASK VISIBILITY DECISION

**Tanggal:** 14 Juni 2026  
**Status:** ✅ Decided

---

## Business Requirement Analysis

### Question

Apakah task bersifat:
- A. **Public Marketplace** - Semua user dapat melihat semua task
- B. **Private Marketplace** - User hanya dapat melihat task sendiri

---

## Analysis

### Current Implementation

```
GET /api/v1/tasks/{id}
```

Semua authenticated user dapat melihat detail task.

### Business Model Context

BantuinYuk adalah platform **outsourcing jasa** dimana:
1. **User** membuat task (project)
2. **Helper** melihat dan menerima task
3. Task harus **visible** agar helper dapat menemukan pekerjaan

---

## Decision

### ✅ Task adalah PUBLIC MARKETPLACE

**Alasan:**

1. Helper perlu melihat semua task yang tersedia untuk memilih mana yang ingin dikerjakan
2. Jika task hanya visible untuk owner, helper tidak dapat menemukan pekerjaan
3. Model bisnis marketplace membutuhkan visibility tinggi
4. Task status open = available untuk semua helper

### Visibility Rules

| Field | Visibility | Alasan |
|-------|------------|--------|
| Title | Public | Helper perlu judul task |
| Description | Public | Helper perlu detail task |
| Price | Public | Helper perlu tahu harga |
| Category | Public | Helper perlu filter task |
| Location | Public | Helper perlu tahu lokasi |
| Deadline | Public | Helper perlu tahu waktu |
| Status | Public | Helper perlu tahu status |
| User Name | Public | Transparansi |
| Helper Name | Public | Setelah diterima |

---

## Security Considerations

### Yang TIDAK di-expose:

1. User email - Tidak perlu untuk marketplace
2. User phone - Privasi
3. Internal notes - Jika ada

### Yang sudah benar:

1. User ID hanya untuk authorization, bukan untuk display
2. Task data hanya berisi informasi yang relevan untuk marketplace

---

## Recommendation

**Tidak perlu menambahkan ownership validation** pada task detail endpoint.

Current implementation sudah sesuai dengan business model marketplace.

---

**End of Decision**
