<?php
session_start();
$pageTitle = "List of Enquiries";

include_once __DIR__ . "/../config/database.php";
include_once __DIR__ . "/../functions/helper_functions.php";
include_once __DIR__ . "/../includes/admin_header.php";

$limit = 10; // Number of enquiries per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Get total number of enquiries
$stmt = $pdo->query("SELECT COUNT(*) AS total FROM enquiries");
$totalEnquiries = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
$totalPages = ceil($totalEnquiries / $limit);

// Fetch enquiries with limit and offset for pagination
$stmt = $pdo->prepare("SELECT * FROM enquiries ORDER BY enquiry_id DESC LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$enquiries = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
                    <div class="card ">
                        <div class="card-header bg-secondary text-white">
                            <h4 class="mb-4">List of Enquiries</h4>

                            <!-- Search Box -->
                            <input type="text" id="searchBox" class="form-control mb-3" placeholder="Search enquiries..." onkeyup="filterTable()">
                        </div>

                        
                            <div class="card-body p-2">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead style="background-color: #6c757d !important; color: white !important;">
                                            <tr>
                                                <th>#</th>
                                                <th>Category</th>
                                                <th>Mobile</th>
                                                <th>Purpose</th>
                                                <th>Name</th>
                                                <th>Whom to Meet</th>
                                                <th>Email</th>
                                                <th>Content</th>
                                                <th>Date</th>
                                            </tr>
                                        </thead>
                                        <tbody id="enquiryTable">
                                            <?php foreach ($enquiries as $index => $row) : ?>
                                                <tr>
                                                    <td><?= $offset + $index + 1 ?></td>
                                                    <td><?= htmlspecialchars($row['category']) ?></td>
                                                    <td><?= htmlspecialchars($row['mobile']) ?></td>
                                                    <td><?= htmlspecialchars($row['purpose']) ?></td>
                                                    <td><?= htmlspecialchars($row['name']) ?></td>
                                                    <td><?= htmlspecialchars($row['whom_to_meet']) ?></td>
                                                    <td><?= htmlspecialchars($row['email']) ?></td>
                                                    <td><?= htmlspecialchars($row['content']) ?></td>
                                                    <td><?= htmlspecialchars($row['date']) ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div> <!-- End of .table-responsive -->

                                <!-- Pagination & Total Count -->
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <div>Total Enquiries: <strong><?= $totalEnquiries ?></strong></div>
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
                            </div> <!-- End of .card-body -->
                         <!-- End of .card -->
                       
                    </div>
                </div>
            </div>
        </div>
         
    </div>
</div>
<!-- Search Filter Script -->
<script>
function filterTable() {
    var input, filter, table, tr, td, i, j, txtValue;
    input = document.getElementById("searchBox");
    filter = input.value.toLowerCase();
    table = document.getElementById("enquiryTable");
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
