<?php
if (isset($_POST['ver']))
{
    $estacion = db_codex($_POST['ver']);
    
    switch ($estacion)
    {
        case 'tiquetes':
            $estacion = '"tiquetes"';
            break;
        
        case 'comandas':
            $estacion = '"comandas"';
            break;

        case 'domicilio':
            $estacion = '"domicilio","cortez"';
            break;
        
        case 'todos':
        case 'todo':
        default:
            $estacion = '"tiquetes","comandas","domicilio","cortez"';
            break;
        
    }
    
    $c = 'SELECT `ID_comanda`, `data`, `impreso` FROM `comandas` WHERE estacion IN ('.$estacion.') AND impreso=0 ORDER BY ID_comanda ASC LIMIT 1';
    $r = db_consultar($c);
    
    if (mysqli_num_rows($r) > 0)
    {
        $f = db_fetch($r);
        $json['aux']['comanda'] = $f;
    }
    return;
}

if (isset($_POST['impreso']))
{
    $c = 'DELETE FROM comandas WHERE ID_comanda="'.db_codex($_POST['impreso']).'" LIMIT 1';
    db_consultar($c);
    return;
}
?>