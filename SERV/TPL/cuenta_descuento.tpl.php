<?php

if (empty($_POST['cuenta']) || empty($_POST['tipo']) || empty($_POST['valor']) || empty($_POST['motivo']))
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
    
    $c = 'UPDATE `pedidos` LEFT JOIN `pedidos_adicionales` USING(ID_pedido) LEFT JOIN ordenes USING( ID_orden ) SET `pedidos`.`precio_grabado` = (`precio_original` * ( 1 - '.numero($porcentaje / 100).')), `pedidos_adicionales`.`precio_grabado` = (`precio_original` * ( 1 - '.numero($porcentaje / 100).')) WHERE cuenta="'.db_codex($cuenta).'"';
    db_consultar($c);
    
    $json['sql'] = $c;
    
    if (!empty($_POST['motivo']))
    {
        $DATOS['fechahora'] = mysql_datetime();
        $DATOS['nota'] = $_POST['motivo'];
        $DATOS['cuenta'] = $cuenta;

        $DATOS['grupo'] = 'ORDENES';
        $DATOS['accion'] = 'DESCUENTO %';

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
    
    
    // Obtengamos una orden de esta cuenta para usarla de base
    $ORDEN = db_obtener_fila('ordenes', 'cuenta="'.$cuenta.'"');
    
    if ($ORDEN == false)
    {
        $json['error'] = 'ORDEN ERRONEA';
        return;
    }
    
    // Overrides
    unset ($ORDEN['ID_orden']);
    $ORDEN['fechahora_pedido'] = mysql_datetime();
    $ORDEN['fechahora_elaborado'] = mysql_datetime();
    $ORDEN['fechahora_entregado'] = mysql_datetime();
    $ORDEN['flag_elaborado'] = 1;
    $ORDEN['flag_despachado'] = 1;
    $ORDEN['nodo'] = 'especial';
    
    $json['orden'] = $ORDEN;

    $ID_orden = db_agregar_datos('ordenes', $ORDEN);
    
    if ($ID_orden > 0) {
        // Ingresamos la orden de descuento
        $PEDIDO['tmpID'] = 0;
        $PEDIDO['ID_producto'] = 113; // Número mágico de producto Descuento
        $PEDIDO['precio_original'] = ($cantidad * -1); // Cantidad negativa
        $PEDIDO['precio_grabado'] = ($cantidad * -1);
        $PEDIDO['ID_orden'] = $ID_orden;

        db_agregar_datos('pedidos', $PEDIDO);
    }
    
    if (!empty($_POST['motivo']))
    {
        $DATOS['fechahora'] = mysql_datetime();
        $DATOS['nota'] = $_POST['motivo'];
        $DATOS['cuenta'] = $cuenta;

        $DATOS['grupo'] = 'ORDENES';
        $DATOS['accion'] = 'DESCUENTO CUPÓN';

        db_agregar_datos('historial',$DATOS);
    }
    
}

CacheDestruir();
?>