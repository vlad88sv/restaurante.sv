<?php
setlocale                   (LC_ALL, 'es_SV.UTF-8');
date_default_timezone_set   ('America/El_Salvador');

function DEPURAR($x, $x){}

define('__BASE_PHP__', str_replace('//','/',dirname(__FILE__).'/'));
require_once(__BASE_PHP__.'db.php');
require_once(__BASE_PHP__.'stubs.php');
?>