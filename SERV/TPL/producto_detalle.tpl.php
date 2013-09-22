<?php
$c = "SELECT t1.`ID_producto` AS 'ID', t2.`descripcion` AS 'Grupo', t1.`nombre` AS 'Nombre', t1.`descripcion` AS 'Descripción', t1.`precio` AS 'Precio', IF(t1.`disponible` = 0, 'No', 'Si') AS 'Disponible' FROM `productos` AS t1 LEFT JOIN `productos_grupos` AS t2 USING(ID_grupo) WHERE `ID_producto` = '".$_POST['producto']."'";
$r = db_consultar($c);

if ($r && $f = db_fetch($r))
{
    $json['aux']['detalle'][] = $f;
}
?>
