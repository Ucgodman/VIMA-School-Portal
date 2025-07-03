<?php
session_start();
$pageTitle = "Edit Class";

// Include necessary files
include_once __DIR__ . "/../config/database.php";
include_once __DIR__ . "/../includes/admin_header.php";

// Check if class ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error'] = "No class ID provided!";
    header("Location: classes.php");
    exit;
}

$class_id = intval($_GET['id']); // Sanitize class ID

// Fetch class details
$stmtClass = $pdo->prepare("SELECT class_name, staff_id FROM classes WHERE class_id = ?");
$stmtClass->execute([$class_id]);
$class = $stmtClass->fetch(PDO::FETCH_ASSOC);

if (!$class) {
    $_SESSION['error'] = "Class not found!";
    header("Location: classes.php");
    exit;
}

// Fetch teachers (staff with role_id = 2)
$stmtTeachers = $pdo->query("SELECT id, firstname, lastname FROM staff WHERE role_id = 2 ORDER BY firstname ASC");
$teachers = $stmtTeachers->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $class_name = trim($_POST['class_name']);
    $staff_id = !empty($_POST['staff_id']) ? intval($_POST['staff_id']) : NULL;

    if (empty($class_name)) {
        $_SESSION['error'] = "Class name is required!";
    } else {
        // Update class details
        $stmtUpdate = $pdo->prepare("UPDATE classes SET class_name = ?, staff_id = ? WHERE class_id = ?");
        if ($stmtUpdate->execute([$class_name, $staff_id, $class_id])) {
            $_SESSION['success'] = "Class updated successfully!";
            header("Location: classes.php");
            exit;
        } else {
            $_SESSION['error'] = "Failed to update class.";
        }
    }
}

?>

<div class="wrapper">
    <?php include_once __DIR__ . "/../includes/sidebar.php"; ?>
    <div class="main-panel">
        <div class="main-header">
            <div class="main-header-logo">
                <div class="logo-header" data-background-color="dark">
                    <a href="index.html" class="logo">
                        <img src="/../assets/images/ogo_light.svg" alt="navbar brand" class="navbar-brand" height="20" />
                    </a>
                    <div class="nav-toggle">
                        <button class="btn btn-toggle toggle-sidebar"><i class="gg-menu-right"></i></button>
                        <button class="btn btn-toggle sidenav-toggler"><i class="gg-menu-left"></i></button>
                    </div>
                    <button class="topbar-toggler more"><i class="gg-more-vertical-alt"></i></button>
                </div>
            </div>
            <?php include_once __DIR__ . "/../includes/navbar.php"; ?>
        </div>

        <div class="container">
            <div class="row">
                <div class="col-md-6 mx-auto">
                    <div class="card shadow">
                        <div class="card-header bg-secondary text-white">
                            <h4>Edit Class</h4>
                        </div>
                        <div class="card-body">
                            <?php if (isset($_SESSION['error'])): ?>
                                <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
                            <?php endif; ?>
                            <form method="POST">
                                <div class="form-group">
                                    <label for="className">Class Name</label>
                                    <input type="text" id="className" name="class_name" class="form-control" required value="<?= htmlspecialchars($class['class_name']); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="teacherSelect">Assign Teacher</label>
                                    <select id="teacherSelect" name="staff_id" class="form-control">
                                        <option value="">-- No Teacher Assigned --</option>
                                        <?php foreach ($teachers as $teacher): ?>
                                            <option value="<?= $teacher['id']; ?>" <?= ($class['staff_id'] == $teacher['id']) ? 'selected' : ''; ?>>
                                                <?= htmlspecialchars($teacher['firstname'] . " " . $teacher['lastname']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-success">Update Class</button>
                                <a href="classes.php" class="btn btn-secondary">Cancel</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
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