<?php
$c = 'SELECT `ID_usuarios` , `usuario` , `clave` , `nivel` FROM `usuarios` WHERE 1';
$r = db_consultar($c);

while ($f = db_fetch($r))
{
    $json['aux'][] = $f;
}
?>