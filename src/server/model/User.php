<?php

namespace Model;

use Model\DB\MConnection;

require_once __DIR__ . '/DB/Connection.php';

class User
{

  public static function hasPermission($username, $password)
  {
    $connectionInstance = MConnection::getInstance();
    $connection = $connectionInstance->getConnection();

    // Usar sentencias preparadas para prevenir inyección SQL
    	$stmt = $connection->prepare("SELECT ID, CIF, Email, psswd FROM erabiltzaileak WHERE email = ?");

    if ($stmt === false) {
      throw new \Exception('DB prepare failed: ' . $connection->error);
    }

    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
      $user = $result->fetch_assoc();
      if (password_verify($password, $user['password_hash'])) {

        $stmt->close();
        return $user;
      }
    }

    $stmt->close();
    return false;
  }

  public static function createUser($cif, $password, $email, $role = 'user')
  {
    $connectionInstance = MConnection::getInstance();
    $connection = $connectionInstance->getConnection();

    // Hashear la contraseña
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $connection->prepare("INSERT INTO Erabiltzaileak (CIF, Email, psswd, rola) VALUES (?, ?, ?, ?)");

		if ($stmt === false) {
			throw new \Exception('DB prepare failed: ' . $connection->error);
		}

    $stmt->bind_param("ssss", $cif, $email, $password_hash, $role);

    $result = $stmt->execute();
    $stmt->close();

    return $result;
    
  }

  public static function userExists($username)
    {
        $connectionInstance = MConnection::getInstance();
        $connection = $connectionInstance->getConnection();
        
        $stmt = $connection->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $exists = $result->num_rows > 0;
        
        $stmt->close();
        return $exists;
    }
}
