<?php
session_start();
$pageTitle = "Assign Permissions";
include_once __DIR__ . "/../config/database.php";
include_once __DIR__ . "/../functions/helper_functions.php";
include_once __DIR__ . "/../includes/admin_header.php";

// Fetch roles (excluding admin)
$roles = $pdo->query("SELECT id, name FROM roles WHERE id != 1")->fetchAll();

// Fetch menu items
$menuItems = $pdo->query("SELECT `key`, name FROM menu_items ORDER BY name ASC")->fetchAll(PDO::FETCH_KEY_PAIR);
?>

<div class="wrapper">
    <?php include_once __DIR__ . "/../includes/sidebar.php"; ?>
    <div class="main-panel">
        <div class="main-header">
            <div class="main-header-logo">
                <div class="logo-header" data-background-color="dark">
                    <a href="#" class="logo">
                        <img src="/../assets/images/logo_light.svg" alt="logo" height="20" />
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

        <div class="container py-4">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4>Assign Page Permissions</h4>
                    <div>
                        <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#staffModal">Staff</button>
                        <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#studentModal">Student</button>
                        <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#parentModal">Parent</button>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
                    <?php elseif (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
                    <?php endif; ?>
                    <p>Select a role to assign permissions to Staff, Students, or Parents.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Staff Modal -->
<div class="modal fade" id="staffModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="save_permissions.php">
                <div class="modal-header">
                    <h5 class="modal-title">Assign Staff Permissions (By Role)</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="user_type" id="staffUserType" value="">
                    <label>Select Role:</label>
                    <select class="form-select" name="role_id" id="staffRoleSelect" required>
                        <option value="">-- Choose Role --</option>
                        <?php foreach ($roles as $role): ?>
                            <?php if (!in_array($role['id'], [3, 4])): // Exclude student and parent ?>
                                <option value="<?= $role['id'] ?>"><?= htmlspecialchars($role['name']) ?></option>
                            <?php endif; ?>
                        <?php endforeach; ?>

                    </select>
                    <hr>
                    <label>Permissions:</label>
                    <div class="row" id="staffPermissions">
                        <?php foreach ($menuItems as $key => $label): ?>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input staff-perm" type="checkbox" name="permissions[]" value="<?= $key ?>">
                                    <label class="form-check-label"><?= htmlspecialchars($label) ?></label>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Save Permissions</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Student Modal -->
<div class="modal fade" id="studentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="save_permissions.php">
                <div class="modal-header">
                    <h5 class="modal-title">Assign Student Permissions</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="user_type" value="student">
                    <input type="hidden" name="role_id" value="3"> <!-- Student Role ID -->
                    <label>Permissions:</label>
                    <div class="row">
                        <?php foreach ($menuItems as $key => $label): ?>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input student-perm" type="checkbox" name="permissions[]" value="<?= $key ?>">
                                    <label class="form-check-label"><?= htmlspecialchars($label) ?></label>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Save Permissions</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Parent Modal -->
<div class="modal fade" id="parentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="save_permissions.php">
                <div class="modal-header">
                    <h5 class="modal-title">Assign Parent Permissions</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="user_type" value="parent">
                    <input type="hidden" name="role_id" value="4"> <!-- Parent Role ID -->
                    <label>Permissions:</label>
                    <div class="row">
                        <?php foreach ($menuItems as $key => $label): ?>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input parent-perm" type="checkbox" name="permissions[]" value="<?= $key ?>">
                                    <label class="form-check-label"><?= htmlspecialchars($label) ?></label>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Save Permissions</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="../assets/js/core/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
function loadPermissions(roleId, targetCheckboxClass) {
    $.post("load_permissions.php", { id: roleId }, function(response) {
        const perms = JSON.parse(response);
        $(`.${targetCheckboxClass}`).each(function () {
            $(this).prop("checked", perms.includes($(this).val()));
        });
    });
}

$(document).ready(function () {
    $('#staffRoleSelect').on('change', function () {
        const roleId = $(this).val();
        if (roleId) loadPermissions(roleId, 'staff-perm');
    });

    // Automatically load for student and parent roles (3 and 4)
    $('#studentModal').on('shown.bs.modal', function () {
        loadPermissions(3, 'student-perm');
    });

    $('#parentModal').on('shown.bs.modal', function () {
        loadPermissions(4, 'parent-perm');
    });
});

const roleUserTypes = {
    2: 'teacher',
    5: 'clerk',
    6: 'accountant',
    7: 'principal',
    8: 'vice_principal',
    9: 'it_support',
    10: 'transport_manager'
};

$('#staffRoleSelect').on('change', function () {
    const roleId = $(this).val();
    const userType = roleUserTypes[roleId];
    $('#staffUserType').val(userType || '');

    if (roleId) loadPermissions(roleId, 'staff-perm');
});

</script>

</body>
</html>
