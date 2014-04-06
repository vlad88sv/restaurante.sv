<?php

/* Migración a uso de cuentas completa. */

if ( empty($_POST['cuenta']) )
{
    $json['error'] = 'Operacion no ejecutada';
    return;
}

$cuenta = db_codex($_POST['cuenta']);

$campo = db_codex($_POST['campo']);
$valor = db_codex($_POST['valor']);

if ($campo == '')
{
    $json['error'] = 'Operacion no ejecutada';
    return;
}

$c = "UPDATE cuentas SET $campo='$valor' WHERE ID_cuenta = '$cuenta'";
db_consultar($c);
$json['resultado'] = 'MODIFICACION OK';

if (!empty($_POST['motivo']))
{
    $DATOS['fechahora'] = mysql_datetime();
    $DATOS['nota'] = $_POST['motivo'];
    $DATOS['ID_cuenta'] = $cuenta;
    
    $DATOS['flag_importante'] = '1';
    $DATOS['grupo'] = 'CUENTA';
    $DATOS['accion'] = $campo;
    
    db_agregar_datos('historial',$DATOS);
}

CacheDestruir();
?>