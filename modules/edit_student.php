<?php
session_start();
$pageTitle = "Edit Student Information";

// Include necessary files
include_once __DIR__ . "/../config/database.php";
include_once __DIR__ . "/../includes/admin_header.php";

// Ensure student ID is provided
if (!isset($_GET['id'])) {
    die("Student ID is required.");
}

$student_id = $_GET['id'];

// List of Nigerian States
$nigerian_states = [
    "Abia", "Adamawa", "Akwa Ibom", "Anambra", "Bauchi", "Bayelsa", "Benue", "Borno", "Cross River", "Delta", "Ebonyi",
    "Edo", "Ekiti", "Enugu", "Gombe", "Imo", "Jigawa", "Kaduna", "Kano", "Katsina", "Kebbi", "Kogi", "Kwara", "Lagos",
    "Nasarawa", "Niger", "Ogun", "Ondo", "Osun", "Oyo", "Plateau", "Rivers", "Sokoto", "Taraba", "Yobe", "Zamfara", "FCT Abuja"
];

// Fetch transport, clubs, dormitory, and house data
$transports = $pdo->query("SELECT * FROM transport ORDER BY transport_route ASC")->fetchAll(PDO::FETCH_ASSOC);
$clubs = $pdo->query("SELECT * FROM clubs ORDER BY club_name ASC")->fetchAll(PDO::FETCH_ASSOC);
$dormitorys = $pdo->query("SELECT * FROM dormitory ORDER BY hostel_name ASC")->fetchAll(PDO::FETCH_ASSOC);
$houses = $pdo->query("SELECT * FROM student_house ORDER BY house_name ASC")->fetchAll(PDO::FETCH_ASSOC);

// Fetch student details
$stmt = $pdo->prepare("SELECT * FROM students WHERE student_id = ?");
$stmt->execute([$student_id]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$student) {
    die("Student not found.");
}

// Fetch classes
$classes = $pdo->query("SELECT class_id, class_name FROM classes ORDER BY class_name ASC")->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstname = $_POST['firstname'];
    $middlename = $_POST['middlename'];
    $lastname = $_POST['lastname'];
    $birthday = $_POST['birthday'];
    $place_birth = $_POST['place_birth'];
    $gender = $_POST['gender'];
    $religion = $_POST['religion'];
    $blood_group = $_POST['blood_group'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $state = $_POST['state'];
    $lga = $_POST['lga'];
    $nationality = $_POST['nationality'];
    $father_name = $_POST['father_name'];
    $mother_name = $_POST['mother_name'];
    $class_id = $_POST['class_id'];
    $house = $_POST['house'];
    $transport = $_POST['transport'];
    $dormitory = $_POST['dormitory'];
    $student_category = $_POST['student_category'];
    $club = $_POST['club'];
    $login_status = $_POST['login_status'];

    // Handle Passport Upload
    if (!empty($_FILES['passport']['name'])) {
        $targetDir = __DIR__ . "/../uploads/passports/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $fileName = $student['admission_no'] . "_" . basename($_FILES["passport"]["name"]);
        $targetFilePath = $targetDir . $fileName;
        $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($fileType, $allowedTypes) && move_uploaded_file($_FILES["passport"]["tmp_name"], $targetFilePath)) {
            $passportPath = "uploads/passports/" . $fileName;
        } else {
            die("Error uploading passport.");
        }
    } else {
        $passportPath = $student['passport'];
    }

    // Prepare the update query
    $sql = "UPDATE students SET firstname = ?, middlename = ?, lastname = ?, birthday = ?, place_birth = ?, gender = ?, religion = ?, blood_group = ?, phone = ?, email = ?, address = ?, state = ?, lga = ?, nationality = ?, father_name = ?, mother_name = ?, class_id = ?, house_id = ?, transport_id = ?, dormitory_id = ?, student_category = ?, club_id = ?, passport = ?, login_status = ?";

    // Check if password is provided
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $sql .= ", password = ?";
    }

    $sql .= " WHERE student_id = ?";

    // Execute the query
    $stmt = $pdo->prepare($sql);

    if (!empty($_POST['password'])) {
        $stmt->execute([$firstname, $middlename, $lastname, $birthday, $place_birth, $gender, $religion, $blood_group, $phone, $email, $address, $state, $lga, $nationality, $father_name, $mother_name, $class_id, $house, $transport, $dormitory, $student_category, $club, $passportPath, $login_status, $password, $student_id]);
    } else {
        $stmt->execute([$firstname, $middlename, $lastname, $birthday, $place_birth, $gender, $religion, $blood_group, $phone, $email, $address, $state, $lga, $nationality, $father_name, $mother_name, $class_id, $house, $transport, $dormitory, $student_category, $club, $passportPath, $login_status, $student_id]);
    }

    echo "<script>alert('Student updated successfully!'); window.location.href='student_information.php';</script>";
}
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
            <div class="card shadow">
                <div class="card-header bg-secondary text-white">
                    <h4 class="mb-0">Edit Student</h4>
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">


                        <!-- PART A: Student Information -->
                        <div class="border p-3 mb-4">
                                <h5 class="bg-light p-2">PART A: Student Information</h5>
                            <div class="row">
                                <!-- Passport Preview -->
                                    <div class="col-md-2 col-6 mb-3">
                                        <label>Current Passport:</label><br>
                                        <img src="../<?= htmlspecialchars($student['passport']); ?>" alt="Passport" width="100" height="100">
                                    </div>

                                    <div class="col-md-2 col-6 mb-3">
                                        <label>Admission No </label>
                                        <span> <?php echo $student['admission_no'];?></span>
                                        
                                    </div>

                                                    

                                    <div class="col-md-3 col-6 mb-3">
                                        <label>First Name</label>
                                        <input type="text" name="firstname" class="form-control" value="<?= htmlspecialchars($student['firstname']); ?>" >
                                    </div>

                                    <div class="col-md-2 col-6 mb-3">
                                        <label>Middle Name</label>
                                        <input type="text" name="middlename" class="form-control" value="<?= htmlspecialchars($student['middlename']); ?>" >
                                    </div>

                                    <div class="col-md-3 col-6 mb-3">
                                        <label>Last Name</label>
                                        <input type="text" name="lastname" class="form-control" value="<?= htmlspecialchars($student['lastname']); ?>">
                                    </div>
                                    <div class="col-md-4 col-6 mb-3">
                                        <label>Birthday</label>
                                        <input type="text" name="birthday" class="form-control" value="<?= htmlspecialchars($student['birthday']); ?>">
                                    </div>

                                    <div class="col-md-4 col-6 mb-3">
                                        <label>Place of Birth</label>
                                       <input type="text" name="place_birth" class="form-control" value="<?= htmlspecialchars($student['place_birth']); ?>" >
                                    </div>

                                    
                                    <div class="col-md-4 col-6 mb-3">
                                        <label>Gender</label>
                                        <select class="form-control" name="gender" required>
                                            <option value="">Select Gender</option>
                                            <option value="Male">Male</option>
                                            <option value="Female">Female</option>
                                        </select>
                                    </div>

                                    <div class="col-md-4 col-4 mb-3">
                                        <label>Religion</label>
                                           <select class="form-control" name="religion" required>
                                                <option value="">Select Religion</option>
                                                <option value="Christianity">Christianity</option>
                                                <option value="Islam">Islam</option>
                                                <option value="Other">Other</option>
                                            </select>
                                    </div>

                                    <div class="col-md-4 col-4 mb-3">
                                        <label>Blood Group</label>
                                        <input type="text" class="form-control" name="blood_group" value="<?= htmlspecialchars($student['blood_group']); ?>">
                                    </div>

                                    <div class="col-md-4 col-4 mb-3">
                                        <label>Phone</label>
                                        <input type="text" class="form-control" name="phone" value="<?= htmlspecialchars($student['phone']); ?>">
                                    </div>

                                    <div class="col-md-6 col-6 mb-3">
                                        <label>Email</label>
                                        <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($student['email']); ?>">
                                    </div>

                                    <div class="col-md-6 col-6 mb-3">
                                        <label>Address</label>
                                        <input type="text" class="form-control" name="address" value="<?= htmlspecialchars($student['address']); ?>">
                                    </div>


                                    <div class="col-md-4 col-4 mb-3">
                                        <label>State</label>
                                        <select class="form-control" name="state" required>
                                           <?php foreach ($nigerian_states as $state) : ?>
                                                <option value="<?= $state ?>"><?= $state ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="col-md-4 col-4 mb-3">
                                        <label>L.G.A</label>
                                        <input type="text" class="form-control" name="lga" value="<?= htmlspecialchars($student['lga']); ?>">
                                    </div>

                                    
                                    <div class="col-md-4 col-4 mb-3">
                                        <label>Nationality</label>
                                        <input type="text" class="form-control" name="nationality" value="Nigeria" value="<?= htmlspecialchars($student['nationality']); ?>">
                                    </div>

                            </div>

                        </div>

                        <div class="border p-3">
                                <h5 class="bg-light p-2">PART B: Additional Information & Parent Details</h5>
                                <div class="row">
                                    <div class="col-md-6 col-6 mb-3">
                                        <label>Father's Name</label>
                                        <input type="text" class="form-control" name="father_name" value="<?= htmlspecialchars($student['father_name']); ?>">
                                    </div>

                                    <div class="col-md-6 col-6 mb-3">
                                        <label>Mother's Name</label>
                                        <input type="text" class="form-control" name="mother_name" value="<?= htmlspecialchars($student['mother_name']); ?>">
                                    </div>

                                    
                                    <div class="col-md-3 col-3 mb-3">
                                        <label>Class of Study</label>
                                        <select name="class_id" class="form-control">
                                            <?php foreach ($classes as $class): ?>
                                                <option value="<?= $class['class_id']; ?>" <?= ($student['class_id'] == $class['class_id']) ? 'selected' : ''; ?>>
                                                    <?= htmlspecialchars($class['class_name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>

                                    </div>

                                    <div class="col-md-3 col-3 mb-3">
                                        <label>Student House</label>
                                        <select class="form-control" name="house">
                                            <?php foreach ($houses as $house) : ?>
                                                <option value="<?= $house['house_id'] ?>" <?= ($student['house_id'] == $house['house_id']) ? 'selected' : ''; ?>>
                                                    <?= htmlspecialchars($house['house_name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="col-md-3 col-3 mb-3">
                                        <label>Transportation</label>
                                        <select class="form-control" name="transport" >
                                            <?php foreach ($transports as $transport) : ?>
                                                <option value="<?= $transport['transport_id'] ?>" <?= ($student['transport_id'] == $transport['transport_id']) ? 'selected' : ''; ?>>
                                                    <?= htmlspecialchars($transport['transport_route']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="col-md-3 col-3 mb-3">
                                        <label>Dormitory</label>
                                        <select class="form-control" name="dormitory" >
                                            <?php foreach ($dormitorys as $dormitory) : ?>
                                                <option value="<?= $dormitory['dormitory_id'] ?>" <?= ($student['dormitory_id'] == $dormitory['dormitory_id']) ? 'selected' : ''; ?>>
                                                    <?= htmlspecialchars($dormitory['hostel_name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
 

                                    <div class="col-md-4 col-6 mb-3">
                                        <label>Student Category</label>
                                        <select class="form-control" name="student_category" required>
                                            <option value="">Select Student Category</option>
                                            <option value="Boarding">Boarding</option>
                                            <option value="Day">Day</option>
                                        </select>
                                    </div>

                                    <div class="col-md-4 col-6 mb-3">
                                        <label>Student Club</label>
                                        <select class="form-control" name="club" required>
                                            <?php foreach ($clubs as $club) : ?>
                                                <option value="<?= $club['club_id'] ?>" <?= ($student['club_id'] == $club['club_id']) ? 'selected' : ''; ?>>
                                                    <?= htmlspecialchars($club['club_name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>


                                    <div class="col-md-4 col-6 mb-3">
                                        <label>Login Status</label>
                                        <select name="login_status" class="form-control">
                                            <option value="1" <?= ($student['login_status'] == 'active') ? 'selected' : ''; ?>>Active</option>
                                            <option value="0" <?= ($student['login_status'] == 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                                        </select>

                                    </div>

                                   <!-- <div class="col-md-3 col-6 mb-3">
                                        <label>Password</label>
                                        <input type="hidden" class="form-control" name="password" required>
                                    </div> -->
                                </div>
                        </div>

                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-success">Update Student</button>
                            <a href="student_information.php" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
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
