<?php

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'php_file_manager');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');

// Application URL (with trailing slash)
define('APP_URL', 'http://your-domain.com/');

// Error reporting (set to 0 in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Session configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 1);

// Time zone
date_default_timezone_set('UTC'); 