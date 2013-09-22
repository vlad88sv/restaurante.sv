<?php
$campo = db_codex(@$_POST['campo']);
$valor = db_codex(@$_POST['valor']);

if ($campo == '')
{
    $json['error'] = 'Operacion no ejecutada';
    return;
}

if (isset($_POST['opciones']) && is_array($_POST['opciones'])) {

    foreach ($_POST['opciones'] as $opcion => $op_valor) {
        switch($opcion) {
            case 'uuid_a_valor':
                $valor = mysql_uuid();
        }
    }

    if (isset($_POST['pedidos']) && is_array($_POST['pedidos'])) {
        $pedidos = implode("','",$_POST['pedidos']);
        $c = "UPDATE pedidos SET $campo='$valor' WHERE ID_pedido IN ('$pedidos') LIMIT 1";
        db_consultar($c);
    }

    return;
}

$pedido = 0;
if (isset($_POST['pedido']) && is_numeric($_POST['pedido']) && $_POST['pedido'] > 0 )
    $pedido = db_codex($_POST['pedido']);
    
if ($pedido == '0') {
    $json['error'] = 'Operacion no ejecutada';
    return;
}

$c = "UPDATE pedidos SET $campo='$valor' WHERE ID_pedido = $pedido LIMIT 1";
db_consultar($c);


// Triggers
switch ($campo) {
    case 'flag_cancelado':
        // si lo cancelo tenemos que devolver al stock los ingredientes que usó
        $ID_producto = db_obtener('pedidos', 'ID_producto', 'ID_pedido="'.$pedido.'"');
        $cDevolverIngredientes = 'INSERT INTO stock (ID_pedido, ID_ingrediente, existencia, cambio, fechahora,  operacion) SELECT "'.$pedido.'", `ID_ingrediente`, (COALESCE((SELECT COALESCE(existencia,0) FROM stock AS tt0 WHERE tt0.ID_ingrediente=t1.ID_ingrediente ORDER BY tt0.ID_stock DESC LIMIT 1),0)+t1.cantidad), `cantidad`, NOW(), "cancelacion" FROM `productos_ingredientes` AS t1 WHERE ID_producto="'.$ID_producto.'"';
        db_consultar($cDescontarIngredientes);    
        break;
}


if (!empty($_POST['nota']))
{
    $DATOS['fechahora'] = mysql_datetime();
    $DATOS['nota'] = $_POST['nota'];
    $DATOS['ID_pedido'] = $pedido;
    
    $DATOS['grupo'] = 'PEDIDOS';
    $DATOS['accion'] = $campo;
    
    db_agregar_datos('historial',$DATOS);
}
?>