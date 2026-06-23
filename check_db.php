<?php
$conn = new mysqli('localhost', 'root', '', 'ci4-BantuinYuk');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "--- TASKS ---\n";
$res = $conn->query("SELECT * FROM tasks");
while ($row = $res->fetch_assoc()) {
    print_r($row);
}

echo "--- CATEGORIES ---\n";
$res = $conn->query("SELECT * FROM categories");
while ($row = $res->fetch_assoc()) {
    print_r($row);
}
