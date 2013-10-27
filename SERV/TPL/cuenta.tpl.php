<?php
$where = '';

if (isset($_POST['mesa']) && is_numeric($_POST['mesa']) && $_POST['mesa'] > 0) {
    $where .= ' AND ID_mesa = ' . $_POST['mesa'];
}

if (isset($_POST['cuenta'])) {
    $where .= ' AND cuenta = "' . db_codex($_POST['cuenta']) . '"';
}

if (isset($_POST['pendientes'])) {
    $where .= ' AND t0.`flag_pagado` = 0 AND t0.`flag_anulado` = 0';
}

if (isset($_POST['facturacion'])) {
    $where .= ' AND t1.`flag_cancelado` = 0';
}

$fecha = ( empty($_POST['fecha']) ?  mysql_date() : db_codex($_POST['fecha']) );

if ( isset($_POST['modificados']) ) 
    $where = ' AND ( t0.cuenta IN ( SELECT cuenta FROM `ordenes` AS x0
LEFT JOIN `pedidos` AS x1 USING(ID_orden) WHERE (x0.flag_nopropina = 1 OR x1.`flag_cancelado` = 1 OR x0.`flag_anulado` = 1) AND DATE(x0.fechahora_pedido) = "'.$fecha.'" ) OR t0.cuenta IN (SELECT x4.cuenta FROM historial AS x2 LEFT JOIN pedidos AS x3 USING (ID_pedido) LEFT JOIN ordenes AS x4 USING (ID_orden) WHERE DATE(x2.fechahora) = "'.$fecha.'") )';

if ( isset($_POST['historial']) && $_POST['historial'] == '1' )
    $where = ' AND DATE(fechahora_pedido) = "'.$fecha.'" AND t0.`flag_pagado` = 1';
    
$c = 'SELECT (SELECT nota FROM historial st0 WHERE grupo="PEDIDOS" AND st0.ID_pedido = t1.ID_pedido ORDER BY ID_historial DESC LIMIT 1) AS "historia", t0.ID_mesero, t4.usuario AS "nombre_mesero", t0.cuenta, t0.`fechahora_elaborado`, t0.`fechahora_pedido` , unix_timestamp(t0.`fechahora_pedido`) AS "fechahora_pedido_uts" , t0.`fechahora_entregado` , t0.`fechahora_pagado` , t0.`flag_nopropina` , t0.`flag_exento` , t0.`flag_pagado`, t0.`flag_elaborado` , t0.`flag_despachado` ,  t0.`flag_anulado`, t0.`flag_tiquetado`, t1.`flag_cancelado`, t0.`metodo_pago` , t1.`ID_orden` , t0.`ID_mesa` , t0.`ID_usuario` , t1.`ID_pedido` , `ID_producto` , `precio_grabado` , t2.`nombre` AS "nombre_producto", `tmpID`, `flag_cancelado`, t2.ID_grupo, t3.descripcion AS "grupo_desc"
FROM `ordenes` AS t0
LEFT JOIN `pedidos` AS t1
USING ( ID_orden )
LEFT JOIN `productos` AS t2
USING ( ID_producto )
LEFT JOIN productos_grupos AS t3
USING ( ID_grupo )
LEFT JOIN usuarios AS t4
ON t0.ID_mesero = t4.ID_usuarios
WHERE 1 AND t1.`ID_producto` IS NOT NULL '.$where.'
ORDER BY t0.ID_mesa ASC, t0.`fechahora_pedido`, t1.`ID_producto`';

$llaveCache = $c;
$cache = CacheObtener($llaveCache);
if ($cache !== false)
{
    $json['aux']['pendientes'] = $cache;
    $json['cachado'] = true;
    return;
}

$r = db_consultar($c);

while ($r && $f = db_fetch($r))
{
    $c = 'SELECT t2.nombre, t1.precio_grabado, t1.tipo FROM `pedidos_adicionales` AS t1 LEFT JOIN `adicionables` AS t2 USING(ID_adicional) WHERE ID_pedido='.$f['ID_pedido'].' AND tipo="poner"';
    $rAdicionales = db_consultar($c);

    while ($rAdicionales && $fAdicionales = db_fetch($rAdicionales))
    {
        $f['adicionales'][] = $fAdicionales;
    }
    
    $c = 'SELECT t2.nombre, t1.precio_grabado, t1.tipo FROM `pedidos_adicionales` AS t1 LEFT JOIN `adicionables` AS t2 USING(ID_adicional) WHERE ID_pedido='.$f['ID_pedido']. ' AND tipo="quitar"';
    $rRemociones = db_consultar($c);
    
    while ($rRemociones && $fRemociones = db_fetch($rRemociones))
    {
        $f['remociones'][] = $fRemociones;
    }
    
    // Historial de cuentas
    $c = 'SELECT `fechahora`, TIME(`fechahora`) AS "hora", `nota`, `grupo`, `accion`, `cuenta` FROM `historial` WHERE `cuenta`="'.$f['cuenta'].'"';
    $rHistorial = db_consultar($c);
    
    $f['historial'] = array();
    
    while ($rHistorial && $fHistorial = db_fetch($rHistorial))
    {
        $f['historial'][] = $fHistorial;
    }
    
    $grupo = 'x'.$f['ID_mesa'].crc32($f['cuenta']);
    
    $json['aux']['pendientes'][$grupo][] = $f;
}

$json['cache'] = (serialize(@$json['aux']['pendientes']) == serialize($cache));

if (!$cache)
    CacheCrear($llaveCache, @$json['aux']['pendientes'], false);
?>