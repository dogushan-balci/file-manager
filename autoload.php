<?php

spl_autoload_register(function ($class) {
    // Map App namespace to src directory
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/src/';

    // Check if class uses App namespace
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    // Get relative class name
    $relative_class = substr($class, $len);

    // Convert namespace separators to directory separators
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    // Load file if exists
    if (file_exists($file)) {
        require $file;
    }
}); 