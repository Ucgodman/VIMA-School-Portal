<?php
session_start();
$pageTitle = "Edit Club";

include_once __DIR__ . "/../config/database.php";
include_once __DIR__ . "/../functions/helper_functions.php";
include_once __DIR__ . "/../includes/admin_header.php";

if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error'] = "Invalid club ID.";
    header("Location: school_clubs.php");
    exit();
}

$club_id = $_GET['id'];

// Fetch existing club data
$stmt = $pdo->prepare("SELECT * FROM clubs WHERE club_id = :club_id");
$stmt->bindParam(':club_id', $club_id, PDO::PARAM_INT);
$stmt->execute();
$club = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$club) {
    $_SESSION['error'] = "Club not found.";
    header("Location: clubs.php");
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $club_name = $_POST['club_name'];
    $desc = $_POST['desc'];

    $stmt = $pdo->prepare("UPDATE clubs SET club_name = :club_name, `desc` = :desc WHERE club_id = :club_id");
    $stmt->bindParam(':club_name', $club_name);
    $stmt->bindParam(':desc', $desc);
    $stmt->bindParam(':club_id', $club_id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Club updated successfully!";
        header("Location: school_clubs.php");
        exit();
    } else {
        $_SESSION['error'] = "Failed to update club.";
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

            <div class="container">
                <div class="card">
                    <div class="card-header bg-secondary text-white">
                        <h2>Edit Club</h2>
                    </div>
                    <div class="card-body">
                        <?php if (isset($_SESSION['error'])) : ?>
                            <div class="alert alert-danger"><?= $_SESSION['error'] ?></div>
                            <?php unset($_SESSION['error']); ?>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="form-group">
                                <label>Club Name</label>
                                <input type="text" name="club_name" class="form-control" value="<?= htmlspecialchars($club['club_name']) ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Description</label>
                                <textarea name="desc" class="form-control" required><?= htmlspecialchars($club['desc']) ?></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary mt-2">Update Club</button>
                            <a href="clubs.php" class="btn btn-secondary mt-2">Cancel</a>
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
