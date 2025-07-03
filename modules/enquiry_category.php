<?php
session_start();
$pageTitle = "Enquiry Categories";

include_once __DIR__ . "/../config/database.php";
include_once __DIR__ . "/../functions/helper_functions.php";
include_once __DIR__ . "/../includes/admin_header.php";

// Fetch categories from the database
$stmt = $pdo->query("SELECT * FROM enquiry_category ORDER BY enquiry_category_id DESC");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
            <div class="page-inner">

                <div class="row">

                    <div class="card">
                        <div class="card-header bg-secondary text-white">
                            <h4 class="mb-4">Enquiry Categories</h4>
                            <?php if (isset($_SESSION['success'])) : ?>
                                <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
                            <?php elseif (isset($_SESSION['error'])) : ?>
                                <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
                            <?php endif; ?>

                            <a href="add_category.php" class="btn btn-white mb-3">Add Category</a>
                        </div>
                        
                        <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Category</th>
                                                <th>Purpose</th>
                                                <th>Whom</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($categories as $index => $row) : ?>
                                                <tr>
                                                    <td><?= $index + 1 ?></td>
                                                    <td><?= htmlspecialchars($row['category']) ?></td>
                                                    <td><?= htmlspecialchars($row['purpose']) ?></td>
                                                    <td><?= htmlspecialchars($row['whom']) ?></td>
                                                    <td>
                                                       <a href="edit_category.php?id=<?= htmlspecialchars($row['enquiry_category_id']); ?>" class="btn btn-warning btn-xs rounded-circle d-flex justify-content-center align-items-center bi bi-pencil" style="width: 26px; height: 26px; color: white; " title="Edit">
                                                            </a>
                                                        <a href="delete_category.php?id=<?= htmlspecialchars($row['enquiry_category_id']); ?>" class="btn btn-danger btn-xs rounded-circle d-flex justify-content-center align-items-center bi bi-trash" style="width: 26px; height: 26px; color: white;" title="Delete"
                                                            onclick="return confirm('Are you sure you want to delete this Category?');">
                                                            </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div> <!-- End .table-responsive -->
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

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
