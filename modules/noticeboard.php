<?php
session_start();
$pageTitle = "Noticeboard";

// Include database connection
include_once __DIR__ . "/../config/database.php";
include_once __DIR__ . "/../functions/helper_functions.php";
include_once __DIR__ . "/../includes/admin_header.php";

try {
    // Fetch Notices
    $noticeQuery = "SELECT * FROM noticeboard ORDER BY noticeboard_date DESC";
    $noticeResult = $pdo->query($noticeQuery)->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<div class="wrapper">
    <?php include_once __DIR__ . "/../includes/sidebar.php"; ?>
    <div class="main-panel">
        <div class="main-header">
             <div class="main-header-logo">
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
                </div>
                    <?php include_once __DIR__ . "/../includes/navbar.php"; ?>
             </div>

        <div class="container">
            <div class="row">
                <!-- Left Card: Add Noticeboard -->
                <div class="col-md-5">
                    <div class="card shadow-sm">
                        <div class="card-header bg-secondary text-white">Add Noticeboard</div>
                        <div class="card-body">
                            <form action="save_notice.php" method="POST">
                                <div class="mb-3">
                                    <label class="form-label">Noticeboard Title</label>
                                    <input type="text" class="form-control" name="title" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Location</label>
                                    <input type="text" class="form-control" name="location" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Content</label>
                                    <textarea class="form-control" name="content" rows="4" required></textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Noticeboard Date</label>
                                    <input type="date" class="form-control" name="noticeboard_date" required>
                                </div>
                                <div class="mb-3 form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="send_mail" value="1">
                                    <label class="form-check-label">Send to Mail</label>
                                </div>
                                <button type="submit" class="btn btn-success">Add Notice</button>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Right Card: List Noticeboard -->
                <div class="col-md-7">
                    <div class="card shadow-sm">
                        <div class="card-header bg-secondary text-white">List of Noticeboard</div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="bg-secondary text-white">
                                        <tr>
                                            <th>#</th>
                                            <th>Title</th>
                                            <th>Location</th>
                                            <th>Date</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($noticeResult as $index => $row) { ?>
                                            <tr>
                                                <td><?= $index + 1 ?></td>
                                                <td><?= htmlspecialchars($row['title']) ?></td>
                                                <td><?= htmlspecialchars($row['location']) ?></td>
                                                <td><?= $row['noticeboard_date'] ?></td>
                                                <td>
                                                    <a href="edit_noticeboard.php?id=<?= $row['noticeboard_id'] ?>" class="btn btn-warning btn-xs rounded-circle d-flex justify-content-center align-items-center bi bi-pencil" style="width: 26px; height: 26px; color: white;" title="Edit"></a>
                                                    <a href="delete_noticeboard.php?id=<?= $row['noticeboard_id'] ?>" class="btn btn-danger btn-xs rounded-circle d-flex justify-content-center align-items-center bi bi-trash" style="width: 26px; height: 26px; color: white; " title="Delete"
                                                            onclick="return confirm('Are you sure you want to delete this Noticeboard?');"></a>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
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
