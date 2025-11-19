<?php
session_start();
$uid = isset($_SESSION['uid']) ? (int)$_SESSION['uid'] : 0;
$profile = [];
if ($uid > 0) {
  $fileA = __DIR__ . '/../../data/users/' . $uid . '.json';
  $fileB = __DIR__ . '/../../server/data/users/' . $uid . '.json';
  $path  = is_file($fileA) ? $fileA : (is_file($fileB) ? $fileB : '');
  if ($path) { $profile = json_decode(file_get_contents($path), true) ?: []; }
}
function esc($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
$pre = [
  'izen'     => esc($profile['izen']   ?? ''),
  'emaila'   => esc($profile['emaila'] ?? ''),
  'tlf'      => esc($profile['tlf']    ?? ''),
  'empresa'  => esc($profile['eizena'] ?? ''),
  'jatorria' => esc($profile['helbidea']['jatorria_txt'] ?? ($profile['ult_pedido']['jatorria_txt'] ?? ''))
];
echo '<script>
document.addEventListener("DOMContentLoaded",function(){
  var f=document.querySelector("form.formularioa"); if(!f) return;
  var m='.json_encode($pre, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES).';
  for (var k in m){ var el=f.querySelector("[name=\'"+k+"\']"); if(el && !el.value){ el.value=m[k]; } }
});
</script>';
?>


