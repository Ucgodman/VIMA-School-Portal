<?php
include 'config/database.php';


// Ensure only admin can access
if ($_SESSION['loggedInUser']['role'] !== 'admin') {
    header("Location: modules/admin/index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_POST['user_id'];
    $menu_items = $_POST['menu_items']; // Array of menu items

    // Remove existing permissions for user
    $stmt = $pdo->prepare("DELETE FROM user_permissions WHERE user_id = ?");
    $stmt->execute([$user_id]);

    // Insert new permissions
    $stmt = $pdo->prepare("INSERT INTO user_permissions (user_id, menu_item) VALUES (?, ?)");
    foreach ($menu_items as $menu) {
        $stmt->execute([$user_id, $menu]);
    }

    echo "Permissions updated successfully!";
}

// Fetch all users
$users = $pdo->query("SELECT id, email FROM users")->fetchAll();
$menu_items = ['dashboard', 'students', 'teachers', 'classes', 'subjects', 'exams', 'attendance', 'fees', 'grades', 'materials', 'hostel', 'transportation', 'settings', 'roles'];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Assign Permissions</title>
</head>
<body>
    <h2>Assign Menu Permissions</h2>
    <form method="POST">
        <label>Select User:</label>
        <select name="user_id">
            <?php foreach ($users as $user): ?>
                <option value="<?= $user['id'] ?>"><?= $user['email'] ?></option>
            <?php endforeach; ?>
        </select>

        <label>Select Menu Items:</label>
        <?php foreach ($menu_items as $item): ?>
            <input type="checkbox" name="menu_items[]" value="<?= $item ?>"> <?= ucfirst($item) ?><br>
        <?php endforeach; ?>

        <button type="submit">Update Permissions</button>
    </form>
</body>
</html>
