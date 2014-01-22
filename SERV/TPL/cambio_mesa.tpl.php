<?php

/* Migración a uso de cuentas completa. */

if ( empty($_POST['cuenta']) )
    return;

$cuenta = db_codex($_POST['cuenta']);

$mesa_nueva = '0';
if (isset($_POST['mesa_nueva']) && is_numeric($_POST['mesa_nueva']) && $_POST['mesa_nueva'] > 0 )
    $mesa_nueva = db_codex($_POST['mesa_nueva']);

if ($mesa_nueva == '0')
{
    $json['error'] = 'Número de mesa incorrecto';
    return;
}

$cuenta_destino = rsv::cuenta_de_mesa($mesa_nueva);

if ($cuenta_destino)
{
    // Si la mesa existe entonces solo apuntamos los pedidos a la cuenta existente
    $DATOS['ID_cuenta'] = $cuenta_destino;
    db_actualizar_datos('pedidos', $DATOS, 'ID_cuenta="'.$cuenta.'"');
    $cuenta = $cuenta_destino;
    $motivo = 'Se fusionó una mesa a esta cuenta';
} else {
    // No existe ninguna mesa con ese número, solo tomemoslo
    $DATOS['ID_mesa'] = $mesa_nueva;
    db_actualizar_datos('cuentas', $DATOS, 'ID_cuenta="'.$cuenta.'"');
    $motivo = 'Se cambio el número de mesa';
}


rsv::integrar();


// HISTORIAL
unset($DATOS);

$DATOS['fechahora'] = mysql_datetime();
$DATOS['nota'] = $motivo;
$DATOS['ID_pedido'] = 0;
$DATOS['ID_cuenta'] = $cuenta;

$DATOS['grupo'] = 'CUENTAS';
$DATOS['accion'] = 'cambio de mesa';

db_agregar_datos('historial',$DATOS);


CacheDestruir();
?>