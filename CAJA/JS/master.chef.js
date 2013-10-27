_ordenes = {}; // Objeto donde mantenemos las ordenes en presentación
bool_mucho_tiempo = true;

function Beep() {
    
    // Beep cada 2 segundos si es necesario
    if(typeof(Storage)=="undefined") return;

    if (new Date().getTime() > (localStorage.beep || 0))
    {
        localStorage.beep = (new Date().getTime() + 5000);
        $('#beep').get(0).play();
    }
}

function timeSince(date) {

    var seconds = Math.floor((new Date() - date) / 1000);

    var interval = Math.floor(seconds / 31536000);

    if (interval > 1) {
        return interval + " a";
    }
    interval = Math.floor(seconds / 2592000);
    if (interval > 1) {
        return interval + " m";
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
    
    seconds = Math.max(seconds, 0);
    return Math.floor(seconds) + " s";
}

function cocina_agregarPedido(grupo)
{
    var orden = $('<div class="orden" />');
    
    orden.append('<div style="height:1.5em;"><span class="grupo"><span class="mesa">#'+_ordenes[grupo][0].ID_mesa+'</span>:<span class="mesero">' + _ordenes[grupo][0].nombre_mesero + '</span></span><span class="tiempo" /></div>');
    orden.append('<hr />');
    orden.append('<div class="pedidos"></div>');
    orden.attr('id','o_'+grupo);
    orden.attr('id_orden',_ordenes[grupo][0].ID_orden);
    
    // Ghost in the shell
    
    if (_ordenes[grupo][0].flag_despachado == 1)
    {
	orden.addClass('ghost');
	orden.find('.tiempo').html('Entregada hace ' + timeSince(new Date(_ordenes[grupo][0].fechahora_entregado_uts*1000)));
    } else {
	orden.find('.tiempo').html(timeSince(new Date(_ordenes[grupo][0].fechahora_pedido_uts*1000)));
    }
    
    
    var pedidos = orden.find('.pedidos');
    
    // Si lleva más de 15m esperando
    if (_ordenes[grupo][0].flag_despachado == 0 && Math.floor((new Date() - new Date(_ordenes[grupo][0].fechahora_pedido_uts*1000)) / 1000) > 900)
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
    
    $("#cocina").append(orden);
}

function cocina_actualizarTiempoTranscurrido()
{
    bool_mucho_tiempo = !bool_mucho_tiempo;
}

function cocina_actualizar() {
    rsv_solicitar('orden_pendientes',{grupo: 'todos', ghost: true},function(datos){
	
        if (datos === false) return;
        
        $("#t_pendientes").html(datos.benchmark + "μs");
        
	if ( typeof datos.aux.pendientes === "undefined" )
	{
	 $('#cocina').html('<div id="nada_pendiente" style="color:red;text-align:center;">Nada pendiente!</div>')
	 return;
	}
	
	$('#cocina').empty();
	_ordenes = {};
	 
	$.each(datos.aux.pendientes, function(index, value) {
	    _ordenes[index] = value;	    
	    cocina_agregarPedido(index);
	});	
    
    },false, true);
}

$(function(){
    cocina_actualizar();
});

setInterval(cocina_actualizarTiempoTranscurrido,2000);
setInterval(cocina_actualizar,500);