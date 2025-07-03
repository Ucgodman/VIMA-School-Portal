<?php
session_start();
$pageTitle = "Edit Student House";

include_once __DIR__ . "/../config/database.php";
include_once __DIR__ . "/../functions/helper_functions.php";
include_once __DIR__ . "/../includes/admin_header.php";

if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error'] = "Invalid request!";
    header("Location: studenthouse.php");
    exit();
}

$house_id = $_GET['id'];

// Fetch existing student house details
$stmt = $pdo->prepare("SELECT * FROM student_house WHERE house_id = :house_id");
$stmt->bindParam(':house_id', $house_id, PDO::PARAM_INT);
$stmt->execute();
$house = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$house) {
    $_SESSION['error'] = "Student House not found!";
    header("Location: studenthouse.php");
    exit();
}

// Handle Form Submission for Update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $house_name = $_POST['house_name'];
    $description = $_POST['description'];

    $stmt = $pdo->prepare("UPDATE student_house SET house_name = :house_name, description = :description WHERE house_id = :house_id");
    $stmt->bindParam(':house_name', $house_name);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':house_id', $house_id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Student House updated successfully!";
        header("Location: studenthouse.php");
        exit();
    } else {
        $_SESSION['error'] = "Failed to update student house.";
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
                                <h5 class="mb-0">Edit Student House</h5>
                            </div>
                            <div class="card-body">
                                <?php if (isset($_SESSION['error'])) : ?>
                                    <div class="alert alert-danger"><?= $_SESSION['error'] ?></div>
                                    <?php unset($_SESSION['error']); ?>
                                <?php endif; ?>

                                <form method="POST">
                                    <div class="form-group">
                                        <label>House Name</label>
                                        <input type="text" name="house_name" class="form-control" value="<?= htmlspecialchars($house['house_name']) ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Description</label>
                                        <textarea name="description" class="form-control" required><?= htmlspecialchars($house['description']) ?></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary mt-2">Update House</button>
                                    <a href="studenthouse.php" class="btn btn-secondary mt-2">Cancel</a>
                                </form>
                            </div>
                        </div>
                    </div>
                </div> <!-- End Row -->
            </div> <!-- End Page Inner -->
        </div> <!-- End Container -->
    </div> <!-- End Main Panel -->
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
