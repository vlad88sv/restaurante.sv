<?php
$permisos[] = 'cuentas';

if ( ! sesion::verificar($permisos) )
{
    $json['AUTORIZADO'] = 'no';
    $json['permisos'] = @$_SESSION['permisos'];
    if ( ! sesion::$autenticado )
        $json['AUT_M'] = 'No ha iniciado sesión';
    else
        $json['AUT_M'] = 'No cuenta con los permisos necesarios';
    return;
}

$where = '';

if ( isset($_POST['mesa']) )
{
    $where .= ' AND t0.`ID_mesa` = "' . db_codex($_POST['mesa']) . '"';
}

if (isset($_POST['cuenta'])) {
    $where .= ' AND t0.`ID_cuenta` = "' . db_codex($_POST['cuenta']) . '"';
}

if (isset($_POST['pendientes'])) {
    $where .= ' AND t0.`flag_pagado` = 0 AND t0.`flag_anulado` = 0';
}

if (isset($_POST['facturacion'])) {
    $where .= ' AND t1.`flag_cancelado` = 0';
}

if ( isset($_POST['fecha']) )
{
    $fecha = db_codex($_POST['fecha']);
} else {
    $fecha = mysql_date();
}


if ( isset($_POST['modificados']) ) 
{
    $where = ' AND DATE(fechahora_pedido) = "'.$fecha.'" AND ID_cuenta IN (SELECT ID_cuenta FROM `historial` WHERE flag_importante = 1 )';    
}

if ( isset($_POST['historial']) && $_POST['historial'] == '1' )
{
    $where = ' AND DATE(fechahora_pedido) = "'.$fecha.'" AND (t0.`flag_pagado` = 1 OR t0.`flag_anulado` = 1)';
}

$c = 'SELECT (SELECT nota FROM historial st0 WHERE ID_pedido>0 AND st0.ID_pedido = t1.ID_pedido ORDER BY ID_historial DESC LIMIT 1) AS "historia", t0.ID_mesero, t4.usuario AS "nombre_mesero", t0.`ID_domicilio`, t1.`ID_cuenta`, t1.`fechahora_despachado`, t1.`fechahora_pedido` , unix_timestamp(t1.`fechahora_pedido`) AS "fechahora_pedido_uts" , t1.`fechahora_despachado` , t0.`fechahora_pagado` , t0.`flag_nopropina` , t0.`flag_exento` , t0.`flag_pagado`, t1.`flag_elaborado` , t1.`flag_despachado` ,  t0.`flag_anulado`, t0.`flag_tiquetado`, t1.`flag_cancelado`, t0.`metodo_pago`, t0.`ID_mesa` , t0.`ID_usuario` , t1.`ID_pedido` , t1.`ID_producto` , t1.`precio_grabado` , t2.`nombre` AS "nombre_producto", t1.`tmpID`, t1.`flag_cancelado`, t2.ID_grupo, t3.descripcion AS "grupo_desc"
FROM `pedidos` AS t1
LEFT JOIN `cuentas` AS t0
USING ( ID_cuenta )
LEFT JOIN `productos` AS t2
USING ( ID_producto )
LEFT JOIN productos_grupos AS t3
USING ( ID_grupo )
LEFT JOIN usuarios AS t4
ON t0.ID_mesero = t4.ID_usuarios
WHERE 1 '.$where.'
ORDER BY ( t0.ID_mesa + 0 ) ASC, t1.`fechahora_pedido` ASC, t1.`tmpID`';

$llaveCache = $c;
$cache = CacheObtener($llaveCache);

if ($cache !== false)
{
    $json['aux']['cuentas'] = @$cache['cuentas'];
    $json['aux']['pendientes'] = @$cache['pendientes'];
    $json['cmp_cache'] = sha1(json_encode($json['aux']['cuentas']) . json_encode($json['aux']['pendientes']));

    $json['cachado'] = true;
    return;
}

$r = db_consultar($c);

if (db_num_resultados($r) == 0)
{
    $json['aux']['cuentas'] = (@$cache['cuentas'] ?: '');
    $json['aux']['pendientes'] = (@$cache['pendientes'] ?: '');
    CacheCrear($llaveCache, @$json['aux'], false);
    return;
}

while ($r && $f = db_fetch($r))
{
    $c = 'SELECT t1.ID_pedido_adicional, t2.nombre, t1.precio_grabado, t1.tipo FROM `pedidos_adicionales` AS t1 LEFT JOIN `adicionables` AS t2 USING(ID_adicional) WHERE ID_pedido="'.$f['ID_pedido'].'" AND tipo="poner"';
    $rAdicionales = db_consultar($c);

    while ($rAdicionales && $fAdicionales = db_fetch($rAdicionales))
    {
        $f['adicionales'][] = $fAdicionales;
    }
    
    $c = 'SELECT t2.nombre, t1.precio_grabado, t1.tipo FROM `pedidos_adicionales` AS t1 LEFT JOIN `adicionables` AS t2 USING(ID_adicional) WHERE ID_pedido="'.$f['ID_pedido']. '" AND tipo="quitar"';
    $rRemociones = db_consultar($c);
    
    while ($rRemociones && $fRemociones = db_fetch($rRemociones))
    {
        $f['remociones'][] = $fRemociones;
    }

    $grupo = 'x'.$f['ID_cuenta'];
    
    $json['aux']['pendientes'][$grupo][] = $f;
}

foreach ( $json['aux']['pendientes'] AS $grupo => $cuenta)
{
    // Datos de cuenta
    $json['aux']['cuentas'][$grupo]['info'] = $cuenta[0];
        
    // Historial de cuentas
    $c = 'SELECT `fechahora`, TIME(`fechahora`) AS "hora", `nota`, `grupo`, `accion` FROM `historial` WHERE `ID_cuenta`="'.$cuenta[0]['ID_cuenta'].'"';
    $rHistorial = db_consultar($c);
    
    while ($rHistorial && $fHistorial = db_fetch($rHistorial))
    {
        $json['aux']['cuentas'][$grupo]['historial'][] = $fHistorial;
    }

    // Domicilio
    $c = 'SELECT `ID_domicilio`, `telefono`, `direccion`, `nombre`, `tarjeta`, `expiracion`, `vuelto`, `notas`, `metodo_pago`, `documento_fiscal`, `detalle_facturacion`, `facturacion_nombre`, `facturacion__dui`, `facturacion_nit`, `facturacion_nrc`, `facturacion_giro`, `facturacion_direccion`, `flag_en_transito`, `fechahora_transito` FROM `domicilio` WHERE `ID_domicilio`="'.$cuenta[0]['ID_domicilio'].'"';
    $rDomicilio = db_consultar($c);
    
    if ($rDomicilio && $fDomicilio = db_fetch($rDomicilio))
    {
        $fDomicilio['notas'] = ($fDomicilio['notas'] == '' ? 'Ninguna ingresada' : $fDomicilio['notas']);
        $json['aux']['cuentas'][$grupo]['domicilio'] = $fDomicilio;
    }
}

$json['cmp_cache'] = sha1(json_encode($json['aux']['cuentas']) . json_encode($json['aux']['pendientes']));
$json['cachado'] = '0';


CacheCrear($llaveCache, @$json['aux'], false);
?>