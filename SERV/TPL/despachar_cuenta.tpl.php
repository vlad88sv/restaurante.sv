<?php

if ( empty($_POST['cuenta']) || ! is_numeric($_POST['cuenta']) )
{
    return;
}

$cuenta = db_codex($_POST['cuenta']);

$c = "UPDATE pedidos SET flag_elaborado = 1, flag_despachado = 1, fechahora_elaborado = IF(fechahora_elaborado = '0000-00-00 00:00:00', NOW(), fechahora_elaborado), fechahora_despachado=NOW() WHERE ID_cuenta = '$cuenta'";
db_consultar($c);

if (isset($_POST['GENERAR_IMPRESION_DOMICILIO'])) {
    rsv::generar_impresion_domicilio($cuenta);
}
