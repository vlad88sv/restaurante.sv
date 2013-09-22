function rsv_solicitar(peticion, data, funcion) {
    var ret_json;
    var objetivo = {TPL: peticion};
    
    
    $.post('../SERV/', $.extend(objetivo,data), function(retorno){
        if (typeof retorno.error != 'undefined') console.log(retorno.error);
        funcion(retorno);
    }, 'json');
    return ret_json;
}

function agregar_info(texto) {
    $("#estadisticas").append('<div style="padding-top:5px;">' + texto + '</div>');
}

function estadisticas() {
    var periodo_inicio = $('#periodo_inicio').val() + ' 00:00:00';
    var periodo_final = $('#periodo_final').val() + ' 23:59:59';

    $("#estadisticas").html('<b>{cargando estadísticas}</b>');
    rsv_solicitar('estadisticas',{periodo_inicio: periodo_inicio, periodo_final: periodo_final},function(datos){
        $("#estadisticas").empty();
        if (typeof datos.aux.dsn != 'undefined') {
           var buffer = '';
   
           for (usuario in datos.aux.dsn)
           {
              buffer += "<li>" + datos.aux.dsn[usuario].usuario + " : " + datos.aux.dsn[usuario].porcentaje + "%</li>";
           }
           agregar_info('<h1>Distribución de carga de servicio entre meseros</h1>');
           agregar_info('<ul>' + buffer + '</ul>');
        } else {
           agregar_info('Sin datos de rendimiento de meseros');
        }
                
        if (typeof datos.aux.venta_por_horas != 'undefined') {
           var buffer = '';
   
           for (hora in datos.aux.venta_por_horas)
           {
              buffer += "<li>" + datos.aux.venta_por_horas[hora].hora + ":00 : $" + datos.aux.venta_por_horas[hora].subtotal + " : " + datos.aux.venta_por_horas[hora].porcentaje + "%</li>";
           }
           agregar_info('<h1>Distribución de ventas por hora</h1>');
           agregar_info('<ul>' + buffer + '</ul>');
        } else {
           agregar_info('Sin datos de rendimiento de ventas por hora');
        }
        
        if (typeof datos.aux.venta_por_dias != 'undefined') {
           var buffer = '';
   
           for (dia in datos.aux.venta_por_dias)
           {
              buffer += "<li>" + datos.aux.venta_por_dias[dia].dia + " : $" + datos.aux.venta_por_dias[dia].total + "</li>";
           }
           agregar_info('<h1>Total de ventas por día</h1>');
           agregar_info('<ul>' + buffer + '</ul>');
        } else {
           agregar_info('Sin datos de rendimiento de ventas por día');
        }
        
        if (typeof datos.aux.venta_por_mes != 'undefined') {
           var buffer = '';
   
           for (indice in datos.aux.venta_por_mes)
           {
              buffer += "<li>" + datos.aux.venta_por_mes[indice].mes + " : $" + datos.aux.venta_por_mes[indice].total + "</li>";
           }
           agregar_info('<h1>Total de ventas por mes</h1>');
           agregar_info('<ul>' + buffer + '</ul>');
        } else {
           agregar_info('Sin datos de rendimiento de ventas por mes');
        }

        if (typeof datos.aux.cuentas_por_horas != 'undefined') {
           var buffer = '';
   
           for (hora in datos.aux.cuentas_por_horas )
           {
              buffer += "<li>" + datos.aux.cuentas_por_horas[hora].hora + " - " + datos.aux.cuentas_por_horas[hora].num_cuentas + "</li>";
           }
           agregar_info('<h1>Cuentas abiertas por hora</h1>');
           agregar_info('<ul>' + buffer + '</ul>');
        } else {
           agregar_info('Sin datos de rendimiento de cuentas por hora');
        }
        
        if (typeof datos.aux.pizzas_por_dia != 'undefined') {
           var buffer = '';
   
           for (index in datos.aux.pizzas_por_dia )
           {
              buffer += "<li>" + datos.aux.pizzas_por_dia[index].dia + " - " + datos.aux.pizzas_por_dia[index].cantidad + "</li>";
           }
           agregar_info('<h1>Pizzas vendidas por día</h1>');
           agregar_info('<ul>' + buffer + '</ul>');
        } else {
           agregar_info('Sin datos de rendimiento de pizzas por día');
        }

        if (typeof datos.aux.productos_por_categoria != 'undefined') {
           var buffer = '<table>';
   
           for (producto in datos.aux.productos_por_categoria )
           {
              buffer += "<tr><td>" + datos.aux.productos_por_categoria[producto].grupo + "</td><td>" + datos.aux.productos_por_categoria[producto].nombre + "</td><td>" + datos.aux.productos_por_categoria[producto].cantidad + "</td></tr>";
           }
           buffer += '</table>';
           agregar_info('<h1>Productos más vendidos por categoría</h1>');
           agregar_info('<ul>' + buffer + '</ul>');
        } else {
           agregar_info('Sin datos de rendimiento de productos vendidos');
        }
        
        if (typeof datos.aux.uso_mesas != 'undefined') {
           var buffer = '<table>';
   
           for (indice in datos.aux.uso_mesas )
           {
              buffer += "<tr><td>Mesa #" + datos.aux.uso_mesas[indice].ID_mesa+ "</td><td>" + datos.aux.uso_mesas[indice].cantidad+ "</td></tr>";
           }
           buffer += '</table>';
           agregar_info('<h1>Mesas más utilizadas</h1>');
           agregar_info('<ul>' + buffer + '</ul>');
        } else {
           agregar_info('Sin datos de uso de mesa');
        }
        
        agregar_info('Tiempo promedio de despacho: ' + datos.aux.tps + ' minutos');
        agregar_info('Tiempo máximo de despacho: ' + datos.aux.tms + ' minutos');

    });
}

$(function(){
    $("#actualizar").click(function(){
        estadisticas();
    });
});