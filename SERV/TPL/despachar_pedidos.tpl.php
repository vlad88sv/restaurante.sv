<?php

/* Migración a uso de cuentas completa - no usa cuenta - no USAR cuenta. */

if ( empty($_POST['pedidos']) || !is_array($_POST['pedidos']) || count($_POST['pedidos']) == 0 ) {
    $json['error'] = 'Operación no completada';
    return;
}
   
$pedidos = implode(',',$_POST['pedidos']);

if (isset($_POST['elaborado']) && (empty($_POST['despacho_completo']) || $_POST['despacho_completo'] == 'no') )
{    
    $c = "UPDATE pedidos SET flag_elaborado = 1, fechahora_elaborado=NOW() WHERE ID_pedido IN ($pedidos)";
    db_consultar($c);
} else {
    
    $c = "UPDATE pedidos SET flag_elaborado = 1, flag_despachado = 1, fechahora_elaborado = IF(fechahora_elaborado = '0000-00-00 00:00:00', NOW(), fechahora_elaborado), fechahora_despachado=NOW() WHERE ID_pedido IN ($pedidos)";
    db_consultar($c);

    if (!empty($_POST['imprimir']) && (defined('CANCELAR_IMPRESION_DESPACHO') && !CANCELAR_IMPRESION_DESPACHO)) 
        db_agregar_datos('comandas', array('data' => $_POST['imprimir'], 'impreso' => '0', 'estacion' => 'comandas'));
}

CacheDestruir();
?>