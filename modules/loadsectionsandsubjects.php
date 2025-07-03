<?php
include_once __DIR__ . "/../config/database.php";

$class_id = isset($_GET['class_id']) ? (int)$_GET['class_id'] : 0;

$sections = [];
$subjects = [];

if ($class_id > 0) {
    // Fetch sections
    $stmt = $pdo->prepare("SELECT section_id AS section_id, name FROM sections WHERE class_id = ?");
    $stmt->execute([$class_id]);
    $sections = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch subjects
    $stmt = $pdo->prepare("SELECT subject_id AS subject_id, name FROM subjects WHERE class_id = ?");
    $stmt->execute([$class_id]);
    $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

echo json_encode([
    'sections' => $sections,
    'subjects' => $subjects
]);
