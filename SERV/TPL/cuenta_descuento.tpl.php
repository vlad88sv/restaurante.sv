<?php

if (empty($_POST['cuenta']) || empty($_POST['tipo']) || !isset($_POST['valor']) || empty($_POST['motivo']))
{
    $json['error'] = 'DATOS ERRONEOS';
    return;
}

$cuenta = $_POST['cuenta'];

// PORCENTAJE
if ($_POST['tipo'] == 'porcentaje')
{
    $porcentaje = numero($_POST['valor']);    
    
    if (!is_numeric($porcentaje))
    {
        $json['error'] = 'PORCENTAJE ERRONEO';
        return;
    }
    
    if ( $porcentaje < 0 || $porcentaje > 100 )
    {
        $json['error'] = 'PORCENTAJE ERRONEO';
        return;
    }
    
    $c = 'UPDATE `pedidos` LEFT JOIN `pedidos_adicionales` USING(ID_pedido) SET `pedidos`.`precio_grabado` = (`pedidos`.`precio_original` * ( 1 - '.numero($porcentaje / 100).')), `pedidos_adicionales`.`precio_grabado` = (`pedidos_adicionales`.`precio_original` * ( 1 - '.numero($porcentaje / 100).')) WHERE ID_cuenta="'.db_codex($cuenta).'"';
    db_consultar($c);
    
    $json['sql'] = $c;
    
    if (!empty($_POST['motivo']))
    {
        $DATOS['fechahora'] = mysql_datetime();
        $DATOS['nota'] = $_POST['motivo'];
        $DATOS['ID_cuenta'] = $cuenta;

        $DATOS['grupo'] = 'ORDENES';
        $DATOS['accion'] = 'DESCUENTO '.$porcentaje.'%';

        db_agregar_datos('historial',$DATOS);
    }

}

// MONTO
if ($_POST['tipo'] == 'cantidad')
{
        
    $cantidad = numero($_POST['valor']);    
    
    if (!is_numeric($cantidad))
    {
        $json['error'] = 'CANTIDAD ERRONEA';
        return;
    }
    
    // Ingresamos la orden de descuento
    $PEDIDO['fechahora_pedido'] = mysql_datetime();
    $PEDIDO['flag_elaborado'] = 1;
    $PEDIDO['flag_despachado'] = 1;
    $PEDIDO['nodo'] = 'especial';
    $PEDIDO['tmpID'] = 0;
    $PEDIDO['ID_producto'] = 113; // Número mágico de producto Descuento
    $PEDIDO['precio_original'] = ($cantidad * -1); // Cantidad negativa
    $PEDIDO['precio_grabado'] = ($cantidad * -1);
    $PEDIDO['ID_cuenta'] = $cuenta;

    db_agregar_datos('pedidos', $PEDIDO);
    
    if (!empty($_POST['motivo']))
    {
        $DATOS['fechahora'] = mysql_datetime();
        $DATOS['nota'] = $_POST['motivo'];
        $DATOS['ID_cuenta'] = $cuenta;

        $DATOS['flag_importante'] = '1';
        $DATOS['grupo'] = 'CUENTA';
        $DATOS['accion'] = 'DESCUENTO CUPÓN';

        db_agregar_datos('historial',$DATOS);
    }
    
}

CacheDestruir();
?>