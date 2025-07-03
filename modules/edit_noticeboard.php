<?php
session_start();
$pageTitle = "Edit Noticeboard";
include_once __DIR__ . "/../config/database.php";
include_once __DIR__ . "/../functions/helper_functions.php";
include_once __DIR__ . "/../includes/admin_header.php";

// Check if notice ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['message'] = "Invalid notice ID!";
    header("Location: noticeboard.php");
    exit();
}

$noticeboard_id = $_GET['id'];

// Fetch notice details
$stmt = $pdo->prepare("SELECT * FROM noticeboard WHERE noticeboard_id = :noticeboard_id");
$stmt->execute([':noticeboard_id' => $noticeboard_id]);
$notice = $stmt->fetch(PDO::FETCH_ASSOC);

// If notice not found
if (!$noticeboard_id) {
    $_SESSION['message'] = "Notice not found!";
    header("Location: noticeboard.php");
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = trim($_POST['title']);
    $location = trim($_POST['location']);
    $content = trim($_POST['content']);
    $notice_date = $_POST['noticeboard_date'];

    if (empty($title) || empty($location) || empty($content) || empty($notice_date)) {
        $_SESSION['message'] = "All fields are required!";
        header("Location: edit_noticeboard.php?id=$noticeboard_id");
        exit();
    }

    // Update notice
    $update_sql = "UPDATE noticeboard SET title = :title, location = :location, content = :content, noticeboard_date = :notice_date WHERE noticeboard_id = :noticeboard_id";
    $stmt = $pdo->prepare($update_sql);
    $stmt->execute([
        ':title' => $title,
        ':location' => $location,
        ':content' => $content,
        ':notice_date' => $notice_date,
        ':noticeboard_id' => $noticeboard_id
    ]);

    $_SESSION['message'] = "Notice updated successfully!";
    header("Location: noticeboard.php");
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
                            src="/../assets/images/ogo_light.svg"
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
                <h2>Edit Notice</h2>
                <?php if (isset($_SESSION['message'])): ?>
                    <div class="alert alert-info"><?= $_SESSION['message']; unset($_SESSION['message']); ?></div>
                <?php endif; ?>
                <form action="edit_noticeboard.php?id=<?= $noticeboard_id ?>" method="POST">
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" class="form-control" name="title" value="<?= htmlspecialchars($notice['title']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Location</label>
                        <input type="text" class="form-control" name="location" value="<?= htmlspecialchars($notice['location']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Content</label>
                        <textarea class="form-control" name="content" rows="4" required><?= htmlspecialchars($notice['content']) ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Date</label>
                        <input type="date" class="form-control" name="noticeboard_date" value="<?= $notice['noticeboard_date'] ?>" required>
                    </div>
                    <button type="submit" class="btn btn-success">Update Notice</button>
                    <a href="noticeboard.php" class="btn btn-secondary">Cancel</a>
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
