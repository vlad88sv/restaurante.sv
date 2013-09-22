<?php
define('__BASE__', str_replace('//','/',dirname(__FILE__).'/'));
require_once('configuracion.php');
require_once('PHP/vital.php');

/* Solo debemos chequear que el token de auth sea válido */

$json['error'] = '';
$json['html'] = '';
$json['aux'] = '';

$TPL = __BASE__.'TPL/'.@$_POST['TPL'].'.tpl.php';

if (empty($_POST['TPL']) || !file_exists($TPL)) {
    $json['error'] = 'Error #1: no exite tal TPL ['.@$_POST['TPL'].']';
} else {
    require_once($TPL);
}


header ('Content-type: text/html; charset=utf-8');
header ('Content-type: application/json');
echo json_encode($json);
?>