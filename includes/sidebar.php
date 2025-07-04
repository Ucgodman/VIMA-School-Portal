<?php

require_once __DIR__ . '/../config/database.php';

// Fetch logged in user info
$user = $_SESSION['user'] ?? [];
$userId = $user['id'] ?? 0;
$userType = $user['type'] ?? ''; // e.g., 'admin', 'staff', 'student', 'parent'
$roleId = $user['role_id'] ?? null;   // Assume role_id = 1 is admin

// Fetch all menu items
$stmt = $pdo->query("SELECT `key`, `name`, `icon_class`, `url` FROM menu_items ORDER BY id ASC");
$allMenuItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Admin sees all
$allowedKeys = [];

if ($roleId == 1 || $userType === 'admin') {
    // Admin sees everything
    $allowedKeys = array_column($allMenuItems, 'key');
} else {
    // Get user permissions from DB
    $permStmt = $pdo->prepare("SELECT menu_item FROM user_permissions WHERE role_id = ? AND user_type = ?");
    $permStmt->execute([$roleId, $userType]);
    $allowedKeys = $permStmt->fetchAll(PDO::FETCH_COLUMN);
}
?>

<div class="sidebar" data-background-color="dark">
    <div class="sidebar-logo">
        <div class="logo-header" data-background-color="dark">
            <a href="index.php" class="logo">
                <img src="assets/img/kaiadmin/logo_light.svg" alt="navbar brand" class="navbar-brand" height="20" />
            </a>
            <div class="nav-toggle">
                <button class="btn btn-toggle toggle-sidebar"><i class="gg-menu-right"></i></button>
                <button class="btn btn-toggle sidenav-toggler"><i class="gg-menu-left"></i></button>
            </div>
            <button class="topbar-toggler more"><i class="gg-more-vertical-alt"></i></button>
        </div>
    </div>

    <div class="sidebar-wrapper scrollbar scrollbar-inner">
        <div class="sidebar-content">
            <ul class="nav nav-secondary">
                <!-- Always show Dashboard -->
                               <?php
                $dashboardLink = match ($userType) {
                    'student' => 'dashboard.php',
                    'parent'  => 'parent_d.php',
                    default   => 'staff_dashboard.php', // for staff
                };
                ?>
                <li class="nav-item active">
                    <a href="<?= $dashboardLink ?>">
                        <i class="fas fa-home"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <li class="nav-section">
                    <span class="sidebar-mini-icon"><i class="fa fa-ellipsis-h"></i></span>
                    <h4 class="text-section">Menu</h4>
                </li>

                <!-- Display menu items based on permission -->
                <?php foreach ($allMenuItems as $item): ?>
                    <?php if (in_array($item['key'], $allowedKeys)): ?>
                        <li class="nav-item">
                            <a href="<?= htmlspecialchars($item['url']) ?>">
                                <i class="<?= htmlspecialchars($item['icon_class']) ?>"></i>
                                <p><?= htmlspecialchars($item['name']) ?></p>
                            </a>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>

                <!-- Logout -->
                <li class="nav-item">
                    <a href="../logout.php">
                        <i class="fas fa-sign-out-alt"></i>
                        <p>Logout</p>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
