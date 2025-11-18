<?php
require_once __DIR__ . '/../model/Connection.php';
$pdo = Connection::getInstance();
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // excepciones
header('Content-Type: application/json; charset=utf-8');
try {
  // ... recoger POST e INSERT como ya tienes ...
  echo json_encode(['ok'=>true,'id'=>$pdo->lastInsertId()]);
} catch (PDOException $ex) {
  $info = $stmt?->errorInfo() ?: $pdo->errorInfo(); // [sqlstate, code, msg]
  http_response_code(400);
  echo json_encode([
    'ok'=>false,
    'sqlstate'=>$info[0]??$ex->getCode(),
    'driver_code'=>$info[1]??null,
    'message'=>$info[2]??$ex->getMessage()
  ]);
}

