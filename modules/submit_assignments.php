<?php
include_once __DIR__ . '/../config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $class_id = $_POST['class_id'];
    $section_id = $_POST['section_id'];
    $subject_id = $_POST['subject_id'];
    $staff_id = $_POST['staff_id'];
    $due_date = $_POST['due_date'];
    $description = $_POST['description'];

    

$stmt = $pdo->prepare("SELECT id FROM staff WHERE staff_id = ? AND role_id = 2");
$stmt->execute([$staff_id]);
$staff = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$staff) {
    echo "Error: Invalid staff ID! Make sure the staff is a teacher.";
    exit;
}

// Use the correct staff 'id' instead of 'staff_id'
$correct_staff_id = $staff['id'];


    // Handle file upload
    $destination = null;
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['file']['tmp_name'];
        $fileName = basename($_FILES['file']['name']);
        $destination = '../uploads/' . $fileName;
        move_uploaded_file($fileTmpPath, $destination);
    }

    // Insert assignment
    $stmt = $pdo->prepare("INSERT INTO assignments (title, class_id, section_id, subject_id, staff_id, due_date, description, file_path, created_at) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
    if ($stmt->execute([$title, $class_id, $section_id, $subject_id, $correct_staff_id, $due_date, $description, $destination])) {
        echo "success";
    } else {
        echo "Error: Could not save assignment.";
    }
}
?>
