<?php
$conn = new mysqli('localhost', 'root', '', 'ci4-BantuinYuk');
$conn->query("ALTER TABLE notifications ADD COLUMN data TEXT NULL AFTER message");
$conn->query("ALTER TABLE notifications MODIFY COLUMN type VARCHAR(50) NOT NULL");
if ($conn->error) {
    echo "Error: " . $conn->error;
} else {
    echo "notifications table altered!";
}
