<?php
setlocale                   (LC_ALL, 'es_SV.UTF-8');
date_default_timezone_set   ('America/El_Salvador');

define('NOMBRE_RESTAURANTE', 'La Pizzeria');
define('URI_SERVIDOR', '/SERV'); // URI relativa o absoluta hacia el servidor

define('db__host','localhost');
define('db__usuario',''); // Nombre de usuario de base de datos
define('db__clave',''); // Clave de base de datos
define('db__db',''); // Base de datos

$__listado_nodos['todos'] = 'Todas las ordenes';
$__listado_nodos['pizzas'] = 'Pizzas y entradas horneadas';
$__listado_nodos['pizzas1'] = 'Pizzas 1 + entradas horneadas';
$__listado_nodos['pizzas2'] = 'Pizzas 2';
$__listado_nodos['pastas'] = 'Pastas';
$__listado_nodos['bebidas_ensaladas_postres_entradas'] = 'Bebidas, Ensaladas, Postres y Entradas';
$__listado_nodos['nada'] = 'Desactivar este nodo';
?>