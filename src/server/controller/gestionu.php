<?php
declare(strict_types=1);
require_once __DIR__ . '/../model/Connection.php';

/* Sesión segura */
$secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
session_set_cookie_params([
  'lifetime' => 0,
  'path'     => '/',
  'domain'   => '',
  'secure'   => $secure,
  'httponly' => true,
  'samesite' => 'Lax'
]);
if (session_status() !== PHP_SESSION_ACTIVE) session_start();

/* Auth */
if (!isset($_SESSION['uid'])) { header('Location: login.php'); exit; }
$uid = (int)$_SESSION['uid'];

$mensaje = ''; $tipo_mensaje = ''; $usuario = null;

try {
  $pdo = Connection::getInstance();
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  /* Usuario */
  $stmt = $pdo->prepare('SELECT ID, CIF, Email, Kizena, Kabizena, Eizena, Telefonoa
                         FROM Erabiltzaileak WHERE ID = :uid LIMIT 1');
  $stmt->execute([':uid'=>$uid]);
  $u = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$u) {
    $mensaje = 'Errorea: Ez da erabiltzailea aurkitu.'; $tipo_mensaje = 'errore';
  } else {
    /* Dirección exacta desde BD (una única fila por usuario) */
    $addr = $pdo->prepare('SELECT Helbidea, CP, Hiria, Probintzia
                           FROM helbideak WHERE ID_erab = :uid LIMIT 1');
    $addr->execute([':uid'=>$uid]);
    $h = $addr->fetch(PDO::FETCH_ASSOC) ?: ['Helbidea'=>'','CP'=>'','Hiria'=>'','Probintzia'=>''];

    $usuario = [
      'id'         => (int)$u['ID'],
      'cif'        => $u['CIF'] ?? '',
      'emaila'     => $u['Email'] ?? '',
      'kizena'     => $u['Kizena'] ?? '',
      'kabizena'   => $u['Kabizena'] ?? '',
      'eizena'     => $u['Eizena'] ?? '',
      'tlf'        => $u['Telefonoa'] ?? '',
      'helbidea'   => $h['Helbidea'],
      'cp'         => $h['CP'],
      'hiria'      => $h['Hiria'],
      'probintzia' => $h['Probintzia']
    ];
  }
} catch (PDOException $e) {
  $mensaje = 'Errorea kargatzean: '.htmlspecialchars($e->getMessage()); $tipo_mensaje = 'errore';
} /* [web:131] */

/* Guardar cambios */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $usuario){
  $kizena     = trim((string)($_POST['kizena'] ?? ''));
  $kabizena   = trim((string)($_POST['kabizena'] ?? ''));
  $email      = trim((string)($_POST['email'] ?? ''));
  $cif        = strtoupper(trim((string)($_POST['cif'] ?? '')));
  $telefonoa  = trim((string)($_POST['telefonoa'] ?? ''));
  $eizena     = trim((string)($_POST['eizena'] ?? ''));
  $psswd      = (string)($_POST['psswd'] ?? '');
  $psswd_conf = (string)($_POST['psswd_confirm'] ?? '');

  /* Dirección directamente desde los inputs */
  $helbidea   = trim((string)($_POST['helbidea'] ?? ''));
  $cp         = trim((string)($_POST['cp'] ?? ''));
  $hiria      = trim((string)($_POST['hiria'] ?? ''));
  $probintzia = trim((string)($_POST['probintzia'] ?? ''));

  $errores=[];
  if ($kizena==='')     $errores[]='Izena derrigorrezkoa da.';
  if ($kabizena==='')   $errores[]='Abizena derrigorrezkoa da.';
  if ($email==='' || !filter_var($email,FILTER_VALIDATE_EMAIL)) $errores[]='Email baliozkoa behar da.';
  if ($cif==='')        $errores[]='CIF derrigorrezkoa da.';
  if ($telefonoa==='')  $errores[]='Telefonoa derrigorrezkoa da.';
  if ($eizena==='')     $errores[]='Enpresa izena derrigorrezkoa da.';
  if ($psswd!==''){
    if (strlen($psswd)<8)        $errores[]='Pasahitzak gutxienez 8 karaktere.';
    if ($psswd!==$psswd_conf)    $errores[]='Pasahitzak ez datoz bat.';
  }

  if (!$errores){
    try{
      /* unicidad */
      $q=$pdo->prepare('SELECT ID FROM Erabiltzaileak WHERE Email=:e AND ID!=:id LIMIT 1');
      $q->execute([':e'=>$email,':id'=>$uid]); if ($q->fetch()) $errores[]='Email erabilia.';
      $q=$pdo->prepare('SELECT ID FROM Erabiltzaileak WHERE CIF=:c AND ID!=:id LIMIT 1');
      $q->execute([':c'=>$cif,':id'=>$uid]); if ($q->fetch()) $errores[]='CIF erabilia.';
    }catch(PDOException $e){ $errores[]='Errorea egiaztatzean.'; }
  }

  if (!$errores){
    try{
      $pdo->beginTransaction();

      /* Update usuario */
      if ($psswd!==''){
        $hash = password_hash($psswd, PASSWORD_DEFAULT);
        $sql='UPDATE Erabiltzaileak SET CIF=:c, Email=:e, psswd=:p, Kizena=:k, Eizena=:ei, Kabizena=:ka, Telefonoa=:t WHERE ID=:id';
        $st=$pdo->prepare($sql);
        $st->execute([':c'=>$cif,':e'=>$email,':p'=>$hash,':k'=>$kizena,':ei'=>$eizena,':ka'=>$kabizena,':t'=>$telefonoa,':id'=>$uid]);
      } else {
        $sql='UPDATE Erabiltzaileak SET CIF=:c, Email=:e, Kizena=:k, Eizena=:ei, Kabizena=:ka, Telefonoa=:t WHERE ID=:id';
        $st=$pdo->prepare($sql);
        $st->execute([':c'=>$cif,':e'=>$email,':k'=>$kizena,':ei'=>$eizena,':ka'=>$kabizena,':t'=>$telefonoa,':id'=>$uid]);
      }

      /* Upsert helbideak (una fila por usuario) */
      $has = $pdo->prepare('SELECT ID FROM helbideak WHERE ID_erab=:id LIMIT 1');
      $has->execute([':id'=>$uid]);
      if ($has->fetch()){
        $upd=$pdo->prepare('UPDATE helbideak
          SET Helbidea=:h, CP=:cp, Hiria=:hi, Probintzia=:pr
          WHERE ID_erab=:id');
        $upd->execute([':h'=>$helbidea,':cp'=>$cp,':hi'=>$hiria,':pr'=>$probintzia,':id'=>$uid]);
      } else {
        $ins=$pdo->prepare('INSERT INTO helbideak(Helbidea,CP,Hiria,Probintzia,ID_erab)
          VALUES(:h,:cp,:hi,:pr,:id)');
        $ins->execute([':h'=>$helbidea,':cp'=>$cp,':hi'=>$hiria,':pr'=>$probintzia,':id'=>$uid]);
      }

      $pdo->commit();

      /* Actualiza JSON de perfil para otras páginas que lo consuman */
      $json_dir = __DIR__ . '/../../data/users';
      if (!is_dir($json_dir)) @mkdir($json_dir,0775,true);
      $jtxt = trim(implode(', ', array_filter([$helbidea,$cp,$hiria,$probintzia])));
      $profile = [
        'id'=>$uid, 'cif'=>$cif, 'emaila'=>$email,
        'kizena'=>$kizena, 'kabizena'=>$kabizena, 'eizena'=>$eizena,
        'izen'=>trim(implode(' ', array_filter([$kizena,$kabizena]))),
        'tlf'=>$telefonoa,
        'helbidea'=>[
          'helbidea'=>$helbidea,
          'cp'=>$cp,
          'hiria'=>$hiria,
          'probintzia'=>$probintzia,
          'jatorria_txt'=>$jtxt
        ]
      ];
      file_put_contents($json_dir . "/{$uid}.json", json_encode($profile, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT));

      /* Refresca datos para repintar */
      $usuario['kizena']=$kizena; $usuario['kabizena']=$kabizena; $usuario['emaila']=$email;
      $usuario['eizena']=$eizena; $usuario['tlf']=$telefonoa; $usuario['cif']=$cif;
      $usuario['helbidea']=$helbidea; $usuario['cp']=$cp; $usuario['hiria']=$hiria; $usuario['probintzia']=$probintzia;

      $mensaje='Datuak ondo eguneratu dira!'; $tipo_mensaje='exito';
    } catch (PDOException $e){
      if ($pdo->inTransaction()) $pdo->rollBack();
      $mensaje='Errorea gordetzean: '.htmlspecialchars($e->getMessage()); $tipo_mensaje='errore';
    }
  } else {
    $mensaje='<ul><li>'.implode('</li><li>', array_map('htmlspecialchars',$errores)).'</li></ul>'; $tipo_mensaje='errore';
  }
}
?>

<!DOCTYPE html>
<html lang="eu">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Nire Profila</title>
  <link rel="stylesheet" href="../../client/css/gestionu.css">
</head>
<body>
<div class="container">
  <div class="header">
    <h1>Nire Profila</h1>
    <div class="bienvenida">
      <p>Kaixo, <strong><?php echo htmlspecialchars(trim(($usuario['kizena']??'').' '.($usuario['kabizena']??''))); ?></strong>!</p>
      <a href="logout.php" class="btn-logout">Saioa itxi</a>
    </div>
  </div>

  <?php if (!empty($mensaje)): ?>
    <div class="<?php echo htmlspecialchars($tipo_mensaje); ?>"><?php echo $mensaje; ?></div>
  <?php endif; ?>

  <?php if ($usuario): ?>
  <form method="POST" action="gestionu.php">
    <fieldset>
      <legend>Datu pertsonalak</legend>
      <div class="form-group">
        <label for="kizena">Izena: <span class="required">*</span></label>
        <input type="text" id="kizena" name="kizena" value="<?php echo htmlspecialchars($usuario['kizena']); ?>" required>
      </div>
      <div class="form-group">
        <label for="kabizena">Abizena: <span class="required">*</span></label>
        <input type="text" id="kabizena" name="kabizena" value="<?php echo htmlspecialchars($usuario['kabizena']); ?>" required>
      </div>
      <div class="form-group">
        <label for="email">Email: <span class="required">*</span></label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($usuario['emaila']); ?>" required>
      </div>
      <div class="form-group">
        <label for="telefonoa">Telefonoa: <span class="required">*</span></label>
        <input type="tel" id="telefonoa" name="telefonoa" value="<?php echo htmlspecialchars($usuario['tlf']); ?>" required>
      </div>
    </fieldset>

    <fieldset>
      <legend>Enpresa datuak</legend>
      <div class="form-group">
        <label for="cif">CIF: <span class="required">*</span></label>
        <input type="text" id="cif" name="cif" value="<?php echo htmlspecialchars($usuario['cif']); ?>" maxlength="9" required>
        <small>Adibidez: A12345678</small>
      </div>
      <div class="form-group">
        <label for="eizena">Enpresa izena: <span class="required">*</span></label>
        <input type="text" id="eizena" name="eizena" value="<?php echo htmlspecialchars($usuario['eizena']); ?>" required>
      </div>
    </fieldset>

    <fieldset>
  <legend>Helbidea (hautazkoa)</legend>

  <div>
    <label for="helbidea">Helbidea:</label>
    <input type="text" id="helbidea" name="helbidea"
           value="<?php echo htmlspecialchars($usuario['helbidea']); ?>">
  </div>

  <div>
    <label for="cp">Posta kodea (CP):</label>
    <input type="text" id="cp" name="cp"
           value="<?php echo htmlspecialchars((string)$usuario['cp']); ?>">
  </div>

  <div>
    <label for="hiria">Hiria:</label>
    <input type="text" id="hiria" name="hiria"
           value="<?php echo htmlspecialchars($usuario['hiria']); ?>">
  </div>

  <div>
    <label for="probintzia">Probintzia:</label>
    <input type="text" id="probintzia" name="probintzia"
           value="<?php echo htmlspecialchars($usuario['probintzia']); ?>">
  </div>
</fieldset>





    <fieldset>
      <legend>Pasahitza aldatu (hautazkoa)</legend>
      <div class="form-group">
        <label for="psswd">Pasahitz berria:</label>
        <input type="password" id="psswd" name="psswd" autocomplete="new-password"
               placeholder="Hutsik utzi pasahitza aldatu nahi ez baduzu">
        <small>Gutxienez 8 karaktere</small>
      </div>
      <div class="form-group">
        <label for="psswd_confirm">Berretsi pasahitza:</label>
        <input type="password" id="psswd_confirm" name="psswd_confirm" autocomplete="new-password"
               placeholder="Errepikatu pasahitz berria">
      </div>
    </fieldset>

    <div class="form-actions">
      <button type="submit">Datuak Eguneratu</button>
      <a href="logout.php" class="btn-secundario">Atzera</a>
    </div>
  </form>
  <?php else: ?>
    <div class="errore"><p>Ezin izan dira erabiltzailearen datuak kargatu.</p></div>
  <?php endif; ?>
</div>
</body>
</html>
