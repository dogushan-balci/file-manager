<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../autoload.php';

use App\Helper;

session_start();

// Oturumu sonlandır
session_destroy();

// Çıkış mesajını ayarla ve giriş sayfasına yönlendir
Helper::setFlashMessage('success', 'Başarıyla çıkış yaptınız.');
Helper::redirect('login.php'); 