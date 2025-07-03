<?php

session_start();
include_once __DIR__ . "/../config/database.php";



if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error_message'] = "Invalid request method.";
    header('Location: student_list.php');
    exit;
}

$student_id = $_POST['student_id'] ?? '';
if (empty($student_id)) {
    $_SESSION['error_message'] = "Student ID is missing.";
    header('Location: student_list.php');
    exit;
}

// Editable fields
$fields = [
    'gender', 'address', 'phone', 'email', 'club_id', 'house_id',
    'transport_id', 'dormitory_id',
    
];


$updates = [];
$params = [];

// Process editable fields
foreach ($fields as $field) {
    if (isset($_POST[$field])) {
        $updates[] = "$field = ?";
        $params[] = trim($_POST[$field]);
    }
}

// Handle password (if provided)
if (!empty(trim($_POST['password']))) {
    $hashed_password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);
    $updates[] = "password = ?";
    $params[] = $hashed_password;
}

// Handle passport upload (if any)
if (!empty($_FILES['passport']['name'])) {
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    $fileType = mime_content_type($_FILES['passport']['tmp_name']);

    if (!in_array($fileType, $allowedTypes)) {
        $_SESSION['error_message'] = "Only JPG, PNG, and GIF images are allowed.";
        header("Location: view_edit_student.php?student_id=$student_id");
        exit;
    }

    $uploadDir = '../uploads/passports/';
    $originalName = basename($_FILES['passport']['name']);
    $safeName = preg_replace('/[^A-Za-z0-9\.\-_]/', '_', $originalName);
    $filename = time() . '_' . $safeName;
    $targetPath = $uploadDir . $filename;

    if (move_uploaded_file($_FILES['passport']['tmp_name'], $targetPath)) {
        $updates[] = "passport = ?";
        $params[] = $filename;
    } else {
        $_SESSION['error_message'] = "Failed to upload passport photo.";
        header("Location: view_edit_student.php?student_id=$student_id");
        exit;
    }
}

if (empty($updates)) {
    $_SESSION['error_message'] = "No changes detected.";
    header("Location: view_edit_student.php?student_id=$student_id");
    exit;
}

// Add updated_at
$updates[] = "updated_at = NOW()";
$params[] = $student_id;

// Final query
$sql = "UPDATE students SET " . implode(", ", $updates) . " WHERE student_id = ?";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $_SESSION['success_message'] = "Student information updated successfully.";
    header("Location: dashboard.php?student_id=$student_id");
    exit;
} catch (PDOException $e) {
    $_SESSION['error_message'] = "Error updating student: " . $e->getMessage();
    header("Location: view_edit_student.php?student_id=$student_id");
    exit;
}
