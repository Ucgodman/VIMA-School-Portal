<?php
session_start();
$pageTitle = "Departments";

// Include database connection
include_once __DIR__ . "/../config/database.php";
include_once __DIR__ . "/../functions/helper_functions.php";
include_once __DIR__ . "/../includes/admin_header.php";

// Fetch departments with employee count
$limit = 5; // Number of records per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

$totalQuery = $pdo->query("SELECT COUNT(*) AS total FROM departments");
$totalRecords = $totalQuery->fetch(PDO::FETCH_ASSOC)['total'];
$totalPages = ceil($totalRecords / $limit);

$query = "SELECT d.*, 
                 (SELECT COUNT(*) FROM users WHERE users.id = d.department_id) AS total_employees 
          FROM departments d 
          LIMIT $start, $limit";
$departments = $pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);
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
            <div class="row">
                <!-- Add Department Card -->
                <div class="col-md-5">
                    <div class="card shadow-sm">
                        <div class="card-header bg-secondary text-white">Add Department</div>
                        <div class="card-body">
                            <?php if (isset($_SESSION['message'])): ?>
                                <div class="alert alert-info"><?= $_SESSION['message']; unset($_SESSION['message']); ?></div>
                            <?php endif; ?>
                            <form action="save_department.php" method="POST">
                                <div class="mb-3">
                                    <label class="form-label">Department Name</label>
                                    <input type="text" class="form-control" name="name" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Description</label>
                                    <textarea class="form-control" name="description" rows="3"></textarea>
                                </div>
                                <button type="submit" class="btn btn-success">Add Department</button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- List Departments Card -->
                <div class="col-md-7">
                    <div class="card shadow-sm">
                        <div class="card-header bg-secondary text-white">List of Departments</div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <thead class="bg-secondary text-white">
                                    <tr>
                                        <th>Department Name</th>
                                        <th>Description</th>
                                        <th>Total Employees</th>
                                        <th>Options</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($departments as $dept): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($dept['name']) ?></td>
                                            <td><?= htmlspecialchars($dept['description']) ?></td>
                                            <td><?= $dept['total_employees'] ?></td>
                                            <td>
                                                <a href="edit_department.php?id=<?= $dept['department_id'] ?>" class="btn btn-warning btn-xs rounded-circle d-flex justify-content-center align-items-center bi bi-pencil" style="width: 26px; height: 26px; color: white;" title="Edit"></a>
                                                <a href="delete_department.php?id=<?= $dept['department_id'] ?>" class="btn btn-danger btn-xs rounded-circle d-flex justify-content-center align-items-center bi bi-trash" style="width: 26px; height: 26px; color: white; " title="Delete"
                                                            onclick="return confirm('Are you sure you want to delete this Department?');"></a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                           <!-- Pagination -->
                            <div class="mt-2">
                                Showing <?= min($totalRecords, ($start + 1)) ?> to <?= min($start + $limit, $totalRecords) ?> of <?= $totalRecords ?> entries
                            </div>
                            <nav>
                                <ul class="pagination">
                                    <!-- First Page -->
                                    <li class="page-item <?= ($page == 1) ? 'disabled' : '' ?>">
                                        <a class="page-link" href="?page=1"><<</a>
                                    </li>

                                    <!-- Two Pages Back -->
                                    <?php if ($page > 2): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?page=<?= max(1, $page - 2) ?>"><< 2</a>
                                        </li>
                                    <?php endif; ?>

                                    <!-- Previous Page -->
                                    <li class="page-item <?= ($page == 1) ? 'disabled' : '' ?>">
                                        <a class="page-link" href="?page=<?= max(1, $page - 1) ?>"><</a>
                                    </li>

                                    <!-- Page Numbers -->
                                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                        <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                            <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                                        </li>
                                    <?php endfor; ?>

                                    <!-- Next Page -->
                                    <li class="page-item <?= ($page == $totalPages) ? 'disabled' : '' ?>">
                                        <a class="page-link" href="?page=<?= min($totalPages, $page + 1) ?>">></a>
                                    </li>

                                    <!-- Two Pages Forward -->
                                    <?php if ($page < $totalPages - 1): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?page=<?= min($totalPages, $page + 2) ?>">2 >></a>
                                        </li>
                                    <?php endif; ?>

                                    <!-- Last Page -->
                                    <li class="page-item <?= ($page == $totalPages) ? 'disabled' : '' ?>">
                                        <a class="page-link" href="?page=<?= $totalPages ?>">>></a>
                                    </li>
                                </ul>
                            </nav>

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
