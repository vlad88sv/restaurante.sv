<?php

if ( empty($_POST['pedidos']) || !is_array($_POST['pedidos']) || count($_POST['pedidos']) === 0 || !is_array($_POST['pedidos']) || empty($_POST['mesa']))
{
    $json['error'] = 'ERROR';
    return;
}  

$PEDIDOS = array();

$cuenta_destino = rsv::cuenta_de_mesa($_POST['mesa']);

unset($DATOS);

if (isset($_POST['SEPARAR_CUENTA'])) {
    // Necesita separar los productos en cuentas con mismo número de mesa
    // pero diferente cuenta.
    $DATOS['ID_cuenta'] = rsv::cuenta_duplicar_mesa($_POST['mesa']);
} elseif ($cuenta_destino) {
    // Si la mesa existe entonces combinamos
    $DATOS['ID_cuenta'] = $cuenta_destino;
} else {
    // No existe ninguna mesa con ese número, creemos la nueva cuenta
    $DATOS['ID_cuenta'] = rsv::cuenta_nueva($_POST['mesa'], $_POST['mesero']);
}

foreach($_POST['pedidos'] as $ID_PEDIDO)
{
    db_actualizar_datos('pedidos', $DATOS, 'ID_pedido="'.$ID_PEDIDO.'"');
}

rsv::integrar();

?>