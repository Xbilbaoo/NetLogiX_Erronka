<?php
// src/server/model/Connection.php
class Connection {
  private static ?PDO $pdo = null;

  public static function getInstance(): PDO {
    if (self::$pdo === null) {
      $host = '127.0.0.1';
      $db   = 'netlogix';
      $user = 'root';
      $pass = '';
      $dsn  = "mysql:host=$host;dbname=$db;charset=utf8mb4";
      self::$pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
      ]);
    }
    return self::$pdo;
  }
}

