_t_id_pedido = 0; // Variable donde se almacena temporalmente el ID de pedido en edición
keys = {};
menu_visible = false;
cmp_cache = 0; // objeto donde almacenamos la última actualización real
clave = (new Date().getDate() + new Date().getDay()).toString(); // Clave dinamica

function actualizar() {

    
    rsv_solicitar('cuenta',{pendientes: 1},function(datos, slam, aut){
        
        if (slam === true) return;
        
        $("#t_cuentas").html(datos.benchmark + "ms");

       if (cmp_cache == datos.cmp_cache) {
        // No redendizar nada, con el beneficio de:
        // * No alterar el DOM y hacer mas facil Firedebuggear
        // * No procesar innecesariamente
        // * Facilitar el click en los botones
        // * Hacer posible mantener estado en los cheques
        return;
       }
       
       cmp_cache = datos.cmp_cache;
       
       if ( typeof datos.aux.pendientes === "undefined" || datos.aux.pendientes === '' )
       {
        $("#pedidos").html('<div style="text-align:center;color:yellow;">Nada encontrado!</div>');
        return;
       }
    
       $("#pedidos").empty();
       for(var x in datos.aux.cuentas)
       {
        $("#pedidos").append(cuenta_obtenerVisual(datos.aux,x, 0));
       }
    
    }, false, true);
}

function cuadrarCorte() {
    var total_a_cuadrar = parseFloat($.trim($("#total_a_cuadrar").val()));
    var total_efectivo = parseFloat($.trim($("#total_efectivo").val()));
    var total_pos = parseFloat($.trim($("#total_pos").val()));
    var total_compras = parseFloat($.trim($("#total_compras").val()));
    var total_diferencia = total_a_cuadrar - (total_efectivo + total_pos + total_compras) ;
    
    $("#total_diferencia").val(total_diferencia.toFixed(2));
    
    return total_diferencia.toFixed(2);
}

function estadisticas() {
    var fecha = $('#fecha_caja').val();
    
     rsv_solicitar('estadisticas',{obtener: ['dsn','tps','tms'], fecha: fecha},function(datos){
         if (typeof datos.aux.dsn != 'undefined') {
            var buffer = '';

            for (usuario in datos.aux.dsn)
            {
               buffer += "[" + datos.aux.dsn[usuario].usuario + " : " + datos.aux.dsn[usuario].porcentaje + "%]";
            }

            $("#dsn").html(buffer);
         } else {
             $("#dsn").html('Sin datos');
         }
         
         $("#tps").html(datos.aux.tps + "'");
         $("#tms").html(datos.aux.tms);
     });
}

setInterval(actualizar,1000);
setInterval(estadisticas,10000);

function activarAdm()
{
   $('#fecha_caja').prop('readonly', false);
   $('#historial_cortez').show();
   $('#cortes').show();
}

$(function(){
    
    actualizar();
    estadisticas();
    
    $(document).keydown(function (e) {
        keys[e.which] = true;
        
        checkKeys();
    });

    $(document).keyup(function (e) {
        delete keys[e.which];
        
        checkKeys();
    });

    function checkKeys() {
        if( typeof keys[65] != "undefined" && typeof keys[68] != "undefined" && typeof keys[77] != "undefined")
        {
            activarAdm();
        }
    }
    
    $(document).on('click','.vaciar_cache_caja',function(){
        cmp_cache = null;
    });
    
    $(document).on('click','.chk_separar_pedido',function(){
        var orden = $(this).parents('.orden');
        tmp_cheques = orden.find('.chk_separar_pedido:checked');
        
        if (tmp_cheques.length > 0) {
            orden.find('.controles_seleccion').show();
        } else {
            orden.find('.controles_seleccion').hide();    
        }
    });
    
    $(document).on('click','.btn_separar_cuenta', function(){
        var orden = $(this).parents('.orden');
        tmp_cheques = orden.find('.chk_separar_pedido:checked');
        
        if (tmp_cheques.length > 0) {
            var pedidos = [];
            
            tmp_cheques.each(function(indice, objeto) {
                pedidos.push($(objeto).val());
            });
        
            rsv_solicitar('pedidos_avanzados',{pedidos: pedidos, mesa: orden.attr('id_mesa'), SEPARAR_CUENTA: true},function(datos){
                // VOID
            });

        }
    });
    
    $(document).on('click','.btn_cambiar_mesa', function(){
        var orden = $(this).parents('.orden');
        tmp_cheques = orden.find('.chk_separar_pedido:checked');
        
        if (tmp_cheques.length > 0) {
            
            var mesa = prompt("¿Cúal es el número de la nueva mesa?");
        
            if ( !$.isNumeric(mesa) || mesa == 0 )
            {
                alert('El nuevo número de mesa es inválido.');
                return;
            }

            var mesero = prompt("¿Código del mesero?");
        
            if ( !$.isNumeric(mesero) || mesero == 0 )
            {
                alert('El código de mesero es inválido.');
                return;
            }
            
            var pedidos = [];
            
            tmp_cheques.each(function(indice, objeto) {
                pedidos.push($(objeto).val());
            });
        
            rsv_solicitar('pedidos_avanzados',{pedidos: pedidos, mesa: mesa, mesero: mesero},function(datos){
                // VOID
            });

        }
    });
    
    $("#ver_historial").click(function(){    
       var fecha = $('#fecha_caja').val();
        
        $.modal('<div style="position:absolute;top:30px;bottom:0;left:0;right:0;overflow-y:auto;"><div id="destino_historial"><h1>Historial de '+fecha+'</h1><br /></div></div>');

        rsv_solicitar('cuenta',{mesa:$("#id_mesa").val(), historial: 1, fecha: fecha},function(datos){
           
           
           if ( typeof datos.aux.cuentas === "undefined" || datos.aux.cuentas === "" )
           {
            $("#destino_historial").append('<div style="text-align:center;color:yellow;">Nada encontrado!</div>');
            return;
           }
           
           for(var x in datos.aux.cuentas)
           {
            $("#destino_historial").append(cuenta_obtenerVisual(datos.aux,x, 1));
           }

        });
    });
    
    $("#id_mesa").keyup(function(){
        if ($("#id_mesa").val() === '777') {
            $("#id_mesa").val('');
            activarAdm();
        }
    });
    

    $("#mostrar_opciones").click(function(){
        menu_visible = !menu_visible;
        $("#menu, #menu2").toggle(menu_visible);
    });


    if ( $(window).width() < 1000) {
        $("#pestana_cocina").remove();
    }

    $("#btn_rapido_cuenta_cerrar, #btn_rapido_cuenta_tiquete").click(function(){
        
        var mesa = $('#id_mesa').val();
        
        if ( mesa == "" || mesa == "0" ) {
           alert('El número de mesa no puede ser "' + mesa + '"');
           $('#id_mesa').val('').focus();
	   return;
	}
        
        // Busquemos si tenemos esa cuenta:
        var orden = $('.orden[id_mesa="' + mesa + '"]');
        
        if (orden.length == 0)
        {
           alert('El número de mesa "' + mesa + '" no tiene cuenta abierta');
           $('#id_mesa').val('').focus();
	   return;
        }
        
        if ($(this).attr('id') == 'btn_rapido_cuenta_cerrar') {
            orden.find('.cerrar_cuenta').click();
        } else if ($(this).attr('id') == 'btn_rapido_cuenta_tiquete') {
            orden.find('.imp_tiquete').click();
        }
        
        $('#id_mesa').val('').focus();
    });
    
    $(document).on('click','.abrir_cuenta', function(){
        if (!confirm('¿Realmente desea abrir nuevamente esta cuenta?'))
            return;
                       
        var orden = $(this).parents('.orden');
                
        var motivo = '';
        var intentos = 0;
        
        while (motivo.length < 3 && intentos < 3) {
            motivo = prompt('Ingrese el motivo de la re-apertura.');
            motivo = $.trim(motivo);
            intentos++;
        }
        
        if (motivo == '') {
            alert('Debe ingresar un motivo para aperturar nuevamente la cuenta');
            return;
        }
        
        rsv_solicitar('cuenta_abrir',{cuenta:orden.attr('cuenta'), motivo: motivo},function(datos){
            cmp_cache = null;
        });
    });
    
    // Descuento por porcentaje
    $('#pedidos').on('click','.descuento_p_cuenta', function(){
        var orden = $(this).parents('.orden');
        
        var motivo = '';
        var valor = 0.00;
        
        motivo = prompt('Ingrese el motivo del descuento.');
        motivo = $.trim(motivo);
        
        if (motivo == '') {
            alert('Debe ingresar un motivo para realizar descuento');
            return;
        }
        
        valor = prompt('Ingrese el porcentaje a descontar.');
        valor = $.trim(valor);
        
        if (valor == '') {
            alert('Debe ingresar un porcentaje para realizar descuento');
            return;
        }
        
        rsv_solicitar('cuenta_descuento',{cuenta: orden.attr('cuenta'), tipo: 'porcentaje', valor: valor, motivo: motivo},function(){
            cmp_cache = null;
       });
       
    });
        
    $('#pedidos').on('click','.cupon_cuenta', function(){
        var orden = $(this).parents('.orden');
        
        var cupon = '';
        
        cupon = prompt('Ingrese el código del cupon.');
        cupon = $.trim(cupon);

        if (cupon === '')
            return;
        
        $.post('http://cupon.lapizzeria.com.sv',{cupon:cupon, operacion:'buscar'}, 'json').done(function(data){
            if (data.cupon.utilizado === '1')
            {
                alert('Cupon utilizado');
                return;
            }
            
            if (confirm('El cupón es válido por '+data.cupon.valor+' ('+data.cupon.tipo+'). ¿aplicar?.'))
            {
                rsv_solicitar('cuenta_descuento',{cuenta: orden.attr('cuenta'), tipo: data.cupon.tipo, valor: data.cupon.valor, motivo: 'Cupon utilizado: ' + data.cupon.cupon},function(){
                    cmp_cache = null;
                    $.post('http://cupon.lapizzeria.com.sv',{cupon:cupon, operacion:'invalidar'});
                });
            }
        }).fail(function(){
            alert('ERROR: intente nuevamente');
        });
        
    });

    $('#pedidos').on('click','.anular_cuenta', function(){
        
        if ( JSOPS.indexOf('sin_clave') === -1 )
        {
            var prueba = prompt('Ingrese la clave dinamica');

            if ( prueba == null || prueba.trim() !== clave ) {
                alert('La clave ingresada es incorrecta!');
                Beep();
                return;
            }
        }
        
        if (!confirm('¿Realmente desea anular esta orden?'))
            return;
                       
        var orden = $(this).parents('.orden');
        
        var motivo = '';
        var intentos = 0;
        
        while (motivo.length < 3 && intentos < 3) {
            motivo = prompt('Ingrese el motivo de la anulación.');
            motivo = $.trim(motivo);
            intentos++;
        }
        
        if (motivo == '') {
            alert('Debe ingresar un motivo para la anulación');
            return;
        }
        
        rsv_solicitar('cuenta_anular',{mesa:orden.attr('id_mesa'), cuenta: orden.attr('cuenta'), motivo: motivo},function(datos){
            cmp_cache = null;
        });
    });
    
    $('#pedidos').on('click','.cerrar_cuenta', function(){
        
        
        if ( JSOPS.indexOf('sin_clave') === -1 )
        {
            var prueba = prompt('Ingrese la clave dinamica');

            if ( prueba == null || prueba.trim() !== clave ) {
                alert('La clave ingresada es incorrecta!');
                Beep();
                return;
            }
        }
        
        if (!confirm('¿Realmente desea cerrar esta cuenta?'))
            return;
   
        var orden = $(this).parents('.orden');
        var cuenta = orden.attr('cuenta');
           
        rsv_solicitar('cuenta_cerrar',{mesa: orden.attr('id_mesa'), cuenta: orden.attr('cuenta')},function(datos){
            cmp_cache = null;
        });
         
         if ($("#habilitar_facturin").is(":checked")) {
            rsv_solicitar('cuenta',{ cuenta: cuenta, facturacion: '1'},function(datos){
                for(x in datos.aux.pendientes) {
                    var xml = crearXmlParaFacturin(datos.aux.pendientes[x], 0, true, true);
                    $.post('http://localhost:40001', {xml:xml}, function(data){alert(data);}, 'text');
                }
           });
         }
         
         
        
    });

    $(document).on('click','.imp_tiquete', function(){        
        var orden = $(this).parents('.orden');
        rsv_solicitar('impresiones',{ imprimir: 'tiquete', cuenta: orden.attr('cuenta'), nota: 'Impresion de tiquetes', estacion: 'tiquetes'}, function(){
            cmp_cache = null;
        });
    });
    
    $(document).on('click','.imp_orden', function(){        
        var orden = $(this).parents('.orden');
        rsv_solicitar('impresiones',{ imprimir: 'orden_de_trabajo', cuenta: orden.attr('cuenta')}, function(){});
    });

    $(document).on('click','.imp_domicilio', function(){        
        var orden = $(this).parents('.orden');
        rsv_solicitar('domicilio',{ imprimir: orden.attr('cuenta')}, function(){});
    });

    $(document).on('contextmenu','.imp_fiscal, .imp_factura', function(event){
        event.preventDefault();
        return false;
    });
    
    $(document).on('mousedown','.imp_factura', function(event){        
        event.preventDefault();
        var orden = $(this).parents('.orden');
        
        rsv_solicitar('cuenta',{ cuenta: orden.attr('cuenta'), facturacion: '1'},function(datos){
            for(x in datos.aux.pendientes)
            {
                var xml = crearXmlParaFacturin(datos.aux.pendientes[x], 0, (event.which == 1), (event.which == 1));
                
                $.post('http://localhost:40001', {xml:xml}, function(data){alert(data)}, 'text');
            }
       });
       return false;
    });
    
    $(document).on('mousedown','.imp_fiscal', function(event){        
        event.preventDefault();
        var orden = $(this).parents('.orden');
        
        rsv_solicitar('cuenta',{ cuenta: orden.attr('cuenta'), facturacion: '1'},function(datos){
            for(var x in datos.aux.pendientes)
            {
                var xml = crearXmlParaFacturin(datos.aux.pendientes[x], 1, (event.which == 1), false);
                $.post('http://localhost:40001', {xml:xml}, function(data){alert(data);}, 'text');
            }
       });
       return false;
    });


    // Evento de fiscalizacion de domicilio
    $(document).on('mousedown','.imp_fiscalizar', function(event){        
        event.preventDefault();
        
        rsv_solicitar('cuenta',{ cuenta: orden.attr('cuenta'), facturacion: '1'},function(datos){
            for(var x in datos.aux.pendientes)
            {
                var xml = crearXmlParaFacturin(datos.aux.pendientes[x], 1, (event.which == 1), false);
                $.post('http://localhost:40001', {xml:xml}, function(data){alert(data);}, 'text');
            }
       });
       return false;
    });


    $(document).on('click','.quitar_propina', function(){
        if (!confirm('¿Realmente desea quitarle los sueños y esperanzas a los empleados?'))
            return;
        
        var motivo = '';
        var intentos = 0;
        
        while (motivo.length < 3 && intentos < 3) {
            motivo = prompt('Ingrese el motivo para quitar propina.');
            motivo = $.trim(motivo);
            intentos++;
        }
        
        if (motivo == '') {
            alert('Debe ingresar un motivo motivo para quitar propina.');
            return;
        }

        var orden = $(this).parents('.orden');
        rsv_solicitar('cuenta_modificar',{cuenta: orden.attr('cuenta'), campo: 'flag_nopropina', valor: '1', motivo: motivo},function(datos){
            cmp_cache = null;
        });
    });
    

    $(document).on('click','.quitar_iva', function(){
        if (!confirm("¿Hacer esta cuenta exenta de I.V.A.?"))
            return;
        
        var motivo = '';
        var intentos = 0;
        
        while (motivo.length < 3 && intentos < 3) {
            motivo = prompt('Ingrese el motivo para quitar I.V.A.');
            motivo = $.trim(motivo);
            intentos++;
        }
        
        if (motivo == '') {
            alert('Debe ingresar un motivo motivo para quitar I.V.A.');
            return;
        }

        var orden = $(this).parents('.orden');
        rsv_solicitar('cuenta_modificar',{cuenta: orden.attr('cuenta'), campo: 'flag_exento', valor: '1', motivo: motivo},function(datos){
            cmp_cache = null;
        });
    });

    
    $('#pedidos').on('click','.editar_pedido', function(){
        var pedido = $(this).parents('.pedido');
        _t_id_pedido = pedido.attr('id_pedido');
        
        var buffer = '<div class="contenedor_edicion_pedido" rel="'+_t_id_pedido+'">';
        buffer += '<h1>Edición de pedido</h1><p>ID de pedido: '+_t_id_pedido+'</p>';
        buffer += '<h1>Cambio de precio</h1><p>Nuevo precio: <input type="text" style="width:75px;" value="0.00" id="pedido_valor_nuevo_precio" /> Razón: <input type="text" style="width:450px;font-size:0.9em;" value="" id="pedido_valor_nuevo_precio_razon" /><button id="pedido_cambiar_precio">Cambiar</button></p>';
        buffer += '</div>';
        $.modal(buffer);
    });    


    $(document).on('click','.adicionales_precio', function(){
        var precio = prompt('Nuevo precio', '0.00');
        
        var motivo = '';
        var intentos = 0;
        
        while (motivo.length < 3 && intentos < 3) {
            motivo = prompt('Ingrese el motivo del cambio de precio de este adicional.');
            motivo = $.trim(motivo);
            intentos++;
        }
        
        if (motivo == '') {
            alert('Debe ingresar un motivo para el cambio de precio de este adicional');
            return;
        }
        
        var id_adicional = $(this).parents('li').attr('id_adicional');
        
        rsv_solicitar('adicional_modificar',{pedido_adicional: id_adicional, campo: 'precio_grabado', valor: precio, nota: motivo },function(datos){
            cmp_cache = null;
        });
        
    });    

    
    $('#pedidos').on('click','.cancelar_pedido', function(){
        var orden = $(this).parents('.orden');
        
        if (!confirm('¿Realmente desea quitar este producto de la mesa #'+orden.attr('id_mesa')+'?'))
            return;
        
        var motivo = '';
        var intentos = 0;
        
        while (motivo.length < 3 && intentos < 3) {
            motivo = prompt('Ingrese el motivo de la eliminación.');
            motivo = $.trim(motivo);
            intentos++;
        }
        
        if (motivo == '') {
            alert('Debe ingresar un motivo para la eliminación');
            return;
        }
        
        var id_pedido = $(this).parents('.pedido').attr('id_pedido');
        
        rsv_solicitar('pedido_modificar',{pedido: id_pedido, campo: 'flag_cancelado', valor: '1', nota: motivo },function(datos){
            cmp_cache = null;
        });
    });    

    $('#inventario').click(function(){
        
        rsv_solicitar('inventario',{},function(datos){
            var buffer = '';
            buffer += '<table class="estandar ancha bordes amplia">';
            buffer += '<tr><th>Ingrediente</th><th>Existencia</th></tr>';
            for (x in datos.aux) {
                var existencia_actual = parseFloat(datos.aux[x].existencia_actual);
                var divisor = parseFloat(datos.aux[x].divisor).toFixed(2);
                buffer += '<tr><td>' + datos.aux[x].nombre + '</td><td>' + existencia_actual + datos.aux[x].unidad + ' / ' + (existencia_actual / divisor)  + datos.aux[x].unidad2 + ' </td></tr>';
            }
            buffer += '</table>';
            $.modal(buffer);
        });
        
        
    });    
        
    $('#pedidos').on('click', '.cambio_mesa', function() {
        if (!confirm("¿Desea mover los pedidos de esta cuenta a otra?\nNota: si la mesa ya existe entonces se combinarán los pedidos"))
            return;
        
        var mesa = prompt("¿Cúal es el número de la nueva mesa?");
        
        if ( !$.isNumeric(mesa) || mesa == 0 )
        {
            alert('El nuevo número de mesa es inválido.');
            return;
        }
        
        var orden = $(this).parents('.orden');
        
        rsv_solicitar('cambio_mesa',{cuenta:orden.attr('cuenta'),  mesa_nueva:mesa},function(datos){
            
        });
        
    });
    
    $("#compras").click(function() {
        rsv_solicitar('producto_ingredientes_y_adicionales',{modo: 'inventario'}, function(datos){
            var buffer = '<h1>Compras</h1>';
            buffer += '<form id="datos_compra">';
            buffer += '<table class="ancha centrada">';
            buffer += '<tr><th>Comprado a</th><th>Descripción</th><th>Precio</th><th>Pagado via</th></tr>';
            buffer += '<tr><td><input type="text" name="empresa" value="" /></td><td><input type="text" name="descripcion" value="" /></td><td><input type="text" name="precio" value="0.00" /></td><td><select name="via"><option value="caja">Caja</option><option value="cheque">Cheque</option></select></td><td><button id="guardar_compra">Guardar</button></td></tr>';            
            buffer += '</table>';
            buffer += '</form>';
            
            $.modal(buffer);
        });
    });
    
    $(document).on('submit','#datos_compra', function(event){
        event.preventDefault();
        return false;
    });
        
    $(document).on('click','#guardar_compra', function(){
        if ($("#empresa").val() === '' || $("#descripcion").val() === '' || $("#precio").val() === '')
        {
            alert('Verifique que los siguientes campos esten llenos: Empresa, Descripcion y Precio. Tip: puede poner precio a 0.00');
            return;
        }
        
        $("#guardar_compra").attr('disabled','disabled');
        
        rsv_solicitar('inventario',{ingreso: true, compra: $("#datos_compra").serialize()},function(datos){
            alert('Compra y/o ingreso de inventario realizado con exito.');
            $.modal.close();
        });
    });
    
    $('#ver_total').click(function(){
        
        var prueba = prompt('Ingrese la clave dinamica');
        
        if ( prueba == null || prueba.trim() !== clave ) {
            alert('La clave ingresada es incorrecta!');
            Beep();
            return;
        }
           
        rsv_solicitar('cortez',{fecha: $('#fecha_caja').val()},function(datos){
            var buffer = '';
            buffer += '<p>Total del día: $' + datos.aux.total + ' - <span style="color:#666;">solo cuentas cerradas</span></p>';
            buffer += '<p>Total posible: $' + datos.aux.total_posible + ' - <span style="color:#666;">cuentas cerradas + abiertas</span></p>';
            buffer += '<p>Total pendiente: $' + datos.aux.total_pendiente + ' - <span style="color:#666;">solo cuentas abiertas</span></p>';
            buffer += '<p>Total anulado: $' + datos.aux.total_anulado + ' - <span style="color:#666;">solo cuentas anuladas</span></p>';
            buffer += '<p>Total eliminado: $' + datos.aux.total_cancelado + ' - <span style="color:#666;">solo pedidos eliminados</span></p>';
            buffer += '<p>Total compras: $' + datos.aux.total_compras + ' - <span style="color:#666;">solo dinero gastado de caja en compras</span></p>';
            buffer += '<p style="color:yellow;">Total a cuadrar: $' + datos.aux.total_cuadrar + ' - <span style="color:#666;">solo dinero desde el último corte Z</span></p>';
            buffer += '<p style="color:yellow;">Total compras a cuadrar: $' + datos.aux.total_compras_cuadrar + ' - <span style="color:#666;">solo compras desde el último corte Z</span></p>';
            
            buffer += '<hr /><h1>Corte Z</h1>';
            buffer += '<table class="ancha">';
            buffer += '<tr>';
            buffer += '<td>';
                
            if (parseFloat(datos.aux.total_pendiente) > 0.00) {
                buffer += '<p style="color:red;">Error: <span style="color:#666;">no puede hacer Corte Z si hay cuentas abiertas</span></p>';
            } else {
                buffer += '<form id="frm_cortez">';
                buffer += '<table>';
                buffer += '<tr><td>Total a cuadrar:</td><td><input id="total_a_cuadrar" name="total_a_cuadrar" type="text" readonly="readonly" value="'+datos.aux.total_cuadrar+'" /></td></tr>';
                buffer += '<tr><td>Total efectivo:</td><td><input id="total_efectivo" name="total_efectivo" type="text" value="0" /></td></tr>';
                buffer += '<tr><td>Total POS:</td><td><input id="total_pos" name="total_pos" type="text" value="0" /></td></tr>';
                buffer += '<tr><td>Total compras:</td><td><input id="total_compras" name="total_compras" readonly="readonly" type="text" value="'+datos.aux.total_compras_cuadrar+'" /></td></tr>';
                buffer += '<tr><td>Diferencia:</td><td><input id="total_diferencia" name="total_diferencia" readonly="readonly" type="text" value="" /></td></tr>';
                buffer += '<tr><td>En caja:</td><td><input id="total_caja" name="total_caja" type="text" value="0.00" /></td></tr>';
                buffer += '</table>';
                buffer += '</form>';
                buffer += '<button id="cortar">Cortar</button>';
            }
            
            buffer += '</td>';
            buffer += '<td style="vertical-align:top;">';
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
            
            $.modal(buffer);
        });
    });

    $(document).on('change','#total_efectivo',function(){
        cuadrarCorte();
    });
    
    $(document).on('change','#total_pos',function(){
        cuadrarCorte();
    });
    
    $(document).on('click',"#cortar", function(){
        if ( ! $.isNumeric($("#total_efectivo").val()) || ! $.isNumeric($("#total_pos").val()) )
        {
            alert('Hay un número mal escrito o faltante, el corte no puede realizarse.');
            return;
        }
        
        rsv_solicitar('cortez',{ cortar: true, datos: $("#frm_cortez").serialize() },function(datos){
            var ID_corte = parseFloat(datos.aux.ID_corte);
            if (ID_corte > 0)
            {
                alert('Corte Z realizado, ID de corte: ' + ID_corte + "\nPresione ENTER para imprimir.");
                rsv_solicitar('cortez',{imprimir: ID_corte}, function(){});
                
                var corte = $('<div id="imp_corte"></div>');
                corte.append('<div style="text-align:center;">Corte #'+ID_corte+'</div>');
                corte.append('<div>'+$("#frm_cortez").html()+'</div>');
                corte.append('<h1>Compras</h1>');
                corte.append('<div>'+$("#contenedor_compras").html()+'</div>');
                $.modal.close();
            } else {
                alert('El corte no fue exitoso!, ¿falta de filo? ');
            }
        }); 
    });
    
    $(document).on('click','#pedido_cambiar_precio', function(){
        var id_pedido = $(this).parents('.contenedor_edicion_pedido').attr('rel');
        
        if ($("#pedido_valor_nuevo_precio_razon").val() == "" ) {
            alert("Debe ingresar una razón para hacer el cambio de precio.\nPlantilla: XXX autorizó por YYY\nEj.: Franklin autorizó por descontento del cliente");
            return;
        }
        
        if (!confirm('¿Realmente desea del precio del pedido #'+id_pedido+' a '+ $('#pedido_valor_nuevo_precio').val() +'?'))
            return;
        
        rsv_solicitar('pedido_modificar',{pedido: id_pedido, campo: 'precio_grabado', valor:  $('#pedido_valor_nuevo_precio').val(), nota: $('#pedido_valor_nuevo_precio_razon').val()},function(datos){
            alert('Precio modificado!');
            $.modal.close();
        });        
    });


    $('#historial_cortez').click(function(){
        rsv_solicitar('cortez',{historial: true},function(datos){
            var buffer = '<table class="estandar ancha bordes resalte">';
            buffer += '<tr><th>Fecha</th><th>Total</th><th>Diferencia 1<th>Diferencia 2</th><th>Efectivo</th><th>POS</th><th>Compras</th><th>Caja</th><th>Estado</th></tr>';
            for(y in datos.aux.historial)
            {
                buffer += '<tr ' + (datos.aux.historial[y].sospechoso == '1' ? 'style="background-color:red"' : '') +' >' + '<td>'+ datos.aux.historial[y].fechatiempo + '</td>' + '<td>'+ datos.aux.historial[y].total_a_cuadrar + '</td>'  + '<td>'+ datos.aux.historial[y].total_diferencia2 + '</td>' + '<td>'+ datos.aux.historial[y].total_diferencia + '</td>' + '<td>'+ datos.aux.historial[y].total_efectivo + '</td>' + '<td>'+ datos.aux.historial[y].total_pos + '</td>' + '<td>'+ datos.aux.historial[y].total_compras + '</td>' + '<td>'+ datos.aux.historial[y].total_caja + '</td>' + '<td>'+ datos.aux.historial[y].estado + '</td>' + '</tr>' ;
            }
            buffer += '</table>';
            
           $.modal(buffer);
        });
    });
    
    $('#cortes').click(function(){
        window.location = '/CAJA?TPL=cortes';
    });
    
    $(document).on('mouseover', '.imp_factura', function(event) {
        $(this).qtip({
            overwrite: true,
            content: '[CLIC_IZQ] para impresión rápida de factura "Clientes varios" con Consumo/Propina.<br />[CLIC_DER] para enviar detalles a Facturin Plus.',
            show: {
                solo: true,
                event: event.type,
                ready: true 
            }
        }, event);
    });

    $(document).on('mouseover', '.imp_fiscal', function(event) {
        $(this).qtip({
            overwrite: true,
            content: '[CLIC_IZQ] para crear credito fiscal en Facturin Plus con consumo/propina.<br />[CLIC_DER] para crear credito fiscal en Facturin Plus con detalle de consumo.',
            show: {
                solo: true,
                event: event.type,
                ready: true 
            }
        }, event);
    });
    
});