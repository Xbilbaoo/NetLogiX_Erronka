<?php
declare(strict_types=1);

function json_response(int $status, array $payload): void {
  http_response_code($status);
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode($payload, JSON_UNESCAPED_UNICODE);
  exit;
}
session_start();
if (!isset($_SESSION['uid'])) {
  json_response(401, ['authenticated'=>false]);
}
$uid = (int)$_SESSION['uid'];
$file = __DIR__ . '/../../data/users/' . $uid . '.json';
if (!is_file($file)) {
  json_response(200, ['authenticated'=>true, 'profile'=>['id'=>$uid, 'emaila'=>$_SESSION['email']]]);
}
$profile = json_decode(file_get_contents($file), true) ?: [];
json_response(200, ['authenticated'=>true, 'profile'=>$profile]);
