<?php
// Include database connection
require '../config/database.php';

$class_id = $_GET['class_id'];
$stmt = $pdo->prepare("SELECT * FROM sections WHERE class_id = ?");
$stmt->execute([$class_id]);

echo "<option value=''>Select Section</option>";
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "<option value='{$row['id']}'>{$row['name']}</option>";
}

