<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../autoload.php';

use App\Database;
use App\File;
use App\Settings;
use App\Helper;

session_start();

// Check session
$settings = new Settings();
if (!isset($_SESSION['user_id'])) {
    Helper::redirect('login.php');
}

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['files'])) {
    if (!Helper::validateCsrfToken($_POST['csrf_token'] ?? '')) {
        Helper::setFlashMessage('error', 'Invalid CSRF token.');
        Helper::redirect('index.php');
    }

    $file = new File();
    $result = $file->upload($_FILES['files']);

    if ($result['success']) {
        Helper::setFlashMessage('success', count($result['files']) . ' files uploaded successfully.');
    } else {
        Helper::setFlashMessage('error', implode('<br>', $result['errors']));
    }

    Helper::redirect('index.php');
}

// Handle file deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_file'])) {
    if (!Helper::validateCsrfToken($_POST['csrf_token'] ?? '')) {
        Helper::setFlashMessage('error', 'Invalid CSRF token.');
        Helper::redirect('index.php');
    }

    $file = new File();
    if ($file->delete((int)$_POST['file_id'])) {
        Helper::setFlashMessage('success', 'File deleted successfully.');
    } else {
        Helper::setFlashMessage('error', 'Error deleting file.');
    }

    Helper::redirect('index.php');
}

// List files
$file = new File();
$search = $_GET['search'] ?? '';
$sort = $_GET['sort'] ?? 'created_at';
$order = $_GET['order'] ?? 'DESC';
$files = $file->getAll($search, $sort, $order);
$stats = $file->getStats();

// Get flash message
$flashMessage = Helper::getFlashMessage();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .file-icon {
            font-size: 2rem;
        }
        .file-size {
            font-size: 0.875rem;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">File Manager</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="settings.php">
                            <i class="bi bi-gear"></i> Settings
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <?php if ($flashMessage): ?>
            <div class="alert alert-<?= $flashMessage['type'] ?> alert-dismissible fade show">
                <?= $flashMessage['message'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Upload Files</h5>
                        <form action="index.php" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="csrf_token" value="<?= Helper::generateCsrfToken() ?>">
                            <div class="mb-3">
                                <input type="file" class="form-control" name="files[]" multiple required>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-upload"></i> Upload
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Statistics</h5>
                        <p class="mb-1">Total Files: <?= $stats['total_files'] ?></p>
                        <p class="mb-0">Total Size: <?= Helper::formatFileSize($stats['total_size']) ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title mb-0">Files</h5>
                    <form class="d-flex" action="index.php" method="get">
                        <input type="text" class="form-control me-2" name="search" placeholder="Search..." value="<?= Helper::sanitize($search) ?>">
                        <button type="submit" class="btn btn-outline-primary">Search</button>
                    </form>
                </div>

                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>
                                    <a href="?sort=filename&order=<?= $sort === 'filename' && $order === 'ASC' ? 'DESC' : 'ASC' ?>" class="text-decoration-none text-dark">
                                        Filename
                                        <?php if ($sort === 'filename'): ?>
                                            <i class="bi bi-arrow-<?= $order === 'ASC' ? 'up' : 'down' ?>"></i>
                                        <?php endif; ?>
                                    </a>
                                </th>
                                <th>
                                    <a href="?sort=file_size&order=<?= $sort === 'file_size' && $order === 'ASC' ? 'DESC' : 'ASC' ?>" class="text-decoration-none text-dark">
                                        Size
                                        <?php if ($sort === 'file_size'): ?>
                                            <i class="bi bi-arrow-<?= $order === 'ASC' ? 'up' : 'down' ?>"></i>
                                        <?php endif; ?>
                                    </a>
                                </th>
                                <th>
                                    <a href="?sort=created_at&order=<?= $sort === 'created_at' && $order === 'ASC' ? 'DESC' : 'ASC' ?>" class="text-decoration-none text-dark">
                                        Upload Date
                                        <?php if ($sort === 'created_at'): ?>
                                            <i class="bi bi-arrow-<?= $order === 'ASC' ? 'up' : 'down' ?>"></i>
                                        <?php endif; ?>
                                    </a>
                                </th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($files as $file): ?>
                                <tr>
                                    <td>
                                        <i class="bi bi-file-earmark file-icon me-2"></i>
                                        <?= Helper::sanitize($file['original_name']) ?>
                                    </td>
                                    <td class="file-size"><?= Helper::formatFileSize($file['file_size']) ?></td>
                                    <td><?= Helper::formatDate($file['created_at']) ?></td>
                                    <td>
                                        <a href="download.php?id=<?= $file['id'] ?>" class="btn btn-sm btn-primary">
                                            <i class="bi bi-download"></i>
                                        </a>
                                        <form action="index.php" method="post" class="d-inline">
                                            <input type="hidden" name="csrf_token" value="<?= Helper::generateCsrfToken() ?>">
                                            <input type="hidden" name="file_id" value="<?= $file['id'] ?>">
                                            <button type="submit" name="delete_file" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this file?')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 