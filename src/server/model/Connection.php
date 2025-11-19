<?php
final class Connection {
  private static ?PDO $pdo = null;
  public static function getInstance(): PDO {
    if (!self::$pdo) {
      $dsn  = 'mysql:host=127.0.0.1;dbname=netlogix;charset=utf8mb4';
      $user = 'root';
      $pass = '';
      self::$pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
      ]);
    }
    return self::$pdo;
  }
}




