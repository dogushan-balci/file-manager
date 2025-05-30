<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../autoload.php';

use App\Settings;
use App\Helper;

session_start();

// Check session
if (isset($_SESSION['user_id'])) {
    Helper::redirect('index.php');
}

$errors = [];

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Helper::validateCsrfToken($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid CSRF token.';
    } else {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($username)) {
            $errors[] = 'Username is required.';
        }

        if (empty($password)) {
            $errors[] = 'Password is required.';
        }

        if (empty($errors)) {
            $settings = new Settings();
            
            // Debug için
            error_log("Login attempt - Username: " . $username);
            
            if ($settings->authenticate($username, $password)) {
                $_SESSION['user_id'] = 1;
                Helper::setFlashMessage('success', 'Login successful.');
                Helper::redirect('index.php');
            } else {
                $errors[] = 'Invalid username or password.';
                // Debug için
                error_log("Login failed for username: " . $username);
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
    <title>Login - File Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .login-container {
            max-width: 400px;
            margin: 100px auto;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-container">
            <div class="card">
                <div class="card-body">
                    <h3 class="card-title text-center mb-4">File Manager</h3>
                    
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <?php foreach ($errors as $error): ?>
                                <?= Helper::sanitize($error) ?><br>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <form action="login.php" method="post">
                        <input type="hidden" name="csrf_token" value="<?= Helper::generateCsrfToken() ?>">
                        
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Login</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 