<?php
require_once '../check_session.php';
require_once '../../config/database.php';

// Include authentication and permission check view
require_once 'auth_check.php';

$database = new Database();
$conn = $database->getConnection();

$skills = [];
if ($conn) {
    try {
        $query = "SELECT id, category, skill_detail, proficiency FROM skills ORDER BY category, skill_detail";
        $stmt = $conn->query($query);
        $skills = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error = "Database error: " . $e->getMessage();
    }
}
?>

<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AdminLTE v4 | My Skills</title>
    <!--begin::Accessibility Meta Tags-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes" />
    <meta name="color-scheme" content="light dark" />
    <meta name="theme-color" content="#007bff" media="(prefers-color-scheme: light)" />
    <meta name="theme-color" content="#1a1a1a" media="(prefers-color-scheme: dark)" />
    <!--end::Accessibility Meta Tags-->
    <!--begin::Primary Meta Tags-->
    <meta name="title" content="AdminLTE v4 | My Skills" />
    <meta name="author" content="ColorlibHQ" />
    <meta
      name="description"
      content="AdminLTE is a Free Bootstrap 5 Admin Dashboard, 30 example pages using Vanilla JS. Fully accessible with WCAG 2.1 AA compliance."
    />
    <!--end::Primary Meta Tags-->
    <!--begin::Accessibility Features-->
    <meta name="supported-color-schemes" content="light dark" />
    <link rel="preload" href="../css/adminlte.css" as="style" />
    <!--end::Accessibility Features-->
    <!--begin::Fonts-->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css"
      integrity="sha256-tXJfXfp6Ewt1ilPzLDtQnJV4hclT9XuaZUKyUvmyr+Q="
      crossorigin="anonymous"
      media="print"
      onload="this.media='all'"
    />
    <!--end::Fonts-->
    <!--begin::Third Party Plugin(OverlayScrollbars)-->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/styles/overlayscrollbars.min.css"
      crossorigin="anonymous"
    />
    <!--end::Third Party Plugin(OverlayScrollbars)-->
    <!--begin::Third Party Plugin(Bootstrap Icons)-->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"
      crossorigin="anonymous"
    />
    <!--end::Third Party Plugin(Bootstrap Icons)-->
    <!--begin::Required Plugin(AdminLTE)-->
    <link rel="stylesheet" href="../css/adminlte.css" />
    <!--end::Required Plugin(AdminLTE)-->
  </head>
  <!--end::Head-->
  <!--begin::Body-->
  <body class="layout-fixed sidebar-expand-lg sidebar-open bg-body-tertiary">
    <!--begin::App Wrapper-->
    <div class="app-wrapper">
      <!--begin::Header-->
      <nav class="app-header navbar navbar-expand bg-body">
        <!--begin::Container-->
        <div class="container-fluid">
          <!--begin::Start Navbar Links-->
          <ul class="navbar-nav">
            <li class="nav-item">
              <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
                <i class="bi bi-list"></i>
              </a>
            </li>
            <li class="nav-item d-none d-md-block"><a href="#" class="nav-link">Home</a></li>
          </ul>
          <!--end::Start Navbar Links-->
          <!--begin::End Navbar Links-->
          <ul class="navbar-nav ms-auto">
            <!--begin::Fullscreen Toggle-->
            <li class="nav-item">
              <a class="nav-link" href="#" data-lte-toggle="fullscreen">
                <i data-lte-icon="maximize" class="bi bi-arrows-fullscreen"></i>
                <i data-lte-icon="minimize" class="bi bi-fullscreen-exit" style="display: none"></i>
              </a>
            </li>
            <!--end::Fullscreen Toggle-->
            <!--begin::User Menu Dropdown-->
            <li class="nav-item dropdown user-menu">
              <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                <i class="bi bi-person-circle"></i>
                <span class="d-none d-md-inline">Admin</span>
              </a>
              <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
                <!--begin::User Image-->
                <li class="user-header text-bg-primary">
                  <img
                    src="../../assets/img/my-profile-img.jpg"
                    class="rounded-circle shadow"
                    alt="User Image"
                  />
                  <p>
                    Admin
                  </p>
                </li>
                <!--end::User Image-->
                <!--begin::Menu Body-->
                <!--begin::Menu Footer-->
                <li class="user-footer">
                  <a href="../logout.php" class="btn btn-default btn-flat float-end">Sign out</a>
                </li>
                <!--end::Menu Footer-->
              </ul>
            </li>
            <!--end::User Menu Dropdown-->
          </ul>
          <!--end::End Navbar Links-->
        </div>
        <!--end::Container-->
      </nav>
      <!--end::Header-->
      <!--begin::Sidebar-->
      <aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
        <!--begin::Sidebar Brand-->
        <div class="sidebar-brand">
          <!--begin::Brand Link-->
          <a href="../index.html" class="brand-link">
            <!--begin::Brand Image-->
            <img
              src="../assets/img/AdminLTELogo.png"
              alt="AdminLTE Logo"
              class="brand-image opacity-75 shadow"
            />
            <!--end::Brand Image-->
            <!--begin::Brand Text-->
            <span class="brand-text fw-light">AdminLTE 4</span>
            <!--end::Brand Text-->
          </a>
          <!--end::Brand Link-->
        </div>
        <!--end::Sidebar Brand-->
        <!--begin::Sidebar Wrapper-->
        <div class="sidebar-wrapper">
          <nav class="mt-2">
            <!--begin::Sidebar Menu-->
            <ul
              class="nav sidebar-menu flex-column"
              data-lte-toggle="treeview"
              role="navigation"
              aria-label="Main navigation"
              data-accordion="false"
              id="navigation"
            >
              <li class="nav-item">
                <a href="../index.html" class="nav-link">
                  <i class="nav-icon bi bi-speedometer"></i>
                  <p>Dashboard</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="./my_skills.php" class="nav-link active">
                  <i class="nav-icon bi bi-star"></i>
                  <p>My Skills</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="./projects.php" class="nav-link">
                  <i class="nav-icon bi bi-images"></i>
                  <p>Projects</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="./users.php" class="nav-link">
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
            <!--end::Sidebar Menu-->
          </nav>
        </div>
        <!--end::Sidebar Wrapper-->
      </aside>
      <!--end::Sidebar-->
      <!--begin::App Main-->
      <main class="app-main">
        <!--begin::App Content Header-->
        <div class="app-content-header">
          <!--begin::Container-->
          <div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
              <div class="col-sm-6"><h3 class="mb-0">My Skills</h3></div>
              <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                  <li class="breadcrumb-item"><a href="../index.html">Home</a></li>
                  <li class="breadcrumb-item active" aria-current="page">My Skills</li>
                </ol>
              </div>
            </div>
            <!--end::Row-->
          </div>
          <!--end::Container-->
        </div>
        <!--end::App Content Header-->
        <!--begin::App Content-->
        <div class="app-content">
          <!--begin::Container-->
          <div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
              <div class="col-md-12">
                <div class="card mb-4">
                  <div class="card-header d-flex align-items-center">
                    <h3 class="card-title">Skills Management</h3>
                    <button class="btn btn-primary btn-sm ms-auto" data-bs-toggle="modal" data-bs-target="#addSkillModal">
                      <i class="bi bi-plus-lg me-2"></i>Add New Skill
                    </button>
                  </div>
                  <!-- /.card-header -->
                  <div class="card-body">
                    <?php if (isset($_GET['success'])): ?>
                      <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong>Success!</strong> Operation completed successfully.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                      </div>
                    <?php endif; ?>
                    <?php if (isset($_GET['error'])): ?>
                      <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Error!</strong> Something went wrong. Please try again.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                      </div>
                    <?php endif; ?>
                    <?php if (isset($error)): ?>
                      <div class="alert alert-danger" role="alert">
                        <?php echo htmlspecialchars($error); ?>
                      </div>
                    <?php endif; ?>
                    <table class="table table-bordered table-striped">
                      <thead>
                        <tr>
                          <th style="width: 10px">#</th>
                          <th>Category</th>
                          <th>Skill Detail</th>
                          <th>Proficiency</th>
                          <th style="width: 120px">Actions</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php if (count($skills) > 0): ?>
                          <?php foreach ($skills as $index => $skill): ?>
                            <tr class="align-middle">
                              <td><?php echo $index + 1; ?></td>
                              <td><?php echo htmlspecialchars($skill['category']); ?></td>
                              <td><?php echo htmlspecialchars($skill['skill_detail']); ?></td>
                              <td>
                                <div class="progress progress-xs">
                                  <div
                                    class="progress-bar text-bg-primary"
                                    style="width: <?php echo htmlspecialchars($skill['proficiency']); ?>%"
                                  ></div>
                                </div>
                                <span class="badge text-bg-primary"><?php echo htmlspecialchars($skill['proficiency']); ?>%</span>
                              </td>
                              <td>
                                <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editSkillModal" onclick="editSkill(<?php echo htmlspecialchars(json_encode($skill)); ?>)">
                                  <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-danger btn-sm" onclick="deleteSkill(<?php echo $skill['id']; ?>)">
                                  <i class="bi bi-trash"></i>
                                </button>
                              </td>
                            </tr>
                          <?php endforeach; ?>
                        <?php else: ?>
                          <tr>
                            <td colspan="5" class="text-center text-muted">No skills found</td>
                          </tr>
                        <?php endif; ?>
                      </tbody>
                    </table>
                  </div>
                  <!-- /.card-body -->
                </div>
                <!-- /.card -->
              </div>
            </div>
            <!--end::Row-->
          </div>
          <!--end::Container-->
        </div>
        <!--end::App Content-->
      </main>
      <!--end::App Main-->
      <!--begin::Footer-->
      <footer class="app-footer">
        <!--begin::To the end-->
        <div class="float-end d-none d-sm-inline">Anything you want</div>
        <!--end::To the end-->
        <!--begin::Copyright-->
        <strong>
          Copyright &copy; 2014-2025&nbsp;
          <a href="https://adminlte.io" class="text-decoration-none">AdminLTE.io</a>.
        </strong>
        All rights reserved.
        <!--end::Copyright-->
      </footer>
      <!--end::Footer-->
    </div>
    <!--end::App Wrapper-->

    <!-- Add Skill Modal -->
    <div class="modal fade" id="addSkillModal" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Add New Skill</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <form action="../handlers/skill_handler.php" method="POST">
            <input type="hidden" name="action" value="add">
            <div class="modal-body">
              <div class="mb-3">
                <label for="category" class="form-label">Category</label>
                <input type="text" class="form-control" id="category" name="category" required>
              </div>
              <div class="mb-3">
                <label for="skill_detail" class="form-label">Skill Detail</label>
                <input type="text" class="form-control" id="skill_detail" name="skill_detail" required>
              </div>
              <div class="mb-3">
                <label for="proficiency" class="form-label">Proficiency (%)</label>
                <input type="number" class="form-control" id="proficiency" name="proficiency" min="0" max="100" required>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-primary">Add Skill</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Edit Skill Modal -->
    <div class="modal fade" id="editSkillModal" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Edit Skill</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <form action="../handlers/skill_handler.php" method="POST">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" id="edit_id" name="id">
            <div class="modal-body">
              <div class="mb-3">
                <label for="edit_category" class="form-label">Category</label>
                <input type="text" class="form-control" id="edit_category" name="category" required>
              </div>
              <div class="mb-3">
                <label for="edit_skill_detail" class="form-label">Skill Detail</label>
                <input type="text" class="form-control" id="edit_skill_detail" name="skill_detail" required>
              </div>
              <div class="mb-3">
                <label for="edit_proficiency" class="form-label">Proficiency (%)</label>
                <input type="number" class="form-control" id="edit_proficiency" name="proficiency" min="0" max="100" required>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-primary">Update Skill</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!--begin::Third Party Plugin(OverlayScrollbars)-->
    <script
      src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/browser/overlayscrollbars.browser.es6.min.js"
      crossorigin="anonymous"
    ></script>
    <!--end::Third Party Plugin(OverlayScrollbars)-->
    <!--begin::Required Plugin(popperjs for Bootstrap 5)-->
    <script
      src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
      crossorigin="anonymous"
    ></script>
    <!--end::Required Plugin(popperjs for Bootstrap 5)-->
    <!--begin::Required Plugin(Bootstrap 5)-->
    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.min.js"
      crossorigin="anonymous"
    ></script>
    <!--end::Required Plugin(Bootstrap 5)-->
    <!--begin::Required Plugin(AdminLTE)-->
    <script src="../js/adminlte.js"></script>
    <!--end::Required Plugin(AdminLTE)-->
    <!--begin::OverlayScrollbars Configure-->
    <script>
      const SELECTOR_SIDEBAR_WRAPPER = '.sidebar-wrapper';
      const Default = {
        scrollbarTheme: 'os-theme-light',
        scrollbarAutoHide: 'leave',
        scrollbarClickScroll: true,
      };
      document.addEventListener('DOMContentLoaded', function () {
        const sidebarWrapper = document.querySelector(SELECTOR_SIDEBAR_WRAPPER);
        if (sidebarWrapper && OverlayScrollbarsGlobal?.OverlayScrollbars !== undefined) {
          OverlayScrollbarsGlobal.OverlayScrollbars(sidebarWrapper, {
            scrollbars: {
              theme: Default.scrollbarTheme,
              autoHide: Default.scrollbarAutoHide,
              clickScroll: Default.scrollbarClickScroll,
            },
          });
        }
      });

      function editSkill(skill) {
        document.getElementById('edit_id').value = skill.id;
        document.getElementById('edit_category').value = skill.category;
        document.getElementById('edit_skill_detail').value = skill.skill_detail;
        document.getElementById('edit_proficiency').value = skill.proficiency;
      }

      function deleteSkill(id) {
        if (confirm('Are you sure you want to delete this skill?')) {
          window.location.href = '../handlers/skill_handler.php?action=delete&id=' + id;
        }
      }
    </script>
    <!--end::OverlayScrollbars Configure-->
    <!-- Permission Manager Script -->
    <script src="../../assets/js/permission-manager.js"></script>
    <!--end::Script-->
  </body>
  <!--end::Body-->
</html>
