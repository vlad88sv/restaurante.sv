_ordenes = {}; // Objeto donde mantenemos las ordenes en presentación
_productos = {};

function rsv_solicitar(peticion, data, funcion, cache) {
    var objetivo = {TPL: peticion};
    var llave = window.btoa(peticion + JSON.stringify(data));
    
    cache = typeof cache !== 'undefined' ? cache : false;
    
    //cache = false;
    if(typeof(Storage)!=="undefined" && cache == true){

        retorno = localStorage.getItem(llave);
        if (retorno !== null){
            //console.log ('Cache hit! ' + peticion);
            var objeto = JSON.parse(retorno);
            //console.log(objeto);
            funcion(objeto);
            return true;
        } else {
            console.log ('No hit!: ' + peticion);
        }
    }    
    
    $.post('/SERV/', $.extend(objetivo,data), function(retorno){
        if(typeof(Storage)!=="undefined" && cache == true){
            localStorage.setItem(llave, JSON.stringify(retorno));
        }
        funcion(retorno);
    }, 'json');
    
    return true;
}

function cuenta_obtenerVisual(objetivo, grupo, modo)
{
    // Modo = 0 : normal
    // Modo = 1 : historial
    
    var botones = '';
    botones += '<span class="cancelar_pedido" title="Cancelar este pedido">X</span>&nbsp;';
    var orden = $('<div class="orden" />');
    var total = 0.00;
    var html = '';
    var controles_fiscales = '<button class="imp_factura btn">Factura</button><button class="imp_fiscal btn">Fiscal</button>';
    var controles = controles_fiscales + '<button class="imp_tiquete btn">Tiquete</button><button class="cerrar_cuenta btn">Cerrar</button><button class="anular_cuenta btn">Anular</button>';

    if ( modo == 0 && _ordenes[grupo][0].flag_tiquetado == '1')
    {
       orden.addClass('pago_pendiente');
    }

    if (modo == 1)
    {
        controles = controles_fiscales + '<button class="imp_tiquete btn">Tiquete</button><button class="abrir_cuenta btn">Abrir</button><button class="anular_cuenta btn">Anular</button>';
        html += '<div class="cuenta">Cerrado: ' + _ordenes[grupo][0].fechahora_pagado+ ' | Cuenta: '+_ordenes[grupo][0].cuenta+'</div>';
       
        if (_ordenes[grupo][0].flag_anulado == '1')
        {
            html += '<div class="cuenta" style="background-color:white;color:red;text-align:center;font-size:14px;font-weight:bold;">¡esta cuenta fue anulada!</div>';
        }
        
    }

    html += '<div class="contenedor_encabezado_orden">';
    html += '<table class="encabezado_orden">';
    html += '<tr>';
    html += '<td class="contenedor_mesa_mesero"><button class="cambio_mesa btn">'+_ordenes[grupo][0].ID_mesa+'</button> → <strong>'+_ordenes[grupo][0].nombre_mesero+'</strong></td>';
    html += '<td class="precio_precalculo"></td>';
    html += '<td class="precio"></td>';
    html += '</tr>';
    html += '</table>';
    html += '</div>';
    html += '<div class="cuenta contenedor_botones" style="text-align:center;">' + controles + '</div>';
    html += '<hr />';
    if (_ordenes[grupo][0].flag_nopropina == '1')
    {
        html += '<div class="cuenta" style="background-color:pink;color:red;text-align:center;font-size:14px;font-weight:bold;">sin propina</div>';
    }
    
    if (_ordenes[grupo][0].flag_exento == '1')
    {
        html += '<div class="cuenta" style="background-color:yellow;color:black;text-align:center;font-size:14px;font-weight:bold;">sin IVA</div>';
    }
    
    
    if ( modo == 0) {
        html += '<div class="cuenta controles_seleccion">';
        html += 'SELECCIONADOS: <button class="btn_separar_cuenta btn">separar cuenta</button>&nbsp;<button class="btn_cambiar_mesa btn">cambiar mesa</button>';
        html += '</div>';
    }

    if (_ordenes[grupo][0].historial != null && _ordenes[grupo][0].historial.length > 0)
    {	
        for (historia in _ordenes[grupo][0].historial) {
            html += '<div class="cuenta" style="background-color:#FFFFA2;color:#676767;text-align:center;">';
            html += _ordenes[grupo][0].historial[historia].hora + ' :: ' + _ordenes[grupo][0].historial[historia].accion + ' :: ' + _ordenes[grupo][0].historial[historia].nota;
            html += '</div>';
        }
	html += '<hr />';
    }
    
    orden.append(html);
    
    orden.attr('id','o_'+grupo);
    orden.attr('id_mesa',_ordenes[grupo][0].ID_mesa);
    orden.attr('cuenta',_ordenes[grupo][0].cuenta);
    
    for (x in _ordenes[grupo])
    {
        var pedido = $('<div class="pedido" />');
        pedido.attr('id','p_'+grupo+_ordenes[grupo][x].ID_pedido);
        pedido.attr('id_pedido',_ordenes[grupo][x].ID_pedido);
        
        pedido.append('<div class="producto" />');
        
        var hora_entregado = '';
	/*
	if (_ordenes[grupo][x].fechahora_elaborado !== '0000-00-00 00:00:00') {
            hora_entregado += '→' + Date.parse(_ordenes[grupo][x].fechahora_elaborado).toString('HH:mm');
        }
        */
        if (_ordenes[grupo][x].fechahora_entregado !== '0000-00-00 00:00:00') {
            hora_entregado += '→' + Date.parse(_ordenes[grupo][x].fechahora_entregado).toString('HH:mm');
        }
        
        var eliminado = '';
        if ( _ordenes[grupo][x].flag_cancelado == '1' ) {
            eliminado = ' - <span style="background-color:black;color:red;">ELIMINADO</span>';
        }
        
        var historial = '';
        if ( _ordenes[grupo][x].historia != null ) {
            historial = ' - <span class="historia">' + _ordenes[grupo][x].historia + '</span>';
        }
        
        var buffer = '';
        if ( modo == '0' ) {
            buffer += '<input class="chk_separar_pedido" type="checkbox" value="'+_ordenes[grupo][x].ID_pedido+'" />&nbsp;';
        }
        buffer += '<span class="estado_despacho" title="P = pendiente | D = despachado">' + (_ordenes[grupo][x].flag_despachado === '0' ? 'P' : 'D') + '</span>&nbsp;';
        buffer += botones;
        buffer += '<span style="color:yellow;" title="' + _ordenes[grupo][x].ID_orden + ':' + _ordenes[grupo][x].ID_pedido + '">' + _ordenes[grupo][x].nombre_producto + '</span>&nbsp;';
        buffer += '<span class="editar_pedido">$' + _ordenes[grupo][x].precio_grabado + "</span>";
        if (!$("#ocultar_fechas").is(':checked'))
            buffer += '&nbsp;<span class="detalle_hora">[' + Date.parse(_ordenes[grupo][x].fechahora_pedido).toString('HH:mm') + hora_entregado  + ']</span>&nbsp;';
        buffer += eliminado;
        buffer += historial;

        pedido.find('.producto').html(buffer);
                
        if ('adicionales' in _ordenes[grupo][x] && _ordenes[grupo][x].adicionales.length > 0)
        {
            pedido.append('<div class="adicionales" ><ul></ul></div>');
            for (adicional in _ordenes[grupo][x].adicionales)
            {
                pedido.find('.adicionales ul').append('<li>' + _ordenes[grupo][x].adicionales[adicional].nombre  + ' $' + _ordenes[grupo][x].adicionales[adicional].precio_grabado + '</li>');
                if (_ordenes[grupo][x].flag_cancelado === '0') {
                    total += parseFloat(_ordenes[grupo][x].adicionales[adicional].precio_grabado);
                }
            }
        }

        if ('remociones' in _ordenes[grupo][x] && _ordenes[grupo][x].remociones.length > 0)
        {
            pedido.append('<div class="remociones" ><ul></ul></div>');
            for (remocion in _ordenes[grupo][x].remociones)
            {
                pedido.find('.remociones ul').append('<li>' + _ordenes[grupo][x].remociones[remocion].nombre + '</li>');
            }
        }

        if (_ordenes[grupo][x].flag_cancelado === '0') {
            total += parseFloat(_ordenes[grupo][x].precio_grabado);
        }
        
	if ( ! $("#cuentas_compactas").is(':checked') )
	    orden.append(pedido);   
    }
    
    var precio_sin_iva = (total / 1.13).toFixed(2);
    var iva = (_ordenes[grupo][0].flag_exento == '0' ? (total - precio_sin_iva).toFixed(2) : 0);
    var propina = ( _ordenes[grupo][0].flag_nopropina == '0' ? ((total * 1.10) - total).toFixed(2) : 0 );
    orden.find('.precio_precalculo').html( '<span style="cursor: not-allowed;" title="Total sin IVA">$' + precio_sin_iva + '</span> + <span class="quitar_iva" style="cursor: pointer;" title="IVA\nClic para quitar IVA">$' + iva + '</span> → <span style="cursor: not-allowed;color:blue;font-weight:bold;" title="Total con IVA sin propina">$' + (parseFloat(precio_sin_iva) + parseFloat(iva)).toFixed(2) + '</span> + <span class="quitar_propina" style="cursor: pointer;color:red;font-weight:bold;" title="Propina\nClic para quitar propina">$' + propina + '</span>' );
    
    total = (parseFloat(precio_sin_iva) + parseFloat(iva) + parseFloat(propina) );
    orden.find('.precio').html( '<span title="Total con IVA y con propina">$' + total.toFixed(2) + '</span>' );

    objetivo.append(orden);
    
} // cuenta_obtenerVisual()

function crearTiquete(_datos)
{
    var orden = $('<div class="orden" />');
    orden.append('<p style="font-weight:bold;text-align:center;">7G, S.A. de C.V.</p>');
    orden.append('<p style="text-align:center;">Plaza Los Castaños,<br />Av. Masferrer Nte.<br />San Salvador, San Salvador</p>');
    orden.append('<p style="text-align:center;">Tel. Oficinas administrativas:<br />(503) 2243-6017</p>');
    
    var total = 0.00;
    orden.append('<br /><br /><div style="height:1.5em;text-align:center;"><span class="grupo" style="height:1.5em;text-align:center;font-size: 16px; font-weight:bold;">Mesa #'+_datos[0].ID_mesa+'</span></div><br /><br />');
    
    for (x in _datos)
    {
        var pedido = $('<div class="pedido" style="padding:0px;margin:0px;"  />');
        
        pedido.append('<div class="producto" style="padding:0px;margin:0px;" />');
        
        pedido.find('.producto').html( _datos[x].nombre_producto.substring(0,23) + ' <div style="z-index:99;float:right;">$' + parseFloat(_datos[x].precio_grabado).toFixed(2) + '</div>' );
                
        if ('adicionales' in _datos[x] && _datos[x].adicionales.length > 0)
        {
            pedido.append('<div class="adicionales" ><ul style="padding:2px;"></ul></div>');
            for (adicional in _datos[x].adicionales)
            {
                pedido.find('.adicionales ul').append('<li>' + _datos[x].adicionales[adicional].nombre.substring(0,13)  + ' <div style="float:right;z-index:99;">$' + parseFloat(_datos[x].adicionales[adicional].precio_grabado).toFixed(2) + '</div>' + '</li>');
		total += parseFloat(_datos[x].adicionales[adicional].precio_grabado);
            }
        }

        total += parseFloat(_datos[x].precio_grabado);
        orden.append(pedido);   
    }

    var date 	= new Date();
    var date    = date.getUTCFullYear() + '-' + ('00' + (date.getUTCMonth()+1)).slice(-2) + '-' + 
            ('00' + date.getUTCDate()).slice(-2) + ' ' + 
            ('00' + date.getUTCHours()).slice(-2) + ':' + 
            ('00' + date.getUTCMinutes()).slice(-2) + ':' + 
            ('00' + date.getUTCSeconds()).slice(-2);
            
    var precio_sin_iva = (total / 1.13).toFixed(2);
    var iva = (_datos[0].flag_exento == '0' ? (total - precio_sin_iva).toFixed(2) : 0);
    var propina = ( _datos[0].flag_nopropina == '0' ? ((total * 1.10) - total).toFixed(2) : 0 );
    
    total = (parseFloat(precio_sin_iva) + parseFloat(iva) + parseFloat(propina) );
    
    orden.append('<br />');
    orden.append('<table style="width:100%;" class="totales"></table>');
    orden.find('table.totales').append('<tr><td>SubTotal:</td><td>' + '$' + (parseFloat(precio_sin_iva) + parseFloat(iva)).toFixed(2) + '</td></tr>' );
    
    if ( _datos[0].flag_nopropina == '0' )
        orden.find('table.totales').append('<tr><td>Propina (10%):</td><td>' + '$' + parseFloat(propina).toFixed(2) + '</td></tr>' );

    orden.find('table.totales').append('<tr><td>Total:</td><td>' + '$' + total.toFixed(2) + '</td></tr>' );
    orden.append('<br /><br /><br /><br /><p style="text-align:center;">La pizzería - Plaza Los Castaños<br />¡Gracias por su compra!<br /><br />' + date + '</p>');
    
    return orden.html();
} // crearTiquete()

function crearXmlParaFacturin(_datos, tipo, simple, directa)
{
    var xml = $('<root><trabajo><general></general><productos></productos></trabajo></root>');
    var general = xml.find('general');
    var productos = xml.find('productos');
    
    var total = 0;
    
    for (x in _datos)
    {        
	var totalProducto = ( _datos[x].precio_grabado );
	
        if ('adicionales' in _datos[x] && _datos[x].adicionales.length > 0)
        {
	    for (adicional in _datos[x].adicionales)
            {
		totalProducto = parseFloat(totalProducto) + parseFloat(_datos[x].adicionales[adicional].precio_grabado);
	    }
        }
	total = parseFloat(total) + parseFloat(totalProducto);
	
	if (!simple) {
	    var producto = $('<producto cantidad="1" nosujeta="0" precio="' + parseFloat(totalProducto).toFixed(2) + '">'+_datos[x].nombre_producto.substring(0,23)+'</producto>');
	    productos.append(producto);
	}   
    }

    if (simple) {
	productos.append('<producto cantidad="1" nosujeta="0" precio="' + parseFloat(total).toFixed(2) + '">Consumo</producto>');
    }
    var propina = ( _datos[0].flag_nopropina == '0' ? ((parseFloat(total) * 1.10) - parseFloat(total) ) : 0 );
    productos.append('<producto cantidad="1" nosujeta="1" precio="' + parseFloat(propina).toFixed(2) + '">Propina</producto>');

    general.append('<impuestos>'+( _datos[0].flag_exento == '0' ? "iva" : "exento" )+'</impuestos>');
    
    general.append('<directa>' + (directa ? 'si' : 'no') + '</directa>');
    
    general.append('<tipo>' + ((tipo == 0) ? 'factura' : 'fiscal') + '</tipo>');
    
    return xml.html();
} // crearXmlParaFacturin()

function cargarEstado() {
    if(typeof(Storage) == "undefined") return;        
    
    $(".auto_guardar[id!='']").each(function(){
        var resultado = localStorage.getItem("CE_" + this.id);
        
        if (resultado)
        {
            switch (this.nodeName + "-" + this.type)
            {
                case "INPUT-checkbox":
                    $(this).prop('checked', (resultado == '1'));
                    break;
                case "SELECT-select-one":
                    $(this).val(resultado);
                    break;
            }
        }
        //console.log(this.id + " :: " + resultado);
    });
}

$(document).ready(function(){
    $('body').append('<img id="ajax_cargando" src="/SERV/IMG/cargando.gif" style="position:fixed;top:50%;left:50%;z-index:20;display: none;" />\n');
    $('body').append('\
    <div id="ajax_error" style="position:fixed;top:25%;left:25%;z-index:90;display: none;text-align: center;">\
        <img src="/SERV/IMG/error.png" />\
        <p id="ajax_error_texto" style="color:greenyellow;background: black;font-weight:bold;font-size: 18px;padding:6px;"></p>\
    </div>\
    ');
    
    cargarEstado();
});

$(function(){
    
    $(".auto_guardar").change(function(){
        switch (this.nodeName + "-" + this.type)
        {
            case "INPUT-checkbox":
                localStorage.setItem("CE_" + this.id, ($(this).is(':checked') ? '1' : '0'));
                break;
            case "SELECT-select-one":
                localStorage.setItem("CE_" + this.id, $(this).val());
                break;
        }
        
        //console.log("Ha cambiado: " + this.nodeName + "-" + this.type);
    });
   
    $.expr[':'].icontains = function (n, i, m) {
        return jQuery(n).text().toUpperCase().indexOf(m[3].toUpperCase()) >= 0;
    };
   
    $(document).ajaxStart(function(){$("#ajax_cargando").show();});
    $(document).ajaxStop(function(){$("#ajax_cargando").hide();});

    $.ajaxSetup({
        cache: false,
        timeout: 3000,
        complete: function (jqXHR, textStatus) {
            if (textStatus == "success") {
                $("#ajax_error").hide();
            }
        },
        error: function(jqXHR, exception) {
            $("#ajax_error").show();
            var textoError = '';
            if (jqXHR.status === 0) {
                textoError = 'No hay conexión.\nVerificar red.';
            } else if (jqXHR.status == 404) {
                textoError = 'Página no encontrada [404]';
            } else if (jqXHR.status == 500) {
                textoError = 'Error interno de servidor [500].';
            } else if (exception === 'timeout') {
                textoError = 'Error: su conexión esta muy lenta.';
            } else if (exception === 'abort') {
                textoError = 'Error: petición AJAX abortada.';
            } else {
                textoError = 'Error desconocido.\nError: ' + jqXHR.responseText;
            }

            $("#ajax_error_texto").html(textoError);
        }
    });

    if ( typeof $.modal != 'undefined' )
    {
        $.extend($.modal.defaults, {
            autoResize: true,
            minHeight: '95%',
            minWidth: '95%'
        }); 

        $(document).on('click', '.facebox_cerrar', function(){
            $.modal.close();
        });
    }
});