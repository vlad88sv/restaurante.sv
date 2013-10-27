<?php

if ( empty($_POST['pedidos']) || !is_array($_POST['pedidos']) || empty($_POST['mesa']) || empty($_POST['cuenta']) )
    return;

$PEDIDOS = array();

foreach($_POST['pedidos'] as $ID_PEDIDO) {
    $PEDIDO = db_obtener_fila('pedidos', 'ID_pedido="'.db_codex($ID_PEDIDO).'"');
    $PEDIDOS[] = array('ID_pedido' => $ID_PEDIDO, 'ID' => $PEDIDO['ID_producto'], 'ID_orden' => $PEDIDO['ID_orden']);
}

$ORDEN = db_obtener_fila('ordenes', 'cuenta="'.db_codex($_POST['cuenta']).'"');

if ( isset($_POST['mesero']) && is_numeric($_POST['mesero']) ) {
    $ID_MESERO = $_POST['mesero'];
} else {
    $ID_MESERO = $ORDEN['ID_mesero'];
}

$FORZAR_CUENTA_NUEVA = ( isset($_POST['FORZAR_CUENTA_NUEVA']) ? true : false );

// ingresar_orden destruye el cache, asi que no hay necesidad de hacerlo aquí.
ingresar_orden($PEDIDOS, $_POST['mesa'], $ID_MESERO, 1, $FORZAR_CUENTA_NUEVA);
?>