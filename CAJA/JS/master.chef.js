bool_mucho_tiempo = true;
cmp_cache_master_chef = {}; // objeto donde almacenamos la última actualización real

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

function cocina_agregarPedido(_orden)
{
    var orden = $('<div class="orden" />');
    
    orden.append('<div style="height:1.5em;"><span class="grupo"><span class="mesa">#'+_orden[0].ID_mesa+'</span>:<span class="mesero">' + _orden[0].nombre_mesero + '</span></span><span class="tiempo" /></div>');
    orden.append('<hr />');
    orden.append('<div class="pedidos"></div>');
    
    // Ghost in the shell
    
    if (_orden[0].flag_despachado == 1)
    {
	orden.addClass('ghost');
	orden.find('.tiempo').html('Hace ' + timeSince(new Date(_orden[0].fechahora_despachado_uts*1000)));
    } else {
	orden.find('.tiempo').html(timeSince(new Date(_orden[0].fechahora_pedido_uts*1000)));
    }
    
    
    var pedidos = orden.find('.pedidos');
    
    // Si lleva más de 15m esperando
    if (_orden[0].flag_despachado == 0 && Math.floor((new Date() - new Date(_orden[0].fechahora_pedido_uts*1000)) / 1000) > 900)
    {
	orden.toggleClass('mucho_tiempo', bool_mucho_tiempo);
    }
    

    for (x in _orden)
    {
        var pedido = $('<div class="pedido" />');
        pedido.attr('id','cocp_'+_orden[x].ID_pedido);
        pedido.append('<div class="producto" />');
        
        pedido.find('.producto').html(_orden[x].nombre_producto);
        
        if ('adicionales' in _orden[x] && _orden[x].adicionales.length > 0)
        {
	    pedido.append('<p style="color:lightskyblue;">Adicionar</p>');
            pedido.append('<div class="adicionales" ><ul></ul></div>');
            for (adicional in _orden[x].adicionales)
            {
                pedido.find('.adicionales ul').append('<li>' + _orden[x].adicionales[adicional].nombre + '</li>');
            }
        }

        if ('ingredientes' in _orden[x] && _orden[x].ingredientes.length > 0)
        {
	    pedido.append('<p style="color:lightcoral;">Quitar</p>');
            pedido.append('<div class="ingredientes" ><ul></ul></div>');
            for (adicional in _orden[x].ingredientes)
            {
                pedido.find('.ingredientes ul').append('<li>' + _orden[x].ingredientes[adicional].nombre + '</li>');
            }
        }

        pedidos.append(pedido);
    }
    orden.append(pedidos);
    
    return orden[0].outerHTML;
}

function cocina_actualizarTiempoTranscurrido()
{
    bool_mucho_tiempo = !bool_mucho_tiempo;
}

function cocina_actualizar() {
    rsv_solicitar('orden_pendientes',{grupo: 'todos', ghost: true},function(datos, slam){
	
        if (slam === true) return;
        
        $("#t_pendientes").html(datos.benchmark + "ms");
        
        if (cmp_cache_master_chef == JSON.stringify(datos.aux.pendientes)) {
        // No redendizar nada, con el beneficio de:
        // * No alterar el DOM y hacer mas facil Firedebuggear
        // * No procesar innecesariamente
        // * Facilitar el click en los botones
        // * Hacer posible mantener estado en los cheques
        return;
        }
       
        cmp_cache_master_chef = JSON.stringify(datos.aux.pendientes);
        
	if ( typeof datos.aux.pendientes === "undefined"  || datos.aux.pendientes === '' )
	{
	 $('#cocina').html('<div id="nada_pendiente" style="color:red;text-align:center;">Nada pendiente!</div>');
	 return;
	}
        
	
	$('#cocina').empty();
        var buffer_visual = '';
	for(x in datos.aux.pendientes)
        {
	    buffer_visual += cocina_agregarPedido(datos.aux.pendientes[x]);
	};	
        $('#cocina').html(buffer_visual);
    
    },false, true);
}

$(function(){
    cocina_actualizar();
});

setInterval(cocina_actualizarTiempoTranscurrido,2000);
setInterval(cocina_actualizar,1000);