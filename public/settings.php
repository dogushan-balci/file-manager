<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../autoload.php';

use App\Settings;
use App\Helper;

session_start();

// Check session
if (!isset($_SESSION['user_id'])) {
    Helper::redirect('login.php');
}

$settings = new Settings();
$currentSettings = $settings->get();
$errors = [];
$success = false;

// Update settings
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Helper::validateCsrfToken($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid CSRF token.';
    } else {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        $allowedExtensions = $_POST['allowed_extensions'] ?? '';
        $maxFileSize = (int)($_POST['max_file_size'] ?? 0);

        if (empty($username)) {
            $errors[] = 'Username is required.';
        }

        if (!empty($password) && strlen($password) < 6) {
            $errors[] = 'Password must be at least 6 characters.';
        }

        if (empty($allowedExtensions)) {
            $errors[] = 'Allowed file extensions are required.';
        }

        if ($maxFileSize <= 0) {
            $errors[] = 'Maximum file size must be a valid number.';
        }

        if (empty($errors)) {
            $updateData = [
                'username' => $username,
                'allowed_extensions' => $allowedExtensions,
                'max_file_size' => $maxFileSize,
                'id' => $currentSettings['id']
            ];

            if (!empty($password)) {
                $updateData['password'] = $password;
            }

            if ($settings->update($updateData)) {
                $success = true;
                $currentSettings = $settings->get();
            } else {
                $errors[] = 'Error updating settings.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - File Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
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
                        <a class="nav-link active" href="settings.php">
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
        <?php if ($success): ?>
            <div class="alert alert-success alert-dismissible fade show">
                Settings updated successfully.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?php foreach ($errors as $error): ?>
                    <?= Helper::sanitize($error) ?><br>
                <?php endforeach; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Settings</h5>
                <form action="settings.php" method="post">
                    <input type="hidden" name="csrf_token" value="<?= Helper::generateCsrfToken() ?>">
                    
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" value="<?= Helper::sanitize($currentSettings['username']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password (Leave blank if you don't want to change)</label>
                        <input type="password" class="form-control" id="password" name="password">
                    </div>

                    <div class="mb-3">
                        <label for="allowed_extensions" class="form-label">Allowed File Extensions (comma separated)</label>
                        <input type="text" class="form-control" id="allowed_extensions" name="allowed_extensions" value="<?= Helper::sanitize($currentSettings['allowed_extensions']) ?>" required>
                        <div class="form-text">Example: jpg,jpeg,png,pdf,doc,docx</div>
                    </div>

                    <div class="mb-3">
                        <label for="max_file_size" class="form-label">Maximum File Size (MB)</label>
                        <input type="number" class="form-control" id="max_file_size" name="max_file_size" value="<?= (int)($currentSettings['max_file_size'] / 1024 / 1024) ?>" required>
                    </div>

                    <button type="submit" class="btn btn-primary">Save</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 