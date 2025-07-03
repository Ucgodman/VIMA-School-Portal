<?php
include_once __DIR__ . '/../config/database.php';

$userId = $_POST['user_id'] ?? '';

if (!$userId) {
    header('Content-Type: application/json');
    echo json_encode(null);
    exit;
}

$stmt = $pdo->prepare("SELECT role_id FROM staff WHERE id = ?");
$stmt->execute([$userId]);
$roleId = $stmt->fetchColumn();

header('Content-Type: application/json');
echo json_encode($roleId ?: null);
