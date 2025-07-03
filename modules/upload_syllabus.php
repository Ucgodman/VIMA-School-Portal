<?php
include_once __DIR__ . "/../config/database.php";


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $class_id = $_POST['class_id'] ?? '';
    $subject_id = $_POST['subject_id'] ?? '';
    $uploader_id = $_SESSION['user_id'] ?? 1; // Default to 1 if session user_id is not set
    
    // File upload handling
    if (!empty($_FILES['file']['name'])) {
        $file_name = $_FILES['file']['name'];
        $file_tmp = $_FILES['file']['tmp_name'];
        $file_size = $_FILES['file']['size'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed_exts = ['pdf', 'doc', 'docx', 'ppt', 'pptx'];
        
        if (in_array($file_ext, $allowed_exts) && $file_size <= 5 * 1024 * 1024) { // 5MB limit
            $new_file_name = time() . '_' . $file_name;
            $upload_dir = __DIR__ . '../uploads/materials/';
            
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            if (move_uploaded_file($file_tmp, $upload_dir . $new_file_name)) {
                try {
                    $stmt = $pdo->prepare("INSERT INTO syllabus (title, description, class_id, subject_id, file_name, uploader_id, timestamp) VALUES (?, ?, ?, ?, ?, ?, NOW())");
                    $stmt->execute([$title, $description, $class_id, $subject_id, $new_file_name, $uploader_id]);
                    $_SESSION['message'] = "Syllabus uploaded successfully!";
                } catch (PDOException $e) {
                    $_SESSION['error'] = "Database error: " . $e->getMessage();
                }
            } else {
                $_SESSION['error'] = "Failed to upload file.";
            }
        } else {
            $_SESSION['error'] = "Invalid file format or file too large.";
        }
    } else {
        $_SESSION['error'] = "No file uploaded.";
    }
}

header("Location: syllabus.php");
exit();
?>
