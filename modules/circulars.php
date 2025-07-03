<?php
session_start();
$pageTitle = "Manage Circulars";

include_once __DIR__ . "/../config/database.php";
include_once __DIR__ . "/../functions/helper_functions.php";
include_once __DIR__ . "/../includes/admin_header.php";

$limit = 10; // Number of circulars per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Get total number of circulars
$stmt = $pdo->query("SELECT COUNT(*) AS total FROM circulars");
$totalCirculars = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
$totalPages = ceil($totalCirculars / $limit);

// Fetch circulars with pagination
$stmt = $pdo->prepare("SELECT * FROM circulars ORDER BY circular_id DESC LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$circulars = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle Add Circular Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $reference = $_POST['reference'];
    $content = $_POST['content'];
    $date = $_POST['date']; // Get user-selected date

    $stmt = $pdo->prepare("INSERT INTO circulars (title, reference, content, date) VALUES (:title, :reference, :content, :date)");
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':reference', $reference);
    $stmt->bindParam(':content', $content);
    $stmt->bindParam(':date', $date);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Circular added successfully!";
        header("Location: circulars.php");
        exit();
    } else {
        $_SESSION['error'] = "Failed to add circular.";
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

        <div class="container ">
            <div class="row">
                <!-- Left Side: Add Circular -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header bg-secondary text-white">
                            <h5 class="mb-0">Add Circular</h5>
                        </div>
                        <div class="card-body">
                            <?php if (isset($_SESSION['success'])) : ?>
                                <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
                                <?php unset($_SESSION['success']); ?>
                            <?php elseif (isset($_SESSION['error'])) : ?>
                                <div class="alert alert-danger"><?= $_SESSION['error'] ?></div>
                                <?php unset($_SESSION['error']); ?>
                            <?php endif; ?>

                            <form method="POST">
                                <div class="form-group">
                                    <label>Title</label>
                                    <input type="text" name="title" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>Reference</label>
                                    <input type="text" name="reference" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>Content</label>
                                    <textarea name="content" class="form-control" required></textarea>
                                </div>
                                <div class="form-group">
                                    <label>Date</label>
                                    <input type="date" name="date" class="form-control" required>
                                </div>
                                <button type="submit" class="btn btn-primary mt-2">Add Circular</button>
                            </form>

                        </div>
                    </div>
                </div>

                <!-- Right Side: List of Circulars -->
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center bg-secondary text-white">
                            <h5 class="mb-0">List of Circulars</h5>
                            <input type="text" id="searchBox" class="form-control w-50" placeholder="Search circulars..." onkeyup="filterTable()">
                        </div>
                        <div class="card-body p-2">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Title</th>
                                            <th>Reference</th>
                                            <th>Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="circularTable">
                                        <?php foreach ($circulars as $index => $row) : ?>
                                            <tr>
                                                <td><?= $offset + $index + 1 ?></td>
                                                <td><?= htmlspecialchars($row['title']) ?></td>
                                                <td><?= htmlspecialchars($row['reference']) ?></td>
                                                <td><?= htmlspecialchars($row['date']) ?></td>
                                                <td>
                                                    <a href="edit_circular.php?id=<?= $row['circular_id']; ?>" class="btn btn-warning btn-xs rounded-circle d-flex justify-content-center align-items-center bi bi-pencil" style="width: 26px; height: 26px; color: white;" title="Edit"></a>
                                                    <a href="delete_circular.php?id=<?= $row['circular_id']; ?>" class="btn btn-danger btn-xs rounded-circle d-flex justify-content-center align-items-center bi bi-trash" style="width: 26px; height: 26px;" title="Delete"
                                                        onclick="return confirm('Are you sure you want to delete this circular?');">
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination & Total Count -->
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <div>Total Circulars: <strong><?= $totalCirculars ?></strong></div>
                                <nav>
                                    <ul class="pagination">
                                        <?php if ($page > 1) : ?>
                                            <li class="page-item"><a class="page-link" href="?page=<?= $page - 1 ?>">Previous</a></li>
                                        <?php endif; ?>

                                        <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
                                            <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                                <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                                            </li>
                                        <?php endfor; ?>

                                        <?php if ($page < $totalPages) : ?>
                                            <li class="page-item"><a class="page-link" href="?page=<?= $page + 1 ?>">Next</a></li>
                                        <?php endif; ?>
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div> <!-- End Row -->
        </div> <!-- End Container -->
    </div> <!-- End Main Panel -->
</div>

<!-- Search Filter Script -->
<script>
function filterTable() {
    var input, filter, table, tr, td, i, j, txtValue;
    input = document.getElementById("searchBox");
    filter = input.value.toLowerCase();
    table = document.getElementById("circularTable");
    tr = table.getElementsByTagName("tr");

    for (i = 0; i < tr.length; i++) {
        let rowVisible = false;
        td = tr[i].getElementsByTagName("td");
        for (j = 0; j < td.length; j++) {
            if (td[j]) {
                txtValue = td[j].textContent || td[j].innerText;
                if (txtValue.toLowerCase().indexOf(filter) > -1) {
                    rowVisible = true;
                    break;
                }
            }
        }
        tr[i].style.display = rowVisible ? "" : "none";
    }
}
</script>

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
