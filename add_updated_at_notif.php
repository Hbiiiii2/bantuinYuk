<?php
$conn = new mysqli('localhost', 'root', '', 'ci4-BantuinYuk');
$conn->query("ALTER TABLE notifications ADD COLUMN updated_at DATETIME NULL AFTER created_at");
if ($conn->error) {
    echo "Error: " . $conn->error;
} else {
    echo "updated_at added to notifications!";
}
