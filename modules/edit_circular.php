<?php

$pageTitle = "Edit Circular";
include_once __DIR__ . "/../config/database.php";
include_once __DIR__ . "/../functions/helper_functions.php";
include_once __DIR__ . "/../includes/admin_header.php";


// Check if circular ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error'] = "Invalid circular ID.";
    header("Location: circulars.php");
    exit();
}

$circular_id = $_GET['id'];

// Fetch circular details
$stmt = $pdo->prepare("SELECT * FROM circulars WHERE circular_id = :circular_id");
$stmt->bindParam(':circular_id', $circular_id);
$stmt->execute();
$circular = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$circular) {
    $_SESSION['error'] = "Circular not found.";
    header("Location: circulars.php");
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $reference = $_POST['reference'];
    $content = $_POST['content'];
    $date = $_POST['date'];

    $stmt = $pdo->prepare("UPDATE circulars SET title = :title, reference = :reference, content = :content, date = :date WHERE circular_id = :circular_id");
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':reference', $reference);
    $stmt->bindParam(':content', $content);
    $stmt->bindParam(':date', $date);
    $stmt->bindParam(':circular_id', $circular_id);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Circular updated successfully!";
        header("Location: circulars.php");
        exit();
    } else {
        $_SESSION['error'] = "Failed to update circular.";
    }
}

?>

<div class="wrapper">
    <?php include_once __DIR__ . "/../includes/admin_sidebar.php"; ?>
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
                <div class="card">
                    <div class="card-header bg-secondary text-white">
                        <h2>Edit Circular</h2>
                    </div>

                    <div class="card-body">
                
                        <?php if (isset($_SESSION['error'])): ?>
                            <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
                        <?php endif; ?>
                
                        <form method="POST">
                            <div class="form-group">
                                <label>Title</label>
                                <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($circular['title']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Reference</label>
                                <input type="text" name="reference" class="form-control" value="<?= htmlspecialchars($circular['reference']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Content</label>
                                <textarea name="content" class="form-control" required><?= htmlspecialchars($circular['content']); ?></textarea>
                            </div>
                            <div class="form-group">
                                <label>Date</label>
                                <input type="date" name="date" class="form-control" value="<?= htmlspecialchars($circular['date']); ?>" required>
                            </div>
                            <button type="submit" class="btn btn-success mt-2">Update Circular</button>
                            <a href="circulars.php" class="btn btn-secondary mt-2">Cancel</a>
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
