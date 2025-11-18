<?php
declare(strict_types=1);

require_once __DIR__ . '/../model/Connection.php';

function json_response(int $status, array $payload): void {
  http_response_code($status);
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode($payload, JSON_UNESCAPED_UNICODE);
  exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Allow: POST');
  json_response(405, ['success'=>false, 'message'=>'Method not allowed']);
}

$ctype = $_SERVER['CONTENT_TYPE'] ?? '';
$in = stripos($ctype, 'application/json') !== false
  ? json_decode(file_get_contents('php://input'), true) ?: []
  : $_POST;

$CIF       = strtoupper(trim((string)($in['cif'] ?? '')));
$email     = trim((string)($in['email'] ?? ''));
$password  = (string)($in['password'] ?? '');
$Kizena    = trim((string)($in['kizena'] ?? ''));
$Eizena    = trim((string)($in['eizena'] ?? ''));
$Kabizena  = trim((string)($in['kabizena'] ?? ''));
$Telefonoa = trim((string)($in['telefonoa'] ?? ''));
$Empresa   = trim((string)($in['empresa'] ?? ''));

if ($CIF === '' || $email === '' || $password === '') {
  json_response(400, ['success'=>false, 'message'=>'CIF, email y password son obligatorios']);
}
if (!preg_match('/^[A-HJNPQRSUVW]\d{7}[0-9A-J]$/', $CIF)) {
  json_response(422, ['success'=>false, 'message'=>'CIF con formato no válido']);
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  json_response(422, ['success'=>false, 'message'=>'Email no válido']);
}
if (strlen($password) < 8) {
  json_response(422, ['success'=>false, 'message'=>'Contraseña demasiado corta']);
}

try {
  $pdo = DB::pdo();

  $check = $pdo->prepare('SELECT ID FROM Erabiltzaileak WHERE Email = :email OR CIF = :cif LIMIT 1');
  $check->execute([':email'=>$email, ':cif'=>$CIF]);
  if ($check->fetch()) {
    json_response(409, ['success'=>false, 'message'=>'Ya existe un usuario con ese Email o CIF']);
  }

  $hash = password_hash($password, PASSWORD_DEFAULT);

  $ins = $pdo->prepare('
    INSERT INTO Erabiltzaileak (CIF, Email, psswd, Kizena, Eizena, Kabizena, Telefonoa)
    VALUES (:cif, :email, :pwd, :kiz, :eiz, :kab, :tel)
  ');
  $ins->execute([
    ':cif'=>$CIF, ':email'=>$email, ':pwd'=>$hash,
    ':kiz'=>$Kizena, ':eiz'=>$Eizena, ':kab'=>$Kabizena, ':tel'=>$Telefonoa
  ]);

  $uid = (int)$pdo->lastInsertId();

  $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
  session_set_cookie_params(['lifetime'=>0,'path'=>'/','domain'=>'','secure'=>$secure,'httponly'=>true,'samesite'=>'Lax']);
  if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
  session_regenerate_id(true);
  $_SESSION['uid'] = $uid;
  $_SESSION['email'] = $email;

  $dir = __DIR__ . '/../../data/users';
  if (!is_dir($dir)) { @mkdir($dir, 0775, true); }

  // JSON mínimo, empresa separada
  $profile = [
    'id'       => $uid,
    'emaila'   => $email,
    'kizena'   => $Kizena,
    'eizena'   => $Eizena,
    'kabizena' => $Kabizena,
    'izen'     => trim(implode(' ', array_filter([$Kizena,$Eizena,$Kabizena], fn($s)=>$s!==''))),
    'tlf'      => $Telefonoa
  ];
  file_put_contents($dir . "/{$uid}.json", json_encode($profile, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT));

  json_response(201, [
    'success'=>true,
    'message'=>'Registro correcto',
    'user'=>['id'=>$uid, 'email'=>$email],
    'profile_json'=>"/src/server/data/users/{$uid}.json"
  ]);
} catch (Throwable $e) {
  json_response(500, ['success'=>false, 'message'=>'Error de servidor']);
}






