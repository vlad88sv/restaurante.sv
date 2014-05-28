_ordenes = {}; // Objeto donde mantenemos las ordenes en presentación
max_id = 0;
bool_mucho_tiempo = true;
cmp_cache = {} // objeto donde almacenamos la última actualización real
anti_flood = 0;

function timeSince(date) {

    var seconds = Math.floor((new Date() - date) / 1000);

    var interval = Math.floor(seconds / 31536000);

    if (interval > 1) {
        return interval + " años";
    }
    interval = Math.floor(seconds / 2592000);
    if (interval > 1) {
        return interval + " meses";
    }
    
    interval = Math.floor(seconds / 86400);
    if (interval > 1) {
        return interval + " d";
    }
    interval = Math.floor(seconds / 3600);
    if (interval > 1) {
        return interval + " h";
    }
    interval = Math.floor(seconds / 60);
    if (interval > 1) {
        return interval + " m";
    }
    return Math.floor(seconds) + " s";
}

function Beep() {
    $('#beep').get(0).pause();
    $('#beep').get(0).currenttime = 0;
    $('#beep').get(0).play();
}

function despacho_agregarOrden(grupo)
{
    var orden = $('<div class="orden" />');
    
    orden.append('<div style="height:1.5em;"><span class="grupo"><span class="mesa">#'+_ordenes[grupo][0].ID_mesa+'</span>:<span class="mesero">' + _ordenes[grupo][0].nombre_mesero + '</span></span><span class="tiempo" /></div>');
    orden.append('<hr />');
    orden.append('<div class="pedidos"></div>');
    orden.attr('id','o_'+grupo);
    orden.attr('id_orden',_ordenes[grupo][0].ID_orden);
    orden.attr('id_mesa',_ordenes[grupo][0].ID_mesa);
    orden.attr('tiempo', (_ordenes[grupo][0].fechahora_pedido_uts*1000));
    orden.find('.tiempo').html('');
    
    var pedidos = orden.find('.pedidos');
    
    // Si lleva más de 15m esperando
    if (Math.floor((new Date() - new Date(_ordenes[grupo][0].fechahora_pedido_uts*1000)) / 1000) > 900)
    {
	orden.toggleClass('mucho_tiempo', bool_mucho_tiempo);
    }

    for (x in _ordenes[grupo])
    {
        var pedido = $('<div class="pedido" />');
        pedido.attr('pedido',_ordenes[grupo][x].ID_pedido);
        pedido.append('<div class="producto" />');
        
	
	if (_ordenes[grupo][x].flag_elaborado == '1') {
	    orden.addClass('orden_tiene_productos_elaborados');
	}
	
        pedido.find('.producto').html(_ordenes[grupo][x].nombre_producto);
        
        if ('adicionales' in _ordenes[grupo][x] && _ordenes[grupo][x].adicionales.length > 0)
        {
	    pedido.append('<p style="color:lightskyblue;">Adicionar</p>');
            pedido.append('<div class="adicionales" ><ul></ul></div>');
            for (adicional in _ordenes[grupo][x].adicionales)
            {
                pedido.find('.adicionales ul').append('<li>' + _ordenes[grupo][x].adicionales[adicional].nombre + '</li>');
            }
        }

        if ('ingredientes' in _ordenes[grupo][x] && _ordenes[grupo][x].ingredientes.length > 0)
        {
	    pedido.append('<p style="color:lightcoral;">Quitar</p>');
            pedido.append('<div class="ingredientes" ><ul></ul></div>');
            for (adicional in _ordenes[grupo][x].ingredientes)
            {
                pedido.find('.ingredientes ul').append('<li>' + _ordenes[grupo][x].ingredientes[adicional].nombre + '</li>');
            }
        }

        pedidos.append(pedido);
    }
    orden.append(pedidos);
    
    $("#pedidos").append(orden);
}

function actualizarTiempoTranscurrido()
{	
    $('.orden').each(function(index) {
	var tiempo = timeSince(new Date(parseInt($(this).attr('tiempo'))));
	$(this).find('.tiempo').html(tiempo);
    });
}

function actualizarOrdenesPendientes() {
    rsv_solicitar('orden_pendientes',{grupo:'todos'},function(datos, slam){
	
        if (slam === true) return;
        
	var max_x = 0;
    
	if ( typeof datos.aux.pendientes === "undefined" || datos.aux.pendientes == "")
	{
	 $('#pedidos').html('<div id="nada_pendiente" style="color:red;font-size:8em;text-align:center;">Nada pendiente!</div>')
	 return;
	}
	
	if (cmp_cache == JSON.stringify(datos.aux.pendientes)) {
        // No redendizar nada, con el beneficio de:
        // * No alterar el DOM y hacer mas facil Firedebuggear
        // * No procesar innecesariamente
        // * Facilitar el click en los botones
        // * Hacer posible mantener estado en los cheques
	return;
	}

	cmp_cache = JSON.stringify(datos.aux.pendientes);
	
	$('#pedidos').empty();
	_ordenes = {};
	 
	for(x in datos.aux.pendientes)
	{    
	    _ordenes[x] = datos.aux.pendientes[x];
	    if (_ordenes[x][0].ID_pedido > max_x) {
		max_x = _ordenes[x][0].ID_pedido;
	    }
	    
	    despacho_agregarOrden(x);
	}
	
	if (max_id < max_x) {
	    Beep();
	    max_id = max_x;
	}
	
    }, false, true);
}

function forzarFoco() {
    $("#mesa").focus();
}

$(function(){
    
    $("#f_mesa").submit(function (event) {
	event.preventDefault();
        
        if (anti_flood == 2)
        {
            anti_flood++;
            rsv_solicitar('impresiones',{imprimir: 'datos', datos: '<div style="text-align:center;height:5cm;">SE HA BLOQUEADO LA IMPRESORA POR SEGURIDAD<br />Verificar que la tecla "Intro" del teclado NO este presionada o atascada.<br />El impresor estará bloqueado hasta que ingresen un número de cuenta existente.</div>' , estacion: 'comandas'},function(datos){});
            return;
        }
        
	if ($('#mesa').val() == "" || $('#mesa').val() == "0") {
            if ( anti_flood < 2 )
                rsv_solicitar('impresiones',{imprimir: 'datos', datos: '<div style="text-align:center;height:5cm;">**NO INGRESO # MESA**</div>' , estacion: 'comandas'},function(datos){});
            
            $('#mesa').val('');
            anti_flood++;
            return;
	}
	
	rsv_solicitar('cuenta',{ mesa: $('#mesa').val(), pendientes: '1', facturacion: '1' },function(datos){
	    
	    if ( anti_flood < 2 && typeof datos.aux['pendientes'] == "undefined" )
	    {
		rsv_solicitar('impresiones',{imprimir: 'datos', datos: '<div style="text-align:center;height:5cm;">**MESA NO EXISTE**<br />Ingresó: '+$('#mesa').val()+'</div>' , estacion: 'comandas'},function(datos){});
                anti_flood++;
	    }
            
            if ( typeof datos.aux['pendientes'] != "undefined" )
            {
	    
                for(x in datos.aux.pendientes)
                {
                    var cuenta = datos.aux.pendientes[x][0].ID_cuenta;
                    rsv_solicitar('impresiones',{ imprimir: 'tiquete', cuenta: cuenta, estacion: 'comandas', nota: 'impresión de tiquete - desde despacho'}, function(){});
                }

                anti_flood = 0;
            }
            
	    $('#mesa').val('');
       });
    });
    
    $(document).on('click', 'div.orden', function(){
     
        if ( JSOPS.indexOf('despacho_aun_sin_elaborar') === -1 && ! $(this).hasClass('orden_tiene_productos_elaborados'))
        {
            alert ('No se pueden despachar productos no elaborados.');
            return;
        }
        
        $(this).addClass('orden_despachando');
        
        var ID_orden = $(this).attr('id_orden');
        var ID_mesa = $(this).attr('id_mesa')

        if (!confirm('Despachar ' + ID_mesa + '?'))
        {
            $(this).removeClass('orden_despachando');
            return;
        }
        
        var datos_imprimir = $(this).html();
        
        var pedidos = [];
        $(this).find('.pedido').each(function(){
            pedidos.push($(this).attr('pedido'));
        });
                
        rsv_solicitar('despachar_pedidos',{pedidos: pedidos, imprimir: datos_imprimir},function(){});
        
        $(this).animate({width: 'hide'}, function() { $(this).remove(); } );        
    });
});

setInterval(actualizarTiempoTranscurrido,2000);
setInterval(actualizarOrdenesPendientes,500);
setInterval(forzarFoco,500);