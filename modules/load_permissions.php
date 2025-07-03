<?php
include_once __DIR__ . '/../config/database.php';

$id = $_POST['id'] ?? '';

if (!$id) {
    echo json_encode([]);
    exit;
}

if ($id === 'student' || $id === 'parent') {
    // Fetch permissions globally for students or parents
    $stmt = $pdo->prepare("SELECT menu_item FROM user_permissions WHERE user_type = ?");
    $stmt->execute([$id]);
} else {
    // Fetch permissions for staff role
    $stmt = $pdo->prepare("SELECT menu_item FROM user_permissions WHERE role_id = ? AND user_type = 'staff'");
    $stmt->execute([$id]);
}

$permissions = $stmt->fetchAll(PDO::FETCH_COLUMN);
echo json_encode($permissions);
