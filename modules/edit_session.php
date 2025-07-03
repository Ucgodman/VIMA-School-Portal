<?php
session_start();
$pageTitle = "Edit Session";

// Include necessary files
include_once __DIR__ . "/../config/database.php";
include_once __DIR__ . "/../includes/admin_header.php";

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>alert('Invalid session ID.'); window.location.href='sessions.php';</script>";
    exit;
}

$sessionId = $_GET['id'];

// Fetch session details
$stmt = $pdo->prepare("SELECT * FROM sessions WHERE session_id = ?");
$stmt->execute([$sessionId]);
$session = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$session) {
    echo "<script>alert('Session not found.'); window.location.href='sessions.php';</script>";
    exit;
}

// Handle session update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_session'])) {
    $sessionYear = trim($_POST['session_year']);

    $updateQuery = "UPDATE sessions SET session_year = ? WHERE id = ?";
    $stmt = $pdo->prepare($updateQuery);
    $stmt->execute([$sessionYear, $sessionId]);

    echo "<script>alert('Session Updated Successfully!'); window.location.href='sessions.php';</script>";
}

// Handle setting active session
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['set_active'])) {
    // First, deactivate all sessions
    $pdo->query("UPDATE sessions SET is_active = 0");

    // Then, activate the selected session
    $stmt = $pdo->prepare("UPDATE sessions SET is_active = 1 WHERE session_id = ?");
    $stmt->execute([$sessionId]);

    echo "<script>alert('Active session updated successfully!'); window.location.href='sessions.php';</script>";
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
                        <div class="card-header bg-primary text-white">
                            <h4>Edit Session</h4>
                        </div>
                        <div class="card-body">
                            <?php if (isset($_SESSION['error'])): ?>
                                <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
                            <?php endif; ?>
                            
                            <!-- Update Session Form -->
                            <form method="POST">
                                <div class="mb-3">
                                    <label class="form-label">Session Year</label>
                                    <input type="text" class="form-control" name="session_year" value="<?= htmlspecialchars($session['session_year']) ?>" required>
                                </div>
                                <button type="submit" name="update_session" class="btn btn-success">Update Session</button>
                                <a href="sessions.php" class="btn btn-secondary">Cancel</a>
                            </form>

                            <hr>

                            <!-- Set Active Session Form -->
                            <form method="POST">
                                <button type="submit" name="set_active" class="btn btn-primary w-100" <?= $session['is_active'] ? 'disabled' : ''; ?>>
                                    <?= $session['is_active'] ? 'Active Session' : 'Set as Active Session'; ?>
                                </button>
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
