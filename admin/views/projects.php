<?php
require_once '../check_session.php';
require_once '../../config/database.php';

$database = new Database();
$conn = $database->getConnection();

// Handle form submissions
$message = '';
$messageType = '';

// Delete project
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = $_GET['delete'];
    
    // Get image filename before deleting
    $query = "SELECT image FROM projects WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$id]);
    $project = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Delete image file if exists
    if ($project && !empty($project['image'])) {
        $imagePath = '../../uploads/projects/' . $project['image'];
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
    }
    
    // Delete from database
    $query = "DELETE FROM projects WHERE id = ?";
    $stmt = $conn->prepare($query);
    
    if ($stmt->execute([$id])) {
        $message = 'Project deleted successfully!';
        $messageType = 'success';
    } else {
        $message = 'Error deleting project!';
        $messageType = 'danger';
    }
}

// Get all projects
$query = "SELECT * FROM projects ORDER BY created_at DESC";
$stmt = $conn->prepare($query);
$stmt->execute();
$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!doctype html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Manage Projects | AdminLTE 4</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes" />
    <meta name="color-scheme" content="light dark" />
    <link rel="preload" href="../css/adminlte.css" as="style" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/styles/overlayscrollbars.min.css" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" crossorigin="anonymous" />
    <link rel="stylesheet" href="../css/adminlte.css" />
    <style>
        .image-preview {
            width: 100%;
            height: 200px;
            border: 2px dashed #ddd;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
            background-color: #f8f9fa;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .image-preview:hover {
            border-color: #007bff;
            background-color: #e9ecef;
        }
        
        .image-preview img {
            max-width: 100%;
            max-height: 100%;
            object-fit: cover;
        }
        
        .image-preview .preview-placeholder {
            text-align: center;
            color: #6c757d;
        }
        
        .image-preview .preview-placeholder i {
            font-size: 48px;
            margin-bottom: 10px;
        }
        
        .project-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 4px;
        }
        
        /* Toast styles */
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
        }
        
        .toast {
            min-width: 300px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            margin-bottom: 10px;
            animation: slideIn 0.3s ease;
        }
        
        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        .toast-header {
            padding: 12px 16px;
            background: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
            border-radius: 8px 8px 0 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .toast-header strong {
            color: #333;
            font-size: 14px;
        }
        
        .toast-header .close {
            cursor: pointer;
            border: none;
            background: none;
            font-size: 20px;
            color: #999;
        }
        
        .toast-body {
            padding: 16px;
        }
        
        .progress {
            height: 8px;
            background: #e9ecef;
            border-radius: 4px;
            overflow: hidden;
            margin-bottom: 8px;
        }
        
        .progress-bar {
            height: 100%;
            background: #007bff;
            transition: width 0.3s ease;
            border-radius: 4px;
        }
        
        .upload-status {
            font-size: 13px;
            color: #6c757d;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .file-info {
            font-size: 12px;
            color: #999;
            margin-top: 4px;
        }
        
        .toast.success .progress-bar {
            background: #28a745;
        }
        
        .toast.error .progress-bar {
            background: #dc3545;
        }
        
        /* Modal styles */
        .modal-lg {
            max-width: 800px;
        }
        
        .btn-close {
            background: transparent;
            border: none;
            font-size: 20px;
            cursor: pointer;
        }
    </style>
</head>
<body class="layout-fixed sidebar-expand-lg sidebar-open bg-body-tertiary">
    <!-- Toast Container -->
    <div id="toastContainer" class="toast-container"></div>

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
                                <i class="nav-icon bi bi-speedometer"></i>
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
                            <a href="projects.php" class="nav-link active">
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
                            <h3 class="mb-0">Manage Projects</h3>
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
                    <!-- Alert Message -->
                    <?php if ($message): ?>
                    <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                        <?php echo $message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php endif; ?>

                    <!-- Add Project Button -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProjectModal">
                                <i class="bi bi-plus-circle"></i> Add New Project
                            </button>
                        </div>
                    </div>

                    <!-- Projects Table -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Projects List</h3>
                                </div>
                                <div class="card-body table-responsive p-0">
                                    <table class="table table-hover text-nowrap">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Image</th>
                                                <th>Title</th>
                                                <th>Category</th>
                                                <th>Period</th>
                                                <th>Description</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($projects as $project): ?>
                                            <tr>
                                                <td><?php echo $project['id']; ?></td>
                                                <td>
                                                    <?php if (!empty($project['image'])): ?>
                                                    <img src="../../uploads/projects/<?php echo $project['image']; ?>" 
                                                         class="project-image" 
                                                         alt="<?php echo htmlspecialchars($project['title']); ?>">
                                                    <?php else: ?>
                                                    <span class="badge bg-secondary">No Image</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo htmlspecialchars($project['title']); ?></td>
                                                <td><?php echo htmlspecialchars($project['category']); ?></td>
                                                <td><?php echo htmlspecialchars($project['period']); ?></td>
                                                <td><?php echo substr(htmlspecialchars($project['description']), 0, 50); ?>...</td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-info" 
                                                            onclick="editProject(<?php echo htmlspecialchars(json_encode($project)); ?>)">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>
                                                    <a href="?delete=<?php echo $project['id']; ?>" 
                                                       class="btn btn-sm btn-danger" 
                                                       onclick="return confirm('Are you sure you want to delete this project?')">
                                                        <i class="bi bi-trash"></i>
                                                    </a>
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

        <!-- Footer -->
        <footer class="app-footer">
            <strong>Copyright &copy; 2014-2025 <a href="https://adminlte.io" class="text-decoration-none">AdminLTE.io</a>.</strong>
            All rights reserved.
        </footer>
    </div>

    <!-- Add Project Modal -->
    <div class="modal fade" id="addProjectModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Project</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="addProjectForm" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="title" class="form-label">Title *</label>
                                    <input type="text" class="form-control" id="title" name="title" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="category" class="form-label">Category *</label>
                                    <input type="text" class="form-control" id="category" name="category" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="period" class="form-label">Period *</label>
                            <input type="text" class="form-control" id="period" name="period" placeholder="e.g., Jan 2023 - Dec 2023" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Description *</label>
                            <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Project Image</label>
                            <div class="image-preview" onclick="document.getElementById('imageInput').click()">
                                <div id="imagePreview" class="preview-placeholder">
                                    <i class="bi bi-cloud-upload"></i>
                                    <p>Click to upload image</p>
                                </div>
                            </div>
                            <input type="file" class="d-none" id="imageInput" name="image" accept="image/*">
                            <small class="text-muted">Supported formats: JPG, PNG, GIF. Max size: 5MB</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Project</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Project Modal -->
    <div class="modal fade" id="editProjectModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Project</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editProjectForm" enctype="multipart/form-data">
                    <input type="hidden" id="edit_id" name="id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_title" class="form-label">Title *</label>
                                    <input type="text" class="form-control" id="edit_title" name="title" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_category" class="form-label">Category *</label>
                                    <input type="text" class="form-control" id="edit_category" name="category" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_period" class="form-label">Period *</label>
                            <input type="text" class="form-control" id="edit_period" name="period" placeholder="e.g., Jan 2023 - Dec 2023" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_description" class="form-label">Description *</label>
                            <textarea class="form-control" id="edit_description" name="description" rows="3" required></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Project Image</label>
                            <div class="image-preview" onclick="document.getElementById('editImageInput').click()">
                                <div id="editImagePreview" class="preview-placeholder">
                                    <i class="bi bi-cloud-upload"></i>
                                    <p>Click to upload new image</p>
                                </div>
                            </div>
                            <input type="file" class="d-none" id="editImageInput" name="image" accept="image/*">
                            <input type="hidden" id="existing_image" name="existing_image">
                            <small class="text-muted">Leave empty to keep current image. Supported formats: JPG, PNG, GIF. Max size: 5MB</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Project</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/browser/overlayscrollbars.browser.es6.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.min.js" crossorigin="anonymous"></script>
    <script src="../js/adminlte.js"></script>
    
    <script>
        // Toast Manager
        const ToastManager = {
            container: document.getElementById('toastContainer'),
            
            show(title, message, type = 'info', progress = 0) {
                const toastId = 'toast_' + Date.now();
                const toast = document.createElement('div');
                toast.className = `toast ${type}`;
                toast.id = toastId;
                
                let progressHtml = '';
                if (progress !== undefined) {
                    progressHtml = `
                        <div class="progress">
                            <div class="progress-bar" style="width: ${progress}%"></div>
                        </div>
                        <div class="upload-status">
                            <span>${progress}% uploaded</span>
                            <span class="file-info"></span>
                        </div>
                    `;
                }
                
                toast.innerHTML = `
                    <div class="toast-header">
                        <strong>${title}</strong>
                        <button type="button" class="close" onclick="this.closest('.toast').remove()">&times;</button>
                    </div>
                    <div class="toast-body">
                        <div class="toast-message">${message}</div>
                        ${progressHtml}
                    </div>
                `;
                
                this.container.appendChild(toast);
                
                // Auto remove after 5 seconds for non-progress toasts
                if (progress === undefined) {
                    setTimeout(() => {
                        const toastEl = document.getElementById(toastId);
                        if (toastEl) {
                            toastEl.remove();
                        }
                    }, 5000);
                }
                
                return toastId;
            },
            
            updateProgress(toastId, progress, message = '') {
                const toast = document.getElementById(toastId);
                if (toast) {
                    const progressBar = toast.querySelector('.progress-bar');
                    const statusText = toast.querySelector('.upload-status span:first-child');
                    const fileInfo = toast.querySelector('.file-info');
                    
                    if (progressBar) {
                        progressBar.style.width = progress + '%';
                    }
                    
                    if (statusText) {
                        statusText.textContent = progress + '% uploaded';
                    }
                    
                    if (fileInfo && message) {
                        fileInfo.textContent = message;
                    }
                    
                    if (progress >= 100) {
                        setTimeout(() => {
                            toast.remove();
                        }, 2000);
                    }
                }
            },
            
            remove(toastId) {
                const toast = document.getElementById(toastId);
                if (toast) {
                    toast.remove();
                }
            }
        };

        // Image Preview Handler
        function handleImagePreview(input, previewId) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    const preview = document.getElementById(previewId);
                    preview.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
                };
                
                reader.readAsDataURL(input.files[0]);
            }
        }

        document.getElementById('imageInput').addEventListener('change', function() {
            handleImagePreview(this, 'imagePreview');
        });

        document.getElementById('editImageInput').addEventListener('change', function() {
            handleImagePreview(this, 'editImagePreview');
        });

        // File Upload Function
        async function uploadFile(file, url, formData) {
            return new Promise((resolve, reject) => {
                const xhr = new XMLHttpRequest();
                
                xhr.upload.addEventListener('progress', (e) => {
                    if (e.lengthComputable) {
                        const percentComplete = Math.round((e.loaded / e.total) * 100);
                        // You can update progress here if needed
                    }
                });
                
                xhr.addEventListener('load', () => {
                    if (xhr.status === 200) {
                        try {
                            const response = JSON.parse(xhr.responseText);
                            resolve(response);
                        } catch (e) {
                            reject(new Error('Invalid response format'));
                        }
                    } else {
                        reject(new Error('Upload failed with status: ' + xhr.status));
                    }
                });
                
                xhr.addEventListener('error', () => reject(new Error('Network error occurred')));
                xhr.addEventListener('abort', () => reject(new Error('Upload aborted')));
                
                xhr.open('POST', url, true);
                xhr.send(formData);
            });
        }

        // Add Project Form
        document.getElementById('addProjectForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData();
            formData.append('action', 'add');
            formData.append('title', document.getElementById('title').value);
            formData.append('category', document.getElementById('category').value);
            formData.append('period', document.getElementById('period').value);
            formData.append('description', document.getElementById('description').value);
            
            const imageFile = document.getElementById('imageInput').files[0];
            if (imageFile) {
                // Validate file size (5MB max)
                if (imageFile.size > 5 * 1024 * 1024) {
                    ToastManager.show('Error', 'File size must be less than 5MB', 'error');
                    return;
                }
                
                // Validate file type
                const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];
                if (!allowedTypes.includes(imageFile.type)) {
                    ToastManager.show('Error', 'Only JPG, PNG and GIF files are allowed', 'error');
                    return;
                }
                
                formData.append('image', imageFile);
            }
            
            // Show upload progress toast
            const toastId = ToastManager.show('Uploading', 'Uploading project...', 'info', 0);
            
            try {
                const result = await uploadFile(imageFile, 'project_handler.php', formData);
                
                ToastManager.updateProgress(toastId, 100, 'Upload complete!');
                
                if (result.success) {
                    ToastManager.show('Success', 'Project added successfully!', 'success');
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    ToastManager.remove(toastId);
                    ToastManager.show('Error', result.message || 'Failed to save project', 'error');
                }
            } catch (error) {
                ToastManager.remove(toastId);
                ToastManager.show('Error', 'Failed to upload project: ' + error.message, 'error');
            }
        });

        // Edit Project Function
        function editProject(project) {
            document.getElementById('edit_id').value = project.id;
            document.getElementById('edit_title').value = project.title;
            document.getElementById('edit_category').value = project.category;
            document.getElementById('edit_period').value = project.period || '';
            document.getElementById('edit_description').value = project.description;
            document.getElementById('existing_image').value = project.image || '';
            
            // Show existing image preview
            const preview = document.getElementById('editImagePreview');
            if (project.image) {
                preview.innerHTML = `<img src="../../uploads/projects/${project.image}" alt="Preview">`;
            } else {
                preview.innerHTML = `
                    <i class="bi bi-cloud-upload"></i>
                    <p>Click to upload new image</p>
                `;
            }
            
            new bootstrap.Modal(document.getElementById('editProjectModal')).show();
        }

        // Edit Project Form
        document.getElementById('editProjectForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData();
            formData.append('action', 'edit');
            formData.append('id', document.getElementById('edit_id').value);
            formData.append('title', document.getElementById('edit_title').value);
            formData.append('category', document.getElementById('edit_category').value);
            formData.append('period', document.getElementById('edit_period').value);
            formData.append('description', document.getElementById('edit_description').value);
            formData.append('existing_image', document.getElementById('existing_image').value);
            
            const imageFile = document.getElementById('editImageInput').files[0];
            if (imageFile) {
                // Validate file size (5MB max)
                if (imageFile.size > 5 * 1024 * 1024) {
                    ToastManager.show('Error', 'File size must be less than 5MB', 'error');
                    return;
                }
                
                // Validate file type
                const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];
                if (!allowedTypes.includes(imageFile.type)) {
                    ToastManager.show('Error', 'Only JPG, PNG and GIF files are allowed', 'error');
                    return;
                }
                
                formData.append('image', imageFile);
            }
            
            // Show upload progress toast
            const toastId = ToastManager.show('Updating', 'Updating project...', 'info', 0);
            
            try {
                const result = await uploadFile(imageFile, 'project_handler.php', formData);
                
                ToastManager.updateProgress(toastId, 100, 'Update complete!');
                
                if (result.success) {
                    ToastManager.show('Success', 'Project updated successfully!', 'success');
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    ToastManager.remove(toastId);
                    ToastManager.show('Error', result.message || 'Failed to update project', 'error');
                }
            } catch (error) {
                ToastManager.remove(toastId);
                ToastManager.show('Error', 'Failed to update project: ' + error.message, 'error');
            }
        });

        // OverlayScrollbars initialization
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarWrapper = document.querySelector('.sidebar-wrapper');
            if (sidebarWrapper && OverlayScrollbarsGlobal?.OverlayScrollbars) {
                OverlayScrollbarsGlobal.OverlayScrollbars(sidebarWrapper, {
                    scrollbars: {
                        theme: 'os-theme-light',
                        autoHide: 'leave',
                        clickScroll: true,
                    },
                });
            }
        });
    </script>
</body>
</html>