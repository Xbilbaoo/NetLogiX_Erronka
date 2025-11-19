<?php
require_once __DIR__ . '/../model/Connection.php';
$pdo = Connection::getInstance();
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
header('Content-Type: application/json; charset=utf-8');

// JSON o form
$raw = file_get_contents('php://input');
$ct  = strtolower($_SERVER['CONTENT_TYPE'] ?? '');
$in  = $_POST;
if ((strpos($ct,'application/json')!==false) || ($raw && preg_match('~^\s*[\{\[]~',$raw))) {
  $json = json_decode($raw, true);
  if (json_last_error()===JSON_ERROR_NONE && is_array($json)) { $in = $json; }
}

// Campos form
$izen        = trim((string)($in['izen'] ?? ''));
$emaila      = trim((string)($in['emaila'] ?? ''));
$tlf         = trim((string)($in['tlf'] ?? ''));
$empresa     = trim((string)($in['empresa'] ?? ''));
$zerbitzuak  = trim((string)($in['zerbitzuak'] ?? ''));
$descripcion = trim((string)($in['descripcion'] ?? ''));
$comentarios = trim((string)($in['comentarios'] ?? ''));

// Usuario
session_start();
$idErab = isset($_SESSION['uid']) ? (int)$_SESSION['uid'] : null;

// Extras
$zg = $in['zerbitzu_gehigarriak'] ?? [];
if (is_string($zg)) { $zg = array_filter(array_map('trim', explode(',',$zg)), 'strlen'); }
$zgCsv = is_array($zg) ? implode(',', array_map('intval',$zg)) : '';

// Fecha / estado
$data = trim((string)($in['data'] ?? ''));
if ($data && preg_match('~^(\d{2})/(\d{2})/(\d{4})$~',$data,$m)) { $data="{$m[3]}-{$m[2]}-{$m[1]}"; }
$egoera  = trim((string)($in['egoera'] ?? 'Pendiente'));
$tamaina = trim((string)($in['tamaina'] ?? ''));
$pisua   = ($in['pisua_kg'] ?? '') === '' ? null : (int)$in['pisua_kg'];

// Direcciones
$jId = isset($in['jatorria_id']) ? (int)$in['jatorria_id'] : null;
$hId = isset($in['helmuga_id'])  ? (int)$in['helmuga_id']  : null;

$j_dir  = trim((string)($in['jatorria_helbidea'] ?? ($in['jatorria'] ?? '')));
$j_cp   = ($in['jatorria_cp'] ?? '') === '' ? null : (int)$in['jatorria_cp'];
$j_hiri = trim((string)($in['jatorria_hiria'] ?? ''));
$j_prob = trim((string)($in['jatorria_probintzia'] ?? ''));

$h_dir  = trim((string)($in['helmuga_helbidea'] ?? ($in['helmuga'] ?? '')));
$h_cp   = ($in['helmuga_cp'] ?? '') === '' ? null : (int)$in['helmuga_cp'];
$h_hiri = trim((string)($in['helmuga_hiria'] ?? ''));
$h_prob = trim((string)($in['helmuga_probintzia'] ?? ''));

if (!$jId && $j_dir==='') { http_response_code(400); echo json_encode(['ok'=>false,'message'=>'Falta Jatorria']); exit; }
if (!$hId && $h_dir==='') { http_response_code(400); echo json_encode(['ok'=>false,'message'=>'Falta Helmuga']); exit; }

try {
  $pdo->beginTransaction();

  if (!$jId) {
    $insAdr = $pdo->prepare("INSERT INTO helbideak (Helbidea,CP,Hiria,Probintzia,ID_erab)
                             VALUES (:dir,:cp,:hiria,:prob,:uid)");
    $insAdr->execute([':dir'=>$j_dir, ':cp'=>$j_cp, ':hiria'=>$j_hiri, ':prob'=>$j_prob, ':uid'=>$idErab]);
    $jId = (int)$pdo->lastInsertId();
  }
  if (!$hId) {
    $insAdr2 = $pdo->prepare("INSERT INTO helbideak (Helbidea,CP,Hiria,Probintzia,ID_erab)
                              VALUES (:dir,:cp,:hiria,:prob,:uid)");
    $insAdr2->execute([':dir'=>$h_dir, ':cp'=>$h_cp, ':hiria'=>$h_hiri, ':prob'=>$h_prob, ':uid'=>$idErab]);
    $hId = (int)$pdo->lastInsertId();
  }

  $stmt = $pdo->prepare("INSERT INTO eskaera
     (Jatorria,Helmuga,Biltegia,Tamaina,Pisua,Eskaera_Data,Egoera,ID_erab,zerbitzu_gehigarriak)
     VALUES (:j,:h,:b,:t,:p,:f,:e,:u,:zg)");
  $stmt->execute([
    ':j'=>$jId, ':h'=>$hId, ':b'=>null, ':t'=>$tamaina, ':p'=>$pisua,
    ':f'=>($data!==''?$data:null), ':e'=>$egoera, ':u'=>$idErab, ':zg'=>$zgCsv
  ]);
  $idEskaera = (int)$pdo->lastInsertId();

  $adr = $pdo->prepare("SELECT Helbidea,CP,Hiria,Probintzia FROM helbideak WHERE ID=:id");
  $adr->execute([':id'=>$jId]); $J = $adr->fetch();
  $jText = trim(implode(', ', array_filter([$J['Helbidea']??'',$J['CP']??'',$J['Hiria']??'',$J['Probintzia']??'']))); // concat [web:217]
  $adr->execute([':id'=>$hId]); $H = $adr->fetch();
  $hText = trim(implode(', ', array_filter([$H['Helbidea']??'',$H['CP']??'',$H['Hiria']??'',$H['Probintzia']??''])));

  if ($idErab) {
    $dirJson = __DIR__ . '/../../data/users/' . $idErab . '.json';
    if (!is_file($dirJson)) { @mkdir(dirname($dirJson), 0775, true); }
    $prof = is_file($dirJson) ? (json_decode(file_get_contents($dirJson), true) ?: []) : [];
    if ($empresa !== '') { $prof['eizena'] = $empresa; }
    $prof['ult_pedido'] = ['jatorria_txt'=>$jText,'helmuga_txt'=>$hText,'zerbitzuak'=>$zerbitzuak,'zg'=>$zg];
    @file_put_contents($dirJson, json_encode($prof, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT));
  }

  $pdo->commit();
  echo json_encode(['ok'=>true,'id'=>$idEskaera,'jatorria'=>$jId,'helmuga'=>$hId,'jatorria_txt'=>$jText,'helmuga_txt'=>$hText]);
} catch (PDOException $ex) {
  if ($pdo->inTransaction()) { $pdo->rollBack(); }
  $info = isset($stmt)?$stmt->errorInfo():$pdo->errorInfo();
  http_response_code(400);
  echo json_encode(['ok'=>false,'sqlstate'=>$info[0]??$ex->getCode(),'driver_code'=>$info[1]??null,'message'=>$info[2]??$ex->getMessage(),'contentType'=>$ct]);
}












