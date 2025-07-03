<?php


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include_once __DIR__ . "/../config/database.php";

    try {
        // Retrieve form values safely
        $firstname = trim($_POST['firstname'] ?? '');
        $middlename = trim($_POST['middlename'] ?? '');
        $lastname = trim($_POST['lastname'] ?? '');
        $admission_no = trim($_POST['admission_no'] ?? '');
        $birthday = trim($_POST['birthday'] ?? '');
        $place_birth = trim($_POST['place_birth'] ?? '');
        $gender = trim($_POST['gender'] ?? '');
        $religion = trim($_POST['religion'] ?? '');
        $blood_group = trim($_POST['blood_group'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $lga = trim($_POST['lga'] ?? '');
        $state = trim($_POST['state'] ?? '');
        $nationality = trim($_POST['nationality'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $previous_attended = trim($_POST['previous_attended'] ?? '');
        $previous_address = trim($_POST['previous_address'] ?? '');
        $purpose_of_leaving = trim($_POST['purpose_of_leaving'] ?? '');
        $class_id = $_POST['class'] ?? null;
        $admission_date = $_POST['admission_date'] ?? null;
        $transfer_cert = trim($_POST['transfer_cert'] ?? '');
        $physical_handicap = trim($_POST['physical_handicap'] ?? '');
        $father_name = trim($_POST['father_name'] ?? '');
        $mother_name = trim($_POST['mother_name'] ?? '');
        $section_id = $_POST['section'] ?? null;
        $transport_id = $_POST['transport'] ?? null;
        $dormitory_id = $_POST['dormitory'] ?? null;
        $club_id = $_POST['club'] ?? null;
        $house_id = $_POST['house'] ?? null;
        $session_id = $_POST['session'] ?? null;
        $student_category = $_POST['student_category'] ?? null;
        $login_status = $_POST['login_status'] ?? 1;
        $created_at = date("Y-m-d H:i:s");
        $updated_at = date("Y-m-d H:i:s");

        // Ensure admission number is unique
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM students WHERE admission_no = ?");
        $stmt->execute([$admission_no]);
        if ($stmt->fetchColumn() > 0) {
            $_SESSION['error_message'] = "Admission number already exists.";
            header("Location: admission_form.php");
            exit();
        }

        // Ensure email is unique
        if (!empty($email)) {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM students WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetchColumn() > 0) {
                $_SESSION['error_message'] = "Email already exists.";
                header("Location: admission_form.php");
                exit();
            }
        }

        // Set default password if empty
        $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : password_hash("123456", PASSWORD_DEFAULT);

        // Ensure uploads directory exists
        $targetDir = __DIR__ . "/../uploads/passports/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        // Handle Passport Upload
        $passportPath = "uploads/passports/default-avatar.jpg"; // Default image
        if (!empty($_FILES['passport']['name']) && $_FILES['passport']['error'] === 0) {
            $fileName = $admission_no . "_" . basename($_FILES["passport"]["name"]);
            $targetFilePath = $targetDir . $fileName;
            $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
            $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

            if (in_array($fileType, $allowedTypes)) {
                if (move_uploaded_file($_FILES["passport"]["tmp_name"], $targetFilePath)) {
                    $passportPath = "uploads/passports/" . $fileName;
                } else {
                    $_SESSION['error_message'] = "Error uploading passport.";
                    header("Location: admission_form.php");
                    exit();
                }
            } else {
                $_SESSION['error_message'] = "Invalid file type. Only JPG, JPEG, PNG, and GIF allowed.";
                header("Location: admission_form.php");
                exit();
            }
        }
        
        $stmt = $pdo->prepare("SELECT id FROM sessions WHERE session_year = ?");
            $stmt->execute([$session_id]);
            $session = $stmt->fetch();

            if ($session) {
                $session_id = $session['id']; // Use the ID
            } else {
                die("Error: Session not found.");
            }

      

        // Insert into database
       $stmt = $pdo->prepare("INSERT INTO students (
    firstname, middlename, lastname, admission_no, birthday, place_birth, gender, religion, blood_group, address, lga, state, nationality, phone, email, 
    previous_attended, previous_address, purpose_of_leaving, class_id, admission_date, transfer_cert, physical_handicap, passport, password, 
    father_name, mother_name, section_id, transport_id, dormitory_id, club_id, house_id, student_category, session_id, login_status, created_at, updated_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            $stmt->execute([
                $firstname, $middlename, $lastname, $admission_no, $birthday, $place_birth, $gender, $religion, $blood_group, $address, $lga, $state, $nationality, 
                $phone, $email, $previous_attended, $previous_address, $purpose_of_leaving, $class_id, $admission_date, $transfer_cert, $physical_handicap, 
                $passportPath, $password, $father_name, $mother_name, $section_id, $transport_id, $dormitory_id, $club_id, $house_id, $student_category, 
                $session_id, $login_status, $created_at, $updated_at
            ]);

        // Store success message and redirect
        $_SESSION['success_message'] = "Student admitted successfully!";
        header("Location: admission_form.php");
        exit();

    } catch (Exception $e) {
    die("Database Error: " . $e->getMessage());
}
}
?>
