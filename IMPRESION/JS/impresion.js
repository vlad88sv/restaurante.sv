function comandas() {
    estacion = $("#estacion :selected").val();
    rsv_solicitar('comanda',{ver: estacion},function(datos){
        
        if ( typeof datos.aux.comanda === "undefined" )
        {
            return;
        }         

	rsv_solicitar('comanda',{impreso: datos.aux.comanda.ID_comanda},function(){} );
        
	var impresion = $('<div style="border-bottom:1px solid black;page-break-after:always"/>').html(datos.aux.comanda.data);
        impresion.jqprint();    
        impresion = null;
        
        $("#ajaxi").prepend('<p>Impresión. :: ' + new Date() + '</p>');
        
    });
}

setInterval(comandas,500);
