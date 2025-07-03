<?php
session_start();
require_once __DIR__ . "/../config/database.php";

// Allow admin and teacher only
if (
    !isset($_SESSION['user']) ||
    (
        $_SESSION['user']['type'] !== 'admin' &&
        !(
            $_SESSION['user']['type'] === 'staff' &&
            $_SESSION['user']['role_id'] == 3
        )
    )
) {
    die("Access denied.");
}

$id = $_GET['id'] ?? null;
if (!$id) die("Class ID missing.");

// If admin, allow delete any
if ($_SESSION['user']['type'] === 'admin') {
    $stmt = $pdo->prepare("DELETE FROM live_classes WHERE id = ?");
    $stmt->execute([$id]);
} else {
    $staff_id = $_SESSION['user']['staff_id'] ?? $_SESSION['user']['id'];
    $stmt = $pdo->prepare("DELETE FROM live_classes WHERE id = ? AND staff_id = ?");
    $stmt->execute([$id, $staff_id]);
}

$_SESSION['success_message'] = "Live class deleted successfully.";
header("Location: teacher_schedule_class.php");
exit;
