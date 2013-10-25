_ordenes = {}; // Objeto donde mantenemos las ordenes en presentación
max_id = 0;
bool_mucho_tiempo = true;
cmp_cache = {} // objeto donde almacenamos la última actualización real

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
    $('#beep').get(0).play();
}

function agregarPedido(grupo)
{
    var orden = $('<div class="orden" />');
    
    orden.append('<div style="height:1.5em;"><span class="grupo"><span class="mesa">#'+_ordenes[grupo][0].ID_mesa+'</span>:<span class="mesero">' + _ordenes[grupo][0].nombre_mesero + '</span></span><span class="tiempo" /></div>');
    orden.append('<hr />');
    orden.append('<div class="pedidos"></div>');
    orden.attr('id','o_'+grupo);
    orden.attr('id_orden',_ordenes[grupo][0].ID_orden);
    orden.attr('id_mesa',_ordenes[grupo][0].ID_mesa);
    orden.attr('tiempo', _ordenes[grupo][0].fechahora_pedido_uts*1000);
    orden.find('.tiempo').html('*NUEVO*');
    
    var pedidos = orden.find('.pedidos');
    
    // Si lleva más de 15m esperando
    if (Math.floor((new Date() - new Date(_ordenes[grupo][0].fechahora_pedido_uts*1000)) / 1000) > 900)
    {
	orden.toggleClass('mucho_tiempo', bool_mucho_tiempo);
    }

    for (x in _ordenes[grupo])
    {
        var pedido = $('<div class="pedido" />');
        pedido.attr('id','p_'+grupo+_ordenes[grupo][x].ID_pedido);
        pedido.append('<div class="producto" />');
        
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
function actualizar() {
    nodo = $("#nodo :selected").val();
    
    rsv_solicitar('orden_pendientes',{grupo:nodo, nodo: true},function(datos){
	
	var max_x = 0;
    
	if ( typeof datos.aux.pendientes === "undefined" )
	{
	 $('#pedidos').html('<div id="nada_pendiente" style="color:red;font-size:8em;text-align:center;">Nada pendiente!</div>')
	 return;
	}
	
	if (JSON.stringify(cmp_cache) == JSON.stringify(datos.aux.pendientes)) {
        // No redendizar nada, con el beneficio de:
        // * No alterar el DOM y hacer mas facil Firedebuggear
        // * No procesar innecesariamente
        // * Facilitar el click en los botones
        // * Hacer posible mantener estado en los cheques
	return;
	}

	cmp_cache = datos.aux.pendientes;
	
	$('#pedidos').empty();
	_ordenes = {};
	 
	for(x in datos.aux.pendientes)
	{    
	    _ordenes[x] = datos.aux.pendientes[x];
	    if (_ordenes[x][0].ID_orden > max_x) {
		max_x = _ordenes[x][0].ID_orden;
	    }
	    
	    agregarPedido(x);
	}
	
	if (max_id < max_x) {
	    Beep();
	    max_id = max_x;
	}
	
    
    });
}

$(function(){
    
     $(document).on('click', 'div.orden', function(){
        
        $(this).addClass('orden_despachando');
        
        var ID_orden = $(this).attr('id_orden');
        var ID_mesa = $(this).attr('id_mesa')

        if (!confirm('Despachar mesa ' + ID_mesa + '?'))
        {
            $(this).removeClass('orden_despachando');
            return;
        }
        
        var datos_imprimir = $(this).html();
        
        rsv_solicitar('despachar_orden',{orden: ID_orden, elaborado: true},function(){});
        
        $(this).animate({width: 'hide'}, function() { $(this).remove(); } );        
    });

});

setInterval(actualizarTiempoTranscurrido,1000);
setInterval(actualizar,2000);