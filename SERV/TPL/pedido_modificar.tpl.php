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
        break;
}

// Como la operacion pudo haber sido cambio de cuenta, obtenemos la cuenta actual
$ID_cuenta = db_obtener('pedidos', 'ID_cuenta', 'ID_pedido="'.$pedido.'"');

if (!empty($_POST['nota']))
{
    $DATOS['fechahora'] = mysql_datetime();
    $DATOS['nota'] = $_POST['nota'];
    $DATOS['ID_cuenta'] = $ID_cuenta;
    $DATOS['ID_pedido'] = $pedido;
    
    $DATOS['flag_importante'] = '1';
    $DATOS['grupo'] = 'PEDIDO';
    $DATOS['accion'] = $campo;
    
    db_agregar_datos('historial',$DATOS);
}

CacheDestruir();
?>