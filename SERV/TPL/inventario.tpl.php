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
    
    $inventario = array();
    parse_str($_POST['inventario'], $inventario);
    
    foreach($inventario['ingrediente_cantidad'] as $ID_ingrediente => $cantidad)
    {
        $ID_ingrediente = db_codex($ID_ingrediente);
        $cantidad = db_codex($cantidad);
        
        if (!is_numeric($ID_ingrediente) || !is_numeric($cantidad)) continue;
        
        $c = 'INSERT INTO stock (ID_compra, ID_ingrediente, existencia, cambio, fechahora,  operacion) VALUES ("'.$ID_compra.'", '.$ID_ingrediente.', (COALESCE((SELECT COALESCE(existencia,0) FROM stock AS tt0 WHERE tt0.ID_ingrediente='.$ID_ingrediente.' ORDER BY tt0.ID_stock DESC LIMIT 1),0)+'.$cantidad.'), "'.$cantidad.'", NOW(), "ingreso")';
        db_consultar($c);
    }
    return;
}


$c = 'SELECT ti.nombre, ti.unidad, ti.unidad2, ti.divisor, (SELECT COALESCE(existencia,0) FROM stock AS tt0 WHERE tt0.ID_ingrediente=ts.ID_ingrediente ORDER BY tt0.fechahora DESC LIMIT 1) AS existencia_actual FROM stock AS ts LEFT JOIN ingredientes AS ti USING(ID_ingrediente) WHERE ti.ID_ingrediente IS NOT NULL GROUP BY ts.ID_ingrediente';
$r = db_consultar($c);

while ($r && $f = db_fetch($r))
{
    $json['aux'][] = $f;
}
?>