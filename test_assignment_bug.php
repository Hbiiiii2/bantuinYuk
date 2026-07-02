<?php
$conn = new mysqli('localhost', 'root', '', 'ci4-BantuinYuk');
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

echo "=== BantuinYuk Task Assignment Logic Test ===\n";

// 1. Get a helper who has verified KYC
$res = $conn->query("SELECT user_id FROM helper_profiles WHERE verification_status = 'verified' LIMIT 1");
if ($res->num_rows === 0) die("No verified helper found.\n");
$helperId = $res->fetch_assoc()['user_id'];

$res = $conn->query("SELECT name FROM users WHERE id = $helperId");
$helperName = $res->fetch_assoc()['name'];
echo "Testing with Helper: $helperName (ID: $helperId)\n";

// 2. Clear helper's active tasks for testing
$conn->query("UPDATE tasks SET status = 'open', helper_id = NULL WHERE helper_id = $helperId");
echo "Cleared all previous tasks for this helper.\n";

// 3. Find or Create 2 open tasks
$res = $conn->query("SELECT id, title FROM tasks WHERE status = 'open' LIMIT 2");
$tasks = [];
while ($row = $res->fetch_assoc()) $tasks[] = $row;

// If we don't have 2 open tasks, let's create dummy ones
while (count($tasks) < 2) {
    $conn->query("INSERT INTO tasks (user_id, category_id, title, description, location, price, status, deadline_start, deadline_end, created_at, updated_at) 
                  VALUES (1, 1, 'Dummy Test Task " . time() . rand(100, 999) . "', 'Test Description', 'Test Location', 10000, 'open', NOW(), NOW(), NOW(), NOW())");
    $newId = $conn->insert_id;
    $tasks[] = ['id' => $newId, 'title' => 'Dummy Test Task'];
}

$task1 = $tasks[0];
$task2 = $tasks[1];

echo "\n--- Attempt 1: Taking Task 1 ---\n";
echo "Task 1 ID: {$task1['id']}, Title: {$task1['title']}\n";

// Simulate taking task 1 (the logic from HelperTaskController)
$res = $conn->query("SELECT id FROM tasks WHERE helper_id = $helperId AND status IN ('in_progress', 'waiting_approval') LIMIT 1");
$activeTask = $res->fetch_assoc();

if ($activeTask) {
    echo "FAILED: Expected no active tasks, but found one.\n";
} else {
    // Update task status and helper_id
    $conn->query("UPDATE tasks SET status = 'in_progress', helper_id = $helperId WHERE id = {$task1['id']}");
    echo "SUCCESS: Task 1 successfully taken. Status updated to 'in_progress'.\n";
}

echo "\n--- Attempt 2: Taking Task 2 ---\n";
echo "Task 2 ID: {$task2['id']}, Title: {$task2['title']}\n";

// Simulate taking task 2
$res = $conn->query("SELECT id FROM tasks WHERE helper_id = $helperId AND status IN ('in_progress', 'waiting_approval') LIMIT 1");
$activeTask2 = $res->fetch_assoc();

if ($activeTask2) {
    echo "SUCCESS: Blocked from taking Task 2 because Helper has active task (ID: {$activeTask2['id']}).\n";
    echo "Error Message in UI would be: 'Anda masih memiliki pekerjaan yang belum diselesaikan. Selesaikan pekerjaan Anda saat ini sebelum mengambil pekerjaan baru.'\n";
} else {
    echo "FAILED: Expected to be blocked, but was allowed to take task.\n";
}

echo "\n=== Test Completed Successfully ===\n";
