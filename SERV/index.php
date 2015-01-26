<?php
$rsv_benchmark = microtime(true);
ini_set('display_errors', 1);
error_reporting(E_ALL);
header ('Content-type: text/html; charset=utf-8');
header ('Content-type: application/json');
header ('Access-Control-Allow-Origin: *');

define('__BASE__', str_replace('//','/',dirname(__FILE__).'/'));
require_once('../configuracion.php');
require_once('PHP/vital.php');

$json['error'] = '';
$json['html'] = '';
$json['aux'] = '';

$TPL = __BASE__.'TPL/'.@$_REQUEST['TPL'].'.tpl.php';

if (empty($_REQUEST['TPL']) || !file_exists($TPL)) {
    $json['error'] = 'Error #1: no exite tal TPL ['.@$_REQUEST['TPL'].']';
} else {
    require_once($TPL);
}

$json['benchmark'] = round(((microtime(true) - $rsv_benchmark) * 1000),1);

$hjson = json_encode($json);

if ($hjson === FALSE)
{
    echo 'JSON ERROR';
}

echo $hjson;