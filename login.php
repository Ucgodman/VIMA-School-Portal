<?php
session_start(); // Ensure session is started if not already

require_once __DIR__ . "/config/database.php";
include_once __DIR__ . "/functions/helper_functions.php";
include_once __DIR__ . "/functions/permission.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (!empty($email) && !empty($password)) {
        $user = null;
        $userType = null;

        // Check staff
        $stmt = $pdo->prepare("SELECT id, staff_id, email, password, role_id, firstname, lastname FROM staff WHERE email = ? AND status = 1");
        $stmt->execute([$email]);
        $staff = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($staff && password_verify($password, $staff['password'])) {
            $user = $staff;
            $user['id'] = $staff['id'];
            $userType = ($staff['role_id'] == 1) ? 'admin' : 'staff';
        }

        // Check student if not found in staff
        if (!$user) {
            $stmt = $pdo->prepare("SELECT student_id, email, password, firstname, lastname FROM students WHERE email = ? AND login_status = 1");
            $stmt->execute([$email]);
            $student = $stmt->fetch(PDO::FETCH_ASSOC);


            if ($student && password_verify($password, $student['password'])) {
                $user = $student;
                $user['id'] = $student['student_id'];
                $userType = 'student';
                $user['role_id'] = 3;  // Student role ID from roles table
                
                $_SESSION['student_id'] = $student['student_id'];
                $_SESSION['student_name'] = $student['firstname'] . ' ' . $student['lastname'];

            }


        }

        // Check parent if not found in student
        if (!$user) {
            $stmt = $pdo->prepare("SELECT parent_id, email, password, name FROM parents WHERE email = ? AND login_status = 1");
            $stmt->execute([$email]);
            $parent = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($parent && password_verify($password, $parent['password'])) {
                $user = $parent;
                $user['id'] = $parent['parent_id'];
                $userType = 'parent';
                $user['role_id'] = 4;  // Parent role ID from roles table

                // Split name into firstname and lastname for consistency
                [$firstname, $lastname] = explode(' ', trim($parent['name'] . ' '), 2);
                $user['firstname'] = $firstname;
                $user['lastname'] = $lastname;
            }
        }

        if ($user) {
            // Fetch permissions
            $stmt = $pdo->prepare("SELECT menu_item FROM user_permissions WHERE role_id = ? AND user_type = ?");
            $stmt->execute([$user['id'], $userType]);
            $permissions = $stmt->fetchAll(PDO::FETCH_COLUMN);

            // Store user info in session
            $_SESSION['user'] = [
                'id' => $user['id'],
                'type' => $userType,
                'firstname' => $user['firstname'],
                'lastname' => $user['lastname'],
                'permissions' => $permissions,
                'role_id' => $user['role_id'] ?? null, // only staff has this
                'staff_id' => $user['staff_id'] ?? null
            ];

            $_SESSION['loggedIn'] = true;

            // Redirect
            if ($userType === 'admin') {
                header("Location: modules/index.php");
            } else {
                header("Location: modules/dashboard.php");
            }
            exit;
        } else {
            $error = "Invalid credentials.";
        }
    } else {
        $error = "Email and password are required.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .login-container {
            height: 100vh;
        }
        .login-image {
            background-image: url("assets/images/edu.jpg");
            background-size: cover;
            background-position: center;
        }
        .login-form {
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 8px;
        }
        input:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 5px rgba(13, 110, 253, 0.5);
        }
        .footer {
            text-align: center;
            font-size: 0.9rem;
            margin-top: 20px;
        }
    </style>
</head>
<body>
<div class="container-fluid login-container">
    <div class="row h-100">
        <div class="col-md-8 login-image d-none d-md-block"></div>
        <div class="col-md-4 d-flex justify-content-center align-items-center">
            <div class="card shadow-lg login-form" style="width: 100%; max-width: 400px;">
                <div class="card-body">
                    <div class="text-center mb-4">
                        <img src="assets/images/noble_logo.png" alt="School Logo" style="max-width: 150px;">
                    </div>
                    <h3 class="text-center mb-4">Login</h3>
                    
                    <?php if (!empty($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" id="email" class="form-control" placeholder="Enter your Email" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" name="password" id="password" class="form-control" placeholder="Enter your password" required>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Login</button>
                        </div>

                        <div class="text-center mt-3">
                            <a href="/school-portal/public/forgot-password" class="text-decoration-none">Forgot Password?</a>
                        </div>
                    </form>
                </div>
                
                <div class="footer">
                    &copy; <?php echo date("Y"); ?> School Management System
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
