<?php

/*if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  exit('Method not allowed');
}

function field(string $k): string { return trim($_POST[$k] ?? ''); }

$cif  = strtoupper(field('cif'));
$email = field('email');
$pass  = $_POST['password'] ?? '';

if ($cif === '' || $email === '' || $pass === '') {
  header('Location: /src/client/pages/register.html?err=missing');
  exit;
}

// Validación básica de formato de CIF en servidor (formato, no dígito de control completo)
if (!preg_match('/^[A-HJNPQRSUVW]\d{7}[0-9A-J]$/', $cif)) {
  header('Location: /src/client/pages/register.html?err=cif');
  exit;
}

try {
  $pdo = DB::pdo();

  // Evitar duplicados por Email o CIF
  $check = $pdo->prepare('SELECT ID FROM Erabiltzaileak WHERE Email = :email OR CIF = :cif LIMIT 1');
  $check->execute([':email' => $email, ':cif' => $cif]);
  if ($check->fetch()) {
    header('Location: /src/client/pages/register.html?err=exists');
    exit;
  }

  $hash = password_hash($pass, PASSWORD_DEFAULT); // se guarda en psswd
  $ins = $pdo->prepare('INSERT INTO Erabiltzaileak (CIF, Email, psswd) VALUES (:cif, :email, :pwd)');
  $ins->execute([
    ':cif'   => $cif,
    ':email' => $email,
    ':pwd'   => $hash,
  ]);

  // Auto-login
  $uid = (int)$pdo->lastInsertId();
  session_regenerate_id(true);
  $_SESSION['uid']   = $uid;
  $_SESSION['email'] = $email;

header('Location: /NetLogiX_Erronka-landing/src/client/index.html?register=ok');
  exit;

} catch (Throwable $e) {
  header('Location: /NetLogiX_Erronka-landing/src/client/pages/register.html?err=server');
  exit;
}*/

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // 405 Method Not Allowed
    echo json_encode(['success' => false, 'message' => 'Método no permitido. Se esperaba POST.']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

$username = $input['username'] ?? null;
$password = $input['password'] ?? null;

?>