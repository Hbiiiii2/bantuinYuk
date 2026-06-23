<?php
$conn = new mysqli('localhost', 'root', '', 'ci4-BantuinYuk');
$conn->query("ALTER TABLE task_reviews ADD COLUMN updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");
echo "updated_at added.";
