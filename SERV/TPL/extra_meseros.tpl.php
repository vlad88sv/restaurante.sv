<?php
$c = 'SELECT `ID_usuarios` , `usuario` , `clave` , `nivel` FROM `usuarios` WHERE disponible = 1 AND nivel IN ("mesero", "gerente")';
$r = db_consultar($c);

while ($f = db_fetch($r))
{
    $json['aux'][] = $f;
}
?>