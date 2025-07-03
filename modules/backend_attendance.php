<?php
include_once __DIR__ . "/../config/database.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    if ($action === 'get_sections') {
        $class_id = $_POST['class_id'];
        $stmt = $pdo->prepare("SELECT * FROM sections WHERE class_id = ?");
        $stmt->execute([$class_id]);
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        exit;
    }

    if ($action === 'get_students') {
        $class_id = $_POST['class_id'];
        $section_id = $_POST['section_id'];

        $stmt = $pdo->prepare("SELECT * FROM students WHERE class_id = ? AND section_id = ?");
        $stmt->execute([$class_id, $section_id]);
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        exit;
    }

    if ($action === 'save_attendance') {
        $attendance = json_decode($_POST['attendance'], true);
        $class_id = $_POST['class_id'] ?? null;
        $section_id = $_POST['section_id'] ?? null;
        $session_id = $_POST['session_id'] ?? null;
        $date = $_POST['date'] ?? null;

        if (!$class_id || !$section_id || !$session_id || !$date || !is_array($attendance)) {
            echo json_encode(['success' => false, 'message' => 'Invalid input.']);
            exit;
        }

        try {
            foreach ($attendance as $entry) {
                $student_id = $entry['student_id'];
                $status = $entry['status'];

                // Check if attendance already exists
                $checkStmt = $pdo->prepare("SELECT attendance_id FROM attendance WHERE student_id = ? AND session_id = ? AND date = ?");
                $checkStmt->execute([$student_id, $session_id, $date]);

                if ($checkStmt->rowCount() > 0) {
                    // Update existing record
                    $row = $checkStmt->fetch();
                    $update = $pdo->prepare("UPDATE attendance SET status = ?, updated_at = NOW() WHERE attendance_id = ?");
                    $update->execute([$status, $row['attendance_id']]);
                } else {
                    // Insert new record
                    $insert = $pdo->prepare("INSERT INTO attendance (student_id, session_id, class_id, section_id, status, date, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
                    $insert->execute([
                        $student_id,
                        $session_id,
                        $class_id,
                        $section_id,
                        $status,
                        $date
                    ]);
                }
            }

            echo json_encode(['success' => true, 'message' => 'Attendance saved successfully.']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
        }

        exit;
    }

    echo json_encode(['success' => false, 'message' => 'Invalid action.']);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid request.']);
exit;


?>
