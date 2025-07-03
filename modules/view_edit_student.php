<?php
session_start();

$pageTitle = "View/Edit Student";
include_once __DIR__ . "/../config/database.php";
include_once __DIR__ . "/../functions/helper_functions.php";
include_once __DIR__ . "/../includes/admin_header.php";

$student_id = $_SESSION['user']['id']?? null;

// Fetch student data
$stmt = $pdo->prepare("SELECT * FROM students WHERE student_id = ?");
$stmt->execute([$student_id]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$student_id) {
    if (isset($_SESSION['user']) && $_SESSION['user']['type'] === 'admin') {
        echo '<div class="container mt-5">
                <a href="../modules/index.php" class="btn btn-secondary mb-3">← Back to Dashboard</a>
                <p>No student selected. Please choose a student from the dashboard.</p>
              </div>';
        exit;
    } else {
        die("Student ID is required.");
    }
}

// Fetch dormitorys from the database
$transports = $pdo->query("SELECT * FROM transport ORDER BY transport_route ASC")->fetchAll(PDO::FETCH_ASSOC);

// Fetch dormitorys from the database
$dormitorys = $pdo->query("SELECT * FROM dormitory ORDER BY hostel_name ASC")->fetchAll(PDO::FETCH_ASSOC);

// Fetch houses from the database
$houses = $pdo->query("SELECT * FROM student_house ORDER BY house_name ASC")->fetchAll(PDO::FETCH_ASSOC);

// Fetch clubs from the database
$clubs = $pdo->query("SELECT * FROM clubs ORDER BY club_name ASC")->fetchAll(PDO::FETCH_ASSOC);


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
                        <h4 class="mb-0">Edit Student Information</h4>
                    </div>
                    <div class="card-body">
                       <form action="update_student_profile.php" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="student_id" value="<?= htmlspecialchars($student['student_id']) ?>">

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
                                        <input type="text" class="form-control" name="admission_no" value="<?= htmlspecialchars($student['admission_no']) ?>" readonly>
                                    </div>

                                     <div class="col-md-6 mb-3">
                                        <label>Passport Photo</label>
                                        <input type="file" class="form-control" name="passport" accept="image/*"  onchange="previewPassport(event)">
                                    </div>

                                    

                                    <div class="col-md-4 mb-3">
                                        <label>First Name</label>
                                        <input type="text" name="firstname" class="form-control" value="<?= htmlspecialchars($student['firstname']) ?>" readonly>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label>Middle Name</label>
                                        <input type="text" name="middlename" class="form-control" value="<?= htmlspecialchars($student['middlename']) ?>" readonly>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label>Last Name</label>
                                        <input type="text" name="lastname"class="form-control" value="<?= htmlspecialchars($student['lastname']) ?>" readonly>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label>Birthday</label>
                                        <input type="date" name="birthday" value="<?= htmlspecialchars($student['birthday']) ?>" class="form-control"readonly>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label>Place of Birth</label>
                                        <input type="text" name="place_birth" value="<?= htmlspecialchars($student['place_birth']) ?>" class="form-control" readonly>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label>Gender</label>
                                        <select name="gender" class="form-select">
                                        <option value="Male" <?= $student['gender'] == 'Male' ? 'selected' : '' ?>>Male</option>
                                        <option value="Female" <?= $student['gender'] == 'Female' ? 'selected' : '' ?>>Female</option>
                                    </select>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label>Religion</label>
                                           <input type="text" name="religion" value="<?= htmlspecialchars($student['religion']) ?>" class="form-control"readonly>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label>Blood Group</label>
                                        <input type="text" name="blood_group" value="<?= htmlspecialchars($student['blood_group']) ?>" class="form-control" readonly>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label>Phone</label>
                                       <input type="text" name="phone" value="<?= htmlspecialchars($student['phone']) ?>" class="form-control">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label>Email</label>
                                        <input type="email" name="email" value="<?= htmlspecialchars($student['email']) ?>" class="form-control">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label>Address</label>
                                       <input type="text" name="address" value="<?= htmlspecialchars($student['address']) ?>" class="form-control">
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label>State</label>
                                        <input type="text" name="state" value="<?= htmlspecialchars($student['state']) ?>" class="form-control" readonly>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label>L.G.A</label>
                                        <input type="text" name="lga" value="<?= htmlspecialchars($student['lga']) ?>" class="form-control" readonly>
                                    </div>

                                    
                                    <div class="col-md-4 mb-3">
                                        <label>Nationality</label>
                                        <input type="text" name="nationality" value="<?= htmlspecialchars($student['nationality']) ?>" class="form-control" readonly>
                                    </div>

                                   

                                </div>
                            </div>

                            <!-- PART B: Additional Information & Parent Details -->
                            <div class="border p-3">
                                <h5 class="bg-light p-2">PART B: Additional Information & Parent Details</h5>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label>Father's Name</label>
                                       <input type="text" name="father_name" value="<?= htmlspecialchars($student['father_name']) ?>" class="form-control" readonly>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label>Mother's Name</label>
                                        <input type="text" name="mother_name" value="<?= htmlspecialchars($student['mother_name']) ?>" class="form-control" readonly>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label>Previous School Attended</label>
                                        <input type="text" name="previous_attended" value="<?= htmlspecialchars($student['previous_attended']) ?>" class="form-control" readonly>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label>Previous School Address</label>
                                        <input type="text" name="previous_address" value="<?= htmlspecialchars($student['previous_address']) ?>" class="form-control" readonly>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label>Purpose of Leaving</label>
                                        <input type="text" name="purpose_of_leaving" value="<?= htmlspecialchars($student['purpose_of_leaving']) ?>" class="form-control" readonly>                              
                                    </div>

                                    <div class="col-md-3 mb-3">
                                        <label>Admission Date</label>
                                        <input type="text" name="admission_date" value="<?= htmlspecialchars($student['admission_date']) ?>" class="form-control" readonly> 
                                    </div>

                                    <div class="col-md-3 mb-3">
                                        <label>Class of Study</label>
                                       <input type="text" class="form-control" name="class_id" value="<?= $student['class_id'] ?>" readonly>
                                   
                                    </div>

                                    <div class="col-md-3 mb-3">
                                        <label>Section</label>
                                        <input type="text" class="form-control" name="section_id" value="<?= $student['section_id'] ?>" readonly>
                                    </div>

                                    <div class="col-md-3 mb-3">
                                        <label>Session</label>
                                        <input type="text" class="form-control" name="session_id" value="<?= $student['session_id'] ?>" readonly>
                                    </div>



                                    <div class="col-md-4 mb-3">
                                        <label>Student House</label>
                                        <select class="form-control" name="house_id" required>
                                        <option value="">Select House</option>
                                        <?php foreach ($houses as $house) : ?>
                                            <option value="<?= $house['house_id'] ?>" <?= ($student['house_id'] == $house['house_id']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($house['house_name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label>Transportation</label>
                                        <select class="form-control" name="transport_id" required>
                                        <option value="">Select Transport Route</option>
                                        <?php foreach ($transports as $transport) : ?>
                                            <option value="<?= $transport['transport_id'] ?>" <?= ($student['transport_id'] == $transport['transport_id']) ? 'selected' : '' ?>>
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
                                         <input type="text" class="form-control" name="transfer_cert" value="<?= $student['transfer_cert'] ?>" readonly>
                                    </div>

                                    <div class="col-md-3 mb-3">
                                        <label>Physical Handicap</label>
                                        <input type="text" class="form-control" name="physical_handicap" value="<?= $student['physical_handicap'] ?>" readonly>
                                    </div>

                                    
                                    <div class="col-md-3 mb-3">
                                        <label>Student Category</label>
                                        <input type="text" name="student_category" value="<?= htmlspecialchars($student['student_category']) ?>" class="form-control" readonly>
                                    </div>

                                    <div class="col-md-3 mb-3">
                                        <label>Student Club</label>
                                       <select class="form-control" name="club_id" required>
                                        <option value="">Select Club</option>
                                        <?php foreach ($clubs as $club) : ?>
                                            <option value="<?= $club['club_id'] ?>" <?= ($student['club_id'] == $club['club_id']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($club['club_name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    </div>


                                    <div class="col-md-4 mb-3">
                                        <label>Login Status</label>
                                         <input type="text" name="login_status" value="<?= htmlspecialchars($student['login_status']) ?>" class="form-control" readonly>                                      
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label>Password</label>
                                        <input type="password" name="password" class="form-control" placeholder="Enter new password">
                                    </div>
                                     <!-- Submit Button -->
                            <div class="col-md-4 mb-3 mt-4">
                                <button type="submit" class="btn btn-primary">Update Student</button>
                                <a href="dashboard.php" class="btn btn-secondary">Cancel</a>

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
</script>
<script src="../assets/js/core/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</body>
</html>
