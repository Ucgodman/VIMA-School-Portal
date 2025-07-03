<?php


// Include database connection
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
        $marital_status = $_POST['marital_status'];
        $phone = $_POST['phone'];
        $email = $_POST['email'];
        $address = $_POST['address'];
        $qualification = $_POST['qualification'];
        $bank_name = $_POST['bank_name'];
        $account_holder = $_POST['account_holder'];
        $account_number = $_POST['account_number'];
        $role_id = $_POST['role_id'];
        $department_id = $_POST['department_id'];
        $salary = $_POST['salary'];
        $hire_date = $_POST['hire_date'];
        $status = isset($_POST['status']) ? 1 : 0;

        // File upload handling for passport and document
        $passport = $_FILES['passport']['name'] ? time() . "_" . $_FILES['passport']['name'] : null;
        $document = $_FILES['document']['name'] ? time() . "_" . $_FILES['document']['name'] : null;

        // Move uploaded files
        if ($passport) {
            move_uploaded_file($_FILES['passport']['tmp_name'], "../uploads/passports/" . $passport);
        }
        if ($document) {
            move_uploaded_file($_FILES['document']['tmp_name'], "../uploads/documents/" . $document);
        }

        // Prepare SQL query
        $query = "UPDATE staff SET 
                    firstname = ?, middlename = ?, lastname = ?, birthday = ?, gender = ?, marital_status = ?, phone = ?, email = ?, address = ?, 
                    qualification = ?, bank_name = ?, account_holder_name = ?, account_number = ?, 
                    role_id = ?, department_id = ?, salary = ?, hire_date = ?, status = ?";

        // Include file updates only if new files are uploaded
        if ($passport) {
            $query .= ", passport = ?";
        }
        if ($document) {
            $query .= ", document = ?";
        }
        
        $query .= " WHERE staff_id = ?";

        // Prepare statement
        $stmt = $pdo->prepare($query);

        // Bind parameters
        $params = [
            $firstname, $middlename, $lastname, $birthday, $gender, $marital_status, $phone, $email, $address,
            $qualification, $bank_name, $account_holder, $account_number,
            $role_id, $department_id, $salary, $hire_date, $status
        ];
        
        if ($passport) {
            $params[] = $passport;
        }
        if ($document) {
            $params[] = $document;
        }
        
        $params[] = $staff_id;

        // Execute update
        if ($stmt->execute($params)) {
            $_SESSION['success'] = "Staff details updated successfully.";
        } else {
            $_SESSION['error'] = "Failed to update staff details.";
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Database error: " . $e->getMessage();
    }
}

// Redirect back to staff page
header("Location: staff.php");
exit;
?>
