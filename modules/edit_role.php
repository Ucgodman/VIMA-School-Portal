<?php
$pageTitle = "Edit Role";
session_start();
// Include database connection
include_once __DIR__ . "/../config/database.php";
include_once __DIR__ . "/../includes/admin_header.php";
include_once __DIR__ . "/../config/database.php";


if (isset($_GET['id'])) {
    $role_id = intval($_GET['id']);
    $stmt = $pdo->prepare("SELECT * FROM roles WHERE id = ?");
    $stmt->execute([$role_id]);
    $role = $stmt->fetch(PDO::FETCH_ASSOC);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);

    if (!empty($name)) {
        $stmt = $pdo->prepare("UPDATE roles SET name = ?, description = ?, date_updated = NOW() WHERE id = ?");
        $stmt->execute([$name, $description, $role_id]);

        $_SESSION['message'] = "Role updated successfully!";
        header("Location: roles.php");
        exit();
    }
}
?>

<div class="wrapper">
    <?php include_once __DIR__ . "/../includes/sidebar.php"; ?>
    <div class="main-panel">

        <div class="main-header">
                <div class="main-header-logo">
                    <!-- Logo Header -->
                    <div class="logo-header" data-background-color="dark">
                    <a href="index.html" class="logo">
                        <img
                        src="/../assets/images/logo_light.svg"
                        alt="navbar brand"
                        class="navbar-brand"
                        height="20"
                        />
                    </a>
                    <div class="nav-toggle">
                        <button class="btn btn-toggle toggle-sidebar">
                        <i class="gg-menu-right"></i>
                        </button>
                        <button class="btn btn-toggle sidenav-toggler">
                        <i class="gg-menu-left"></i>
                        </button>
                    </div>
                    <button class="topbar-toggler more">
                        <i class="gg-more-vertical-alt"></i>
                    </button>
                    </div>
                    <!-- End Logo Header -->
                </div>
                <!-- Navbar Header -->
            <?php include_once __DIR__ . "/../includes/navbar.php";?>
            <!-- End Navbar -->
        </div>

        <div class="container mt-5">
            <div class="card">
                <div class="card-header bg-primary text-white">Edit Role</div>
                <div class="card-body">
                    <form action="" method="POST">
                        <div class="mb-3">
                            <label class="form-label">Role Name</label>
                            <input type="text" class="form-control" name="name" value="<?= htmlspecialchars($role['name']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="3"><?= htmlspecialchars($role['description']) ?></textarea>
                        </div>
                        <button type="submit" class="btn btn-success">Update Role</button>
                        <a href="roles.php" class="btn btn-secondary">Back</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
  <!--   Core JS Files   -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="../assets/js/core/jquery-3.7.1.min.js"></script>
    <script src="../assets/js/core/popper.min.js"></script>
    <script src="../assets/js/core/bootstrap.min.js"></script>

    <!-- jQuery Scrollbar -->
    <script src="../assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>
    <!-- jQuery Sparkline -->
    <script src="../assets/js/plugin/jquery.sparkline/jquery.sparkline.min.js"></script>

    <!-- Bootstrap Notify -->
    <script src="../assets/js/plugin/bootstrap-notify/bootstrap-notify.min.js"></script>

    <!-- Sweet Alert -->
    <script src="../assets/js/plugin/sweetalert/sweetalert.min.js"></script>

    <!-- Kaiadmin JS -->
    <script src="../assets/js/kaiadmin.min.js"></script>
</body>
</html>
