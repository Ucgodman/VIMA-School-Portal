<?php
session_start();
$pageTitle = "Edit Section";

// Include database connection
include_once __DIR__ . "/../config/database.php";
include_once __DIR__ . "/../includes/admin_header.php";

// Check if section ID is provided
$sectionId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($sectionId == 0) {
    echo "<script>alert('Invalid Section ID'); window.location.href='sections.php';</script>";
    exit;
}

// Fetch section details
try {
    $query = "SELECT s.*, 
                     c.class_name, 
                     st.id AS staff_id, 
                     st.firstname, 
                     st.lastname, 
                     r.name AS role_name 
              FROM sections s 
              JOIN classes c ON s.class_id = c.class_id
              LEFT JOIN staff st ON s.staff_id = st.id 
              LEFT JOIN roles r ON st.role_id = r.id 
              WHERE s.section_id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$sectionId]);
    $section = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$section) {
        echo "<script>alert('Section not found'); window.location.href='sections.php';</script>";
        exit;
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

// Fetch the current active session
try {
    $sessionQuery = "SELECT session_id, session_year FROM sessions WHERE is_active = 1 LIMIT 1";
    $sessionStmt = $pdo->query($sessionQuery);
    $activeSession = $sessionStmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching active session: " . $e->getMessage());
}

// Fetch list of teachers (only teachers with role_id = 2)
try {
    $teacherQuery = "SELECT id, firstname, lastname FROM staff WHERE role_id = 2 ORDER BY firstname ASC";
    $teacherResult = $pdo->query($teacherQuery)->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching teachers: " . $e->getMessage());
}

// Handle form submission for updating section
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $updatedSectionId = $_POST['section_id'];
    $updatedName = trim($_POST['name']);
    $updatedTeacherId = $_POST['staff_id'];

    // Validate inputs
    if (empty($updatedName)) {
        echo "<script>alert('Term is required!'); window.location.href='edit_section.php?id=$updatedSectionId';</script>";
        exit;
    }

    try {
        // Update section details
        $updateQuery = "UPDATE sections 
                        SET name = ?, staff_id = ?, updated_at = NOW() 
                        WHERE section_id = ?";
        $stmt = $pdo->prepare($updateQuery);
        $stmt->execute([$updatedName, $updatedTeacherId, $updatedSectionId]);

        if ($stmt->rowCount() > 0) {
            echo "<script>alert('Section updated successfully!'); window.location.href='sections.php';</script>";
        } else {
            echo "<script>alert('No changes were made or update failed.');</script>";
        }
    } catch (PDOException $e) {
        die("Database error: " . $e->getMessage());
    }
}
?>

<div class="wrapper">
    <?php include_once __DIR__ . "/../includes/sidebar.php"; ?>
    <div class="main-panel">
        <div class="container mt-4">
            <div class="card shadow">
                <div class="card-header bg-secondary text-white">
                    Edit Section
                </div>
                <div class="card-body">
                    <form action="" method="POST">
                        <input type="hidden" name="section_id" value="<?= $section['section_id'] ?>">

                        <!-- Class Name (Not Editable) -->
                        <div class="mb-3">
                            <label class="form-label">Class Name</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($section['class_name']) ?>" readonly>
                        </div>

                        <!-- Select Term -->
                        <div class="mb-3">
                            <label class="form-label">Select Term</label>
                            <select name="name" class="form-control" required>
                                <option value="First Term" <?= ($section['name'] == 'First Term') ? 'selected' : '' ?>>First Term</option>
                                <option value="Second Term" <?= ($section['name'] == 'Second Term') ? 'selected' : '' ?>>Second Term</option>
                                <option value="Third Term" <?= ($section['name'] == 'Third Term') ? 'selected' : '' ?>>Third Term</option>
                            </select>
                        </div>

                        <!-- Assign Teacher -->
                        <div class="mb-3">
                            <label class="form-label">Assign Teacher</label>
                            <select name="staff_id" id="staff_id" class="form-control" required>
                                <option value="">-- Select Teacher --</option>
                                <?php foreach ($teacherResult as $teacher) { ?>
                                    <option value="<?= $teacher['id'] ?>" <?= ($teacher['id'] == $section['staff_id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($teacher['firstname'] . ' ' . $teacher['lastname']) ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <!-- Session (Not Editable) -->
                        <div class="mb-3">
                            <label class="form-label">Current Session</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($activeSession['session_year']) ?>" readonly>
                        </div>

                        <button type="submit" class="btn btn-success">Update Section</button>
                        <a href="sections.php" class="btn btn-danger">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
