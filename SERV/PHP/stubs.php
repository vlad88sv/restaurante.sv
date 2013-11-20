<?php
function numero($numero)
{
    if (!is_numeric($numero))
        return 0.00;
    
    return number_format($numero,2,'.','');
}

function ingresar_orden($PEDIDOS, $MESA, $MESERO, $MODO = 0, $FORZAR_CUENTA_NUEVA = false){
    
    /* $MODO:
     * 0 = agregar pedidos
     * 1 = actualizar pedidos con nueva orden
     */
    
    if ($MODO == 0)
    {
        $ORDEN['fechahora_pedido'] = mysql_datetime();
    }
        
    if ( $FORZAR_CUENTA_NUEVA ) {
        $cuenta = false;
    } else {
        $cuenta = db_obtener('ordenes', 'cuenta', 'flag_pagado=0 AND flag_anulado=0 AND ID_mesa="'.db_codex($MESA).'"');
    }
    
    if ($cuenta) {
        $ORDEN['cuenta'] = $cuenta;
    } else {
        $ORDEN['cuenta'] = mysql_uuid();
    }
    
    $ORDEN['ID_mesa'] = $MESA;
    $ORDEN['ID_mesero'] = (empty($MESERO) ? 0 : $MESERO);
    
    $ID_orden = 0;
    $ID_orden_pizzas_1 = 0;
    $ID_orden_pizzas_2 = 0;
    $ID_orden_bebidas = 0;
    $ID_orden_pastas = 0;
    $ID_orden_ensaladas = 0;
    $ID_orden_entradas = 0;
    $ID_orden_entradas_horno = 0;
    $ID_orden_postres = 0;
    $ID_esta_orden = 0;
    
    /* Variables para estrategias de balanceo */
    $bal_pizzas_flipflop = apc_fetch('bal_pizzas_flipflop');
    
    $bal2_pizzas_flipflop = apc_fetch('bal2_pizzas_flipflop');
    apc_store('bal2_pizzas_flipflop', !$bal2_pizzas_flipflop);
    /*-Variables para estrategias de balanceo */
    
    foreach($PEDIDOS as $tmpID => $pedido)
    {
        // Reseteamos el registro para que pueda volver a escoger el selector
        $ID_esta_orden = 0;
        
        // Reseteamos la prioridad para que pueda volver a escoger el selector
        $ORDEN['prioridad'] = 'baja';
        
        // Reseteamos el estado de despacho
        $ORDEN['flag_elaborado'] = 0;
        $ORDEN['flag_despachado'] = 0;
        
        if ($MODO == 1)
        {
            // Rescatemos los estados de la orden del pedido en particular
            $ORDEN_PREVIA = db_obtener_fila('ordenes', 'ID_orden="'.db_codex($pedido['ID_orden']).'"');
            
            $ORDEN['flag_elaborado'] = $ORDEN_PREVIA['flag_elaborado'];
            $ORDEN['flag_despachado'] = $ORDEN_PREVIA['flag_despachado'];
            $ORDEN['fechahora_pedido'] = $ORDEN_PREVIA['fechahora_pedido'];
            $ORDEN['fechahora_elaborado'] = $ORDEN_PREVIA['fechahora_elaborado'];
            $ORDEN['fechahora_entregado'] = $ORDEN_PREVIA['fechahora_entregado'];
        }
        
        
        // Reseteamos el nodo afin
        $ORDEN['nodo'] = '';
        
        // busquemos el ID de grupo de este pedido.
        $ID_grupo = db_obtener('productos', 'ID_grupo', 'ID_producto = '. db_codex($pedido['ID']));
        
        // Este es un error grave, nunca deberia de no encontrar grupo.
        if ($ID_grupo == 0)
        {
            error_log('GRUPO ZERO: '.db_codex($pedido['ID']));
        }
        
        // Si el grupo es de pizzas entonces balanceemos
        if ($ID_grupo == 1)
        {
            if ($ID_orden_pizzas_1 == 0)
            {
                $ORDEN['nodo'] = 'pizzas1';
                $ID_orden_pizzas_1 = db_agregar_datos('ordenes', $ORDEN);
            }
            
            if (1) {
                // Sin balanceo: todas las pizzas van al cielo
                $ID_esta_orden = $ID_orden_pizzas_1;
            }
            
            if (0) {
                
                // Balanceo por desborde: trata de que ambos nodos tengan la misma cantidad de pizzas
                
                if ($ID_orden_pizzas_2 == 0)
                {
                    $ORDEN['nodo'] = 'pizzas2';
                    $ID_orden_pizzas_2 = db_agregar_datos('ordenes', $ORDEN);
                }

                $ID_esta_orden = ($bal2_pizzas_flipflop ? $ID_orden_pizzas_1 : $ID_orden_pizzas_2);
                
                $bal_pizzas_flipflop = !$bal_pizzas_flipflop;
                
                apc_store('bal_pizzas_flipflop', $bal_pizzas_flipflop);
                
            }
            
            if (0) {
                
                // Balanceo por mesa: como el desborde pero en lugar de
                // hacerlo por pedidos, se hace por orden
                
                if ($ID_orden_pizzas_2 == 0)
                {
                    $ORDEN['nodo'] = 'pizzas2';
                    $ID_orden_pizzas_2 = db_agregar_datos('ordenes', $ORDEN);
                }

                $ID_esta_orden = ($bal2_pizzas_flipflop ? $ID_orden_pizzas_1 : $ID_orden_pizzas_2) ;
            }
        }

        // Si el grupo es de postres
        if ($ID_grupo == 4)
        {
            if ($ID_orden_postres == 0)
            {
                $ORDEN['nodo'] = 'postres';
                $ID_orden_postres = db_agregar_datos('ordenes', $ORDEN);
            }
            
            $ID_esta_orden = $ID_orden_postres;
        }
        
        // Si el grupo es de bebidas preparadas en cocina creemos una nueva orden
        if ($ID_grupo == 5)
        {
            if ($ID_orden_bebidas == 0)
            {
                $ORDEN['nodo'] = 'bebidas_preparadas';
                $ID_orden_bebidas = db_agregar_datos('ordenes', $ORDEN);
            }
            
            $ID_esta_orden = $ID_orden_bebidas;
        }

        // Si el grupo es de pastas creemos una nueva orden
        if ($ID_grupo == 3)
        {
            if ($ID_orden_pastas == 0)
            {
                $ORDEN['nodo'] = 'pastas';
                $ID_orden_pastas = db_agregar_datos('ordenes', $ORDEN);
            }
            
            $ID_esta_orden = $ID_orden_pastas;
        }
        
        // Si el grupo es de ensaladas creemos una nueva orden
        if ($ID_grupo == 8)
        {
            if ($ID_orden_ensaladas == 0)
            {
                $ORDEN['nodo'] = 'ensaladas';
                $ID_orden_ensaladas = db_agregar_datos('ordenes', $ORDEN);
            }
            
            $ID_esta_orden = $ID_orden_ensaladas;
        }
        
        // Si el grupo es de entradas creemos una nueva orden
        if ($ID_grupo == 2)
        {
            if ($ID_orden_entradas == 0)
            {
                $ORDEN['nodo'] = 'entradas';
                $ID_orden_entradas = db_agregar_datos('ordenes', $ORDEN);
            }
            
            $ID_esta_orden = $ID_orden_entradas;
        }
      
        // Si el grupo es de entradas horneadas creemos una nueva orden
        if ($ID_grupo == 13)
        {
            if ($ID_orden_entradas_horno == 0)
            {
                $ORDEN['prioridad'] = 'alta';
                $ORDEN['nodo'] = 'entradas_horno';
                $ID_orden_entradas_horno = db_agregar_datos('ordenes', $ORDEN);
            }
            
            $ID_esta_orden = $ID_orden_entradas_horno;
        }
        
        // Todo lo demas que no va hacia ningun nodo es autodespachado
        
        if ($ID_esta_orden == 0)
        {
            if ($ID_orden == 0)
            {
                $ORDEN['flag_elaborado'] = 1;
                $ORDEN['flag_despachado'] = 1;
                $ID_orden = db_agregar_datos('ordenes', $ORDEN);
            }
            
            $ID_esta_orden = $ID_orden;
        }
        
        if ($MODO == 0) {
    
            $BUFFER_DB_DATOS['tmpID'] = $tmpID;
            $BUFFER_DB_DATOS['ID_producto'] = $pedido['ID'];
            $BUFFER_DB_DATOS['precio_original'] = $pedido['precio'];
            $BUFFER_DB_DATOS['precio_grabado'] = $pedido['precio'];
            $BUFFER_DB_DATOS['ID_orden'] = $ID_esta_orden;
            
            $ID_pedido = db_agregar_datos('pedidos', $BUFFER_DB_DATOS);
                        
            if (isset($pedido['adicionales']) && is_array($pedido['adicionales']) && count($pedido['adicionales']) > 0 )
            {
                foreach ($pedido['adicionales'] as $adicional)
                {
                    $precio_grabado = db_obtener('adicionables','precio','ID_adicional = '.$adicional);
                    db_agregar_datos ('pedidos_adicionales',array('ID_pedido' => $ID_pedido, 'ID_adicional' => $adicional, 'precio_grabado' => $precio_grabado, 'tipo' => 'poner'));
                }
            }
            
            if ( isset($pedido['ingredientes']) && is_array($pedido['ingredientes']) && count($pedido['ingredientes']) > 0 )
            {
                foreach ($pedido['ingredientes'] as $adicional)
                {
                    $precio_grabado = db_obtener('adicionables','precio','ID_adicional = '.$adicional);
                    db_agregar_datos ('pedidos_adicionales',array('ID_pedido' => $ID_pedido, 'ID_adicional' => $adicional, 'precio_grabado' => $precio_grabado, 'tipo' => 'quitar'));
                }
            }
        } elseif ($MODO == 1) {
            
            $BUFFER_DB_DATOS['ID_orden'] = $ID_esta_orden;
            
            db_actualizar_datos('pedidos', $BUFFER_DB_DATOS, 'ID_pedido="'.$pedido['ID_pedido'].'"');
        }
    } // foreach($PEDIDOS as $tmpID => $pedido)
    
    CacheDestruir();
}


function CacheDestruir()
{
    $toDelete = new APCIterator('user', '/^RSV_SQL_/', APC_ITER_VALUE);
    apc_delete($toDelete); 
}

function CacheCrear($llave, $valor, $destructivo = false)
{
    if ($destructivo)
        CacheDestruir ();

    $llave = "RSV_SQL_" .  crc32($llave);
    apc_store($llave, $valor, 30);
}

function CacheObtener($llave)
{
    $cache = apc_fetch("RSV_SQL_" .  crc32($llave));
    return $cache;
}
?>