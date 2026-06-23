<?php
$conn = new mysqli('localhost', 'root', '', 'ci4-BantuinYuk');
$conn->query("ALTER TABLE task_reviews MODIFY COLUMN reviewer_id bigint unsigned NOT NULL");
$conn->query("ALTER TABLE task_reviews MODIFY COLUMN reviewee_id bigint unsigned NOT NULL");

$conn->query("ALTER TABLE task_reviews ADD CONSTRAINT tr_reviewer_fk FOREIGN KEY (reviewer_id) REFERENCES users(id) ON DELETE CASCADE");
$conn->query("ALTER TABLE task_reviews ADD CONSTRAINT tr_reviewee_fk FOREIGN KEY (reviewee_id) REFERENCES users(id) ON DELETE CASCADE");

echo "Constraints added.\n";
