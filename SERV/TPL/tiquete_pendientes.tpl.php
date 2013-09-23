<?php

/* Migración a uso de cuentas completa - no usa cuenta - no USAR cuenta. */

if (isset($_POST['imprimir']))
{
    if (empty($_POST['cuenta']))
    {
        error_log('tiquete_pendientes.tpl: se imprimio tiquete sin cuenta');
    } else {
        $cuenta = db_codex($_POST['cuenta']);
        $c = 'UPDATE `ordenes` SET `flag_tiquetado`=1 WHERE `cuenta`="'.$cuenta.'"';
        db_consultar($c);
        
        /****** HISTORIAL *******/
            
            if (empty($_POST['no_historial']))
            {
                $DATOS['grupo'] = 'ORDENES';
                $DATOS['accion'] = 'TIQUETE';
                
                if (empty($_POST['nota']))
                    $DATOS['nota'] = 'impresión de tiquete';
                else
                    $DATOS['nota'] = $_POST['nota'];
                
                $DATOS['fechahora'] = mysql_datetime();
                $DATOS['cuenta'] = $cuenta;
                
                db_agregar_datos('historial',$DATOS);
            }
            
        /****** HISTORIAL *******/
    }
    
    db_agregar_datos('comandas', array('data' => $_POST['imprimir'], 'impreso' => '0', 'estacion' => $_POST['estacion']));
        
    return;
}

if (!empty($_POST['impreso']) && is_numeric($_POST['impreso']))
{
    $c = 'UPDATE `tiquetes` SET `impreso`= 1 WHERE `ID_tiquete`="'.db_codex($_POST['impreso']).'" LIMIT 1';
    $r = db_consultar($c);
    return;
}

$c = 'SELECT `ID_tiquete`, `ID_mesa`, `cuenta`, `fechahora`, `impreso` FROM `tiquetes` WHERE impreso=0 LIMIT 1';
$r = db_consultar($c);

if (mysqli_num_rows($r) > 0 && $f = db_fetch($r)) {
    $json['aux']['tiquetes'] = $f;
}
?>