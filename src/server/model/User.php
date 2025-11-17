<?php

use Model\DB\Connection;



class User {
  public static function findByEmail(string $email): ?array
  {
    $pdo = DB::pdo();
    $stmt = $pdo->prepare('SELECT ID, Email, psswd FROM Erabiltzaileak WHERE Email = :email LIMIT 1');
    $stmt->execute([':email' => $email]);
    $row = $stmt->fetch();
    return $row ?: null;
  }

  public static function createUser($username, $password, $firstName, $lastName, $email = null, $role = 'user')
  {
    $connectionInstance = Connection::getInstance();
    $connection = $connectionInstance->getConnection();

    // Hashear la contraseña
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $connection->prepare("INSERT INTO users (username, password_hash, email, first_name, last_name, role) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $username, $password_hash, $email, $firstName, $lastName, $role);

    $result = $stmt->execute();
    $stmt->close();

    return $result;
  }

  
}
