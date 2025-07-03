<?php

$pageTitle = "Edit Activity";

include_once __DIR__ . "/../config/database.php";
include_once __DIR__ . "/../functions/helper_functions.php";
include_once __DIR__ . "/../includes/admin_header.php";

// Check if activity ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error'] = "Invalid activity ID.";
    header("Location: student_activities.php");
    exit();
}

$activity_id = $_GET['id'];

// Fetch activity details
$stmt = $pdo->prepare("SELECT * FROM activities WHERE activity_id = :id");
$stmt->bindParam(':id', $activity_id, PDO::PARAM_INT);
$stmt->execute();
$activity = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$activity) {
    $_SESSION['error'] = "Activity not found.";
    header("Location: student_activity.php");
    exit();
}

// Fetch all available clubs
$clubs = $pdo->query("SELECT * FROM clubs ORDER BY club_name ASC")->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $activity_name = $_POST['activity_name'];
    $club_id = $_POST['club_id'];
    $description = $_POST['description'];
    $activity_date = $_POST['activity_date'];

    $stmt = $pdo->prepare("UPDATE activities SET activity_name = :activity_name, club_id = :club_id, description = :description, activity_date = :activity_date WHERE activity_id = :activity_id");
    $stmt->bindParam(':activity_name', $activity_name);
    $stmt->bindParam(':club_id', $club_id);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':activity_date', $activity_date);
    $stmt->bindParam(':activity_id', $activity_id);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Activity updated successfully!";
        header("Location: student_activity.php");
        exit();
    } else {
        $_SESSION['error'] = "Failed to update activity.";
    }
}
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
        
        <div class="container">
            <div class="page-inner">
                <div class="row">
                    <div class="col-md-6 mx-auto">
                        <div class="card">
                            <div class="card-header bg-secondary text-white">
                                <h5 class="mb-0">Edit Activity</h5>
                            </div>
                            <div class="card-body">
                                <?php if (isset($_SESSION['error'])) : ?>
                                    <div class="alert alert-danger"><?= $_SESSION['error'] ?></div>
                                    <?php unset($_SESSION['error']); ?>
                                <?php endif; ?>

                                <form method="POST">
                                    <div class="form-group">
                                        <label>Name</label>
                                        <input type="text" name="activity_name" class="form-control" value="<?= htmlspecialchars($activity['activity_name']) ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Select Club</label>
                                        <select name="club_id" class="form-control" required>
                                            <?php foreach ($clubs as $club) : ?>
                                                <option value="<?= $club['club_id'] ?>" <?= ($club['club_id'] == $activity['club_id']) ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($club['club_name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Description</label>
                                        <textarea name="description" class="form-control" required><?= htmlspecialchars($activity['description']) ?></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label>Date</label>
                                        <input type="date" name="activity_date" class="form-control" value="<?= htmlspecialchars($activity['activity_date']) ?>" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary mt-2">Update Activity</button>
                                    <a href="student_activities.php" class="btn btn-secondary mt-2">Cancel</a>
                                </form>
                            </div>
                        </div>
                    </div>
                </div> <!-- End Row -->
            </div> <!-- End Page Inner -->
        </div> <!-- End Container -->
    </div> <!-- End Main Panel -->
</div>


