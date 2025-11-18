<?php
// src/server/controller/session.php
declare(strict_types=1);

ini_set('session.use_strict_mode', '1');
ini_set('session.use_only_cookies', '1');
ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_secure', (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? '1' : '0');
ini_set('session.cookie_samesite', 'Lax');

session_name('SID');
if (session_status() !== PHP_SESSION_ACTIVE) {
  session_start();
}
