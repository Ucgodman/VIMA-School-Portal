<?php
require_once __DIR__ . '/../vendor/autoload.php'; // Make sure you have the MPDF library installed
include_once __DIR__ . "/../config/database.php";

if (!isset($_GET['id'])) {
    die("Invalid request!");
}

$staff_id = $_GET['id'];

// Fetch staff details
$stmt = $pdo->prepare("SELECT s.*, r.name AS role_name, d.name AS department_name FROM staff s
                       JOIN roles r ON s.role_id = r.id
                       JOIN departments d ON s.department_id = d.id
                       WHERE s.staff_id = ?");
$stmt->execute([$staff_id]);
$staff = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$staff) {
    die("Staff not found!");
}

$mpdf = new \Mpdf\Mpdf();
$html = "
    <h2>Staff Details</h2>
    <table border='1' cellpadding='10'>
        <tr><th>Name</th><td>{$staff['firstname']}</td></tr>
        <tr><th>Name</th><td>{$staff['middlename']}</td></tr>
        <tr><th>Name</th><td>{$staff['lastname']}</td></tr>
        <tr><th>Email</th><td>{$staff['email']}</td></tr>
        <tr><th>Phone</th><td>{$staff['phone']}</td></tr>
        <tr><th>Role</th><td>{$staff['role_name']}</td></tr>
        <tr><th>Department</th><td>{$staff['department_name']}</td></tr>
        <tr><th>Salary</th><td>₦" . number_format($staff['salary'], 2) . "</td></tr>
        <tr><th>Status</th><td>" . ($staff['status'] == 1 ? "Active" : "Inactive") . "</td></tr>
    </table>
";

$mpdf->WriteHTML($html);
$mpdf->Output("staff_{$staff_id}.pdf", "D");
exit;
