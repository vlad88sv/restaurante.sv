<?php

/* Migración a uso de cuentas completa - no usa cuenta - no USAR cuenta. */

if ( empty($_POST['orden']) || !is_numeric($_POST['orden']) ) {
    $json['error'] = 'Operación no completada';
} else {
    
    if (isset($_POST['elaborado']))
    {
        $ID_orden = db_codex($_POST['orden']);
        $c = "UPDATE ordenes SET flag_elaborado = 1, fechahora_elaborado=NOW() WHERE ID_orden=$ID_orden";
        db_consultar($c);
    } else {
        $ID_orden = db_codex($_POST['orden']);
        $c = "UPDATE ordenes SET flag_elaborado = 1, flag_despachado = 1, fechahora_entregado=NOW() WHERE ID_orden=$ID_orden";
        db_consultar($c);
        
        if (isset($_POST['comanda']))
            $_POST['imprimir'] = $_POST['comanda'];
        
        db_agregar_datos('comandas', array('data' => $_POST['imprimir'], 'impreso' => '0', 'estacion' => 'comandas'));
    }
    
    CacheDestruir();
}
?>