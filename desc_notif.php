<?php
$conn = new mysqli('localhost', 'root', '', 'ci4-BantuinYuk');
$r = $conn->query("DESCRIBE notifications");
while($row = $r->fetch_assoc()) {
    print_r($row);
}
