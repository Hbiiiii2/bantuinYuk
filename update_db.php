<?php
$conn = new mysqli('localhost', 'root', '', 'ci4-BantuinYuk');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Alter task_reviews table
$res = $conn->query("SHOW COLUMNS FROM task_reviews LIKE 'reviewer_id'");
if ($res->num_rows == 0) {
    $conn->query("TRUNCATE TABLE task_reviews");

    $conn->query("ALTER TABLE task_reviews DROP FOREIGN KEY task_reviews_user_id_foreign");
    $conn->query("ALTER TABLE task_reviews DROP FOREIGN KEY task_reviews_helper_id_foreign");

    $conn->query("ALTER TABLE task_reviews DROP COLUMN user_id, DROP COLUMN helper_id");

    $conn->query("ALTER TABLE task_reviews ADD COLUMN reviewer_id bigint unsigned NOT NULL AFTER task_id");
    $conn->query("ALTER TABLE task_reviews ADD COLUMN reviewee_id bigint unsigned NOT NULL AFTER reviewer_id");

    $conn->query("ALTER TABLE task_reviews ADD CONSTRAINT tr_reviewer_fk FOREIGN KEY (reviewer_id) REFERENCES users(id) ON DELETE CASCADE");
    $conn->query("ALTER TABLE task_reviews ADD CONSTRAINT tr_reviewee_fk FOREIGN KEY (reviewee_id) REFERENCES users(id) ON DELETE CASCADE");
    
    echo "task_reviews altered successfully.\n";
} else {
    echo "Columns already exist.\n";
}

$conn->close();
