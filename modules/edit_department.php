<?php
session_start();
$pageTitle = "Edit Department";
include_once __DIR__ . "/../config/database.php";
include_once __DIR__ . "/../functions/helper_functions.php";
include_once __DIR__ . "/../includes/admin_header.php";

// Check if department ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['message'] = "Invalid department ID!";
    header("Location: departments.php");
    exit();
}

$department_id = $_GET['id'];

// Fetch department details
$stmt = $pdo->prepare("SELECT * FROM departments WHERE department_id = :department_id");
$stmt->execute([':department_id' => $department_id]);
$department = $stmt->fetch(PDO::FETCH_ASSOC);

// If department not found
if (!$department) {
    $_SESSION['message'] = "Department not found!";
    header("Location: department.php");
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);

    if (empty($name)) {
        $_SESSION['message'] = "Department name is required!";
        header("Location: edit_department.php?id=$department_id");
        exit();
    }

    // Update department
    $update_sql = "UPDATE departments SET name = :name, description = :description WHERE department_id = :department_id";
    $stmt = $pdo->prepare($update_sql);
    $stmt->execute([
        ':name' => $name,
        ':description' => $description,
        ':department_id' => $department_id
    ]);

    $_SESSION['message'] = "Department updated successfully!";
    header("Location: department.php");
    exit();
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
            <div class="container">
                <h2>Edit Department</h2>
                <?php if (isset($_SESSION['message'])): ?>
                    <div class="alert alert-info"><?= $_SESSION['message']; unset($_SESSION['message']); ?></div>
                <?php endif; ?>
                <form action="edit_department.php?id=<?= $department_id ?>" method="POST">
                    <div class="mb-3">
                        <label class="form-label">Department Name</label>
                        <input type="text" class="form-control" name="name" value="<?= htmlspecialchars($department['name']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description"><?= htmlspecialchars($department['description']) ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-success">Update Department</button>
                    <a href="department.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
    </div>
</div>
<!--   Core JS Files   -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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
