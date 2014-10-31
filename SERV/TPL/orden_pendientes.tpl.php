<?php

/** ACTIVACIONES **/
// Aprovechemos esta llamada para chequear ordenes pendientes - hacerlo una vez cada 30 segundos
if (CacheObtener('op_lock_activador') == false)
{
    CacheCrear('op_lock_activador', '1');
    $c = 'UPDATE `pedidos` SET `flag_pausa` = 0 WHERE `flag_pausa` = 1 AND `fechahora_activacion` < NOW()';
    db_consultar($c);
}

/*********************/

$CRITERIO_BASE = '';
$GRUPO = (empty($_POST['grupo']) ? '' : $_POST['grupo']);


if (empty ($__listado_nodos_sql[$GRUPO]))
    return;

$GRUPO = $__listado_nodos_sql[$GRUPO];


$CRITERIO_BASE = ' AND t1.`flag_pausa` = 0 AND t1.`flag_despachado` = 0';

if (isset($_POST['modo_domicilio']))
{
    $CRITERIO_BASE = ' AND tc.`flag_pagado` = 0';
}

if (isset($_POST['ghost']))
{
    $CRITERIO_BASE = ' AND (`flag_despachado` = 0 OR `fechahora_despachado` > (NOW() - INTERVAL 5 MINUTE))';
}

if (isset($_POST['nodo']))
{
    $CRITERIO_BASE .= ' AND `flag_elaborado` = 0'; 
}

if (!empty($_POST['mesa']))
{
    $CRITERIO_BASE .= ' AND tc.`ID_mesa` = "'.db_codex($_POST['mesa']).'"'; 
}

$campos = 'SELECT tc.ID_cuenta, tc.ID_mesero, t1.nodo, t1.prioridad, t1.grupo, t4.usuario AS "nombre_mesero", t1.`fechahora_pedido` , unix_timestamp(t1.`fechahora_pedido`) AS "fechahora_pedido_uts" , t1.`fechahora_despachado` , unix_timestamp(t1.`fechahora_despachado`) AS "fechahora_despachado_uts" , tc.`fechahora_pagado` , tc.`flag_pagado` , t1.`flag_elaborado`, t1.`flag_despachado` , tc.`metodo_pago` , tc.`ID_mesa` , tc.`ID_usuario` , t1.`ID_pedido` , t1.`ID_producto` , t1.`precio_grabado` , t2.`nombre` AS "nombre_producto", t1.`tmpID`, t1.`flag_cancelado`, t2.ID_grupo, t3.descripcion AS "grupo_desc"
FROM `pedidos` AS t1
LEFT JOIN `cuentas` AS tc
USING ( ID_cuenta )
LEFT JOIN `productos` AS t2
USING ( ID_producto )
LEFT JOIN productos_grupos AS t3
USING ( ID_grupo )
LEFT JOIN usuarios AS t4
ON tc.ID_mesero = t4.ID_usuarios
';

$where = 'WHERE t1.`flag_cancelado` = 0 AND flag_anulado = 0 '.$CRITERIO_BASE.' '.$GRUPO;

$order_by = 'ORDER BY t1.flag_despachado ASC, t1.fechahora_despachado DESC, t1.fechahora_pedido ASC, FIELD(t1.prioridad, "alta", "media", "baja"), t2.ID_grupo,  t1.ID_producto ASC, t1.ID_pedido';

$c = $campos.' '.$where.' '.$order_by;

$llaveCache = $c;

$cache = CacheObtener($llaveCache);
if ($cache !== false)
{
    $json['aux']['pendientes'] = $cache;
    $json['cachado'] = true;
    return;
}

// No hubo hit en cache, hacemos la consulta y todo el procesamiento necesario
$r = db_consultar($c);

if (db_num_resultados($r) == 0)
{
    CacheCrear($llaveCache, '', false);
    $json['aux']['pendientes'] = '';
    return;
}

while ($r && $f = db_fetch($r))
{
    
    $c = 'SELECT t2.nombre FROM `pedidos_adicionales` AS t1 LEFT JOIN `adicionables` AS t2 USING(ID_adicional) WHERE ID_pedido='.$f['ID_pedido'] . ' AND tipo="poner"';
    $rAdicionales = db_consultar($c);

    while ($rAdicionales && $fAdicionales = db_fetch($rAdicionales))
    {
        $f['adicionales'][] = $fAdicionales;
    }

    $c = 'SELECT t2.nombre FROM `pedidos_adicionales` AS t1 LEFT JOIN `adicionables` AS t2 USING(ID_adicional) WHERE ID_pedido='.$f['ID_pedido'] . ' AND tipo="quitar"';
    $rEliminados = db_consultar($c);

    while ($rEliminados && $fEliminados= db_fetch($rEliminados))
    {
        $f['ingredientes'][] = $fEliminados;
    }    
    
    if (empty($_POST['modo_cuenta']))
    {
        $grupo = ID_SERVIDOR.'x'.sha1($f['nodo'].$f['prioridad'].$f['grupo']);
    } else {
        $grupo = ID_SERVIDOR.'x'.sha1($f['ID_cuenta']);
    }
    
    $json['aux']['pendientes'][$grupo][] = $f;
}

/***************************/
/* SERVIDORES EXTERNOS */
if (0 && isset($__servidor_externo_pp) && is_array($__servidor_externo_pp) && count($__servidor_externo_pp) > 0) {

    foreach ($__servidor_externo_pp as $ID_SERVIDOR => $SERVIDOR_EXTERNO) {
        
        $cacheDespacho = CacheObtener($llaveCache . $ID_SERVIDOR);
        if ($cacheDespacho !== false) {
            array_merge($cacheDespacho, $json['aux']['pendientes']);
        } else {
            $PARAMETROS = array_merge(array('solicitud_externa' => true), $_POST);

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $SERVIDOR_EXTERNO);

            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            curl_setopt($ch, CURLOPT_POST, count($PARAMETROS));
            curl_setopt($ch, CURLOPT_POSTFIELDS, $PARAMETROS);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $resultado = json_decode(curl_exec($ch), true);
            curl_close($ch);

            if (isset($resultado['aux']['pendientes'])) {
                CacheCrear($llaveCache . $ID_SERVIDOR, $resultado['aux']['pendientes']);
                $json['aux']['pendientes'] = array_merge($resultado['aux']['pendientes'], $json['aux']['pendientes']);
            }
        }
    }
}
/***************************/
if (! $cache)
    CacheCrear($llaveCache, @$json['aux']['pendientes'], false);
?>