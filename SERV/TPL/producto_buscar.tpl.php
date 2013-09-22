<?php
$WHERE = "";
if ( isset($_POST['grupo']) && is_numeric($_POST['grupo']) )
    $WHERE = " AND `ID_menu` = '".db_codex($_POST['grupo'])."'";

$c = "SELECT `ID_producto`, `ID_grupo`, `nombre`, `orden`, `descripcion`, `precio`, `disponible`, `descontinuado`, `creacion` FROM `productos` WHERE 1 ".$WHERE." ORDER BY `precio`, `nombre` ASC";
$r = db_consultar($c);

while ($r && $f = db_fetch($r))
{
    $json['aux'][] = $f;
}
?>
