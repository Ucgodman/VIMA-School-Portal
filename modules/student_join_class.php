<?php
session_start();
$pageTitle = "Join Class";

// Include necessary files
include_once __DIR__ . "/../config/database.php";
include_once __DIR__ . "/../includes/admin_header.php";


$class_id = null;
$section_id = null;

if ($_SESSION['user']['type'] === 'student') {
    $student_id = $_SESSION['user']['id'];

    $stmt = $pdo->prepare("SELECT class_id, section_id, firstname, lastname FROM students WHERE student_id = ?");
    $stmt->execute([$student_id]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($student) {
        $class_id = $student['class_id'];
        $section_id = $student['section_id'];

        // Optional: store student name temporarily for Jitsi display
        $_SESSION['student_name'] = $student['firstname'] . ' ' . $student['lastname'];
    } else {
        die("Student record not found.");
    }
}

if ($class_id && $section_id) {
    $stmt = $pdo->prepare("SELECT * FROM live_classes 
    WHERE class_id = ? AND section_id = ? 
    AND start_time <= DATE_ADD(NOW(), INTERVAL 5 MINUTE)
    ORDER BY start_time DESC LIMIT 1");


    $stmt->execute([$class_id, $section_id]);
    $class = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$class) {
        die("No live class is currently active.");
    }
}



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
        <div class="container my-4">
        <div class="card shadow">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Live Class Room</h5>
                    <button id="toggleFullscreen" class="btn btn-light btn-sm">Fullscreen</button>
                </div>
                <div class="card-body p-0" id="jitsi-container" style="height: 70vh;">
                    <div id="jitsi" style="width: 100%; height: 100%;"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- JS Includes (keep as in your template) -->
<script src="../assets/js/core/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Jitsi Script -->
<script src="https://meet.jit.si/external_api.js"></script>
<script>
    const domain = "meet.jit.si";
    const options = {
        roomName: "<?= htmlspecialchars($class['jitsi_room']) ?>",
        width: "100%",
        height: "100%",
        parentNode: document.querySelector('#jitsi'),
        userInfo: {
            displayName: "<?= $_SESSION['student_name'] ?>"
        }
    };
    const api = new JitsiMeetExternalAPI(domain, options);

    // Fullscreen toggle logic
    const fullscreenBtn = document.getElementById('toggleFullscreen');
    const jitsiContainer = document.getElementById('jitsi-container');

    fullscreenBtn.addEventListener('click', () => {
        if (!document.fullscreenElement) {
            jitsiContainer.requestFullscreen().catch(err => {
                alert(`Error trying to enter fullscreen: ${err.message}`);
            });
        } else {
            document.exitFullscreen();
        }
    });
</script>


</body>
</html>




