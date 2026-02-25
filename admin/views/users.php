<?php
require_once '../check_session.php';
require_once '../../config/database.php';
require_once '../../config/action_validator.php';

// Include authentication and permission check view
require_once 'auth_check.php';

$validator = new ActionValidator('users');
// $permCheck = $validator->validateRead();
// if (!$permCheck['allowed']) {
//     die('Access Denied: ' . htmlspecialchars($permCheck['message']));
// }

$database = new Database();
$conn = $database->getConnection();

// Fetch users for initial render
$query = "SELECT id, username, email, full_name, is_active FROM users ORDER BY username";
$stmt = $conn->prepare($query);
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

$message = '';
$messageType = '';
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Manage Users</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/styles/overlayscrollbars.min.css" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" crossorigin="anonymous" />
    <link rel="stylesheet" href="../css/adminlte.css">
    <style>
        .action-btn { margin-right: 6px; }
        .table-actions { display:flex; align-items:center; }
    </style>
</head>
<body class="layout-fixed sidebar-expand-lg sidebar-open bg-body-tertiary">
    <div class="app-wrapper">
        <!-- Navbar -->
        <nav class="app-header navbar navbar-expand bg-body">
            <div class="container-fluid">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
                            <i class="bi bi-list"></i>
                        </a>
                    </li>
                    <li class="nav-item d-none d-md-block"><a href="../index.html" class="nav-link">Home</a></li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown user-menu">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i>
                            <span class="d-none d-md-inline">Admin</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
                            <li class="user-header text-bg-primary">
                                <img src="../../assets/img/my-profile-img.jpg" class="rounded-circle shadow" alt="User Image" />
                                <p>Admin</p>
                            </li>
                            <li class="user-footer">
                                <a href="../logout.php" class="btn btn-default btn-flat float-end">Sign out</a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Sidebar -->
        <aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
            <div class="sidebar-brand">
                <a href="../index.html" class="brand-link">
                    <img src="../assets/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image opacity-75 shadow" />
                    <span class="brand-text fw-light">AdminLTE 4</span>
                </a>
            </div>
            <div class="sidebar-wrapper">
                <nav class="mt-2">
                    <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview">
                        <li class="nav-item">
                            <a href="../index.html" class="nav-link">
                                <i class="nav-icon bi bi-speedometer2"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="my_skills.php" class="nav-link">
                                <i class="nav-icon bi bi-star"></i>
                                <p>My Skills</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="projects.php" class="nav-link">
                                <i class="nav-icon bi bi-images"></i>
                                <p>Projects</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="users.php" class="nav-link active">
                                <i class="nav-icon bi bi-people"></i>
                                <p>Users</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="../migrate/index.php" class="nav-link">
                                <i class="nav-icon bi bi-arrow-repeat"></i>
                                <p>Migrate</p>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="app-main">
            <!-- App Content Header -->
            <div class="app-content-header">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <h3 class="mb-0">Users Management</h3>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-end">
                                <li class="breadcrumb-item"><a href="../index.html">Home</a></li>
                                <li class="breadcrumb-item active">Users</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <!-- App Content -->
            <div class="app-content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
            <?php if (!empty($message)): ?>
                <div class="alert alert-<?php echo htmlspecialchars($messageType); ?> alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

                            <div class="card">
                                <div class="card-header d-flex align-items-center">
                                    <h3 class="card-title">Users List</h3>
                                    <?php $canCreate = $validator->validateCreate(); ?>
                                    <?php if ($canCreate['allowed']): ?>
                                        <button id="btnAddUser" class="btn btn-primary btn-sm ms-auto">
                                            <i class="bi bi-plus-lg me-2"></i>Add User
                                        </button>
                                    <?php endif; ?>
                                </div>
                                <div class="card-body table-responsive p-0">
            <table class="table table-striped table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): ?>
                        <tr data-id="<?php echo $u['id']; ?>">
                            <td><?php echo htmlspecialchars($u['username']); ?></td>
                            <td><?php echo htmlspecialchars($u['full_name']); ?></td>
                            <td><?php echo htmlspecialchars($u['email']); ?></td>
                            <td><?php echo $u['is_active'] ? 'Active' : 'Inactive'; ?></td>
                            <td class="table-actions">
                                <?php $canEdit = $validator->validateUpdate(); ?>
                                <?php if ($canEdit['allowed']): ?>
                                    <button class="btn btn-sm btn-secondary action-btn btnEdit">Edit</button>
                                <?php endif; ?>
                                <?php $canDelete = $validator->validateDelete(); ?>
                                <?php if ($canDelete['allowed']): ?>
                                    <button class="btn btn-sm btn-danger action-btn btnDelete">Delete</button>
                                <?php endif; ?>
                                <a class="btn btn-sm btn-info" href="manage_permissions.php">Permissions</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        </main>
    </div>

    <!-- Modal -->
    <div id="userModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.4); z-index:9999;">
        <div style="background:#fff; width:420px; margin:80px auto; padding:20px; border-radius:6px;">
            <h4 id="modalTitle">Add User</h4>
            <form id="userForm">
                <input type="hidden" name="id" id="user_id">
                <div class="mb-2">
                    <label>Username</label>
                    <input class="form-control" name="username" id="username" required>
                </div>
                <div class="mb-2">
                    <label>Full name</label>
                    <input class="form-control" name="full_name" id="full_name">
                </div>
                <div class="mb-2">
                    <label>Email</label>
                    <input class="form-control" type="email" name="email" id="email">
                </div>
                <div class="mb-2">
                    <label>Password</label>
                    <input class="form-control" type="password" name="password" id="password">
                    <small id="passwordHelp" class="form-text text-muted">Leave blank when editing to keep current password.</small>
                </div>
                <div class="mb-2">
                    <label>Status</label>
                    <select class="form-control" name="is_active" id="is_active">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
                <div style="text-align:right; margin-top:10px;">
                    <button type="button" id="btnCancel" class="btn btn-secondary">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="btnSave">Save</button>
                </div>
            </form>
        </div>
    </div>

    <script src="../js/adminlte.js"></script>
    <script>
    (function(){
        const modal = document.getElementById('userModal');
        const form = document.getElementById('userForm');

        document.getElementById('btnAddUser').addEventListener('click', ()=>{
            document.getElementById('modalTitle').innerText = 'Add User';
            form.reset();
            document.getElementById('user_id').value = '';
            modal.style.display = 'block';
        });

        document.getElementById('btnCancel').addEventListener('click', ()=> modal.style.display = 'none');

        document.querySelectorAll('.btnEdit').forEach(btn=>{
            btn.addEventListener('click', (e)=>{
                const tr = e.target.closest('tr');
                const id = tr.getAttribute('data-id');
                // Populate form by reading table cells
                document.getElementById('modalTitle').innerText = 'Edit User';
                document.getElementById('user_id').value = id;
                document.getElementById('username').value = tr.children[0].innerText.trim();
                document.getElementById('full_name').value = tr.children[1].innerText.trim();
                document.getElementById('email').value = tr.children[2].innerText.trim();
                document.getElementById('is_active').value = tr.children[3].innerText.trim() === 'Active' ? '1' : '0';
                document.getElementById('password').value = '';
                modal.style.display = 'block';
            });
        });

        document.querySelectorAll('.btnDelete').forEach(btn=>{
            btn.addEventListener('click', async (e)=>{
                if (!confirm('Delete this user?')) return;
                const tr = e.target.closest('tr');
                const id = tr.getAttribute('data-id');
                const formData = new FormData();
                formData.append('action','delete');
                formData.append('id', id);
                const res = await fetch('user_handler.php',{method:'POST', body: formData});
                const json = await res.json();
                if (json.success) location.reload(); else alert(json.message||'Error');
            });
        });

        form.addEventListener('submit', async (ev)=>{
            ev.preventDefault();
            const fd = new FormData(form);
            const isEdit = !!document.getElementById('user_id').value;
            fd.append('action', isEdit ? 'edit' : 'add');
            const res = await fetch('user_handler.php', { method: 'POST', body: fd });
            const json = await res.json();
            if (json.success) location.reload(); else alert(json.message || 'Error');
        });
    })();
    </script>
</body>
</html>
