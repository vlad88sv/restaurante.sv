<?php
switch (@$_POST['imprimir'])
{
    case 'orden_de_trabajo':
        rsv::generar_impresion_orden_trabajo(@$_POST['cuenta']);
        break;
    
    case 'tiquete':
        rsv::generar_impresion_tiquete(@$_POST['cuenta'], @$_POST['nota'], @$_POST['estacion']);
        break;
}
