<?php

include_once __DIR__ . "/../config/database.php";
include_once __DIR__ . "/../functions/helper_functions.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Retrieve form data
        $staff_id = $_POST['staff_id'];
        $firstname = $_POST['firstname'];
        $middlename = $_POST['middlename'];
        $lastname = $_POST['lastname'];
        $birthday = $_POST['birthday'];
        $gender = $_POST['gender'];
        $phone = $_POST['phone'];
        $marital_status = $_POST['marital_status'];
        $email = $_POST['email'];
        $qualification = $_POST['qualification'];
        $address = $_POST['address'];
        $role_id = $_POST['role_id'];
        $department_id = $_POST['department_id'];
        $salary = $_POST['salary'];
        $hire_date = $_POST['hire_date'];
        $account_holder_name = $_POST['account_holder_name'];
        $account_number = $_POST['account_number'];
        $bank_name = $_POST['bank_name'];
        $status = isset($_POST['status']) ? 1 : 0;

        // Handle file uploads
        $passport = null;
        $document = null;
        $uploadDir = __DIR__ . "/../uploads/";

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        if (!empty($_FILES['passport']['name'])) {
            $passport = time() . "_" . basename($_FILES['passport']['name']);
            move_uploaded_file($_FILES['passport']['tmp_name'], $uploadDir . $passport);
        }

        if (!empty($_FILES['document']['name'])) {
            $document = time() . "_" . basename($_FILES['document']['name']);
            move_uploaded_file($_FILES['document']['tmp_name'], $uploadDir . $document);
        }

        // Insert staff data into the database
        $query = "INSERT INTO staff 
                  (staff_id, firstname, middlename, lastname, birthday, gender, phone, marital_status, email, qualification, address, role_id, department_id, salary, hire_date, account_holder_name, account_number, bank_name, passport, document, status) 
                  VALUES 
                  (?,?,?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $pdo->prepare($query);
        $stmt->execute([
            $staff_id, $name, $middlename, $lastname, $birthday, $gender, $phone, $marital_status, $email, $qualification, $address,
            $role_id, $department_id, $salary, $hire_date, $account_holder_name, $account_number, $bank_name,
            $passport, $document, $status
        ]);

        $_SESSION['success'] = "Staff added successfully!";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error adding staff: " . $e->getMessage();
    }

    // Redirect back to staff management page
    header("Location: staff.php");
    exit();
} else {
    $_SESSION['error'] = "Invalid request!";
    header("Location: staff.php");
    exit();
}
?>
