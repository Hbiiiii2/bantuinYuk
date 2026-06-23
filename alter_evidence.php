<?php
$conn = new mysqli('localhost', 'root', '', 'ci4-BantuinYuk');
$conn->query("ALTER TABLE disputes MODIFY COLUMN evidence_file TEXT NULL");
if ($conn->error) {
    echo "Error: " . $conn->error;
} else {
    echo "Column altered successfully";
}
