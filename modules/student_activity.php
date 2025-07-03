<?php

$pageTitle = "Manage Activities";

include_once __DIR__ . "/../config/database.php";
include_once __DIR__ . "/../functions/helper_functions.php";
include_once __DIR__ . "/../includes/admin_header.php";

// Fetch available clubs
$clubs = $pdo->query("SELECT * FROM clubs ORDER BY club_name ASC")->fetchAll(PDO::FETCH_ASSOC);

// Handle Add Activity Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $activity_name = $_POST['activity_name'];
    $club_id = $_POST['club_id'];
    $description = $_POST['description'];
    $activity_date = $_POST['activity_date'];

    $stmt = $pdo->prepare("INSERT INTO activities (activity_name, club_id, description, activity_date) VALUES (:activity_name, :club_id, :description, :activity_date)");
    $stmt->bindParam(':activity_name', $activity_name);
    $stmt->bindParam(':club_id', $club_id);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':activity_date', $activity_date);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Activity added successfully!";
        header("Location: student_activity.php");
        exit();
    } else {
        $_SESSION['error'] = "Failed to add activity.";
    }
}

// Fetch activities
$activities = $pdo->query("SELECT a.*, c.club_name FROM activities a JOIN clubs c ON a.club_id = c.club_id ORDER BY a.activity_date DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="wrapper">
    <?php include_once __DIR__ . "/../includes/admin_sidebar.php"; ?>
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
                    <!-- Left Side: Add Activity -->
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header bg-secondary text-white">
                                <h5 class="mb-0">Activity</h5>
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
                                        <label>Activity Name</label>
                                        <input type="text" name="activity_name" class="form-control" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Select Club</label>
                                        <select name="club_id" class="form-control" required>
                                            <option value="">Select Club</option>
                                            <?php foreach ($clubs as $club) : ?>
                                                <option value="<?= $club['club_id'] ?>"><?= htmlspecialchars($club['club_name']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Description</label>
                                        <textarea name="description" class="form-control" required></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label>Date</label>
                                        <input type="date" name="activity_date" class="form-control" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary mt-2">Add Activity</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Right Side: List of Activities -->
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center bg-secondary text-white">
                                <h5 class="mb-0">List of Activities</h5>
                                <input type="text" id="searchBox" class="form-control w-50 mt-2" placeholder="Search activities..." onkeyup="filterTable()">
                            </div>
                            <div class="card-body p-2">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Activity Name</th>
                                                <th>Club</th>
                                                <th>Description</th>
                                                <th>Date</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody id="activityTable">
                                            <?php foreach ($activities as $index => $row) : ?>
                                                <tr>
                                                    <td><?= $index + 1 ?></td>
                                                    <td><?= htmlspecialchars($row['activity_name']) ?></td>
                                                    <td><?= htmlspecialchars($row['club_name']) ?></td>
                                                    <td><?= htmlspecialchars($row['description']) ?></td>
                                                    <td><?= htmlspecialchars($row['activity_date']) ?></td>
                                                    <td>
                                                        <a href="edit_activity.php?id=<?= $row['activity_id']; ?>" class="btn btn-warning btn-xs rounded-circle d-flex justify-content-center align-items-center bi bi-pencil" style="width: 26px; height: 26px; color: white;" title="Edit"></a>
                                                        <a href="delete_activity.php?id=<?= $row['activity_id']; ?>" class="btn btn-danger btn-xs rounded-circle d-flex justify-content-center align-items-center bi bi-trash" style="width: 26px; height: 26px; color: white; " title="Delete"
                                                            onclick="return confirm('Are you sure you want to delete this Activity?');"></a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
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
    table = document.getElementById("activityTable");
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

<!-- JS Files -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/core/popper.min.js"></script>
<script src="../assets/js/core/bootstrap.min.js"></script>
<script src="../assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>
<script src="../assets/js/plugin/jquery.sparkline/jquery.sparkline.min.js"></script>
<script src="../assets/js/plugin/bootstrap-notify/bootstrap-notify.min.js"></script>
<script src="../assets/js/plugin/sweetalert/sweetalert.min.js"></script>
<script src="../assets/js/kaiadmin.min.js"></script>

</body>
</html>
