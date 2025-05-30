<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../autoload.php';

use App\File;
use App\Helper;

session_start();

// Check session
if (!isset($_SESSION['user_id'])) {
    Helper::redirect('login.php');
}

// Check file ID
if (!isset($_GET['id'])) {
    Helper::setFlashMessage('error', 'File ID not specified.');
    Helper::redirect('index.php');
}

$file = new File();
$fileData = $file->getById((int)$_GET['id']);

if (!$fileData) {
    Helper::setFlashMessage('error', 'File not found.');
    Helper::redirect('index.php');
}

$filePath = __DIR__ . '/../uploads/' . $fileData['filename'];

if (!file_exists($filePath)) {
    Helper::setFlashMessage('error', 'File not found on disk.');
    Helper::redirect('index.php');
}

// Download file
header('Content-Type: ' . $fileData['file_type']);
header('Content-Disposition: attachment; filename="' . $fileData['original_name'] . '"');
header('Content-Length: ' . $fileData['file_size']);
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

readfile($filePath);
exit; 