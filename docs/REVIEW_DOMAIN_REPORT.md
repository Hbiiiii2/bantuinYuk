# REVIEW DOMAIN REPORT

**Tanggal:** 14 Juni 2026  
**Sprint:** Review & Rating Implementation  
**Status:** ✅ Completed

---

## Table of Contents

1. [File yang Dibuat/Diubah](#1-file-yang-dibuatdiubah)
2. [Rating Calculation Formula](#2-rating-calculation-formula)
3. [Authorization Matrix](#3-authorization-matrix)
4. [Security Review](#4-security-review)
5. [Business Rules](#5-business-rules)
6. [Testing Checklist](#6-testing-checklist)

---

## 1. File yang Dibuat/Diubah

### New Files

| File | Description |
|------|-------------|
| `app/Models/TaskReviewModel.php` | Model untuk task_reviews |
| `app/Services/ReviewService.php` | Service untuk review & rating management |

### Modified Files

| File | Changes |
|------|---------|
| `app/Controllers/TaskController.php` | Added createReview(), getReview() |
| `app/Controllers/HelperController.php` | Added getReviews(), getRatingSummary() |
| `app/Controllers/AdminController.php` | Added getReviews() |
| `app/Config/Routes.php` | Added review routes (user, helper, admin) |

---

## 2. Rating Calculation Formula

### Average Rating

```
Average Rating = SUM(all ratings) / COUNT(all reviews)
```

**Example:**
- Review 1: rating = 5
- Review 2: rating = 4
- Review 3: rating = 3

```
Average Rating = (5 + 4 + 3) / 3 = 4.00
```

### Rating Distribution

```json
{
    "1": count,
    "2": count,
    "3": count,
    "4": count,
    "5": count
}
```

### Database Updates

When a review is created:

1. **users.rating** - Updated with new average rating
2. **helper_profiles.completed_tasks** - Recalculated from tasks table

```php
// Update rating
$averageRating = $this->reviewModel->getAverageRating($helperId);
$this->userModel->update($helperId, ['rating' => $averageRating]);

// Update completed_tasks
$completedCount = $this->taskModel
    ->where('helper_id', $helperId)
    ->where('status', 'completed')
    ->countAllResults();
$this->helperProfileModel->update($profileId, ['completed_tasks' => $completedCount]);
```

---

## 3. Authorization Matrix

### Review Operations

| Operation | user (owner) | user (other) | helper (assigned) | helper (other) | admin |
|-----------|--------------|--------------|-------------------|----------------|-------|
| Create review | ✅ | ❌ | ❌ | ❌ | ❌ |
| View task review | ✅ | ❌ | ✅ | ❌ | ✅ |
| View helper reviews | ❌ | ❌ | ✅ (own) | ❌ | ✅ |
| View rating summary | ❌ | ❌ | ✅ (own) | ❌ | ✅ |

### Route Access

| Route | Method | Auth | Role | Description |
|-------|--------|------|------|-------------|
| `/tasks/{id}/review` | POST | ✅ | user (owner) | Create review |
| `/tasks/{id}/review` | GET | ✅ | owner/helper | Get task review |
| `/helpers/reviews` | GET | ✅ | helper (own) | Get helper reviews |
| `/helpers/rating-summary` | GET | ✅ | helper (own) | Get rating summary |
| `/admin/reviews` | GET | ✅ | admin | Get all reviews |

---

## 4. Security Review

### Implemented Security

| Security | Status | Implementation |
|----------|--------|----------------|
| Authentication | ✅ | Shield tokens filter |
| Authorization | ✅ | Role + ownership check |
| Ownership validation | ✅ | user_id check |
| Review duplication prevention | ✅ | hasReview() check |
| Rating validation | ✅ | Min/Max range check |
| Task status validation | ✅ | STATUS_COMPLETED check |
| SQL Injection | ✅ | Query builder |

### Protected Against

| Threat | Status | Protection |
|--------|--------|------------|
| IDOR | ✅ | Ownership check before create |
| Duplicate review | ✅ | hasReview() check |
| Invalid rating | ✅ | Min/Max validation (1-5) |
| Review on incomplete task | ✅ | Status check |
| Unauthorized review | ✅ | Owner check |
| Rating manipulation | ✅ | Server-side calculation |

### Validation Rules

| Rule | Validation | Error Message |
|------|------------|---------------|
| Rating required | isset($data['rating']) | Rating is required |
| Rating range | 1 <= rating <= 5 | Rating must be between 1 and 5 |
| Task exists | $this->taskModel->find($taskId) | Task not found |
| Task completed | $task['status'] === 'completed' | Only completed tasks can be reviewed |
| Task ownership | $task['user_id'] == $userId | You can only review your own tasks |
| No duplicate | !$this->reviewModel->hasReview($taskId) | This task already has a review |
| Helper exists | $task['helper_id'] | Task has no assigned helper |

---

## 5. Business Rules

### Create Review

1. Task must be COMPLETED
2. User must be task owner
3. Task must not have existing review
4. Task must have assigned helper
5. Rating must be 1-5
6. Comment is optional

### After Review Created

1. Update users.rating with new average
2. Update helper_profiles.completed_tasks with count

### Review Fields

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| task_id | int | ✅ | From route parameter |
| rating | int | ✅ | 1-5 |
| review | string | ❌ | Max 2000 chars |

### Response Fields

| Field | Type | Description |
|-------|------|-------------|
| id | int | Review ID |
| task_id | int | Task ID |
| user_id | int | Reviewer ID |
| helper_id | int | Helper ID |
| rating | int | Rating 1-5 |
| review | string | Comment |
| user_name | string | Reviewer name |
| task_title | string | Task title |
| created_at | datetime | Creation timestamp |

---

## 6. Testing Checklist

### Create Review

- [ ] User dapat review task sendiri yang COMPLETED
- [ ] Review gagal jika task bukan COMPLETED (409)
- [ ] Review gagal jika task bukan milik sendiri (403)
- [ ] Review gagal jika task sudah ada review (409)
- [ ] Review gagal jika rating < 1 (422)
- [ ] Review gagal jika rating > 5 (422)
- [ ] Review berhasil dengan rating saja
- [ ] Review berhasil dengan rating + comment
- [ ] Helper rating terupdate setelah review
- [ ] completed_tasks terupdate setelah review

### View Task Review

- [ ] Owner dapat melihat review task sendiri
- [ ] Helper dapat melihat review task yang di-assign
- [ ] Return 404 jika belum ada review

### View Helper Reviews

- [ ] Helper dapat melihat reviews sendiri
- [ ] Reviews terurut berdasarkan created_at DESC
- [ ] Pagination bekerja

### View Rating Summary

- [ ] Helper dapat melihat rating summary sendiri
- [ ] Average rating benar
- [ ] Total reviews benar
- [ ] Completed tasks benar
- [ ] Distribution benar

### Security

- [ ] Tidak ada IDOR
- [ ] Tidak ada duplicate review
- [ ] Rating selalu 1-5
- [ ] Hanya COMPLETED task yang bisa direview
- [ ] Hanya owner yang bisa create review
- [ ] Semua logic berada di Service

---

**End of Report**
