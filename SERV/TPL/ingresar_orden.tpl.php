<?php
// En el array de pedidos, cada pedido (producto) contiene 3 partes fundamentales:
// 1. ID de producto y precio
// 2. Array de ingredientes que se van a _quitar_
// 3. Array de extras/adicionales que se van a _agregar_

$permisos[] = 'ingresar_pedidos';

if ( ! sesion::verificar($permisos) )
{
    $json['AUT'] = 'no';
    $json['permisos'] = @$_SESSION['permisos'];
    if ( ! sesion::$autenticado )
        $json['AUT_M'] = 'No ha iniciado sesión';
    else
        $json['AUT_M'] = 'No cuenta con los permisos necesarios';
    return;
}


if (!is_numeric($_POST['mesa']) || !isset($_POST['orden']) || !is_array($_POST['orden']) || count($_POST['orden']) == 0)
{
    $json['error'][] = 'La orden es inválida. Error 1';
}

$cuenta = 0;

if ( ! isset($_POST['FORZAR_CUENTA_NUEVA']) )
{
    // Será que esta mesa ya tiene cuenta?. Agregar.
    $cuenta = rsv::cuenta_de_mesa($_POST['mesa']);
}


// No hay cuenta para esta mesa?. Crear.
if ( ! $cuenta )
{
    $cuenta = rsv::cuenta_nueva($_POST['mesa'],$_POST['mesero']);
}

// Es domicilio?
if (isset($_POST['domicilio']) && is_array($_POST['domicilio']))
{
    rsv::cuenta_anexar_domicilio( $cuenta, rsv::domicilio_crear_registro($_POST['domicilio']) );
}

// Es sin propina?
if ( isset($_POST['FORZAR_NO_PROPINA']) )
{
    // 1 = NO PROPINA
    rsv::cuenta_propina($cuenta, 1);
}

$opciones = array();

if ( isset($_POST['PAUSAR_ELABORACION']) && $_POST['PAUSAR_ELABORACION'] == 'si' )
{
    $opciones['pausar'] = $_POST['pedido_fechahora_activacion'];
}

// ingresar_orden destruye el cache, asi que no hay necesidad de hacerlo aquí.
$grupo = rsv::ingresar_pedidos($cuenta, $_POST['orden'], $opciones);


// En domicilio generamos la impresión de una sola vez
if ( 1 && isset($_POST['GENERAR_IMPRESION_DOMICILIO']))
{
    rsv::generar_impresion_domicilio($cuenta);
}

// En domicilio generamos la impresión de una sola vez
if ( 1 && isset($_POST['GENERAR_IMPRESION_ORDEN_TRABAJO']))
{
    rsv::generar_impresion_orden_trabajo($cuenta, $grupo);
}

$json['post'] = $_POST;
?>