<?php
$pageTitle = "Roles";
session_start();
// Include database connection
include_once __DIR__ . "/../config/database.php";
include_once __DIR__ . "/../includes/admin_header.php";

// Handle role deletion
if (isset($_GET['delete'])) {
    $role_id = intval($_GET['delete']);
    $pdo->prepare("DELETE FROM roles WHERE id = ?")->execute([$role_id]);
    $_SESSION['message'] = "Role deleted successfully!";
    header("Location: roles.php");
    exit();
}

// Pagination setup
$limit = 5; // Number of roles per page
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $limit;

// Get total records
$total_roles = $pdo->query("SELECT COUNT(*) FROM roles")->fetchColumn();
$total_pages = ceil($total_roles / $limit);

// Fetch roles with limit
$query = "SELECT * FROM roles ORDER BY date_created ASC LIMIT :limit OFFSET :offset";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$roles = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
            <div class="row">
                <!-- Add Role Form -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header bg-primary text-white">Add Role</div>
                        <div class="card-body">
                            <?php if (isset($_SESSION['message'])): ?>
                                <div class="alert alert-info"><?= $_SESSION['message']; unset($_SESSION['message']); ?></div>
                            <?php endif; ?>
                            <form action="save_role.php" method="POST">
                                <div class="mb-3">
                                    <label class="form-label">Role Name</label>
                                    <input type="text" class="form-control" name="name" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Description</label>
                                    <textarea class="form-control" name="description" rows="3"></textarea>
                                </div>
                                <button type="submit" class="btn btn-success">Add Role</button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Roles List -->
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header bg-secondary text-white">List of Roles</div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <thead class="bg-secondary text-white">
                                    <tr>
                                        <th>ID</th>
                                        <th>Role Name</th>
                                        <th>Description</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($roles as $role): ?>
                                        <tr>
                                            <td><?= $role['id'] ?></td>
                                            <td><?= htmlspecialchars($role['name']) ?></td>
                                            <td><?= htmlspecialchars($role['description']) ?></td>
                                            <td>
                                                <a href="edit_role.php?id=<?= $role['id'] ?>" class="btn btn-warning btn-xs">Edit</a>
                                                <a href="?delete=<?= $role['id'] ?>" class="btn btn-danger btn-xs"
                                                   onclick="return confirm('Are you sure you want to delete this role?');">
                                                   Delete
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>

                            <!-- Pagination -->
                            <nav>
                                <ul class="pagination">
                                    <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                                        <a class="page-link" href="?page=1">First</a>
                                    </li>
                                    <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                                        <a class="page-link" href="?page=<?= max(1, $page - 1) ?>">Previous</a>
                                    </li>

                                    <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                                        <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                            <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                                        </li>
                                    <?php endfor; ?>

                                    <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
                                        <a class="page-link" href="?page=<?= min($total_pages, $page + 1) ?>">Next</a>
                                    </li>
                                    <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
                                        <a class="page-link" href="?page=<?= $total_pages ?>">Last</a>
                                    </li>
                                </ul>
                            </nav>
                            <!-- End Pagination -->

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>  
</div>

<!-- Core JS Files -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/core/jquery-3.7.1.min.js"></script>
<script src="../assets/js/core/popper.min.js"></script>
<script src="../assets/js/core/bootstrap.min.js"></script>
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
