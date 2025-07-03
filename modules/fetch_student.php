<?php
session_start();
include_once __DIR__ . "/../config/database.php";

header("Content-Type: application/json");

// Check if class_id is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode(["error" => "Class ID is missing."]);
    exit;
}

$class_id = $_GET['id'];

try {
    // Fetch students for the selected class
    $stmt = $pdo->prepare("
        SELECT s.student_id AS student_id, s.passport, s.admission_no, s.firstname AS first_name, 
               c.class_name AS class_name, s.email, 
               CONCAT(s.father_name, ' / ', s.mother_name) AS parent_name
        FROM students s
        INNER JOIN classes c ON s.class_id = c.class_id
        WHERE s.class_id = ?
    ");
    $stmt->execute([$class_id]);
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Debugging: Print query result
    if (!$students) {
        echo json_encode(["error" => "No students found in this class."]);
    } else {
        echo json_encode($students);
    }
} catch (Exception $e) {
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
}
?>
