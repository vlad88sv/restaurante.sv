<?php
if (isset($_POST['buscar_telefono'])) {
    $telefono = db_codex(preg_replace('/[^\d]/','',trim($_POST['buscar_telefono'])));
    $json['aux']['resultado'] = db_contar('domicilio','telefono="'.$telefono.'"');
    return;
}

if (isset($_POST['info'])) {
    $telefono = db_codex(preg_replace('/[^\d]/','',trim($_POST['info'])));
    
    $cNombres = 'SELECT cliente FROM domicilio WHERE telefono="'.$telefono.'"';
    $rNombres = db_consultar($cNombres);
    
    while ($fNombres = db_fetch($rNombres))
    {
        $json['aux']['resultado']['nombres'][] = $fNombres;
    }    

    $cDirecciones = 'SELECT direccion FROM domicilio WHERE telefono="'.$telefono.'"';
    $rDirecciones = db_consultar($cDirecciones);
    
    while ($fDirecciones = db_fetch($rDirecciones))
    {
        $json['aux']['resultado']['direcciones'][] = $fDirecciones;
    }
}
?>