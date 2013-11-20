<?php
$campo = db_codex(@$_POST['campo']);
$valor = db_codex(@$_POST['valor']);

if ($campo == '')
{
    $json['error'] = 'Operacion no ejecutada';
    return;
}

$pedidos_adicional = 0;
if (isset($_POST['pedido_adicional']) && is_numeric($_POST['pedido_adicional']) && $_POST['pedido_adicional'] > 0 )
    $pedidos_adicional = db_codex($_POST['pedido_adicional']);
    
if ($pedidos_adicional == '0') {
    $json['error'] = 'Operacion no ejecutada';
    return;
}

$c = "UPDATE pedidos_adicionales SET $campo='$valor' WHERE ID_pedido_adicional = $pedidos_adicional LIMIT 1";
db_consultar($c);


$ID_pedido = db_obtener('pedidos LEFT JOIN pedidos_adicionales USING (ID_pedido)', 'ID_pedido', 'ID_pedido_adicional = "'.$pedidos_adicional.'"');

if (!empty($_POST['nota']))
{
    $DATOS['fechahora'] = mysql_datetime();
    $DATOS['nota'] = $_POST['nota'];
    $DATOS['ID_pedido'] = $ID_pedido;
    
    $DATOS['grupo'] = 'PEDIDOS::ADICIONALES';
    $DATOS['accion'] = $campo;
    
    db_agregar_datos('historial',$DATOS);
}

CacheDestruir();
?>