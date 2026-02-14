<?php
/**
 * Create New Migration File
 * Halaman untuk membuat file migrasi baru
 */

require_once __DIR__ . '/index.php';

$runner = new MigrationRunner();
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';
    
    if (empty($description)) {
        $error = 'Deskripsi perubahan harus diisi';
    } else {
        $filename = $runner->createMigrationFile($description);
        $message = "File migrasi berhasil dibuat: {$filename}";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat File Migrasi Baru</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h3 class="mb-0">📝 Buat File Migrasi Baru</h3>
            </div>
            <div class="card-body">
                <?php if ($message): ?>
                    <div class="alert alert-success"><?php echo $message; ?></div>
                <?php endif; ?>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="mb-3">
                        <label for="description" class="form-label">Deskripsi Perubahan Database</label>
                        <textarea class="form-control" id="description" name="description" rows="3" 
                            placeholder="Contoh: Menambahkan kolom phone ke tabel users" required></textarea>
                        <small class="text-muted">
                            Deskripsi akan digunakan untuk membuat nama file migrasi
                        </small>
                    </div>
                    
                    <button type="submit" class="btn btn-success">
                        ➕ Buat File Migrasi
                    </button>
                    <a href="index.php" class="btn btn-secondary">
                        ↩ Kembali
                    </a>
                </form>
                
                <hr>
                
                <h5 class="mt-4">📌 Panduan:</h5>
                <ul>
                    <li>File migrasi akan dibuat dengan format: <code>YYYY-MM-DD_timestamp_deskripsi.sql</code></li>
                    <li>Edit file migrasi yang dibuat untuk menambahkan SQL perubahan</li>
                    <li>Jalankan migrasi melalui <code>index.php</code> setelah selesai mengedit</li>
                </ul>
            </div>
        </div>
    </div>
</body>
</html>