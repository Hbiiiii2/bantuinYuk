# HELPER TASK MODULE REPORT

This document reports the implementation details and verification results of the **Helper Task Module** for Sprint 13.5. All backend and user modules are final, and implementation focus has been purely on completing the helper functionality.

---

## 1. Dashboard Implementation
- **Route:** `/helper/dashboard`
- **Interface Structure:**
  - **Header & Quick Actions:** Navigational links to find tasks and access the wallet.
  - **Earnings Summary:** Displays the helper's total earnings fetched from the wallet API balance.
  - **Current Task Card:** Renders the currently active task assigned to the helper. If no task is active, shows an empty state prompting the helper to search for tasks.
  - **Available Tasks List:** Shows a list of nearby tasks that the helper can accept.
  - **Statistics Component:** Displays Completed Tasks, Current/Active Tasks, Rating, and Total Earnings using a grid layout of cards.

---

## 2. Available Tasks
- **Route:** `/helper/tasks`
- **Features:**
  - **Search Bar:** Real-time query text matching.
  - **Category Filters:** Horizontal pill filters utilizing task categories from the API.
  - **Task List:** Lists tasks with a details card showing: Title, Category name, Budget/Price, Distance, Deadline end date, and status.
  - **Pagination:** Bottom navigation allowing page increments of 10 items per page.
  - **Base Path Config:** Pass `basePath="helper"` to `TaskCard` instances to correctly redirect helpers to `/helper/tasks/:id` details instead of `/user/tasks/:id`.

---

## 3. Task Detail
- **Route:** `/helper/tasks/:id`
- **Features:**
  - Displays full task details, description, deadline range, location, and budget.
  - Displays the creator/requester information with initials avatar.
  - Shows the task attachments and historical status change timeline.
  - **Accept Task Action:**
    - Visible only when `status = OPEN` and task does not belong to the logged-in user.
    - Prompts the helper with a confirmation modal. Clicking "Accept" triggers `useAcceptTask` and navigates directly to `/helper/current-task`.

---

## 4. Current Task
- **Route:** `/helper/current-task`
- **Features:**
  - Shows the details of the active task accepted by the helper.
  - **Start Work Action:** Visible if the task status is `ACCEPTED`. Helper clicks to transition task status to `IN_PROGRESS`.
  - **Milestone Updates:** Form for progress updates, combined with a vertical timeline listing notes and attachments.
  - **Submit Task Action:** Visible when the status is `IN_PROGRESS`. Prompts confirmation dialog and transitions status to `WAITING_APPROVAL`.

---

## 5. Progress Upload & Attachments
- **Features:**
  - Notes field validation requiring a minimum length of 10 characters.
  - Multi-file selection support (up to 3 files).
  - **Sequential Upload Queue:** Files are uploaded in sequence to `POST /helpers/tasks/{id}/attachments` before progress creation.
  - **Attachment ID Association:** Retreived file attachment IDs are aggregated and posted as `attachment_ids` inside the progress request payload (`POST /helpers/tasks/{id}/progress`), fully matching backend validation constraints.
  - Local error alerts display in the form for failed uploads or notes validations.

---

## 6. Profile
- **Route:** `/helper/profile`
- **Features:**
  - Displays name, email, avatar image (or initials), and dynamic verification badge based on `verification_status` (`pending`, `verified`, `rejected`, or `unverified`).
  - **Rating Distribution Graph:** Star ratings breakdown (5 to 1 star counts) with horizontal percentage meters.
  - **Profile Editor:** In-line editing fields for Bio (max 1000 characters) and Skills (max 500 characters) saving to the server using the update profile query.

---

## 7. API Integration
All interactions utilize pre-configured endpoints without introducing new paths:
- `GET /helpers/stats` -> Retrieve task count summaries.
- `GET /helpers/my-tasks` -> Fetch assigned/current helper tasks.
- `GET /helpers/profile` -> Fetch helper bio, skills, and KTP status.
- `PUT /helpers/profile` -> Update bio and skills.
- `GET /helpers/rating-summary` -> Fetch ratings distribution.
- `POST /helpers/tasks/{taskId}/accept` -> Accept a task.
- `POST /helpers/tasks/{taskId}/start` -> Start working on an accepted task.
- `POST /helpers/{taskId}/submit` -> Submit task for review.
- `POST /helpers/tasks/{taskId}/progress` -> Append progress milestones with `attachment_ids`.
- `GET /helpers/tasks/{taskId}/progress` -> Fetch list of progress events.
- `POST /helpers/tasks/{taskId}/attachments` -> Upload progress file attachments.

---

## 8. Caching Strategy
Query caching and invalidation are handled by TanStack React Query using `helperKeys`:
- **Mutations & Invalidation Table:**
  - `Accept Task` -> Invalidates `dashboard`, `availableTasks`, `currentTask`, `myTasks`.
  - `Start Task` -> Invalidates `taskDetail`, `currentTask`, `myTasks`.
  - `Upload Progress` -> Invalidates `progress`.
  - `Submit Task` -> Invalidates `taskDetail`, `currentTask`, `myTasks`, `dashboard`.

---

## 9. Accessibility Review
- **Keyboard Navigation:** `TaskCard` has `tabIndex={0}`, `role="button"`, `onKeyDown` listeners reacting to `Enter` and `Space`, and visible focus rings.
- **Form Controls:** Textareas and inputs have appropriate ARIA label bindings and required declarations (`aria-required="true"`, `aria-labelledby`).
- **Touch Targets:** Minimum dimension constraints for action buttons and removal toggles mapped to `>= 44px` on mobile screens.

---

## 10. Testing Results

Verification test suite outcomes:

- [x] Dashboard tampil
- [x] Available Tasks tampil
- [x] Search bekerja
- [x] Filter bekerja
- [x] Task Detail tampil
- [x] Accept Task berhasil
- [x] Current Task tampil
- [x] Upload Progress berhasil
- [x] Submit Task berhasil
- [x] Profile tampil
- [x] Mobile responsive
- [x] Tablet responsive
- [x] Desktop responsive

---
*Report generated successfully and aligned with the Decoupled architecture specifications.*
