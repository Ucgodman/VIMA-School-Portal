<?php
// Include database connection
require  '../config/database.php';

$class_id = $_GET['class_id'];
$stmt = $pdo->prepare("SELECT * FROM subjects WHERE class_id = ?");
$stmt->execute([$class_id]);

echo "<option value=''>Select Subject</option>";
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "<option value='{$row['subject_id']}'>{$row['name']}</option>";


}
