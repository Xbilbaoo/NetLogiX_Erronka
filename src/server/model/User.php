<?php
declare(strict_types=1);

require_once __DIR__ . '/Connection.php';

class UserModel {
  public static function findByEmail(string $email): ?array {
    $pdo = DB::pdo();
    $stmt = $pdo->prepare('SELECT ID, Email, psswd FROM Erabiltzaileak WHERE Email = :email LIMIT 1');
    $stmt->execute([':email' => $email]);
    $row = $stmt->fetch();
    return $row ?: null;
  }
}

