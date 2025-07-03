<?php
session_start();
$pageTitle = "Edit Schedule";

include_once __DIR__ . "/../config/database.php";
include_once __DIR__ . "/../functions/helper_functions.php";
include_once __DIR__ . "/../includes/admin_header.php";

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

$user = $_SESSION['user'];
$staff_id = $user['staff_id'] ?? $user['id']; // fallback for admin
$id = $_GET['id'] ?? null;
if (!$id) die("Class ID missing.");

// Admins can edit any class; teachers only their own
$sql = ($_SESSION['user']['type'] === 'admin')
    ? "SELECT * FROM live_classes WHERE id = ?"
    : "SELECT * FROM live_classes WHERE id = ? AND staff_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute($_SESSION['user']['type'] === 'admin' ? [$id] : [$id, $staff_id]);
$class = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$class) die("Class not found or access denied.");

$classes = $pdo->query("SELECT * FROM classes ORDER BY class_name")->fetchAll(PDO::FETCH_ASSOC);
$sections = $pdo->query("SELECT * FROM sections ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
$subjects = $pdo->query("SELECT * FROM subjects ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $start_time = $_POST['start_time'];
    $duration = $_POST['duration_minutes'];
    $class_id = $_POST['class_id'];
    $section_id = $_POST['section_id'];
    $subject_id = $_POST['subject_id'];

    $stmt = $pdo->prepare("UPDATE live_classes SET class_id=?, section_id=?, subject_id=?, title=?, description=?, start_time=?, duration_minutes=? WHERE id=?");
    $stmt->execute([$class_id, $section_id, $subject_id, $title, $description, $start_time, $duration, $id]);

    $_SESSION['success_message'] = "Live class updated successfully.";
    header("Location: teacher_schedule_class.php");
    exit;
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

            <div class="container mt-4">
                <div class="card shadow">
                    <div class="card-header bg-warning text-white">
                        <h4 class="mb-0">Edit Live Class</h4>
                    </div>
                    <div class="card-body">
                        <form method="post">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label>Class</label>
                                    <select name="class_id" class="form-control" required>
                                        <?php foreach ($classes as $c): ?>
                                            <option value="<?= $c['class_id'] ?>" <?= $c['class_id'] == $class['class_id'] ? 'selected' : '' ?>><?= $c['class_name'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label>Section</label>
                                    <select name="section_id" class="form-control" required>
                                        <?php foreach ($sections as $s): ?>
                                            <option value="<?= $s['section_id'] ?>" <?= $s['section_id'] == $class['section_id'] ? 'selected' : '' ?>><?= $s['name'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label>Subject</label>
                                    <select name="subject_id" class="form-control" required>
                                        <?php foreach ($subjects as $sub): ?>
                                            <option value="<?= $sub['subject_id'] ?>" <?= $sub['subject_id'] == $class['subject_id'] ? 'selected' : '' ?>><?= $sub['name'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label>Title</label>
                                <input name="title" class="form-control" value="<?= htmlspecialchars($class['title']) ?>" required>
                            </div>

                            <div class="mb-3">
                                <label>Description</label>
                                <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($class['description']) ?></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label>Start Time</label>
                                    <input type="datetime-local" name="start_time" value="<?= date('Y-m-d\TH:i', strtotime($class['start_time'])) ?>" class="form-control" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label>Duration (minutes)</label>
                                    <input type="number" name="duration_minutes" value="<?= $class['duration_minutes'] ?>" class="form-control" required>
                                </div>
                            </div>

                            <div class="text-end">
                                <a href="teacher_schedule_class.php" class="btn btn-secondary">Back</a>
                                <button type="submit" class="btn btn-success">Update Class</button>
                            </div>
                        </form>
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
