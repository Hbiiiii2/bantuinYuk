<?php
$conn = new mysqli('localhost', 'root', '', 'ci4-BantuinYuk');

// Helper to User
$r1 = $conn->query("SELECT reviewee_id, AVG(rating) as avg_rating FROM task_reviews GROUP BY reviewee_id");
while($row = $r1->fetch_assoc()) {
    $conn->query("UPDATE users SET rating = " . round($row['avg_rating'], 2) . " WHERE id = " . $row['reviewee_id']);
}

echo "Ratings updated!";
