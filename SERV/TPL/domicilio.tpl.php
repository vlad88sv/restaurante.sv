<?php

if ( !empty($_POST['transitar']) && is_numeric($_POST['transitar']) )
{    
    rsv::domicilio_modificar($_POST['transitar'], 'flag_en_transito', '1');
    return;
}


if (isset($_POST['imprimir']))
{    
    rsv::generar_impresion_domicilio($_POST['imprimir']);
    return;
}

if (isset($_POST['guardar_datos']) && is_array($_POST['guardar_datos']))
{
    $json['test'] = $_POST['guardar_datos'];
    rsv::domicilio_crear_registro($_POST['guardar_datos']);
    return;
}

if (isset($_POST['buscar_telefono'])) {
    $telefono = db_codex(preg_replace('/[^\d]/','',trim($_POST['buscar_telefono'])));
    $json['aux']['resultado'] = db_contar('domicilio','telefono="'.$telefono.'"');
    return;
}

if (isset($_POST['info'])) {
    $telefono = db_codex(trim($_POST['info']));
    
    $cNombres = 'SELECT DISTINCT nombre FROM domicilio WHERE telefono="'.$telefono.'"';
    $rNombres = db_consultar($cNombres);
    
    while ($fNombres = db_fetch($rNombres))
    {
        $json['aux']['resultado']['nombres'][] = $fNombres;
    }    

    $cDirecciones = 'SELECT DISTINCT direccion FROM domicilio WHERE telefono="'.$telefono.'"';
    $rDirecciones = db_consultar($cDirecciones);
    
    while ($fDirecciones = db_fetch($rDirecciones))
    {
        $json['aux']['resultado']['direcciones'][] = $fDirecciones;
    }
}
?>