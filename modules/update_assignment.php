<?php

include_once __DIR__ . '/../config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $title = trim($_POST['title']);
    $class_id = $_POST['class_id'];
    $section_id = $_POST['section_id'];
    $subject_id = $_POST['subject_id'];
    $staff_id = $_POST['staff_id'];
    $due_date = $_POST['due_date'];
    $description = trim($_POST['description']);

    // Validate inputs
    if (empty($title) || empty($class_id) || empty($section_id) || empty($subject_id) || empty($staff_id) || empty($due_date)) {
        $_SESSION['error'] = "All fields are required!";
        header("Location: edit_assignment.php?id=$id");
        exit;
    }

    // Fetch current assignment details
    $stmt = $pdo->prepare("SELECT file_path FROM assignments WHERE id = ?");
    $stmt->execute([$id]);
    $assignment = $stmt->fetch();

    // File Upload Handling
    $filePath = $assignment['file_path'];
    if (!empty($_FILES["file"]["name"])) {
        $targetDir = "../uploads/assignments/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true); // Ensure directory exists
        }

        $fileName = basename($_FILES["file"]["name"]);
        $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);
        $allowedTypes = ["pdf", "doc", "docx", "ppt", "pptx", "txt"];

        if (!in_array(strtolower($fileExt), $allowedTypes)) {
            $_SESSION['error'] = "Invalid file format. Allowed formats: PDF, DOC, DOCX, PPT, PPTX, TXT.";
            header("Location: edit_assignment.php?id=$id");
            exit;
        }

        $uniqueName = time() . "_" . preg_replace("/[^a-zA-Z0-9.]/", "_", $fileName);
        $filePath = $targetDir . $uniqueName;

        if (!move_uploaded_file($_FILES["file"]["tmp_name"], $filePath)) {
            $_SESSION['error'] = "Failed to upload file.";
            header("Location: edit_assignment.php?id=$id");
            exit;
        }
    }

    // Update assignment in the database
    try {
        $stmt = $pdo->prepare("UPDATE assignments SET title = :title, class_id = :class_id, section_id = :section_id, subject_id = :subject_id, staff_id = :staff_id, due_date = :due_date, description = :description, file_path = :file_path, updated_at = NOW() WHERE id = :id");

        $stmt->execute([
            ':title'       => $title,
            ':class_id'    => $class_id,
            ':section_id'  => $section_id,
            ':subject_id'  => $subject_id,
            ':staff_id'    => $staff_id,
            ':due_date'    => $due_date,
            ':description' => $description,
            ':file_path'   => $filePath,
            ':id'          => $id
        ]);

        if ($stmt->rowCount() > 0) {
            $_SESSION['success'] = "Assignment updated successfully!";
            header("Location: assignments.php");
        } else {
            $_SESSION['error'] = "Failed to update assignment!";
            header("Location: edit_assignment.php?id=$id");
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
        header("Location: edit_assignment.php?id=$id");
    }
}
?>