<?php
// src/server/controller/login.php
declare(strict_types=1);

require_once __DIR__ . '/session.php';
require_once __DIR__ . '/../model/Connection.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  exit('Method not allowed');
}

$email = trim($_POST['email'] ?? ''); // tu form usa "username"
$pass  = $_POST['password'] ?? '';

if ($email === '' || $pass === '') {
  header('Location: /src/client/index.html?login=missing');
  exit;
}

$pdo = DB::pdo();
$st = $pdo->prepare('SELECT ID, Email, psswd FROM Erabiltzaileak WHERE Email = :email LIMIT 1');
$st->execute([':email' => $email]);
$user = $st->fetch();

if (!$user || !password_verify($pass, $user['psswd'])) {
  header('Location: /src/client/index.html?login=invalid');
  exit;
}

session_regenerate_id(true);
$_SESSION['uid'] = (int)$user['ID'];
$_SESSION['email'] = $user['Email'];

header('Location: ../../../formulario.html?login=ok');
exit;

