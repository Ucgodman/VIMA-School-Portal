<?php
require_once __DIR__ . '/../config/database.php';

if (isset($_POST['class_id'])) {
    $class_id = $_POST['class_id'];

    // Fetch Sections
    $stmt = $pdo->prepare("SELECT section_id, name FROM sections WHERE class_id = ?");
    $stmt->execute([$class_id]);
    $sections = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch Subjects
    $stmt2 = $pdo->prepare("SELECT subject_id, name FROM subjects WHERE class_id = ?");
    $stmt2->execute([$class_id]);
    $subjects = $stmt2->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'sections' => $sections,
        'subjects' => $subjects
    ]);
}
