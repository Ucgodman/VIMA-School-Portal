<?php
session_start();
$pageTitle = "Edit Category";

include_once __DIR__ . "/../config/database.php";
include_once __DIR__ . "/../functions/helper_functions.php";
include_once __DIR__ . "/../includes/admin_header.php";

// Check if enquiry_id is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error'] = "Invalid category ID!";
    header("Location: enquiry_category.php");
    exit;
}

$enquiry_category_id = $_GET['id'];

// Fetch the category details
$stmt = $pdo->prepare("SELECT * FROM enquiry_category WHERE enquiry_category_id = :enquiry_category_id");
$stmt->execute([':enquiry_category_id' => $enquiry_category_id]);
$category = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$category) {
    $_SESSION['error'] = "Category not found!";
    header("Location: enquiry_category.php");
    exit;
}

// Handle form submission for updating category
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $category_name = trim($_POST['category']);
    $purpose = trim($_POST['purpose']);
    $whom = trim($_POST['whom']);

    if (!empty($category_name) && !empty($purpose) && !empty($whom)) {
        try {
            $stmt = $pdo->prepare("UPDATE enquiry_category SET category = :category, purpose = :purpose, whom = :whom WHERE enquiry_category_id = :enquiry_category_id");
            $stmt->execute([
                ':category' => $category_name,
                ':purpose' => $purpose,
                ':whom' => $whom,
                ':enquiry_category_id' => $enquiry_category_id
            ]);

            $_SESSION['success'] = "Category updated successfully!";
            header("Location: enquiry_category.php");
            exit;
        } catch (PDOException $e) {
            $_SESSION['error'] = "Database error: " . $e->getMessage();
        }
    } else {
        $_SESSION['error'] = "All fields are required!";
    }
}
?>

<div class="wrapper">
    <!-- Sidebar -->
    <?php include_once __DIR__ . "/../includes/sidebar.php"; ?>
    <!-- End Sidebar -->

    <div class="main-panel">
        <div class="main-header">
            <!-- Navbar -->
            <?php include_once __DIR__ . "/../includes/navbar.php"; ?>
            <!-- End Navbar -->
        </div>

        <div class="container mt-4">
            <div class="page-inner">
                <div class="row">
                    <div class="col-md-8 offset-md-2">
                        <?php if (isset($_SESSION['success'])) : ?>
                            <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
                        <?php elseif (isset($_SESSION['error'])) : ?>
                            <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
                        <?php endif; ?>

                        <div class="card">
                            <div class="card-header bg-secondary text-white">
                                <h4>Edit Enquiry Category</h4>
                            </div>
                            <div class="card-body">
                                <form action="" method="POST">
                                    <div class="mb-3">
                                        <label for="category" class="form-label">Category</label>
                                        <input type="text" class="form-control" name="category" value="<?= htmlspecialchars($category['category']); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="purpose" class="form-label">Purpose</label>
                                        <input type="text" class="form-control" name="purpose" value="<?= htmlspecialchars($category['purpose']); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="whom" class="form-label">Whom</label>
                                        <input type="text" class="form-control" name="whom" value="<?= htmlspecialchars($category['whom']); ?>" required>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <a href="enquiry_category.php" class="btn btn-secondary">Back</a>
                                        <button type="submit" class="btn btn-primary">Update Category</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- End main-panel -->
</div> <!-- End wrapper -->

<!-- Core JS Files -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
