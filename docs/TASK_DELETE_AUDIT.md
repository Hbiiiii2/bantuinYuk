# TASK DELETE ENDPOINT AUDIT

**Date:** 2026-06-14  
**Endpoint:** DELETE /tasks/{id}

---

## 1. AUDIT RESULTS

### Backend Verification

| Check | Status | Location |
|-------|--------|----------|
| Route registered | ✅ YES | `Routes.php:42` |
| Controller method | ✅ YES | `TaskController::delete()` (line 121) |
| Service method | ✅ YES | `TaskService::cancelTask()` (line 295) |
| OpenAPI documented | ❌ NO | Not in OPENAPI.yaml |

---

## 2. ROUTE VERIFICATION

**File:** `app/Config/Routes.php`

```php
// Line 42
$routes->delete('(:num)', 'TaskController::delete/$1');
```

**Status:** ✅ Route exists and is properly registered under:
- Group: `api/v1/tasks`
- Filter: `tokens` (authentication required)

---

## 3. CONTROLLER VERIFICATION

**File:** `app/Controllers/TaskController.php`

```php
// Line 121-136
public function delete($id)
{
    try {
        $userId = auth()->id();
        $task = $this->taskService->cancelTask((int) $id, $userId);
        return $this->successResponse($task, 'Task cancelled successfully');

    } catch (BusinessException $e) {
        return $this->errorResponse($e->getMessage(), $e->getStatusCode());

    } catch (\Exception $e) {
        return $this->errorResponse('An unexpected error occurred', 500);
    }
}
```

**Status:** ✅ Controller implementation exists

---

## 4. SERVICE VERIFICATION

**File:** `app/Services/TaskService.php`

```php
// Line 295-330
public function cancelTask(int $taskId, int $userId, ?string $note = null): array
{
    $task = $this->taskModel->find($taskId);

    if (!$task) {
        throw BusinessException::notFound('Task not found');
    }

    if ($task['user_id'] != $userId) {
        throw BusinessException::forbidden('You can only cancel your own tasks');
    }

    if (!in_array($task['status'], [TaskModel::STATUS_OPEN, TaskModel::STATUS_ACCEPTED])) {
        throw BusinessException::conflict('Cannot cancel task in current status');
    }

    $result = $this->transaction(function () use ($taskId, $userId, $note, $task) {
        $this->changeStatus($taskId, TaskModel::STATUS_CANCELLED, $userId, $note);

        // Send notification to helper if task was accepted
        if ($task['helper_id']) {
            $owner = $this->userModel->find($userId);
            $this->notificationService->notifyTaskCancelled(
                $taskId,
                $task['title'],
                $task['helper_id'],
                $userId,
                $owner['name'] ?? 'User'
            );
        }

        return $this->getTaskById($taskId);
    });

    return $result;
}
```

**Status:** ✅ Service implementation exists

---

## 5. BUSINESS LOGIC ANALYSIS

### What Actually Happens

| Aspect | Behavior |
|--------|----------|
| Operation | **CANCEL** (not hard delete) |
| Status Change | Sets task status to `cancelled` |
| Valid From | Only `open` or `accepted` status |
| Ownership | Only task owner can cancel |
| Notification | Sends notification to helper (if assigned) |

### Status Transition

```
OPEN ──────────────► CANCELLED
ACCEPTED ───────────► CANCELLED
IN_PROGRESS ────────► ❌ NOT ALLOWED
WAITING_APPROVAL ───► ❌ NOT ALLOWED
COMPLETED ──────────► ❌ NOT ALLOWED
```

---

## 6. OPENAPI DOCUMENTATION STATUS

**Finding:** Endpoint exists in backend but is NOT documented in OPENAPI.yaml

**Missing Documentation:**

```yaml
# Should be added to OPENAPI.yaml under /tasks/{id}
delete:
  tags: [Task]
  summary: Cancel task
  description: Cancel a task. Owner only. Only works for open or accepted tasks.
  parameters:
    - name: id
      in: path
      required: true
      schema:
        type: integer
  responses:
    '200':
      description: Task cancelled successfully
    '403':
      description: You can only cancel your own tasks
    '409':
      description: Cannot cancel task in current status
    '404':
      description: Task not found
```

---

## 7. FRONTEND USAGE

### Current Frontend Code

**File:** `src/features/tasks/services/task.service.ts`

```typescript
async deleteTask(id: number): Promise<void> {
  await api.delete(`/tasks/${id}`)
}
```

### Issue

The frontend method is named `deleteTask` but the backend actually **cancels** the task, not deletes it.

### Recommended Refactor

**Rename for clarity:**

```typescript
// Before
async deleteTask(id: number): Promise<void> {
  await api.delete(`/tasks/${id}`)
}

// After
async cancelTask(id: number): Promise<void> {
  await api.delete(`/tasks/${id}`)
}
```

**Update hooks:**

```typescript
// Before
export function useDeleteTask() {
  return useMutation({
    mutationFn: (taskId: number) => taskService.deleteTask(taskId),
    ...
  })
}

// After
export function useCancelTask() {
  return useMutation({
    mutationFn: (taskId: number) => taskService.cancelTask(taskId),
    ...
  })
}
```

---

## 8. SUMMARY

| Item | Status |
|------|--------|
| Endpoint exists | ✅ YES |
| Route registered | ✅ YES |
| Controller exists | ✅ YES |
| Service exists | ✅ YES |
| OpenAPI documented | ❌ NO |
| Frontend uses it | ✅ YES |

### Conclusion

**The endpoint EXISTS and WORKS.**

The only issues are:
1. OpenAPI documentation is missing (backend issue, not frontend)
2. Frontend naming is misleading (`deleteTask` vs actual `cancelTask` behavior)

### Action Items

| Priority | Action | Owner |
|----------|--------|-------|
| LOW | Add DELETE /tasks/{id} to OPENAPI.yaml | Backend |
| LOW | Rename `deleteTask` to `cancelTask` in frontend | Frontend |

---

**Report Generated:** 2026-06-14  
**Status:** ✅ Endpoint Verified - Exists and Working  
**Frontend Refactored:** ✅ Complete

---

## 9. FRONTEND REFACTORING COMPLETED

### Changes Made

| File | Before | After |
|------|--------|-------|
| `task.service.ts` | `deleteTask()` | `cancelTask()` |
| `useTasks.ts` | `useDeleteTask()` | `useCancelTask()` |
| `hooks/index.ts` | `useDeleteTask` export | `useCancelTask` export |
| `tasks/index.ts` | `useDeleteTask` export | `useCancelTask` export |

### Code Changes

**Before:**
```typescript
// task.service.ts
async deleteTask(id: number): Promise<void> {
  await api.delete(`/tasks/${id}`)
}

// useTasks.ts
export function useDeleteTask() {
  return useMutation({
    mutationFn: (taskId: number) => taskService.deleteTask(taskId),
    ...
  })
}
```

**After:**
```typescript
// task.service.ts
async cancelTask(id: number): Promise<void> {
  await api.delete(`/tasks/${id}`)
}

// useTasks.ts
export function useCancelTask() {
  return useMutation({
    mutationFn: (taskId: number) => taskService.cancelTask(taskId),
    ...
  })
}
```

### Build Verification

| Test | Result |
|------|--------|
| npm run build | ✅ Success |
| TypeScript | ✅ No errors |

### Final Endpoint Status

| Endpoint | Backend | Route | Controller | OpenAPI | Frontend |
|----------|---------|-------|------------|---------|----------|
| DELETE /tasks/{id} | ✅ | ✅ | ✅ | ❌ Missing | ✅ Renamed to cancelTask |
