Implementasikan domain Task.

Database:

tasks
task_attachments
task_progress
task_status_histories

Status Flow:

OPEN
ACCEPTED
IN_PROGRESS
WAITING_APPROVAL
COMPLETED
DISPUTED
CANCELLED

Buat:

* TaskController
* TaskService
* Validation

Fitur:

* Create Task
* Update Task
* Delete Task
* Detail Task
* List Task
* My Task
* Task History

Authorization:

User hanya boleh mengelola task miliknya.

Gunakan transaction jika status berubah.

Output:

Controller
Service
Validation
Routes
API Documentation
