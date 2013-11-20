<?php
if (isset($_POST['ingreso']))
{
    $compra = array();
    parse_str($_POST['compra'], $compra);
    
    $DATOS['empresa'] = $compra['empresa'];
    $DATOS['descripcion'] = $compra['descripcion'];
    $DATOS['precio'] = $compra['precio'];
    $DATOS['via'] = $compra['via'];
    $DATOS['fechatiempo'] = mysql_datetime();
    
    $ID_compra = db_agregar_datos('compras',$DATOS);
    return;
}
?>