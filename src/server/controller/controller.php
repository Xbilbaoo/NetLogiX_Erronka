<?php
// server/model/DB/Connection.php
declare(strict_types=1);

class DB {
  public static function pdo(): PDO {
    static $pdo = null;
    if ($pdo instanceof PDO) return $pdo;

    $dsn = 'mysql:host=127.0.0.1;dbname=NetlogiX;charset=utf8mb4';
    $user = 'root';
    $pass = '';

    $opts = [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
      PDO::ATTR_EMULATE_PREPARES => false,
    ];
    $pdo = new PDO($dsn, $user, $pass, $opts);
    return $pdo;
  }
}