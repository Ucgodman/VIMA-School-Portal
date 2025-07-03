<?php
session_start();
$pageTitle = "Manage Sections";

// Include database connection
include_once __DIR__ . "/../config/database.php";
include_once __DIR__ . "/../includes/admin_header.php";

try {
    // Fetch classes for selection
    $stmtClasses = $pdo->query("SELECT class_id, class_name, staff_id FROM classes ORDER BY class_name ASC");
    $classes = $stmtClasses->fetchAll(PDO::FETCH_ASSOC);


    // Fetch the active session year
    $stmtSession = $pdo->query("SELECT session_id, session_year FROM sessions WHERE is_active = 1 LIMIT 1");
    $activeSession = $stmtSession->fetch(PDO::FETCH_ASSOC);
    $activeSessionYear = $activeSession ? $activeSession['session_year'] : "N/A";
    $activeSessionId = $activeSession ? $activeSession['session_id'] : null;


    // Fetch sections with class and teacher details
    $stmtSections = $pdo->query("SELECT s.section_id, s.name AS section_name, c.class_name AS class_name, 
                                    st.firstname, st.lastname 
                             FROM sections s
                             JOIN classes c ON s.class_id = c.class_id
                             LEFT JOIN staff st ON c.staff_id = st.id  -- Fix: Use c.staff_id instead of s.staff_id
                             ORDER BY c.class_name, s.name ASC");


    $sections = $stmtSections->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Query Error: " . $e->getMessage());
}
?>

<div class="wrapper">
    <?php include_once __DIR__ . "/../includes/sidebar.php"; ?>
    <div class="main-panel">
        <div class="main-header">
            <?php include_once __DIR__ . "/../includes/navbar.php"; ?>
        </div>

        <div class="container">
            <div class="row">
                <!-- Add Section Form -->
                <div class="col-md-4">
                    <div class="card shadow-sm">
                        <div class="card-header bg-secondary text-white">Add Section</div>
                        <div class="card-body">
                            <form id="addSectionForm">
                                <div class="mb-3">
                                    <label class="form-label">Section Name</label>
                                    <select name="name" class="form-control" required>
                                        <option value="">-- Select Term --</option>
                                        <option value="First Term">First Term</option>
                                        <option value="Second Term">Second Term</option>
                                         <option value="Second Term">Third Term</option>
                                    </select>
                                </div>

                                
                               <div class="mb-3">
                                    <label class="form-label">Select Class</label>
                                    <select name="class_id" id="class_id" class="form-control" required>
                                        <option value="">-- Select Class --</option>
                                        <?php foreach ($classes as $class): ?>
                                            <option value="<?= $class['class_id']; ?>"><?= $class['class_name']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Assigned Teacher</label>
                                    <input type="text" id="assigned_teacher" class="form-control" readonly>
                                    <input type="hidden" name="staff_id" id="staff_id">
                                </div>


                                <div class="mb-3">
                                    <label class="form-label">Active Session</label>
                                    <input type="text" class="form-control" value="<?= htmlspecialchars($activeSessionYear); ?>" readonly>
                                    <input type="hidden" name="session_id" value="<?= htmlspecialchars($activeSessionId); ?>">
                                </div>

                                <button type="submit" class="btn btn-success">Add Section</button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- List Sections -->
                <div class="col-md-8">
                    <div class="card shadow-sm">
                        <div class="card-header bg-secondary text-white">List Sections</div>
                        <div class="card-body">
                            <!-- Class Filter Buttons -->
                            <div class="mb-3">
                                <?php foreach ($classes as $class): ?>
                                    <button class="btn btn-info btn-sm filter-section" data-class-id="<?= $class['class_id']; ?>">
                                        <?= $class['class_name']; ?>
                                    </button>
                                <?php endforeach; ?>
                            </div>

                            <!-- Sections Table -->
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="bg-secondary text-white">
                                        <tr>
                                            <th>Section Name</th>
                                            <th>Class</th>
                                            <th>Teacher</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="sectionTableBody">
                                        <?php foreach ($sections as $section): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($section['section_name']); ?></td>
                                                <td><?= htmlspecialchars($section['class_name']); ?></td>
                                                <td>
                                                    <?= !empty($section['firstname']) ? htmlspecialchars($section['firstname'] . " " . $section['lastname']) : "No Teacher Assigned"; ?>
                                                </td>
                                                <td>
                                                    <a href="edit_section.php?id=<?= $section['section_id']; ?>" class="btn btn-warning btn-xs rounded-circle d-flex justify-content-center align-items-center bi bi-pencil" style="width: 26px; height: 26px; color: white;" title="Edit"></a>
                                                    <a href="delete_section.php?id=<?= $section['section_id']; ?>" class="btn btn-danger btn-xs rounded-circle d-flex justify-content-center align-items-center bi bi-trash" style="width: 26px; height: 26px; color: white; " title="Delete"
                                                            onclick="return confirm('Are you sure you want to delete this section?');"></a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div class="d-flex justify-content-between">
                                <p id="entryInfo">Showing 1 to 2 of 2 entries</p>
                                <nav>
                                    <ul class="pagination">
                                        <li class="page-item"><a class="page-link" href="#">First</a></li>
                                        <li class="page-item disabled"><a class="page-link" href="#">Previous</a></li>
                                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                                        <li class="page-item"><a class="page-link" href="#">Next</a></li>
                                        <li class="page-item"><a class="page-link" href="#">Last</a></li>
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script>
// Filter sections by class
document.querySelectorAll(".filter-section").forEach(button => {
    button.addEventListener("click", function() {
        let classId = this.getAttribute("data-class-id");
        fetch(`get_sections.php?class_id=${classId}`)
            .then(response => response.text())
            .then(data => {
                document.getElementById("sectionTableBody").innerHTML = data;
            });
    });
});

document.getElementById("class_id").addEventListener("change", function() {
    let classId = this.value;
    
    if (classId) {
        fetch(`get_assigned_teacher.php?class_id=${classId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById("assigned_teacher").value = data.teacher_name;
                    document.getElementById("staff_id").value = data.staff_id;
                } else {
                    document.getElementById("assigned_teacher").value = "No teacher assigned";
                    document.getElementById("staff_id").value = "";
                }
            })
            .catch(error => console.error("Error fetching teacher:", error));
    } else {
        document.getElementById("assigned_teacher").value = "";
        document.getElementById("staff_id").value = "";
    }
});


// Handle Add Section Form Submission
document.getElementById("addSectionForm").addEventListener("submit", function(e) {
    e.preventDefault();
    let formData = new FormData(this);

    fetch("add_section.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        alert(data);
        location.reload();
    })
    .catch(error => console.error("Error adding section:", error));
});

document.querySelectorAll(".filter-section").forEach(button => {
    button.addEventListener("click", function() {
        let classId = this.getAttribute("data-class-id");
        fetch(`get_sections.php?id=${classId}`)
            .then(response => response.text())
            .then(data => {
                document.getElementById("sectionTableBody").innerHTML = data;
            })
            .catch(error => console.error("Error fetching sections:", error));
    });
});



</script>




<!-- Bootstrap & Other Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
