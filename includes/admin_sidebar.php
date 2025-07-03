<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../functions/permission.php';

?>

<div class="sidebar" data-background-color="dark">
    <div class="sidebar-logo">
        <div class="logo-header" data-background-color="dark">
            <a href="../modules/index.php" class="logo">
                <h1 class="text-white"><?= ucfirst($_SESSION['user']['type']); ?></h1>
            </a>
            <div class="nav-toggle">
                <button class="btn btn-toggle toggle-sidebar"><i class="gg-menu-right"></i></button>
                <button class="btn btn-toggle sidenav-toggler"><i class="gg-menu-left"></i></button>
            </div>
            <button class="topbar-toggler more"><i class="gg-more-vertical-alt"></i></button>
        </div>
    </div>

    <div class="sidebar-wrapper scrollbar scrollbar-inner">
        <div class="sidebar-content">
            <ul class="nav nav-secondary">

                <?php if (hasPermission('dashboard')): ?>
                    <li class="nav-item <?= $pageTitle === 'Dashboard' ? 'active' : '' ?>">
                        <a href="../modules/index.php">
                            <i class="fas fa-home"></i>
                            <p><?= $pageTitle; ?></p>
                        </a>
                    </li>
                <?php endif; ?>

                <!-- Section: Core -->
                <li class="nav-section">
                    <span class="sidebar-mini-icon"><i class="fa fa-ellipsis-h"></i></span>
                    <h4 class="text-section">Core</h4>
                </li>

                <!-- Group: Academics -->
                <?php if (hasAnyPermission(['enquiry_category', 'list_enquiries', 'school_clubs', 'circulars', 'syllabus', 'noticeboard'])): ?>
                    <?= renderMenuGroup('enquiry', 'Academics', 'fas fa-file-alt', [
                        ['enquiry_category', 'enquiry_category.php', 'Enquiry Category'],
                        ['list_enquiries', 'list_enquiries.php', 'List Enquiries'],
                        ['school_clubs', 'school_clubs.php', 'School Clubs'],
                        ['circulars', 'circulars.php', 'Manage Circular'],
                        ['syllabus', 'syllabus.php', 'Syllabus'],
                        ['noticeboard', 'noticeboard.php', 'Manage Events'],
                    ]) ?>
                <?php endif; ?>

                <!-- Group: Staff -->
                <?php if (hasAnyPermission(['department', 'staff', 'roles'])): ?>
                    <?= renderMenuGroup('staffs', 'Staff', 'fas fa-chalkboard-teacher', [
                        ['department', 'department.php', 'Department'],
                        ['staff', 'staff.php', 'Staff'],
                        ['roles', 'roles.php', 'Roles'],
                    ]) ?>
                <?php endif; ?>

                <!-- Group: Student Management -->
                <?php if (hasAnyPermission(['admission_form', 'student_information', 'studenthouse'])): ?>
                    <?= renderMenuGroup('students', 'Student Management', 'fas fa-user-graduate', [
                        ['admission_form', 'admission_form.php', 'Admission Form'],
                        ['student_information', 'student_information.php', 'List Students'],
                        ['studenthouse', 'studenthouse.php', 'Student House'],
                    ]) ?>
                <?php endif; ?>

                <!-- Group: Attendance -->
                <?php if (hasAnyPermission(['student_attendance', 'staff_attendance', 'view_attendance'])): ?>
                    <?= renderMenuGroup('attendance', 'Attendance Management', 'fas fa-school', [
                        ['student_attendance', 'student_attendance.php', 'Students Attendance'],
                        ['staff_attendance', 'staff_attendance.php', 'Staff Attendance'],
                        ['view_attendance', 'view_attendance.php', 'Manage View'],
                    ]) ?>
                <?php endif; ?>

                <!-- Group: Download -->
                <?php if (hasAnyPermission(['assignments', 'materials'])): ?>
                    <?= renderMenuGroup('downloads', 'Download Page', 'fas fa-download', [
                        ['assignments', 'assignments.php', 'Assignments'],
                        ['materials', 'materials.php', 'Study Materials'],
                    ]) ?>
                <?php endif; ?>

                <!-- Single: Parents -->
                <?php if (hasPermission('parents')): ?>
                    <li class="nav-item">
                        <a href="modules/parents/parents.php">
                            <i class="fas fa-users"></i>
                            <p>Parents</p>
                        </a>
                    </li>
                <?php endif; ?>

                <!-- Group: Class/Section/Session -->
                <?php if (hasAnyPermission(['classes', 'sections', 'sessions'])): ?>
                    <?= renderMenuGroup('classes', 'Class/Section/Session', 'fas fa-school', [
                        ['classes', 'classes.php', 'Manage Classes'],
                        ['sections', 'sections.php', 'Manage Sections'],
                        ['sessions', 'sessions.php', 'Manage Sessions'],
                    ]) ?>
                <?php endif; ?>

                <!-- Single: Subjects -->
                <?php if (hasPermission('subjects')): ?>
                    <li class="nav-item">
                        <a href="subjects.php">
                            <i class="fas fa-book"></i>
                            <p>Subjects</p>
                        </a>
                    </li>
                <?php endif; ?>

                <!-- Group: Questions -->
                <?php if (hasAnyPermission(['set_exams', 'add_question', 'manage_questions'])): ?>
                    <?= renderMenuGroup('question', 'Manage Questions', 'fas fa-file-alt', [
                        ['set_exams', 'set_exams.php', 'Exams'],
                        ['add_question', 'add_question.php', 'Add Question'],
                        ['manage_questions', 'manage_questions.php', 'Manage Questions'],
                    ]) ?>
                <?php endif; ?>

                <!-- Single: Role Management -->
                <?php if (hasPermission('assign_permissions')): ?>
                    <li class="nav-item">
                        <a href="assign_permissions.php">
                            <i class="fas fa-user-shield"></i>
                            <p>Role Management</p>
                        </a>
                    </li>
                <?php endif; ?>

            </ul>
        </div>
    </div>
</div>
