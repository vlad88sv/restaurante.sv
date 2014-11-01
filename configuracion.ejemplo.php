<?php
setlocale                   (LC_ALL, 'es_SV.UTF-8');
date_default_timezone_set   ('America/El_Salvador');

define('SUCURSAL_EMPRESA','7G, S.A. de C.V.');
define('SUCURSAL_DIRECCION', 'La Antigua, Antiguo Cuscatlán, 1a Cl. Pte., n.23'); // direccion de la sucursal
define('SUCURSAL_TELEFONO', '(503) 2563-1948');


define('NOMBRE_RESTAURANTE', 'El Restaurante');
define('ID_SERVIDOR', 'ELRESTAURANTE');
define('MODO_GLOBAL', 'NORMAL'); // MODOS: [ NORMAL | DOMICILIO ]
define('URI_SERVIDOR', '/SERV'); // URI relativa o absoluta hacia el servidor
define('URI_AUT', '/'); // URI relativa o absoluta hacia el autorizador

define('SUCURSAL_EMPRESA','7G, S.A. de C.V.'); // Nombre de empresa
define('SUCURSAL_DIRECCION', 'San Salvador'); // direccion de la sucursal
define('SUCURSAL_TELEFONO', '(503) 2222-2222'); // Telefono de empresa


define('USAR_AUT', false); // forzar autorización para CAJA y PEDIDOS

define('ID_CACHE', "RSV_SQL_" . crc32(ID_SERVIDOR . URI_SERVIDOR) );

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

$__listado_nodos_sql['todos'] = 'AND t1.nodo IN ("pizzas","pizzas1","pizzas2", "pastas","entradas_horno","bebidas_preparadas","ensaladas","postres","entradas")';
$__listado_nodos_sql['horno'] = 'AND t1.nodo IN ("pizzas",pizzas1","pizzas2","entradas_horno","pastas")';
$__listado_nodos_sql['pizzas'] = 'AND t1.nodo IN ("pizzas","pizzas1","pizzas2","entradas_horno")';
$__listado_nodos_sql['pizzas1'] = 'AND t1.nodo IN ("pizzas","pizzas1","entradas_horno")';
$__listado_nodos_sql['pizzas2'] = 'AND t1.nodo IN ("pizzas2")';
$__listado_nodos_sql['pastas'] = 'AND t1.nodo IN ("pastas")';
$__listado_nodos_sql['bebidas_ensaladas_postres_entradas'] = 'AND t1.nodo IN ("bebidas_preparadas","ensaladas","postres","entradas")';
$__listado_nodos_sql['pastas'] = 'AND t1.nodo IN ("pastas")';
$__listado_nodos_sql['nada'] = 'AND 0';
$__listado_nodos_sql['domicilio'] = 'domicilio';

// OPCIONES ESPECIALES
define('TIQUETE_AGRUPADO', false); // Agrupar los productos en tiquete
define('CANCELAR_IMPRESION_DESPACHO', false); // No imprimir la orden al momento de despachar

//$JSOPS[] = 'despacho_aun_sin_elaborar';
//$JSOPS[] = 'sin_clave';

// Servidores externos para pedidos pedientes
//$__servidor_externo_pp['LPDOMICILIO'] = 'http://serv.domicilio.lapizzeria.com.sv/';

?>