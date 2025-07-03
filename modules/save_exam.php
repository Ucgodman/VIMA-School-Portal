<?php

// save_exam.php

// Include database connection
include_once __DIR__ . '/../config/database.php';

// Check if form is submitted via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate inputs
    $class_id = $_POST['class_id'] ?? null;
    $section_id = $_POST['section_id'] ?? null;
    $session_id = $_POST['session_id'] ?? null;
    $subject_id = $_POST['subject_id'] ?? null;
    $staff_id = $_POST['staff_id'] ?? null;
    $title = trim($_POST['title'] ?? '');
    $total_marks = $_POST['total_marks'] ?? null;
    $pass_marks = $_POST['pass_marks'] ?? null;
    $duration_minutes = $_POST['duration_minutes'] ?? null;
    $start_time = $_POST['start_time'] ?? null;
    $end_time = $_POST['end_time'] ?? null;

    // Basic validation (can be expanded)
    if (!$class_id || !$section_id || !$session_id || !$subject_id || !$staff_id || !$title || !$total_marks || !$pass_marks || !$duration_minutes || !$start_time || !$end_time) {
        die('Please fill in all required fields.');
    }

    try {
        // Prepare insert statement
        $stmt = $pdo->prepare("
            INSERT INTO exams (
                class_id, section_id, session_id, subject_id, staff_id, 
                title, total_marks, pass_marks, duration_minutes, start_time, end_time
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $class_id,
            $section_id,
            $session_id,
            $subject_id,
            $staff_id,
            $title,
            $total_marks,
            $pass_marks,
            $duration_minutes,
            $start_time,
            $end_time
        ]);

        // Redirect back to form with success
        header('Location: set_exams.php?success=1');
        exit;

    } catch (PDOException $e) {
        echo "Error saving exam: " . $e->getMessage();
        exit;
    }
} else {
    echo "Invalid request.";
    exit;
}
?>
