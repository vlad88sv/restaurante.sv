_ordenes = {}; // Objeto donde mantenemos las ordenes en presentación
max_id = 0;
bool_mucho_tiempo = true;

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
        return interval + " días";
    }
    interval = Math.floor(seconds / 3600);
    if (interval > 1) {
        return interval + " horas";
    }
    interval = Math.floor(seconds / 60);
    if (interval > 1) {
        return interval + " minutos";
    }
    return Math.floor(seconds) + " segundos";
}

function Beep() {
    $('#beep').get(0).play();
}

function agregarPedido(grupo)
{
    var orden = $('<div class="orden" />');
    
    orden.append('<div style="height:1em;"><span class="grupo"><span class="mesa">#'+_ordenes[grupo][0].ID_mesa+ ':' + _ordenes[grupo][0].nombre_mesero + '</span></span> <span class="tiempo" /></div>');
    orden.append('<hr />');
    orden.append('<button class="despachar" style="width:100%;">DESPACHAR</button>');
    orden.append('<hr />');
    orden.attr('id','o_'+grupo);
    orden.attr('id_orden',_ordenes[grupo][0].ID_orden);
    orden.find('.tiempo').html(timeSince(new Date(_ordenes[grupo][0].fechahora_pedido_uts*1000)));
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
        
	
	var producto = _ordenes[grupo][x].nombre_producto;        
        
        if ('adicionales' in _ordenes[grupo][x] && _ordenes[grupo][x].adicionales.length > 0)
        {
            for (adicional in _ordenes[grupo][x].adicionales)
            {
                producto += ' + <span style="color:yellow">' + _ordenes[grupo][x].adicionales[adicional].nombre + '</span>';
            }
        }
	
	pedido.find('.producto').html(producto);

        orden.append(pedido);
    }
    $("#pedidos").append(orden);
}

function actualizarTiempoTranscurrido()
{
    bool_mucho_tiempo = !bool_mucho_tiempo;
}

function actualizar() {
    rsv_solicitar('orden_pendientes',{grupo:'bebidas_preparadas'},function(datos){
	
	var max_x = 0;
    
	if ( typeof datos.aux.pendientes === "undefined" )
	{
	 $('#pedidos').html('<div id="nada_pendiente" style="color:yellow;font-size:8em;text-align:center;">Nada pendiente!</div>')
	 return;
	}
	
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
	
	Beep();
    });
}

setInterval(actualizarTiempoTranscurrido,2000);
setInterval(actualizar,1000);

$(function(){
    
    $('.despachar').live('click', function(){
        
        var ID_orden = $(this).parents('.orden').attr('id_orden');
        if (!confirm('Desea despachar esta orden?'))
            return;
        
        var datos_imprimir = $(this).parents('.orden').html();
        
        rsv_solicitar('despachar_orden',{orden: ID_orden, imprimir: datos_imprimir},function(){});      
    });

});
