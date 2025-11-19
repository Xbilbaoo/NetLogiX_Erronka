<?php
declare(strict_types=1);
require_once __DIR__ . '/../model/Connection.php';

function json_response(int $status, array $payload): void {
  http_response_code($status);
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode($payload, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
  exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Allow: POST'); json_response(405, ['success'=>false,'message'=>'Method not allowed']); }

$ctype = strtolower($_SERVER['CONTENT_TYPE'] ?? '');
$in = stripos($ctype,'application/json')!==false ? json_decode(file_get_contents('php://input'), true) ?: [] : $_POST;

$CIF       = strtoupper(trim((string)($in['cif'] ?? '')));
$email     = trim((string)($in['email'] ?? ''));
$password  = (string)($in['password'] ?? '');
$Kizena    = trim((string)($in['kizena'] ?? ''));
$Kabizena  = trim((string)($in['kabizena'] ?? ''));
$Eizena    = trim((string)($in['eizena'] ?? ''));
$Telefonoa = trim((string)($in['telefonoa'] ?? ''));

$Helbidea   = trim((string)($in['helbidea'] ?? ''));
$CP         = trim((string)($in['cp'] ?? ''));
$Hiria      = trim((string)($in['hiria'] ?? ''));
$Probintzia = trim((string)($in['probintzia'] ?? ''));

if ($CIF===''||$email===''||$password===''||$Helbidea===''||$CP===''||$Hiria===''||$Probintzia==='') {
  json_response(400, ['success'=>false,'message'=>'CIF, email, password y dirección son obligatorios']);
}

// VALIDACIÓN LAZA SOLO PARA DESARROLLO:
// 1 letra + 7 dígitos + 1 alfanumérico (no comprueba control real)
if (!preg_match('/^[A-Z]\d{7}[A-Z0-9]$/i', $CIF)) {
  json_response(422, ['success'=>false,'message'=>'CIF con formato no válido (dev)']);
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { json_response(422, ['success'=>false,'message'=>'Email no válido']); }
if (strlen($password) < 8) { json_response(422, ['success'=>false,'message'=>'Contraseña demasiado corta']); }

try {
  $pdo = Connection::getInstance();
  $dup = $pdo->prepare('SELECT ID FROM Erabiltzaileak WHERE Email=:email OR CIF=:cif LIMIT 1');
  $dup->execute([':email'=>$email, ':cif'=>$CIF]);
  if ($dup->fetch()) { json_response(409, ['success'=>false,'message'=>'Ya existe un usuario con ese Email o CIF']); }

  $hash = password_hash($password, PASSWORD_DEFAULT); // columna VARCHAR(255) [web:257][web:252]

  $pdo->beginTransaction();
  $insU = $pdo->prepare('INSERT INTO Erabiltzaileak (CIF,Email,psswd,Kizena,Eizena,Kabizena,Telefonoa)
                         VALUES (:cif,:email,:pwd,:kiz,:eiz,:kab,:tel)');
  $insU->execute([':cif'=>$CIF, ':email'=>$email, ':pwd'=>$hash, ':kiz'=>$Kizena, ':eiz'=>$Eizena, ':kab'=>$Kabizena, ':tel'=>$Telefonoa]);
  $uid = (int)$pdo->lastInsertId();

  $insA = $pdo->prepare('INSERT INTO helbideak (Helbidea,CP,Hiria,Probintzia,ID_erab)
                         VALUES (:dir,:cp,:hiria,:prob,:uid)');
  $insA->execute([':dir'=>$Helbidea, ':cp'=>$CP, ':hiria'=>$Hiria, ':prob'=>$Probintzia, ':uid'=>$uid]);
  $pdo->commit();

  $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
  session_set_cookie_params(['lifetime'=>0,'path'=>'/','domain'=>'','secure'=>$secure,'httponly'=>true,'samesite'=>'Lax']);
  if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
  session_regenerate_id(true);
  $_SESSION['uid']=$uid; $_SESSION['email']=$email;

  $dir = __DIR__ . '/../../data/users';
  if (!is_dir($dir)) { @mkdir($dir, 0775, true); }
  $jatorria_txt = trim(implode(', ', array_filter([$Helbidea,$CP,$Hiria,$Probintzia]))); // concat [web:217]
  $profile = [
    'id'=>$uid,'emaila'=>$email,'kizena'=>$Kizena,'kabizena'=>$Kabizena,'eizena'=>$Eizena,
    'izen'=>trim(implode(' ', array_filter([$Kizena,$Kabizena]))),'tlf'=>$Telefonoa,
    'helbidea'=>['helbidea'=>$Helbidea,'cp'=>$CP,'hiria'=>$Hiria,'probintzia'=>$Probintzia,'jatorria_txt'=>$jatorria_txt]
  ];
  file_put_contents($dir . "/{$uid}.json", json_encode($profile, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT));
  json_response(201, ['success'=>true,'message'=>'Registro correcto','user'=>['id'=>$uid,'email'=>$email]]);
} catch (PDOException $e) {
  if ($pdo->inTransaction()) { $pdo->rollBack(); }
  $info = isset($insA)?$insA->errorInfo():(isset($insU)?$insU->errorInfo():$pdo->errorInfo());
  json_response(500, ['success'=>false,'sqlstate'=>$info[0]??$e->getCode(),'driver_code'=>$info[1]??null,'message'=>$info[2]??$e->getMessage()]);
}
















