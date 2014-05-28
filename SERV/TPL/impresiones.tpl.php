<?php
switch (@$_POST['imprimir'])
{
    case 'orden_de_trabajo':
        rsv::generar_impresion_orden_trabajo(@$_POST['cuenta']);
        break;
    
    case 'tiquete':
        rsv::generar_impresion_tiquete(@$_POST['cuenta'], @$_POST['nota'], @$_POST['estacion']);
        break;

    case 'datos':
        impresiones_datos(@$_POST['datos'], @$_POST['estacion'], @$_POST['cuenta'], @$_POST['nota']);
        break;
}


function impresiones_datos($datos, $estacion, $cuenta, $nota)
{     
    // HISTORIAL
    if (is_numeric($cuenta) && $historial)
    {
        $DATOS['grupo'] = 'ORDENES';
        $DATOS['accion'] = 'TIQUETE';
        $DATOS['nota'] = $nota;
        $DATOS['fechahora'] = mysql_datetime();
        $DATOS['ID_cuenta'] = $cuenta;
        db_agregar_datos('historial',$DATOS);
    }
    db_agregar_datos('comandas', array('data' => $datos, 'estacion' => $estacion));
}