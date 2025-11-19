<?php
declare(strict_types=1);
function json_response(int $status, array $payload): void {
  http_response_code($status);
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode($payload, JSON_UNESCAPED_UNICODE);
  exit;
}
session_start();
if (!isset($_SESSION['uid'])) { json_response(401, ['authenticated'=>false]); }
$uid = (int)$_SESSION['uid'];
$file = __DIR__ . '/../../data/users/' . $uid . '.json';
$profile = is_file($file) ? (json_decode(file_get_contents($file), true) ?: []) : ['id'=>$uid,'emaila'=>$_SESSION['email']];

if (empty($profile['helbidea']['jatorria_txt'])) {
  require_once __DIR__ . '/../model/Connection.php';
  $pdo = Connection::getInstance();
  $q = $pdo->prepare("SELECT CONCAT_WS(', ', Helbidea, CP, Hiria, Probintzia) AS jtxt
                      FROM helbideak WHERE ID_erab=:u ORDER BY ID DESC LIMIT 1");
  $q->execute([':u'=>$uid]);
  $profile['helbidea']['jatorria_txt'] = $q->fetchColumn() ?: '';
}
json_response(200, ['authenticated'=>true, 'profile'=>$profile]);
