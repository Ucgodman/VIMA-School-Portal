<?php
session_start();
$pageTitle = "Attendance Report";

// Include necessary files
include_once __DIR__ . "/../config/database.php";
include_once __DIR__ . "/../includes/admin_header.php";

$student_id = $_GET['student_id'] ?? ($_SESSION['user']['id'] ?? null);

if (!$student_id) {
    die("Student ID is required.");
}

// Handle filter month/year
$current_month = $_GET['month'] ?? date('m');
$current_year  = $_GET['year'] ?? date('Y');
$filter_date = "$current_year-$current_month";

// Get records for selected month
$stmt = $pdo->prepare("SELECT * FROM attendance WHERE student_id = ? AND DATE_FORMAT(date, '%Y-%m') = ? ORDER BY date DESC");
$stmt->execute([$student_id, $filter_date]);
$records = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Summary
$summary_stmt = $pdo->prepare("SELECT 
    COUNT(*) AS total,
    SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) AS present_count,
    SUM(CASE WHEN status = 'absent' THEN 1 ELSE 0 END) AS absent_count
    FROM attendance 
    WHERE student_id = ? AND DATE_FORMAT(date, '%Y-%m') = ?");
$summary_stmt->execute([$student_id, $filter_date]);
$summary = $summary_stmt->fetch(PDO::FETCH_ASSOC);

// Percentages
$present_percent = $summary['total'] ? round(($summary['present_count'] / $summary['total']) * 100, 2) : 0;
$absent_percent = $summary['total'] ? round(($summary['absent_count'] / $summary['total']) * 100, 2) : 0;

// Generate year and month options
$years = range(date('Y') - 3, date('Y'));
$months = [
    '01'=>'January', '02'=>'February', '03'=>'March', '04'=>'April', '05'=>'May', '06'=>'June',
    '07'=>'July', '08'=>'August', '09'=>'September', '10'=>'October', '11'=>'November', '12'=>'December'
];

?>
<div class="wrapper">
    <?php include_once __DIR__ . "/../includes/sidebar.php"; ?>
    <div class="main-panel">
        <div class="main-header">
            <div class="main-header-logo">
                <div class="logo-header" data-background-color="dark">
                    <a href="index.html" class="logo">
                        <img src="/../assets/images/logo_light.svg" alt="navbar brand" class="navbar-brand" height="20" />
                    </a>
                    <div class="nav-toggle">
                        <button class="btn btn-toggle toggle-sidebar"><i class="gg-menu-right"></i></button>
                        <button class="btn btn-toggle sidenav-toggler"><i class="gg-menu-left"></i></button>
                    </div>
                    <button class="topbar-toggler more"><i class="gg-more-vertical-alt"></i></button>
                </div>
            </div>
            <?php include_once __DIR__ . "/../includes/navbar.php"; ?>
        </div>
        <div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Attendance Report</h5>
            <form method="get" class="d-flex align-items-center">
                <input type="hidden" name="student_id" value="<?= $student_id ?>">
                <select name="month" class="form-select me-2" required>
                    <?php foreach ($months as $key => $label): ?>
                        <option value="<?= $key ?>" <?= ($current_month == $key) ? 'selected' : '' ?>>
                            <?= $label ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <select name="year" class="form-select me-2" required>
                    <?php foreach ($years as $year): ?>
                        <option value="<?= $year ?>" <?= ($current_year == $year) ? 'selected' : '' ?>>
                            <?= $year ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn btn-light btn-sm">Filter</button>
            </form>
        </div>

		<div class="card-body">
		            <!-- Summary -->
		            <div class="row text-center mb-4">
		                <div class="col-md-4">
		                    <h6>Total Days</h6>
		                    <p class="fw-bold"><?= $summary['total'] ?></p>
		                </div>
		                <div class="col-md-4">
		                    <h6>Present</h6>
		                    <p class="text-success fw-bold"><?= $summary['present_count'] ?> (<?= $present_percent ?>%)</p>
		                </div>
		                <div class="col-md-4">
		                    <h6>Absent</h6>
		                    <p class="text-danger fw-bold"><?= $summary['absent_count'] ?> (<?= $absent_percent ?>%)</p>
		                </div>
		            </div>

		            <!-- Attendance Table -->
		            <div class="table-responsive">
		                <table class="table table-bordered table-striped">
		                    <thead class="table-secondary">
		                        <tr>
		                            <th>Date</th>
		                            <th>Status</th>
		                            <th>Marked At</th>
		                        </tr>
		                    </thead>
		                    <tbody>
		                        <?php if (count($records) > 0): ?>
		                            <?php foreach ($records as $row): ?>
		                                <tr>
		                                    <td><?= date('D, j M Y', strtotime($row['date'])) ?></td>
		                                    <td class="<?= $row['status'] === 'present' ? 'text-success' : 'text-danger' ?>">
		                                        <?= ucfirst($row['status']) ?>
		                                    </td>
		                                    <td><?= date('d/m/Y h:i A', strtotime($row['created_at'])) ?></td>
		                                </tr>
		                            <?php endforeach; ?>
		                        <?php else: ?>
		                            <tr><td colspan="3" class="text-center">No records for selected month.</td></tr>
		                        <?php endif; ?>
		                    </tbody>
		                </table>
		            </div>

		            <a href="dashboard.php" class="btn btn-secondary">← Back to Dashboard</a>
		        </div>
		    </div>
		</div>

    </div>
</div>

<script src="../assets/js/core/bootstrap.bundle.min.js"></script>
<script src="../assets/js/core/jquery-3.7.1.min.js"></script>
</body>
</html>