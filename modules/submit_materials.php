<?php

include_once __DIR__ . '/../config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $class_id = $_POST['class_id'];
    $subject_id = $_POST['subject_id'];
    $staff_id = $_POST['staff_id'];
    $due_date = $_POST['due_date'];
    $description = trim($_POST['description']);

    // Validate inputs
    if (empty($title) || empty($class_id) || empty($subject_id) || empty($staff_id) || empty($due_date)) {
        echo "All fields are required!";
        exit;
    }

    // File Upload Handling
    $targetDir = "../uploads/materials/";
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true); // Ensure directory exists
    }

    $filePath = "";
    if (!empty($_FILES["file"]["name"])) {
        $fileName = basename($_FILES["file"]["name"]);
        $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);
        $allowedTypes = ["pdf", "doc", "docx", "ppt", "pptx", "txt"];

        if (!in_array(strtolower($fileExt), $allowedTypes)) {
            echo "Invalid file format. Allowed formats: PDF, DOC, DOCX, PPT, PPTX, TXT.";
            exit;
        }

        $uniqueName = time() . "_" . preg_replace("/[^a-zA-Z0-9.]/", "_", $fileName);
        $filePath = $targetDir . $uniqueName;

        if (!move_uploaded_file($_FILES["file"]["tmp_name"], $filePath)) {
            echo "Failed to upload file.";
            exit;
        }
    }

    // Insert assignment into the database
    $stmt = $pdo->prepare("INSERT INTO assignments (title, class_id, subject_id, staff_id, due_date, description, file_path, created_at) 
                           VALUES (:title, :class_id, :subject_id, :staff_id, :due_date, :description, :file_path, NOW())");

    $stmt->execute([
        ':title'       => $title,
        ':class_id'    => $class_id,
        ':subject_id'  => $subject_id,
        ':staff_id'    => $staff_id,
        ':due_date'    => $due_date,
        ':description' => $description,
        ':file_path'   => $filePath
    ]);

    if ($stmt) {
        echo "success";
    } else {
        echo "Failed to add material!";
    }
}
?>
