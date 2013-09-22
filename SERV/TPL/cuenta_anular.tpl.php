<?php

/* Migración a uso de cuentas completa. */

if ( empty($_POST['cuenta']) )
    return;

$cuenta = db_codex($_POST['cuenta']);

$c = 'UPDATE ordenes SET flag_tiquetado=0, flag_anulado=1, flag_pagado=0, fechahora_pagado="0000-00-00 00:00:00", fechahora_anulado=NOW() WHERE cuenta="'.$cuenta.'"';
db_consultar($c);

if (!empty($_POST['motivo']))
{
    $DATOS['fechahora'] = mysql_datetime();
    $DATOS['nota'] = $_POST['motivo'];
    $DATOS['cuenta'] = $cuenta;
    
    $DATOS['grupo'] = 'ORDENES';
    $DATOS['accion'] = 'anulacion';
    
    db_agregar_datos('historial',$DATOS);
}

?>