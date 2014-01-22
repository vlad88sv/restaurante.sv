<?php
if (defined('USAR_AUT') && USAR_AUT === false )
{
    $json['AUTENTICADO'] = 'si';
    $json['AUTORIZADO'] = 'si';    
    return;
}

if ( isset($_POST['terminar']) )
{
    sesion::terminar();
}

if ( isset($_POST['usuario']) && isset($_POST['clave']) )
{
    $json['intento_aut'] = sesion::iniciar($_POST['usuario'], $_POST['clave']);
}

if ( isset($_POST['permisos']) && is_array($_POST['permisos']) )
{
    sesion::verificar($_POST['permisos']);
}

// enviamos estado de autenticacion y autorizacion
$json['AUTENTICADO'] = (sesion::$autenticado ? 'si' : 'no');
$json['AUTORIZADO'] = (sesion::$autorizado ? 'si' : 'no');
