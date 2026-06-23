<?php
$conn = new mysqli('localhost', 'root', '', 'ci4-BantuinYuk');
$res = $conn->query("SELECT * FROM users LIMIT 5");
while ($row = $res->fetch_assoc()) {
    print_r($row);
}
