<?php
$rsv_benchmark = microtime(true);
header ('Content-type: text/html; charset=utf-8');
header ('Content-type: application/json');
header ('Access-Control-Allow-Origin: *');

define('__BASE__', str_replace('//','/',dirname(__FILE__).'/'));
require_once('../configuracion.php');
require_once('PHP/vital.php');

$json['error'] = '';
$json['html'] = '';
$json['aux'] = '';

$TPL = __BASE__.'TPL/'.@$_POST['TPL'].'.tpl.php';

if (empty($_POST['TPL']) || !file_exists($TPL)) {
    $json['error'] = 'Error #1: no exite tal TPL ['.@$_POST['TPL'].']';
} else {
    require_once($TPL);
}

$json['benchmark'] = round(((microtime(true) - $rsv_benchmark) * 1000),1);

echo json_encode($json);
?>