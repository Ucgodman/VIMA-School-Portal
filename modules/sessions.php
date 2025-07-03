<?php
session_start();
$pageTitle = "Manage Sessions";
include_once __DIR__ . "/../config/database.php";
include_once __DIR__ . "/../includes/admin_header.php";

// Fetch sessions
try {
    $stmtSessions = $pdo->query("SELECT session_id, session_year, is_active FROM sessions ORDER BY session_year DESC");
    $sessions = $stmtSessions->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Query Error: " . $e->getMessage());
}
?>

<div class="wrapper">
    <?php include_once __DIR__ . "/../includes/sidebar.php"; ?>
    <div class="main-panel">
        <div class="container mt-4">
            <div class="row">
                <!-- Add Session Form -->
                <div class="col-md-4">
                    <div class="card shadow-sm">
                        <div class="card-header bg-secondary text-white">Add Session</div>
                        <div class="card-body">
                            <form id="addSessionForm">
                                <div class="mb-3">
                                    <label class="form-label">Session Year</label>
                                    <input type="text" class="form-control" name="session_year" placeholder="e.g. 2024/2025" required>
                                </div>
                                <button type="submit" class="btn btn-success">Add Session</button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- List Sessions -->
                <div class="col-md-8">
                    <div class="card shadow-sm">
                        <div class="card-header bg-secondary text-white">List Sessions</div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="bg-secondary text-white">
                                        <tr>
                                            <th>#</th>
                                            <th>Session Year</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="sessionTableBody">
                                        <?php if (count($sessions) > 0): ?>
                                            <?php foreach ($sessions as $index => $session): ?>
                                                <tr class="<?= $session['is_active'] ? 'table-success' : ''; ?>">
                                                    <td><?= $index + 1; ?></td>
                                                    <td><?= htmlspecialchars($session['session_year']); ?></td>
                                                    <td>
                                                        <?php if ($session['is_active']): ?>
                                                            <span class="badge bg-success">Active</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-secondary">Inactive</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <a href="edit_session.php?id=<?= $session['session_id']; ?>" 
                                                           class="btn btn-warning btn-xs rounded-circle d-flex justify-content-center align-items-center bi bi-pencil"
                                                           style="width: 26px; height: 26px; color: white;" title="Edit">
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="4" class="text-center">No sessions found.</td>
                                            </tr>
                                        <?php endif; ?>
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

<script>

// Handle Add Session Form Submission
document.getElementById("addSessionForm").addEventListener("submit", function(e) {
    e.preventDefault();
    let formData = new FormData(this);

    fetch("add_session.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        alert(data);
        location.reload();
    })
    .catch(error => console.error("Error adding session:", error));
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
