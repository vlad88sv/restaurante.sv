<?php

$GRUPO = (empty($_POST['grupo']) ? '' : $_POST['grupo']);

switch ($GRUPO)
{       
    case 'pizzas':
        $GRUPO = 'AND t0.nodo IN ("pizzas1","pizzas2","entradas_horno")';
        break;

    case 'pizzas1':
        $GRUPO = 'AND t0.nodo IN ("pizzas1","entradas_horno")';
        break;

    case 'pizzas2':
        $GRUPO = 'AND t0.nodo IN ("pizzas2")';
        break;    

    case 'pastas':
        $GRUPO = 'AND t0.nodo IN ("pastas")';
        break;
        
   case 'bebidas_ensaladas_postres_entradas':
        $GRUPO = 'AND t0.nodo IN ("bebidas_preparadas","ensaladas","postres","entradas")';
        break;
    
    case 'nada':
        $GRUPO = 'AND 0';
        break;
    
    case 'todos':
    default:
        $GRUPO = 'AND t0.nodo IN ("pizzas1","pizzas2", "pastas","entradas_horno","bebidas_preparadas","ensaladas","postres","entradas")';
        break;
}

$CRITERIO_BASE = 't0.`flag_despachado` = 0';

if (isset($_POST['ghost']))
{
    $CRITERIO_BASE = '(t0.`flag_despachado` = 0 OR fechahora_entregado > (NOW() - INTERVAL 15 MINUTE))';
}

if (isset($_POST['nodo']))
{
    $CRITERIO_BASE .= ' AND flag_elaborado = 0'; 
}

$campos = 'SELECT t0.ID_mesero, t4.usuario AS "nombre_mesero", t0.`fechahora_pedido` , unix_timestamp(t0.`fechahora_pedido`) AS "fechahora_pedido_uts" , t0.`fechahora_entregado` , unix_timestamp(t0.`fechahora_entregado`) AS "fechahora_entregado_uts" , t0.`fechahora_pagado` , t0.`flag_pagado` , t0.`flag_elaborado`, t0.`flag_despachado` , t0.`metodo_pago` , t0.`ID_orden` , t0.`ID_mesa` , t0.`ID_usuario` , `ID_pedido` , `ID_producto` , `precio_grabado` , t2.`nombre` AS "nombre_producto", `tmpID`, `flag_cancelado`, t2.ID_grupo, t3.descripcion AS "grupo_desc"
FROM `ordenes` AS t0
LEFT JOIN `pedidos` AS t1
USING ( ID_orden )
LEFT JOIN `productos` AS t2
USING ( ID_producto )
LEFT JOIN productos_grupos AS t3
USING ( ID_grupo )
LEFT JOIN usuarios AS t4
ON t0.ID_mesero = t4.ID_usuarios
';

$where = 'WHERE '.$CRITERIO_BASE.' AND t1.`flag_cancelado` = 0 AND t0.flag_anulado = 0 '.$GRUPO;

$order_by = 'ORDER BY t0.flag_despachado ASC, t0.fechahora_entregado DESC, t0.fechahora_pedido ASC, FIELD(prioridad, "alta", "media", "baja"), t2.ID_grupo,  t1.ID_producto ASC, t1.ID_pedido';

$c = $campos.' '.$where.' '.$order_by;

$json['sql'] = $c;
//error_log($c);

$r = db_consultar($c);

while ($r && $f = db_fetch($r))
{
    
    $c = 'SELECT t2.nombre FROM `pedidos_adicionales` AS t1 LEFT JOIN `adicionables` AS t2 USING(ID_adicional) WHERE ID_pedido='.$f['ID_pedido'] . ' AND tipo="poner"';
    $rAdicionales = db_consultar($c);

    while ($rAdicionales && $fAdicionales = db_fetch($rAdicionales))
    {
        $f['adicionales'][] = $fAdicionales;
    }

    $c = 'SELECT t2.nombre FROM `pedidos_adicionales` AS t1 LEFT JOIN `adicionables` AS t2 USING(ID_adicional) WHERE ID_pedido='.$f['ID_pedido'] . ' AND tipo="quitar"';
    $rAdicionales = db_consultar($c);

    while ($rAdicionales && $fAdicionales = db_fetch($rAdicionales))
    {
        $f['ingredientes'][] = $fAdicionales;
    }    
    
    $grupo = 'id_'.sha1($f['ID_orden']);
    
    $json['aux']['pendientes'][$grupo][] = $f;
}
?>