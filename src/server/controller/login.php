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

$email = trim((string)($in['email'] ?? ''));
$pass  = (string)($in['password'] ?? '');

if ($email === '' || $pass === '') {
  json_response(400, ['success'=>false, 'message'=>'Email y password son obligatorios']);
}

try {
  $pdo = DB::pdo();
  $st = $pdo->prepare('
    SELECT ID, Email, psswd, Kizena, Eizena, Kabizena, Telefonoa
    FROM Erabiltzaileak
    WHERE Email = :email
    LIMIT 1
  ');
  $st->execute([':email'=>$email]);
  $user = $st->fetch(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
  json_response(500, ['success'=>false, 'message'=>'Error de servidor']);
}

if (!$user || !password_verify($pass, $user['psswd'])) {
  json_response(401, ['success'=>false, 'message'=>'Credenciales inválidas']);
}

$secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
session_set_cookie_params(['lifetime'=>0,'path'=>'/','domain'=>'','secure'=>$secure,'httponly'=>true,'samesite'=>'Lax']);
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
session_regenerate_id(true);
$_SESSION['uid']   = (int)$user['ID'];
$_SESSION['email'] = $user['Email'];

$dir = __DIR__ . '/../../data/users';
if (!is_dir($dir)) { @mkdir($dir, 0775, true); }
$file = $dir . '/' . $_SESSION['uid'] . '.json';

$minimal = [
  'id'       => (int)$user['ID'],
  'emaila'   => $user['Email'],
  'kizena'   => $user['Kizena'] ?? '',
  'eizena'   => $user['Eizena'] ?? '',
  'kabizena' => $user['Kabizena'] ?? '',
  'izen'     => trim(implode(' ', array_filter([$user['Kizena'] ?? '', $user['Kabizena'] ?? ''], fn($s)=>$s!==''))),
  'tlf'      => $user['Telefonoa'] ?? '',
];

if (!is_file($file)) {
  file_put_contents($file, json_encode($minimal, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT));
} else {
  $curr = json_decode(file_get_contents($file), true) ?: [];
  $curr = array_merge($curr, array_filter($minimal, fn($v)=>$v!==null));
  file_put_contents($file, json_encode($curr, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT));
}

json_response(200, [
  'success'=>true,
  'message'=>'Login correcto',
  'user'=>['id'=>(int)$user['ID'], 'email'=>$user['Email']],
  'profile_json'=>"/src/server/data/users/{$_SESSION['uid']}.json"
]);








