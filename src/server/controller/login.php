<?php
declare(strict_types=1);
require_once __DIR__ . '/../model/Connection.php';

function json_response(int $status, array $payload): void {
  http_response_code($status);
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode($payload, JSON_UNESCAPED_UNICODE);
  exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Allow: POST'); json_response(405, ['success'=>false,'message'=>'Method not allowed']); }

$ct = strtolower($_SERVER['CONTENT_TYPE'] ?? '');
$in = stripos($ct,'application/json')!==false ? json_decode(file_get_contents('php://input'), true) ?: [] : $_POST;

$email = trim((string)($in['email'] ?? ''));
$pass  = (string)($in['password'] ?? '');
if ($email===''||$pass==='') { json_response(400, ['success'=>false,'message'=>'Email y password son obligatorios']); }

try {
  $pdo = Connection::getInstance();
  $st = $pdo->prepare('SELECT ID,Email,psswd,Kizena,Eizena,Kabizena,Telefonoa FROM Erabiltzaileak WHERE Email=:email LIMIT 1');
  $st->execute([':email'=>$email]);
  $user = $st->fetch(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
  json_response(500, ['success'=>false,'message'=>'Error de servidor']);
}

if (!$user) { json_response(401, ['success'=>false,'message'=>'Email no encontrado']); }
$hash = (string)($user['psswd'] ?? '');
$len  = strlen($hash);
if (!password_verify($pass, $hash)) {
  json_response(401, ['success'=>false,'message'=>'Password incorrecta','hash_len'=>$len]); // si <55, hash truncado
}

$secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
session_set_cookie_params(['lifetime'=>0,'path'=>'/','domain'=>'','secure'=>$secure,'httponly'=>true,'samesite'=>'Lax']);
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
session_regenerate_id(true);
$_SESSION['uid']=(int)$user['ID'];
$_SESSION['email']=$user['Email'];

$dir = __DIR__ . '/../../data/users';
if (!is_dir($dir)) { @mkdir($dir, 0775, true); }
$path = $dir . "/{$_SESSION['uid']}.json";
$profile = [
  'id'=>(int)$user['ID'],'emaila'=>$user['Email'],
  'kizena'=>$user['Kizena'] ?? '','kabizena'=>$user['Kabizena'] ?? '',
  'eizena'=>$user['Eizena'] ?? '',
  'izen'=>trim(implode(' ', array_filter([$user['Kizena']??'',$user['Kabizena']??'']))),
  'tlf'=>$user['Telefonoa'] ?? ''
];
$old = is_file($path) ? (json_decode(file_get_contents($path), true) ?: []) : [];
file_put_contents($path, json_encode(array_merge($old, $profile), JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT));

json_response(200, ['success'=>true,'message'=>'Login correcto','user'=>['id'=>(int)$user['ID'],'email'=>$user['Email']]]);









