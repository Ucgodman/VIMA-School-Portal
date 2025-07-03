<?php
session_start();
$pageTitle = "Manage Student Houses";

include_once __DIR__ . "/../config/database.php";
include_once __DIR__ . "/../functions/helper_functions.php";
include_once __DIR__ . "/../includes/admin_header.php";

$limit = 10; // Number of student houses per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Get total number of student houses
$stmt = $pdo->query("SELECT COUNT(*) AS total FROM student_house");
$totalHouses = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
$totalPages = ceil($totalHouses / $limit);

// Fetch student houses with pagination
$stmt = $pdo->prepare("SELECT * FROM student_house ORDER BY house_id DESC LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$houses = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle Add Student House Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $house_name = $_POST['house_name'];
    $description = $_POST['description'];
    $date = date("Y-m-d");

    $stmt = $pdo->prepare("INSERT INTO student_house (house_name, description,created_at) VALUES (:house_name, :description, :date)");
    $stmt->bindParam(':house_name', $house_name);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':date', $date);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Student House added successfully!";
        header("Location: studenthouse.php");
        exit();
    } else {
        $_SESSION['error'] = "Failed to add student house.";
    }
}
?>

<div class="wrapper">
    <?php include_once __DIR__ . "/../includes/sidebar.php"; ?>
    <div class="main-panel">
        <div class="container mt-8">
            <div class="page-inner">
                <div class="row">
                    <!-- Left Side: Add Student House -->
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header bg-secondary text-white">
                                <h5 class="mb-0">Add Student House</h5>
                            </div>
                            <div class="card-body">
                                <?php if (isset($_SESSION['success'])) : ?>
                                    <div class="alert alert-success"><?= $_SESSION['success'] ?>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                    <?php unset($_SESSION['success']); ?>
                                <?php elseif (isset($_SESSION['error'])) : ?>
                                    <div class="alert alert-danger"><?= $_SESSION['error'] ?>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                    <?php unset($_SESSION['error']); ?>
                                <?php endif; ?>

                                <form method="POST">
                                    <div class="form-group">
                                        <label>House Name</label>
                                        <input type="text" name="house_name" class="form-control" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Description</label>
                                        <textarea cols="2" name="description" class="form-control" required> </textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary mt-2">Add House</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Right Side: List of Student Houses -->
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center bg-secondary text-white">
                                <h5 class="mb-0">List </h5>
                                <input type="text" id="searchBox" class="form-control w-50 mt-2" placeholder="Search student houses..." onkeyup="filterTable()">
                            </div>
                            <div class="card-body p-2">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-responsive">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>House Name</th>
                                                <th>Description</th>
                                                <th>Date Added</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody id="houseTable">
                                            <?php foreach ($houses as $index => $row) : ?>
                                                <tr>
                                                    <td><?= $offset + $index + 1 ?></td>
                                                    <td><?= htmlspecialchars($row['house_name']) ?></td>
                                                    <td><?= htmlspecialchars($row['description']) ?></td>
                                                    <td><?= htmlspecialchars($row['created_at']) ?></td>
                                                    <td>
                                                        <a href="edit_studenthouse.php?id=<?= $row['house_id']; ?>" class="btn btn-warning btn-xs rounded-circle d-flex justify-content-center align-items-center bi bi-pencil" style="width: 26px; height: 26px; color: white;" title="Edit"></a>
                                                        <a href="delete_studenthouse.php?id=<?= $row['house_id']; ?>" class="btn btn-danger btn-xs rounded-circle d-flex justify-content-center align-items-center bi bi-trash" style="width: 26px; height: 26px; color: white; " title="Delete"
                                                            onclick="return confirm('Are you sure you want to delete this House?');"></a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Pagination & Total Count -->
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <div>Showing <?= ($offset + 1) ?> to <?= min($offset + $limit, $totalHouses) ?> of <?= $totalHouses ?> entries</div>
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
            </div> <!-- End Page Inner -->
        </div> <!-- End Container -->
    </div> <!-- End Main Panel -->
</div>

<!-- Search Filter Script -->
<script>
function filterTable() {
    var input, filter, table, tr, td, i, j, txtValue;
    input = document.getElementById("searchBox");
    filter = input.value.toLowerCase();
    table = document.getElementById("houseTable");
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
