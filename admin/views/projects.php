<?php
require_once '../check_session.php';
require_once '../../config/database.php';

$database = new Database();
$conn = $database->getConnection();
?>

<!doctype html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AdminLTE v4 | Projects Management</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes" />
    <meta name="color-scheme" content="light dark" />
    
    <!-- Fonts -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css" crossorigin="anonymous" />
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" crossorigin="anonymous" />
    
    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="../css/adminlte.css" />
    
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css" />
    
    <!-- Toastr CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
    
    <!-- Summernote CSS -->
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
    
    <style>
        .modal-dialog {
            max-width: 700px;
        }
        .modal-content {
            border-radius: 8px;
        }
        .note-editor.note-frame {
            border: 1px solid #ced4da;
            border-radius: 4px;
        }
        .dt-buttons {
            margin-bottom: 10px;
        }
        .project-image-preview {
            max-width: 100px;
            max-height: 60px;
            object-fit: cover;
            border-radius: 4px;
        }
        /* Toastr customization */
        .toast-success {
            background-color: #28a745 !important;
        }
        .toast-error {
            background-color: #dc3545 !important;
        }
        .toast-warning {
            background-color: #ffc107 !important;
        }
    </style>
</head>
<body class="layout-fixed sidebar-expand-lg sidebar-open bg-body-tertiary">
    <div class="app-wrapper">
        <!-- Header -->
        <nav class="app-header navbar navbar-expand bg-body">
            <div class="container-fluid">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
                            <i class="bi bi-list"></i>
                        </a>
                    </li>
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
                    <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="navigation" id="navigation">
                        <li class="nav-item">
                            <a href="../index.html" class="nav-link">
                                <i class="nav-icon bi bi-speedometer"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="./my_skills.php" class="nav-link">
                                <i class="nav-icon bi bi-star"></i>
                                <p>My Skills</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="./projects.php" class="nav-link active">
                                <i class="nav-icon bi bi-images"></i>
                                <p>Projects</p>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="app-main">
            <div class="app-content-header">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <h3 class="mb-0">Projects Management</h3>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-end">
                                <li class="breadcrumb-item"><a href="../index.html">Home</a></li>
                                <li class="breadcrumb-item active">Projects</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="app-content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Projects List</h3>
                                    <div class="card-tools">
                                        <button class="btn btn-primary btn-sm" onclick="openAddModal()">
                                            <i class="bi bi-plus-lg me-2"></i>Add New Project
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <table id="projectsTable" class="table table-bordered table-striped w-100">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Title</th>
                                                <th>Description</th>
                                                <th>Image</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Data will be loaded via AJAX -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Add/Edit Project Modal -->
    <div class="modal fade" id="projectModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalTitle">Add New Project</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="projectForm" enctype="multipart/form-data">
                        <input type="hidden" id="projectId" name="id">
                        
                        <div class="mb-3">
                            <label for="projectTitle" class="form-label">Project Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="projectTitle" name="title" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="projectDescription" class="form-label">Description <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="projectDescription" name="description" rows="4" required></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="projectCategory" class="form-label">Category</label>
                            <input type="text" class="form-control" id="projectCategory" name="category" placeholder="e.g., Websites, Apps, Games">
                        </div>
                        
                        <div class="mb-3">
                            <label for="projectImage" class="form-label">Image Path <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="projectImage" name="image" placeholder="e.g., assets/img/portfolio/web-1.png" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="saveProject()">
                        <i class="bi bi-save me-2"></i>Save Project
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this project?</p>
                    <p class="text-danger"><strong>This action cannot be undone!</strong></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDelete">
                        <i class="bi bi-trash me-2"></i>Delete
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Required Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    
    <!-- Toastr -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    
    <!-- Summernote -->
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
    
    <!-- AdminLTE -->
    <script src="../js/adminlte.js"></script>

    <script>
        let projectsTable;
        let deleteId = null;
        
        // Configure Toastr
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": "3000"
        };

        $(document).ready(function() {
            initializeSummernote();
            loadProjects();
        });

        function initializeSummernote() {
            $('.summernote').summernote({
                height: 200,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'underline', 'clear']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                    ['insert', ['link', 'picture']],
                    ['view', ['fullscreen', 'codeview']]
                ]
            });
        }

        function loadProjects() {
            if (projectsTable) {
                projectsTable.destroy();
            }

            projectsTable = $('#projectsTable').DataTable({
                processing: true,
                serverSide: false,
                searching: false,
                ajax: {
                    url: '../../admin/handlers/project_actions.php?action=list',
                    type: 'GET',
                    dataSrc: function(json) {
                        return json;
                    },
                    error: function(xhr, error, thrown) {
                        console.error('DataTable error:', error);
                        toastr.error('Failed to load projects');
                    }
                },
                responsive: true,
                columns: [
                    { data: 'id' },
                    { data: 'title' },
                    { 
                        data: 'description',
                        render: function(data) {
                            return data ? data.substring(0, 100) + (data.length > 100 ? '...' : '') : '-';
                        }
                    },
                    {
                        data: 'image',
                        render: function(data) {
                            if (data) {
                                return `<img src="../../${data}" class="project-image-preview" style="max-height: 50px;" onerror="this.src='../../assets/img/no-image.png'">`;
                            }
                            return '<span class="text-muted">No image</span>';
                        }
                    },
                    {
                        data: null,
                        render: function(data) {
                            return `
                                <button class="btn btn-warning btn-sm" onclick="openEditModal(${data.id})">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-danger btn-sm" onclick="openDeleteModal(${data.id})">
                                    <i class="bi bi-trash"></i>
                                </button>
                            `;
                        }
                    }
                ],
                order: [[0, 'desc']],
                pageLength: 10,
                language: {
                    processing: "Loading...",
                    search: "Search:",
                    lengthMenu: "Show _MENU_ entries",
                    info: "Showing _START_ to _END_ of _TOTAL_ entries",
                    infoEmpty: "Showing 0 to 0 of 0 entries",
                    infoFiltered: "(filtered from _MAX_ total entries)"
                }
            });
        }

        function openAddModal() {
            $('#modalTitle').text('Add New Project');
            $('#projectId').val('');
            $('#projectTitle').val('');
            $('#projectDescription').val('');
            $('#projectCategory').val('');
            $('#projectImage').val('');
            $('#projectModal').modal('show');
        }

        function openEditModal(id) {
            $.ajax({
                url: `../handlers/project_actions.php?action=get&id=${id}`,
                type: 'GET',
                dataType: 'json',
                success: function(project) {
                    $('#modalTitle').text('Edit Project');
                    $('#projectId').val(project.id);
                    $('#projectTitle').val(project.title);
                    $('#projectDescription').val(project.description);
                    $('#projectCategory').val(project.category);
                    $('#projectImage').val(project.image);
                    $('#projectModal').modal('show');
                },
                error: function() {
                    toastr.error('Failed to load project data');
                }
            });
        }

        function openDeleteModal(id) {
            deleteId = id;
            $('#deleteModal').modal('show');
        }

        $('#confirmDelete').click(function() {
            if (deleteId) {
                $.ajax({
                    url: `../handlers/project_actions.php?action=delete&id=${deleteId}`,
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            toastr.success(response.message);
                            projectsTable.ajax.reload(null, false);
                        } else {
                            toastr.error(response.message);
                        }
                        $('#deleteModal').modal('hide');
                        deleteId = null;
                    },
                    error: function() {
                        toastr.error('Failed to delete project');
                        $('#deleteModal').modal('hide');
                        deleteId = null;
                    }
                });
            }
        });

        function saveProject() {
            // Validate form
            if (!$('#projectTitle').val()) {
                toastr.warning('Please enter project title');
                return;
            }
            if (!$('#projectDescription').val()) {
                toastr.warning('Please enter project description');
                return;
            }
            if (!$('#projectImage').val()) {
                toastr.warning('Please enter project image path');
                return;
            }

            let formData = new FormData($('#projectForm')[0]);
            formData.append('action', 'save');

            $.ajax({
                url: '../../admin/handlers/project_actions.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        toastr.success(response.message);
                        $('#projectModal').modal('hide');
                        projectsTable.ajax.reload(null, false);
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function(xhr) {
                    console.error('Save error:', xhr.responseText);
                    toastr.error('Failed to save project');
                }
            });
        }

        // Reset form when modal is closed
        $('#projectModal').on('hidden.bs.modal', function() {
            $('#projectForm')[0].reset();
            $('#projectDescription').val('');
        });
    </script>
</body>
</html>