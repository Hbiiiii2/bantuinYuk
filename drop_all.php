<?php
$db = new mysqli('localhost', 'root', '', 'ci4-BantuinYuk');
$db->query('SET FOREIGN_KEY_CHECKS = 0');
$result = $db->query('SHOW TABLES');
while($row = $result->fetch_array()) {
    $db->query('DROP TABLE ' . $row[0]);
}
$db->query('SET FOREIGN_KEY_CHECKS = 1');
echo 'All tables dropped.';
