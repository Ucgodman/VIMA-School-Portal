<?php

// permission.php
require_once realpath(__DIR__ . '/../config/database.php');



function hasPermission($menu_item) {
    if (!isset($_SESSION['user'])) {
        return false;
    }

    global $pdo;

    $role_id = $_SESSION['user']['role_id'] ?? null;
    $user_type = $_SESSION['user']['type'];

    $stmt = $pdo->prepare("SELECT * FROM user_permissions WHERE user_type = ? AND role_id = ? AND menu_item = ?");
    $stmt->execute([$user_type, $role_id, $menu_item]);
    return $stmt->rowCount() > 0;
}

function hasAnyPermission(array $menu_keys): bool {
    foreach ($menu_keys as $key) {
        if (hasPermission($key)) return true;
    }
    return false;
}

function renderMenuGroup(string $id, string $title, string $icon, array $items): string {
    $html = '<li class="nav-item">';
    $html .= "<a data-bs-toggle=\"collapse\" href=\"#$id\">";
    $html .= "<i class=\"$icon\"></i>";
    $html .= "<p>$title</p><span class=\"caret\"></span></a>";
    $html .= "<div class=\"collapse\" id=\"$id\"><ul class=\"nav nav-collapse\">";

    foreach ($items as [$perm, $url, $label]) {
        if (hasPermission($perm)) {
            $html .= "<li><a href=\"$url\"><span class=\"sub-item\">$label</span></a></li>";
        }
    }

    $html .= "</ul></div></li>";
    return $html;
}

