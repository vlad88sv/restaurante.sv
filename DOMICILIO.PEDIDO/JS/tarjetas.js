function actualizarTarjetas()
{
    $("#tarjetas").html('<ul></ul>');
    
    rsv_solicitar('domicilio', {tarjetas:true}, function(){
        for(x in datos.aux.tarjetas) {
            $("#tarjetas ul").append('<li>' + datos.aux.tarjetas[x].tarjeta + ' - Exp: '+ datos.aux.tarjetas[x].expiracion + ' - Total: ' + datos.aux.tarjetas[x].total + '</li>');
        }
    });
}

window.setInterval(actualizarTarjetas, 1000);