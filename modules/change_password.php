<?php
session_start();
$pageTitle = "Password Reset";
include_once __DIR__ . "/../config/database.php";
include_once __DIR__ . "/../functions/helper_functions.php";
include_once __DIR__ . "/../includes/admin_header.php";

if (!isset($_GET['id'])) {
    die("Student ID is required.");
}

$student_id = $_GET['id'];

// Fetch student details
$stmt = $pdo->prepare("SELECT * FROM students WHERE student_id = ?");
$stmt->execute([$student_id]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$student) {
    die("Student not found.");
}

// Handle password reset
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("UPDATE students SET password = ? WHERE student_id = ?");
    $stmt->execute([$new_password, $student_id]);

    echo "<script>alert('Password updated successfully!'); window.location.href='student_information.php';</script>";
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

            <div class="container mt-8">
            <div class="page-inner">
                <div class="row">
                    <div class="col-md-6 offset-md-3">
                        <div class="card">
                            <div class="card-header bg-secondary text-white">
                                <h5 class="mb-0">Student Password Resent</h5>
                            </div>
                            <div class="card-body">
                                <?php if (isset($_SESSION['error'])) : ?>
                                    <div class="alert alert-danger"><?= $_SESSION['error'] ?></div>
                                    <?php unset($_SESSION['error']); ?>
                                <?php endif; ?>

                                <form method="POST">
                                    <div class="form-group">
                                        <label>Change Password</label>
                                        <input type="password" name="password" class="form-control" required>
                                    </div>
                           
                                    <button type="submit" class="btn btn-primary mt-2">Reset Password</button>
                                    <a href="student_information.php" class="btn btn-secondary mt-2">Cancel</a>
                                    
                                </form>
                            </div>
                        </div>
                    </div>
                </div> <!-- End Row -->
            </div> <!-- End Page Inner -->
        </div> <!-- End Container -->

           

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

