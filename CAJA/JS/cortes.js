fecha = Date.today();

function actualizarTotales() {

    $("#contenedor_cortes").html('');
    rsv_solicitar('cortez',{fecha: fecha.toString('yyyy-MM-dd')},function(datos){
        var buffer = '';
        buffer += '<p>Total del día: $' + datos.aux.total + '</p>';
        buffer += '<p>Total posible: $' + datos.aux.total_posible + '</p>';
        buffer += '<p>Total pendiente: $' + datos.aux.total_pendiente + '</p>';
        buffer += '<p>Total anulado: $' + datos.aux.total_anulado + '</p>';
        buffer += '<p>Total eliminado: $' + datos.aux.total_cancelado + '</p>';
        buffer += '<p>Total compras: $' + datos.aux.total_compras + '</p>';
        
        buffer += '<hr />';
        buffer += '<h1>Compras</h1>';
        buffer += '<div id="contenedor_compras">';
        buffer += '<table class="ancha estandar bordes">';
        buffer += '<tr><th>Empresa</th><th>Descripción</th><th>Precio</th></tr>';
        for(compra in datos.aux.compras)
        {
            buffer += '<tr><td>'+datos.aux.compras[compra].empresa+'</td><td>'+datos.aux.compras[compra].descripcion+'</td><td>$'+datos.aux.compras[compra].precio+'</td></tr>';
        }
        buffer += '</table>';
        buffer += '</div>';
        buffer += '</td>';
        buffer += '</tr>';
        buffer += '</table>';
        
        $("#contenedor_cortes").html(buffer);
    });
}

function actualizarCuentas() {
    $("#contenedor_cuentas").html('Analizando...');
    
    rsv_solicitar('cuenta',{modificados: 1, fecha: fecha.toString('yyyy-MM-dd')},function(datos){
        $("#contenedor_cuentas").html('');    
        
       if ( typeof datos.aux.pendientes === "undefined" )
       {
        $("#contenedor_cuentas").html('<div style="text-align:center;color:yellow;">Nada encontrado!</div>');
        return;
       }
    
       var buffer_visual = '';
       $("#pedidos").empty();
       for(var x in datos.aux.cuentas)
       {
        buffer_visual += cuenta_obtenerVisual(datos.aux,x, 0);
       }
       
       $("#contenedor_cuentas").html(buffer_visual);
    });
}

function actualizarInfo() {
    
    $("#contenedores_info").html('Cargando cortes'); 
    
    rsv_solicitar('cortez',{historial: true, fecha: fecha.toString('yyyy-MM-dd')},function(datos){
        var buffer = '<table class="estandar ancha bordes">';
        buffer += '<tr><th>Fecha</th><th>Total</th><th>Diferencia</th><th>Efectivo</th><th>POS</th><th>Compras</th><th>Caja</th><th>Estado</th></tr>';
        for(y in datos.aux.historial)
        {
            buffer += '<tr>' + '<td>'+ datos.aux.historial[y].fechatiempo + '</td>' + '<td>'+ datos.aux.historial[y].total_a_cuadrar + '</td>' + '<td>'+ datos.aux.historial[y].total_diferencia + '</td>' + '<td>'+ datos.aux.historial[y].total_efectivo + '</td>' + '<td>'+ datos.aux.historial[y].total_pos + '</td>' + '<td>'+ datos.aux.historial[y].total_compras + '</td>' + '<td>'+ datos.aux.historial[y].total_caja + '</td>' + '<td><select id="sel_estado_corte_' + datos.aux.historial[y].ID_cortes + '"><option value="pendiente">Pendiente</option><option ' + (datos.aux.historial[y].estado == 'recibido' ? 'selected="selected"' : '') + ' value="recibido">Recibido</option><option ' + (datos.aux.historial[y].estado == 'remesado' ? 'selected="selected"' : '') + ' value="remesado">Remesado</option></select><button class="guardar_estado_corte" rel="' + datos.aux.historial[y].ID_cortes + '">guardar</button></td>' + '</tr>' ;
        }
        buffer += '</table>';
        $("#contenedores_info").html(buffer);
    });
}

function sincrofecha() {
    $("#fecha").val(fecha.toString('yyyy-MM-dd'));
    
    actualizarTotales();
    actualizarCuentas();
    actualizarInfo();
}

function actualizarCortes() {
    $('#historial_cortez').click(function(){
        rsv_solicitar('cortez',{historial: true},function(datos){
            var buffer = '<table class="estandar ancha bordes">';
            buffer += '<tr><th>Fecha</th><th>Total</th><th>Diferencia</th><th>Efectivo</th><th>POS</th><th>Compras</th><th>Caja</th><th>Estado</th></tr>';
            for(y in datos.aux.historial)
            {
                buffer += '<tr>' + '<td>'+ datos.aux.historial[y].fechatiempo + '</td>' + '<td>'+ datos.aux.historial[y].total_a_cuadrar + '</td>' + '<td>'+ datos.aux.historial[y].total_diferencia + '</td>' + '<td>'+ datos.aux.historial[y].total_efectivo + '</td>' + '<td>'+ datos.aux.historial[y].total_pos + '</td>' + '<td>'+ datos.aux.historial[y].total_compras + '</td>' + '<td>'+ datos.aux.historial[y].total_caja + '</td>' + '<td>'+ datos.aux.historial[y].estado + '</td>' + '</tr>' ;
            }
            buffer += '</table>';
            
           $.modal(buffer);
        });
    });    
}

$(function(){
    $("#fecha").change(function(){
        fecha = Date.parse($("#fecha").val());
        sincrofecha();
    });
    
    $("#atras").click(function(){
        fecha = fecha.add(-1).day();
        sincrofecha();        
    });
    
    $("#adelante").click(function(){
        fecha = fecha.add(1).day();
        sincrofecha();        
    });
    
    $(document).on('click','.guardar_estado_corte',function(){
        alert($('#sel_estado_corte_' + $(this).attr('rel')).val());
    });
    
    sincrofecha();
});