<?php
if ( isset($_POST['imprimir'])) {
    rsv::generar_impresion_cortez($_POST['imprimir']);
}

if ( isset($_POST['ultimo']))
{
    $c = 'SELECT `ID_cortez`, `fechahora_recibido`, `fechahora_remesado`, `total_a_cuadrar`, `total_diferencia`, `total_efectivo`, `total_pos`, `total_compras`, `total_caja`, `inventario`, `ID_usuario`, `fechatiempo`, `estado`, `remesa` FROM `cortez` WHERE 1 ORDER BY fechatiempo DESC LIMIT 1';
    $r = db_consultar($c);
    
    while ($r && $f = db_fetch($r))
    {
        $json['aux']['historial'][] = $f;
    }
    
    return;    
}

if ( isset($_POST['historial']))
{
    $fecha = '';

    if ( isset($_POST['fecha']) )
        $fecha = ' AND DATE(fechatiempo) = "'.$_POST['fecha'].'"';

    $c = 'SELECT `ID_cortez`, `fechahora_recibido`, `fechahora_remesado`, IF(`total_diferencia` = (`total_a_cuadrar` - (`total_efectivo` + `total_pos` + `total_compras`)), 0, 1) AS "sospechoso", (`total_a_cuadrar` - (`total_efectivo` + `total_pos` + `total_compras`)) AS "total_diferencia2", `total_a_cuadrar`, `total_diferencia`, `total_efectivo`, `total_pos`, `total_compras`, `total_caja`, `inventario`, `ID_usuario`, `fechatiempo`, `estado`, `remesa` FROM `cortez` WHERE 1 '.$fecha.' ORDER BY fechatiempo DESC';
    $r = db_consultar($c);
    
    while ($r && $f = db_fetch($r))
    {
        $json['aux']['historial'][] = $f;
    }
    
    return;
}

if ( isset($_POST['cortar']) )
{
    unset($DATOS);
    $inventario = '';
    
    $corte = array();
    parse_str($_POST['datos'], $corte);
    
    $DATOS['total_a_cuadrar'] = $corte['total_a_cuadrar'];
    $DATOS['total_diferencia'] = $corte['total_diferencia'];
    $DATOS['total_compras'] = $corte['total_compras'];
    $DATOS['total_efectivo'] = $corte['total_efectivo'];
    $DATOS['total_pos'] = $corte['total_pos'];
    $DATOS['total_caja'] = $corte['total_caja'];
    $DATOS['fechatiempo'] = mysql_datetime();
    
    // Hacer un snapshot del inventario
    $DATOS['inventario'] = $inventario;
    
    $ID_corte = db_agregar_datos('cortez',$DATOS);
    $json['aux']['ID_corte'] = $ID_corte;
    return;
}

if (empty($_POST['fecha']))
{
    $fecha = mysql_date();
} else {
    $fecha = db_codex($_POST['fecha']);
}

$c_adicionales = '( SELECT COALESCE(SUM(precio_grabado),0 ) FROM `pedidos_adicionales` AS t3 WHERE t3.tipo="poner" AND t3.ID_pedido=t2.ID_pedido )';
$c_total_bruto = '( ( (COALESCE(t2.precio_grabado,0) + '.$c_adicionales.') / IF(flag_exento = 0, 1, 1.13) ) * IF(flag_nopropina = 0, 1.10, 1) )';
$c_total = 'SUM( ROUND('.$c_total_bruto.',2) ) AS total';

$c = 'SELECT '.$c_total.' FROM `cuentas` AS t1 LEFT JOIN `pedidos` AS t2 USING (ID_cuenta) WHERE DATE(fechahora_pedido) = "'.$fecha.'" AND flag_pagado=1 AND flag_anulado=0 AND flag_cancelado=0';
$r = db_consultar($c);
$f = db_fetch($r);

$total = ( empty($f['total']) ? '0.00' : $f['total'] );
$json['aux']['total'] = numero($total);

$c = 'SELECT '.$c_total.' FROM `cuentas` AS t1 LEFT JOIN `pedidos` AS t2 USING (ID_cuenta) WHERE DATE(fechahora_pedido) = "'.$fecha.'" AND flag_pagado=0 AND flag_anulado=0 AND flag_cancelado=0';
$r = db_consultar($c);
$f = db_fetch($r);

$total = ( empty($f['total']) ? '0.00' : $f['total'] );
$json['aux']['total_pendiente'] = numero($total);

$c = 'SELECT '.$c_total.' FROM `cuentas` AS t1 LEFT JOIN `pedidos` AS t2 USING (ID_cuenta) WHERE DATE(fechahora_pedido) = "'.$fecha.'" AND flag_anulado=0 AND flag_cancelado=0';
$r = db_consultar($c);
$f = db_fetch($r);

$total = ( empty($f['total']) ? '0.00' : $f['total'] );
$json['aux']['total_posible'] = numero($total);

$c = 'SELECT '.$c_total.' FROM `cuentas` AS t1 LEFT JOIN `pedidos` AS t2 USING (ID_cuenta) WHERE DATE(fechahora_pedido) = "'.$fecha.'" AND flag_anulado=1';
$r = db_consultar($c);
$f = db_fetch($r);

$total = ( empty($f['total']) ? '0.00' : $f['total'] );
$json['aux']['total_anulado'] = numero($total);

$c = 'SELECT '.$c_total.' FROM `cuentas` AS t1 LEFT JOIN `pedidos` AS t2 USING (ID_cuenta) WHERE DATE(fechahora_pedido) = "'.$fecha.'" AND flag_cancelado=1';
$r = db_consultar($c);
$f = db_fetch($r);

$total = ( empty($f['total']) ? '0.00' : $f['total'] );
$json['aux']['total_cancelado'] = numero($total);

$c = 'SELECT SUM( `precio`) AS total FROM `compras` WHERE `fechatiempo` BETWEEN "'.$fecha.' 00:00:00" AND "'.$fecha.' 23:59:59" AND via = "caja"';
$r = db_consultar($c);
$f = db_fetch($r);

$total = ( empty($f['total']) ? '0.00' : $f['total'] );
$json['aux']['total_compras'] = numero($total);

$c = 'SELECT empresa, descripcion, precio, fechatiempo FROM compras WHERE DATE(fechatiempo) = "'.$fecha.'" AND precio > 0 AND via="caja"';
$r = db_consultar($c);

while ($f = db_fetch($r))
{
    $json['aux']['compras'][] = $f;
}

// Total a cuadrar: total desde el ultimo corte Z
$c = 'SELECT '.$c_total.' FROM `cuentas` AS t1 LEFT JOIN `pedidos` AS t2 USING (ID_cuenta) WHERE fechahora_pedido >= COALESCE((SELECT fechatiempo FROM cortez ORDER BY fechatiempo DESC LIMIT 1),"0") AND flag_pagado=1 AND flag_anulado=0 AND flag_cancelado=0';
$r = db_consultar($c);
$f = db_fetch($r);

$total = ( empty($f['total']) ? '0.00' : $f['total'] );
$json['aux']['total_cuadrar'] = numero($total);


// Total a cuadrar de compras: total desde el ultimo corte z
$c = 'SELECT SUM( `precio`) AS total FROM `compras` WHERE fechatiempo >= (SELECT fechatiempo FROM cortez ORDER BY fechatiempo DESC LIMIT 1) AND via = "caja"';
$r = db_consultar($c);
$f = db_fetch($r);

$total = ( empty($f['total']) ? '0.00' : $f['total'] );
$json['aux']['total_compras_cuadrar'] = numero($total);

?>