<?php
session_start();
if (isset($_SESSION['success_message'])) {
    echo '<div class="alert alert-success">' . $_SESSION['success_message'] . '</div>';
    unset($_SESSION['success_message']); // Clear the message after displaying it
}
if (isset($_SESSION['error_message'])) {
    echo '<div class="alert alert-danger">' . $_SESSION['error_message'] . '</div>';
    unset($_SESSION['error_message']); // Clear the error message after displaying it
}

$pageTitle = "Student Admission Form";

// Include necessary files
include_once __DIR__ . "/../config/database.php";
include_once __DIR__ . "/../includes/admin_header.php";

// Generate a unique Admission Number
function generateAdmissionNumber($pdo) {
    $year = date("Y");

    do {
        // Get the last inserted student ID
        $stmt = $pdo->query("SELECT MAX(student_id) as last_id FROM students");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $next_id = $row['last_id'] ? $row['last_id'] + 1 : 1;

        // Format the admission number
        $admission_no = "ADM" . $year . str_pad($next_id, 4, "0", STR_PAD_LEFT);

        // Check if the admission number already exists
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM students WHERE admission_no = ?");
        $stmt->execute([$admission_no]);
        $checkRow = $stmt->fetch(PDO::FETCH_ASSOC);
    } while ($checkRow['count'] > 0); // Loop until a unique admission number is found

    return $admission_no;
}

$admission_no = generateAdmissionNumber($pdo);

// Fetch dormitorys from the database
$transports = $pdo->query("SELECT * FROM transport ORDER BY transport_route ASC")->fetchAll(PDO::FETCH_ASSOC);

// Fetch dormitorys from the database
$dormitorys = $pdo->query("SELECT * FROM dormitory ORDER BY hostel_name ASC")->fetchAll(PDO::FETCH_ASSOC);

// Fetch sections from the database
$sections = $pdo->query("SELECT * FROM sections ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);


// Fetch houses from the database
$houses = $pdo->query("SELECT * FROM student_house ORDER BY house_name ASC")->fetchAll(PDO::FETCH_ASSOC);

// Fetch clubs from the database
$clubs = $pdo->query("SELECT * FROM clubs ORDER BY club_name ASC")->fetchAll(PDO::FETCH_ASSOC);

// Fetch classes from the database
$classes = $pdo->query("SELECT * FROM classes ORDER BY class_name ASC")->fetchAll(PDO::FETCH_ASSOC);

// Fetch available sessions
$sessions = $pdo->query("SELECT * FROM sessions ORDER BY session_year DESC")->fetchAll(PDO::FETCH_ASSOC);


// List of Nigerian States
$nigerian_states = [
    "Abia", "Adamawa", "Akwa Ibom", "Anambra", "Bauchi", "Bayelsa", "Benue", "Borno", "Cross River", "Delta", "Ebonyi",
    "Edo", "Ekiti", "Enugu", "Gombe", "Imo", "Jigawa", "Kaduna", "Kano", "Katsina", "Kebbi", "Kogi", "Kwara", "Lagos",
    "Nasarawa", "Niger", "Ogun", "Ondo", "Osun", "Oyo", "Plateau", "Rivers", "Sokoto", "Taraba", "Yobe", "Zamfara", "FCT Abuja"
];
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
                            src="../assets/images/logo_light.svg"
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
                             <?php if (isset($_SESSION['success_message'])) : ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo $_SESSION['success_message']; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php unset($_SESSION['success_message']); ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['error_message'])) : ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo $_SESSION['error_message']; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php unset($_SESSION['error_message']); ?>
                <?php endif; ?>
                <div class="card shadow">
                    <div class="card-header bg-secondary text-white">
                        <h4 class="mb-0">Student Admission Form</h4>
                    </div>
                    <div class="card-body">
                        <form action="save_admission.php" method="POST" enctype="multipart/form-data">
                            
                            <!-- PART A: Student Information -->
                            <div class="border p-3 mb-4">
                                <h5 class="bg-light p-2">PART A: Student Information</h5>
                                <div class="row">

                                    <!-- Passport Preview -->
                                    <div class="col-md-12 mb-3">
                                        <label>Preview</label><br>
                                        <img id="passportPreview" src="../assets/images/default-avatar.jpg" alt="Passport Preview" class="img-thumbnail" style="width: 150px; height: 150px;">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label>Admission No (Auto-generated)</label>
                                        <input type="text" class="form-control" name="admission_no" value="<?= $admission_no ?>" readonly>
                                    </div>

                                     <div class="col-md-6 mb-3">
                                        <label>Passport Photo</label>
                                        <input type="file" class="form-control" name="passport" accept="image/*" required onchange="previewPassport(event)">
                                    </div>

                                    

                                    <div class="col-md-4 mb-3">
                                        <label>First Name</label>
                                        <input type="text" class="form-control" name="firstname" required>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label>Middle Name</label>
                                        <input type="text" class="form-control" name="middlename">
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label>Last Name</label>
                                        <input type="text" class="form-control" name="lastname" required>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label>Birthday</label>
                                        <input type="date" class="form-control" name="birthday" required>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label>Place of Birth</label>
                                        <input type="text" class="form-control" name="place_birth" required>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label>Gender</label>
                                        <select class="form-control" name="gender" required>
                                            <option value="Male">Male</option>
                                            <option value="Female">Female</option>
                                        </select>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label>Religion</label>
                                           <select class="form-control" name="religion">
                                                <option value="Christianity">Christianity</option>
                                                <option value="Islam">Islam</option>
                                                <option value="Other">Other</option>
                                            </select>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label>Blood Group</label>
                                        <input type="text" class="form-control" name="blood_group">
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label>Phone</label>
                                        <input type="text" class="form-control" name="phone">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label>Email</label>
                                        <input type="email" class="form-control" name="email">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label>Address</label>
                                        <input type="text" class="form-control" name="address" required>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label>State</label>
                                        <select class="form-control" name="state" required>
                                            <option value="">Select State</option>
                                            <?php foreach ($nigerian_states as $state) : ?>
                                                <option value="<?= $state ?>"><?= $state ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label>L.G.A</label>
                                        <input type="text" class="form-control" name="lga" required>
                                    </div>

                                    
                                    <div class="col-md-4 mb-3">
                                        <label>Nationality</label>
                                        <input type="text" class="form-control" name="nationality" value="Nigeria" required>
                                    </div>

                                   

                                </div>
                            </div>

                            <!-- PART B: Additional Information & Parent Details -->
                            <div class="border p-3">
                                <h5 class="bg-light p-2">PART B: Additional Information & Parent Details</h5>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label>Father's Name</label>
                                        <input type="text" class="form-control" name="father_name">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label>Mother's Name</label>
                                        <input type="text" class="form-control" name="mother_name">
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label>Previous School Attended</label>
                                        <input type="text" class="form-control" name="previous_attended">
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label>Previous School Address</label>
                                        <input type="text" class="form-control" name="previous_address">
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label>Purpose of Leaving</label>
                                        <input type="text" class="form-control" name="purpose_of_leaving">
                                    </div>

                                    <div class="col-md-3 mb-3">
                                        <label>Admission Date</label>
                                        <input type="date" class="form-control" name="admission_date">
                                    </div>

                                    <div class="col-md-3 mb-3">
                                        <label>Class of Study</label>
                                        <select class="form-control" name="class" id="classSelect" required>
                                            <option value="">Select Class</option>
                                            <?php foreach ($classes as $class) : ?>
                                                <option value="<?= $class['class_id'] ?>">
                                                    <?= htmlspecialchars($class['class_name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="col-md-3 mb-3">
                                        <label>Section</label>
                                        <select class="form-control" name="section" id="sectionSelect" required>
                                            <option value="">Select Section</option>
                                        </select>
                                    </div>

                                    <div class="col-md-3 mb-3">
                                        <label>Session</label>
                                        <input type="text" class="form-control" name="session" value="<?= htmlspecialchars($sessions[0]['session_year']) ?>" readonly>
                                    </div>



                                    <div class="col-md-4 mb-3">
                                        <label>Student House</label>
                                        <select class="form-control" name="house" required>
                                            <option value="">Select House</option>
                                            <?php foreach ($houses as $house) : ?>
                                                <option value="<?= $house['house_id'] ?>">
                                                    <?= htmlspecialchars($house['house_name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label>Transportation</label>
                                        <select class="form-control" name="transport" >
                                            <option value="">Select Transport Route</option>
                                            <?php foreach ($transports as $transport) : ?>
                                                <option value="<?= $transport['transport_id'] ?>">
                                                    <?= htmlspecialchars($transport['transport_route']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label>Dormitory</label>
                                        <select class="form-control" name="dormitory" >
                                            <option value="">Select Hostel</option>
                                            <?php foreach ($dormitorys as $dormitory) : ?>
                                                <option value="<?= $dormitory['dormitory_id'] ?>">
                                                    <?= htmlspecialchars($dormitory['hostel_name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    
                                    

                                    <div class="col-md-3 mb-3">
                                        <label>Transfer Certificate</label>
                                        <select class="form-control" name="transfer_cert" required>
                                            <option value="Yes">Yes</option>
                                            <option value="No">No</option>
                                        </select>
                                    </div>

                                    <div class="col-md-3 mb-3">
                                        <label>Physical Handicap</label>
                                        <select class="form-control" name="physical_handicap">
                                            <option value="None">None</option>
                                            <option value="Yes">Yes</option>
                                        </select>
                                    </div>

                                    


                                    <div class="col-md-3 mb-3">
                                        <label>Student Category</label>
                                        <select class="form-control" name="student_category" required>
                                            <option value="Boarding">Boarding</option>
                                            <option value="Day">Day</option>
                                        </select>
                                    </div>

                                    <div class="col-md-3 mb-3">
                                        <label>Student Club</label>
                                        <select class="form-control" name="club" required>
                                            <option value="">Select Club</option>
                                            <?php foreach ($clubs as $club) : ?>
                                                <option value="<?= $club['club_id'] ?>">
                                                    <?= htmlspecialchars($club['club_name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>


                                    <div class="col-md-4 mb-3">
                                        <label>Login Status</label>
                                        <select class="form-control" name="login_status">
                                            <option value="1">Active</option>
                                            <option value="0">Inactive</option>
                                        </select>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label>Password</label>
                                        <input type="password" class="form-control" name="password" required>
                                    </div>
                                     <!-- Submit Button -->
                            <div class="col-md-4 mb-3 mt-4">
                                <button type="submit" class="btn btn-success">Submit Admission</button>
                            </div>

                                </div>
                            </div>

                           
                        </form>
                    </div>
                </div>
            </div>
    </div>
</div>
<script>
function previewPassport(event) {
    const input = event.target;
    const preview = document.getElementById('passportPreview');

    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.src = e.target.result;
        };

        reader.readAsDataURL(input.files[0]);
    }
}

document.getElementById("classSelect").addEventListener("change", function() {
    let classId = this.value;
    let sectionSelect = document.getElementById("sectionSelect");
    
    // Clear previous options
    sectionSelect.innerHTML = '<option value="">Select Section</option>';
    
    if (classId) {
        fetch('get_sections_admission_form.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'id=' + classId
        })
        .then(response => response.json())
        .then(data => {
            data.forEach(section => {
                let option = document.createElement("option");
                option.value = section.id;
                option.textContent = section.name;
                sectionSelect.appendChild(option);
            });
        })
        .catch(error => console.error('Error:', error));
    }
});
</script>


<!--   Core JS Files   -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/core/popper.min.js"></script>
<script src="../assets/js/core/bootstrap.min.js"></script>

<!-- jQuery Scrollbar -->
<script src="../assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>
<script src="../assets/js/plugin/jquery.sparkline/jquery.sparkline.min.js"></script>

<!-- Bootstrap Notify -->
<script src="../assets/js/plugin/bootstrap-notify/bootstrap-notify.min.js"></script>

<!-- Sweet Alert -->
<script src="../assets/js/plugin/sweetalert/sweetalert.min.js"></script>

<!-- Kaiadmin JS -->
<script src="../assets/js/kaiadmin.min.js"></script>
</body>
</html>
