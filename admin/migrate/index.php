<?php
/**
 * Database Migration Runner
 * Menjalankan semua file migrasi yang belum dieksekusi
 * Akses: http://localhost:8000/admin/migrate/index.php
 */

require_once __DIR__ . '/../check_session.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../views/auth_check.php';

class MigrationRunner {
    private $conn;
    private $migrationDir;
    private $logFile;
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
        $this->migrationDir = __DIR__ . '/';
        $this->logFile = __DIR__ . '/../MIGRATION_SYSTEM_README.txt';
        
        // Buat tabel log migrasi jika belum ada
        $this->createMigrationLogTable();
    }
    
    /**
     * Buat tabel untuk mencatat migrasi yang sudah dijalankan
     */
    private function createMigrationLogTable() {
        $sql = "CREATE TABLE IF NOT EXISTS migration_log (
            id INT AUTO_INCREMENT PRIMARY KEY,
            migration_file VARCHAR(255) NOT NULL UNIQUE,
            executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            status ENUM('success', 'failed') DEFAULT 'success',
            error_message TEXT
        )";
        $this->conn->exec($sql);
    }
    
    /**
     * Mendapatkan daftar file migrasi yang tersedia
     */
    private function getMigrationFiles() {
        $files = glob($this->migrationDir . '*.sql');
        $migrations = [];
        
        foreach ($files as $file) {
            $filename = basename($file);
            // Hanya ambil file dengan format YYYY-MM-DD_*.sql
            if (preg_match('/^\d{4}-\d{2}-\d{2}_/', $filename)) {
                $migrations[] = $filename;
            }
        }
        
        sort($migrations); // Urutkan berdasarkan tanggal
        return $migrations;
    }
    
    /**
     * Mendapatkan daftar migrasi yang sudah dijalankan
     */
    private function getExecutedMigrations() {
        $stmt = $this->conn->query("SELECT migration_file FROM migration_log");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    
    /**
     * Menjalankan migrasi
     */
    public function runMigrations() {
        $migrations = $this->getMigrationFiles();
        $executed = $this->getExecutedMigrations();
        $pending = array_diff($migrations, $executed);
        
        $results = [
            'total' => count($migrations),
            'executed' => count($executed),
            'pending' => count($pending),
            'success' => [],
            'failed' => []
        ];
        
        if (empty($pending)) {
            return $results;
        }
        
        foreach ($pending as $migration) {
            try {
                $sql = file_get_contents($this->migrationDir . $migration);
                
                // Jalankan SQL
                $this->conn->exec($sql);
                
                // Catat migrasi berhasil
                $stmt = $this->conn->prepare("INSERT INTO migration_log (migration_file, status) VALUES (?, 'success')");
                $stmt->execute([$migration]);
                
                $results['success'][] = $migration;
                
                // Update log file
                $this->appendToLogFile("✅ Migrasi berhasil: {$migration}");
                
            } catch (PDOException $e) {
                // Catat migrasi gagal
                $stmt = $this->conn->prepare("INSERT INTO migration_log (migration_file, status, error_message) VALUES (?, 'failed', ?)");
                $stmt->execute([$migration, $e->getMessage()]);
                
                $results['failed'][] = [
                    'file' => $migration,
                    'error' => $e->getMessage()
                ];
                
                $this->appendToLogFile("❌ Migrasi gagal: {$migration} - " . $e->getMessage());
            }
        }
        
        return $results;
    }
    
    /**
     * Menambahkan log ke file README
     */
    private function appendToLogFile($message) {
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "\n[{$timestamp}] {$message}";
        file_put_contents($this->logFile, $logMessage, FILE_APPEND);
    }
    
    /**
     * Membuat file migrasi baru untuk perubahan database
     */
    public function createMigrationFile($description) {
        $date = date('Y-m-d');
        $filename = $date . '_' . time() . '_' . preg_replace('/[^a-z0-9]+/', '_', strtolower($description)) . '.sql';
        $filepath = $this->migrationDir . $filename;
        
        // Buat template file migrasi
        $template = "-- Migration: {$description}\n";
        $template .= "-- Date: " . date('Y-m-d H:i:s') . "\n";
        $template .= "-- Author: System Generated\n\n";
        $template .= "START TRANSACTION;\n\n";
        $template .= "-- Tulis SQL perubahan di sini\n";
        $template .= "-- Contoh:\n";
        $template .= "-- ALTER TABLE users ADD COLUMN phone VARCHAR(20);\n\n";
        $template .= "COMMIT;\n";
        
        file_put_contents($filepath, $template);
        
        $this->appendToLogFile("📝 File migrasi dibuat: {$filename} - {$description}");
        
        return $filename;
    }
    
    /**
     * Menampilkan status migrasi
     */
    public function displayStatus() {
        $migrations = $this->getMigrationFiles();
        $executed = $this->getExecutedMigrations();
        
        $html = "<!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Database Migration</title>
            <link rel='preload' href='../css/adminlte.css' as='style' />
            <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css' crossorigin='anonymous' media='print' onload=\"this.media='all'\" />
            <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/styles/overlayscrollbars.min.css' crossorigin='anonymous' />
            <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css' crossorigin='anonymous' />
            <link rel='stylesheet' href='../css/adminlte.css' />
        </head>
        <body class='layout-fixed sidebar-expand-lg sidebar-open bg-body-tertiary'>
            <div class='app-wrapper'>
                <!-- Header -->
                <nav class='app-header navbar navbar-expand bg-body'>
                    <div class='container-fluid'>
                        <ul class='navbar-nav'>
                            <li class='nav-item'>
                                <a class='nav-link' data-lte-toggle='sidebar' href='#' role='button'>
                                    <i class='bi bi-list'></i>
                                </a>
                            </li>
                            <li class='nav-item d-none d-md-block'><a href='../index.html' class='nav-link'>Home</a></li>
                        </ul>
                        <ul class='navbar-nav ms-auto'>
                            <li class='nav-item dropdown user-menu'>
                                <a href='#' class='nav-link dropdown-toggle' data-bs-toggle='dropdown'>
                                    <i class='bi bi-person-circle'></i>
                                    <span class='d-none d-md-inline'>Admin</span>
                                </a>
                                <ul class='dropdown-menu dropdown-menu-lg dropdown-menu-end'>
                                    <li class='user-header text-bg-primary'>
                                        <img src='../../assets/img/my-profile-img.jpg' class='rounded-circle shadow' alt='User Image' />
                                        <p>Admin</p>
                                    </li>
                                    <li class='user-footer'>
                                        <a href='../logout.php' class='btn btn-default btn-flat float-end'>Sign out</a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </nav>

                <!-- Sidebar -->
                <aside class='app-sidebar bg-body-secondary shadow' data-bs-theme='dark'>
                    <div class='sidebar-brand'>
                        <a href='../index.html' class='brand-link'>
                            <img src='../assets/img/AdminLTELogo.png' alt='AdminLTE Logo' class='brand-image opacity-75 shadow' />
                            <span class='brand-text fw-light'>AdminLTE 4</span>
                        </a>
                    </div>
                    <div class='sidebar-wrapper'>
                        <nav class='mt-2'>
                            <ul class='nav sidebar-menu flex-column' data-lte-toggle='treeview'>
                                <li class='nav-item'>
                                    <a href='../index.html' class='nav-link'>
                                        <i class='nav-icon bi bi-speedometer2'></i>
                                        <p>Dashboard</p>
                                    </a>
                                </li>
                                <li class='nav-item'>
                                    <a href='../views/my_skills.php' class='nav-link'>
                                        <i class='nav-icon bi bi-star'></i>
                                        <p>My Skills</p>
                                    </a>
                                </li>
                                <li class='nav-item'>
                                    <a href='../views/projects.php' class='nav-link'>
                                        <i class='nav-icon bi bi-images'></i>
                                        <p>Projects</p>
                                    </a>
                                </li>
                                <li class='nav-item'>
                                    <a href='../views/users.php' class='nav-link'>
                                        <i class='nav-icon bi bi-people'></i>
                                        <p>Users</p>
                                    </a>
                                </li>
                                <li class='nav-item'>
                                    <a href='./index.php' class='nav-link active'>
                                        <i class='nav-icon bi bi-arrow-repeat'></i>
                                        <p>Migrate</p>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </aside>

                <!-- Main Content -->
                <main class='app-main'>
                    <!-- App Content Header -->
                    <div class='app-content-header'>
                        <div class='container-fluid'>
                            <div class='row'>
                                <div class='col-sm-6'>
                                    <h3 class='mb-0'>Database Migration</h3>
                                </div>
                                <div class='col-sm-6'>
                                    <ol class='breadcrumb float-sm-end'>
                                        <li class='breadcrumb-item'><a href='../index.html'>Home</a></li>
                                        <li class='breadcrumb-item active'>Migration</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- App Content -->
                    <div class='app-content'>
                        <div class='container-fluid'>
                            <div class='row'>
                                <div class='col-md-12'>
                                    <div class='card'>
                                        <div class='card-header'>
                                            <h3 class='card-title'>📊 Migration Status</h3>
                                        </div>
                                        <div class='card-body'>
                                            <div class='row mb-4'>
                                                <div class='col-md-4'>
                                                    <div class='card text-center'>
                                                        <div class='card-body'>
                                                            <h5>Total Migrations</h5>
                                                            <h2 class='text-primary'>" . count($migrations) . "</h2>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class='col-md-4'>
                                                    <div class='card text-center'>
                                                        <div class='card-body'>
                                                            <h5>Executed</h5>
                                                            <h2 class='text-success'>" . count($executed) . "</h2>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class='col-md-4'>
                                                    <div class='card text-center'>
                                                        <div class='card-body'>
                                                            <h5>Pending</h5>
                                                            <h2 class='text-warning'>" . (count($migrations) - count($executed)) . "</h2>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class='mb-3'>
                                                <form method='POST' class='d-inline'>
                                                    <button type='submit' name='run_migrations' class='btn btn-success'>
                                                        🚀 Run Pending Migrations
                                                    </button>
                                                </form>
                                            </div>
                                            
                                            <h4 class='mt-4'>📋 Migration Files</h4>
                                            <div class='table-responsive'>
                                            <table class='table table-bordered'>
                                                <thead class='table-light'>
                                                    <tr>
                                                        <th>No</th>
                                                        <th>Migration File</th>
                                                        <th>Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>";
        
        $no = 1;
        foreach ($migrations as $migration) {
            $status = in_array($migration, $executed) ? 
                "<span class='badge bg-success'>Executed</span>" : 
                "<span class='badge bg-warning'>Pending</span>";
            
            $html .= "<tr>
                                                        <td>{$no}</td>
                                                        <td>{$migration}</td>
                                                        <td>{$status}</td>
                                                      </tr>";
            $no++;
        }
        
        $html .= "                        </tbody>
                                            </table>
                                            </div>
                                            
                                            <h4 class='mt-4'>📜 Migration Log</h4>
                                            <pre class='bg-light p-3' style='max-height: 300px; overflow-y: auto;'>";
        
        // Display log file content
        if (file_exists($this->logFile)) {
            $html .= htmlspecialchars(file_get_contents($this->logFile));
        } else {
            $html .= "No migration log available yet.";
        }
        
        $html .= "      </pre>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </main>
            </div>

            <script src='../js/adminlte.js'></script>
            <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js'></script>
        </body>
        </html>";
        
        return $html;
    }
}

// Handle requests
$runner = new MigrationRunner();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['run_migrations'])) {
    $results = $runner->runMigrations();
    
    if (!empty($results['success'])) {
        echo "<div class='alert alert-success'>✅ " . count($results['success']) . " migrasi berhasil dijalankan</div>";
    }
    
    if (!empty($results['failed'])) {
        foreach ($results['failed'] as $failed) {
            echo "<div class='alert alert-danger'>❌ {$failed['file']}: {$failed['error']}</div>";
        }
    }
    
    // Redirect untuk menghindari resubmit
    echo "<script>setTimeout(function() { window.location.href = 'index.php'; }, 3000);</script>";
} else {
    echo $runner->displayStatus();
}
?>