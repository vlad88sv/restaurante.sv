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

$cuenta_destino = db_obtener('ordenes', 'cuenta', 'flag_pagado=0 AND flag_anulado=0 AND ID_mesa="'.db_codex($mesa_nueva).'"');

if ($cuenta_destino)
{
    $DATOS['cuenta'] = $cuenta_destino;
}

$DATOS['ID_mesa'] = $mesa_nueva;

db_actualizar_datos('ordenes', $DATOS, 'cuenta="'.$cuenta.'"');

?>