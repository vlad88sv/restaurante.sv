<?php
$WHERE = '';

if (isset($_POST['modo']) && $_POST['modo'] == 'inventario')
{
    return;
}

$WHERE = '';
if (isset($_POST['producto']) && is_numeric($_POST['producto']))
{
    $WHERE .= "AND t2.`ID_producto` = '".$_POST['producto']."' OR t1.ID_grupo = '-1' OR t1.ID_grupo = (SELECT ID_grupo FROM `productos` AS tt1 WHERE tt1.ID_producto='".$_POST['producto']."')";
}

$c = "SELECT `ID_adicional`, `precio`, `ID_grupo`, `disponible`, `nombre`, `afinidad`
FROM `adicionables` AS t1
LEFT JOIN `adicionables_producto` AS t2
USING ( ID_adicional )
WHERE 1 $WHERE
ORDER BY precio ASC, nombre ASC
";
$r = db_consultar($c);

while ($r && $f = db_fetch($r))
{
    $json['aux']['adicionables'][] = $f;
}
?>
