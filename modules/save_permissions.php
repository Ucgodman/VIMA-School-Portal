<?php
session_start();
require_once "../config/database.php";

$userType = $_POST['user_type'];
$permissions = $_POST['permissions'] ?? [];

try {
    $pdo->beginTransaction();

    // Specific user types for staff roles
    if (in_array($userType, ['teacher', 'clerk', 'accountant', 'principal', 'vice_principal', 'it_support', 'transport_manager'])) {
        $roleId = $_POST['role_id'] ?? null;

        if (!$roleId || !is_numeric($roleId)) {
            throw new Exception("Invalid or missing role ID.");
        }

        $del = $pdo->prepare("DELETE FROM user_permissions WHERE role_id = ? AND user_type = ?");
        $del->execute([$roleId, $userType]);

        if (!empty($permissions)) {
            $now = date("Y-m-d H:i:s");
            $ins = $pdo->prepare("INSERT INTO user_permissions (role_id, user_type, menu_item, created_at, updated_at) VALUES (?, ?, ?, ?, ?)");
            foreach ($permissions as $perm) {
                if (!empty($perm)) {
                    $ins->execute([$roleId, $userType, $perm, $now, $now]);
                }
            }
        }

    } elseif (in_array($userType, ['student', 'parent'])) {
        $roleId = $_POST['role_id'] ?? null;

        if (!$roleId || !is_numeric($roleId)) {
            throw new Exception("Invalid or missing role ID.");
        }

        $del = $pdo->prepare("DELETE FROM user_permissions WHERE role_id = ? AND user_type = ?");
        $del->execute([$roleId, $userType]);

        if (!empty($permissions)) {
            $now = date("Y-m-d H:i:s");
            $ins = $pdo->prepare("INSERT INTO user_permissions (role_id, user_type, menu_item, created_at, updated_at) VALUES (?, ?, ?, ?, ?)");
            foreach ($permissions as $perm) {
                if (!empty($perm)) {
                    $ins->execute([$roleId, $userType, $perm, $now, $now]);
                }
            }
        }

    } else {
        throw new Exception("Invalid user type: $userType");
    }

    $pdo->commit();
    $_SESSION['success'] = "Permissions saved successfully!";
} catch (Exception $e) {
    $pdo->rollBack();
    $_SESSION['error'] = "Error: " . $e->getMessage();
}

header("Location: assign_permissions.php");
exit;
