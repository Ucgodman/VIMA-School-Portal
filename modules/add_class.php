<?php
include_once __DIR__ . "/../config/database.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $class_name = $_POST['class_name'];
    $staff_id = !empty($_POST['staff_id']) ? $_POST['staff_id'] : NULL; // Allow NULL values

    $stmt = $pdo->prepare("INSERT INTO classes (class_name, staff_id) VALUES (:class_name, :staff_id)");
    $stmt->execute(['class_name' => $class_name, 'staff_id' => $staff_id]);

    echo "Class added successfully!";
}

?>
