<?php

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/error.log');

// Session settings
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.cookie_samesite', 'Strict');
ini_set('session.gc_maxlifetime', 3600);
ini_set('session.use_strict_mode', 1);

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'php_file_manager');
define('DB_USER', 'root');
define('DB_PASS', '');

// Application settings
define('APP_NAME', 'PHP File Manager');
define('APP_URL', 'https://siteniz.com');
define('APP_ROOT', dirname(__DIR__));

// Time zone and character encoding
date_default_timezone_set('UTC');
mb_internal_encoding('UTF-8');

// Security headers
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Content-Security-Policy: default-src \'self\'; script-src \'self\' \'unsafe-inline\' \'unsafe-eval\' https://cdn.jsdelivr.net; style-src \'self\' \'unsafe-inline\' https://cdn.jsdelivr.net; font-src \'self\' https://cdn.jsdelivr.net; img-src \'self\' data:;'); 