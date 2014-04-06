<?php
$ORDER = "`precio` DESC, `nombre` ASC";
$WHERE = "";

if ( isset($_POST['grupo']) && is_numeric($_POST['grupo']) )
    $WHERE = " AND `ID_menu` = '".db_codex($_POST['grupo'])."'";

if ( isset($_POST['agrupar']))
    $ORDER = "`ID_grupo` ASC, `orden` ASC";

$c = "SELECT `ID_producto`, `ID_grupo`, `nombre`, `orden`, `descripcion`, `precio`, `disponible`, `descontinuado`, `complementar`, `creacion` FROM `productos` WHERE 1 $WHERE ORDER BY $ORDER";
$r = db_consultar($c);

while ($r && $f = db_fetch($r))
{
    $json['aux'][] = $f;
}
?>
