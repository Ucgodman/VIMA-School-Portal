<?php

$pageTitle = "Add Category";

include_once __DIR__ . "/../config/database.php";
include_once __DIR__ . "/../functions/helper_functions.php";
include_once __DIR__ . "/../includes/admin_header.php";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $category = trim($_POST['category']);
    $purpose = trim($_POST['purpose']);
    $whom = trim($_POST['whom']);

    if (!empty($category) && !empty($purpose) && !empty($whom)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO enquiry_category (category, purpose, whom) VALUES (:category, :purpose, :whom)");
            $stmt->execute([
                ':category' => $category,
                ':purpose' => $purpose,
                ':whom' => $whom
            ]);

            $_SESSION['success'] = "Category added successfully!";
            header("Location: enquiry_category.php");
            exit;
        } catch (PDOException $e) {
            $_SESSION['error'] = "Database error: " . $e->getMessage();
        }
    } else {
        $_SESSION['error'] = "All fields are required!";
    }
}
?>

<div class="wrapper">
    <!-- Sidebar -->
    <?php include_once __DIR__ . "/../includes/admin_sidebar.php"; ?>
    <!-- End Sidebar -->

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

        <div class="container mt-7">
            <div class="page-inner">
                <div class="row">
                    <div class="col-md-8 offset-md-2">
                        <?php if (isset($_SESSION['success'])) : ?>
                            <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
                        <?php elseif (isset($_SESSION['error'])) : ?>
                            <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
                        <?php endif; ?>

                        <div class="card">
                            <div class="card-header bg-secondary text-white">
                                <h4>Add New Enquiry Category</h4>
                            </div>
                            <div class="card-body">
                                <form action="add_category.php" method="POST">
                                    <div class="mb-3">
                                        <label for="category" class="form-label">Category</label>
                                        <input type="text" class="form-control" name="category" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="purpose" class="form-label">Purpose</label>
                                        <input type="text" class="form-control" name="purpose" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="whom" class="form-label">Whom</label>
                                        <input type="text" class="form-control" name="whom" required>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <a href="enquiry_category.php" class="btn btn-secondary">Back</a>
                                        <button type="submit" class="btn btn-primary">Save Category</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- End main-panel -->
</div> <!-- End wrapper -->

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
