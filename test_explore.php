<?php
$conn = new mysqli('localhost', 'root', '', 'ci4-BantuinYuk');
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$sql = "SELECT tasks.*, categories.name as category_name, users.name as user_name 
        FROM tasks 
        LEFT JOIN categories ON categories.id = tasks.category_id 
        LEFT JOIN users ON users.id = tasks.user_id 
        WHERE tasks.status = 'open' 
        ORDER BY tasks.created_at DESC";

$res = $conn->query($sql);
while ($row = $res->fetch_assoc()) {
    print_r($row);
}
