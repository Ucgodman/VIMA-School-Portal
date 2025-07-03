<?php
include_once __DIR__ . "/../config/database.php";

$class_id = $_POST['class_id'];
$section_id = $_POST['section_id'] ?? null;
$response = ['sections' => [], 'subjects' => []];

// Fetch sections
$section_stmt = $pdo->prepare("SELECT id, name FROM sections WHERE class_id = ?");
$section_stmt->execute([$class_id]);
$response['sections'] = $section_stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch subjects
if ($section_id) {
    $subject_stmt = $pdo->prepare("SELECT subject_id, name FROM subjects WHERE class_id = ? AND section_id = ?");
    $subject_stmt->execute([$class_id, $section_id]);
} else {
    $subject_stmt = $pdo->prepare("SELECT subject_id, name FROM subjects WHERE class_id = ?");
    $subject_stmt->execute([$class_id]);
}
$response['subjects'] = $subject_stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($response);
?>
