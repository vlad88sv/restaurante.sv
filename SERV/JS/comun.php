<?php
header('Content-type: text/javascript');
require_once('../../configuracion.php');
?> 

URI_SERVIDOR = "<?php echo URI_SERVIDOR; ?>";
URI_AUT = "<?php echo URI_AUT; ?>";
MODO_GLOBAL = "<?php echo MODO_GLOBAL; ?>";

JSOPS = [];

<?php
foreach ($JSOPS as $opcion)
{
    echo 'JSOPS.push("'.$opcion.'");'."\n";
}
?>


_ajax = {};
slam_defense = true;
ult_AUT_M = '';

function rsv_solicitar(peticion, data, funcion, cache, slam) {    
    var objetivo = {TPL: peticion};
    var llave = window.btoa(peticion + JSON.stringify(data));    

    slam = typeof slam !== 'undefined' ? true : false;
    
    if (slam_defense && slam && _ajax[llave] === true)
    {
        console.log ("Diferido: " + peticion + " :: " + _ajax[llave]);
        funcion(false,true);
        return false;
    }
    
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
            //console.log ('No hit!: ' + peticion);
        }
    }    
    
    _ajax[llave] = true;
    //console.log( "Iniciado :: " + peticion + " :: " + _ajax[llave]);
    
    $.post(URI_SERVIDOR + '/?REFERENCIA='+peticion, $.extend(objetivo,data), function(retorno){
        if(typeof(Storage)!=="undefined" && cache == true){
            localStorage.setItem(llave, JSON.stringify(retorno));
        }
        
        if ( typeof(retorno.AUT) !== "undefined" )
        {
            // DATOS; SLAM; AUT (fallo);
            $("#ajax_aut_texto").html(retorno.AUT_M);
            funcion(false,false,true);
        } else {
            funcion(retorno);
        }
        
        
    }, 'json').always(function(){
        //console.log( "Completado :: " + peticion + " :: " + _ajax[llave]);
        delete _ajax[llave];
    });
    
    return true;
}

function aut_solicitar() {
    
    var html = '';
    
    html += '<h1>AUTORIZACION REQUERIDA</h1>';
    html += '<div style="text-align:center;">';
    html += 'Una o mas acciones realizadas fueron detenidas por falta de permisos.<br /><br />';
    html += '<a class="btn" target="_blank" href="' + URI_AUT + '">Autorización</a>';
    html += '<br /><p>Una ves realizada la autorización puede cerrar esta ventana</p>.';
    html += '<a class="btn facebox_cerrar" href="#">Cerrar ventana</a>';
    html += '</div>';   
    
    $.modal(html);
}

function cuenta_obtenerVisual(_datos, _grupo, modo)
{
    // Modo = 0 : normal
    // Modo = 1 : historial
    
    var _cuenta = _datos['cuentas'][_grupo];
    var _orden = _datos['pendientes'][_grupo];
    
    var cuenta_tiene_domicilio = ( typeof _cuenta.domicilio != 'undefined' )
    
    var orden = $('<div class="orden" />');
    var total = 0.00;
    var html = '';
    var controles_fiscales = ( true ? '<button class="imp_factura btn">Factura</button><button class="imp_fiscal btn">Fiscal</button>&nbsp;' : '<button class="imp_fiscalizar btn">Fiscalizar</button>&nbsp;');
    var control_domicilio = ( cuenta_tiene_domicilio ? '<button class="imp_domicilio btn">Domicilio</button>' : '');
    var control_tiquete = ( ! cuenta_tiene_domicilio ? '<button class="imp_tiquete btn">Tiquete</button>' : '');
    var controles = controles_fiscales + control_domicilio + control_tiquete + '<button class="cerrar_cuenta btn">Cerrar</button><button class="anular_cuenta btn">Anular</button>&nbsp;<button class="descuento_p_cuenta btn">Descuento</button><button class="cupon_cuenta btn">Cupon</button>';

    if ( modo == 0 && _cuenta.info.flag_tiquetado == '1')
    {
       orden.addClass('pago_pendiente');
    }

    if (modo == 1)
    {
        controles = controles_fiscales + control_domicilio + control_tiquete +  '<button class="abrir_cuenta btn">Abrir</button><button class="anular_cuenta btn">Anular</button>';
        html += '<div class="cuenta">Cuenta: '+_cuenta.info.ID_cuenta+' | atendida por <b>'+_cuenta.info.nombre_mesero+'</b></div>';
       
        if (_cuenta.info.flag_anulado == '1')
        {
            html += '<div class="vineta" style="background-color:white;color:red;text-align:center;">¡esta cuenta fue anulada!</div>';
        }
        
    } else {
        html += '<div class="cuenta">Cuenta <b>#'+_cuenta.info.ID_cuenta+'</b> | atendida por <b>'+_cuenta.info.nombre_mesero+'</b></div>';
    }

    html += '<div class="contenedor_encabezado_orden">';
    html += '<table class="encabezado_orden">';
    html += '<tr>';
    html += '<td class="contenedor_mesa_mesero"><button class="cambio_mesa btn">'+_cuenta.info.ID_mesa+'</button></td>';
    html += '<td class="precio_precalculo"></td>';
    html += '<td class="precio"></td>';
    html += '</tr>';
    html += '</table>';
    html += '</div>';
    html += '<div class="cuenta contenedor_botones" style="text-align:center;">' + controles + '</div>';
    
    // Información de domicilio
    
    if ( cuenta_tiene_domicilio )
    {
        var domicilio = '';
        domicilio += '<div>Entregar a: <b>' + _cuenta.domicilio.nombre + '</b></div>';
        domicilio += '<div>Entregar en: <b>' + _cuenta.domicilio.direccion + '</b></div>';        
        domicilio += '<div>Notas: <b>' + _cuenta.domicilio.notas + '</b></div>';
        domicilio += '<div>Método pago: <b>' + _cuenta.domicilio.metodo_pago + '</b>. ' + (_cuenta.domicilio.metodo_pago == 'efectivo' ? 'Cambio para: <b>$' + _cuenta.domicilio.vuelto + '</b>.': '') + '</div>';
        domicilio += '<div>Facturación: <b> '+_cuenta.domicilio.documento_fiscal + '</b> elaborar por <b>' + _cuenta.domicilio.detalle_facturacion + '</b></div>';
        domicilio += '<div>Nombre fiscal: <b>' + _cuenta.domicilio.facturacion_nombre + '</b>. DUI: <b> ' + _cuenta.domicilio.facturacion__dui + '</b>. NIT: <b>' + _cuenta.domicilio.facturacion_nit + '</b>. NRC: <b>' + _cuenta.domicilio.facturacion_nrc + '</b>. Giro: <b>' + _cuenta.domicilio.facturacion_giro  + '</b>. Dirección físcal: <b>' + _cuenta.domicilio.facturacion_direccion  + '</b>.</div>';
        html += '<div class="info_domicilio">' + domicilio + '</div>';
    }
    
    
    if ( modo == 0) {
        html += '<div class="cuenta controles_seleccion">';
        html += 'SELECCIONADOS: <button class="btn_separar_cuenta btn">separar cuenta</button>&nbsp;<button class="btn_cambiar_mesa btn">cambiar mesa</button>';
        html += '</div>';
    }
    
    if (_cuenta.historial != null && _cuenta.historial.length > 0)
    {	
        for (var historia in _cuenta.historial) {
            html += '<div class="vineta" style="background-color:#FFFFA2;color:#676767;text-align:center;">';
            html += _cuenta.historial[historia].hora + ' :: ' + _cuenta.historial[historia].accion + ' :: ' + _cuenta.historial[historia].nota;
            html += '</div>';
        }
    }
    
    orden.append(html);    

    var notificaciones = $('<div class="cuenta_notificaciones" style="text-align:center;" />');
    orden.append(notificaciones);
    
    orden.attr('id','o_'+_cuenta.info.ID_cuenta);
    orden.attr('id_mesa',_cuenta.info.ID_mesa);
    orden.attr('cuenta',_cuenta.info.ID_cuenta);
    
    orden.append($('<hr />'));
    
    for (x in _orden)
    {
        var pedido = $('<div class="pedido" />');
        pedido.attr('id','p_'+_orden[x].ID_pedido);
        pedido.attr('id_pedido',_orden[x].ID_pedido);
        
        pedido.append('<div class="producto" />');
        
        var hora_entregado = '';
	
	if (_orden[x].fechahora_despachado !== '0000-00-00 00:00:00') {
            try {
                hora_entregado += '→' + Date.parse(_orden[x].fechahora_despachado).toString('HH:mm');
            } catch(error) {
                console.log(error);
            }
        }
        
        if (_orden[x].fechahora_despachado !== '0000-00-00 00:00:00') {
            try {
                hora_entregado += '→' + Date.parse(_orden[x].fechahora_despachado).toString('HH:mm');
            } catch(error) {
                console.log(error);
            }
        }
        
        var eliminado = '';
        if ( _orden[x].flag_cancelado == '1' ) {
            eliminado = ' - <span style="background-color:black;color:red;">ELIMINADO</span>';
        }
        
        var historial = '';
        if ( _orden[x].historia != null ) {
            historial = ' - <span class="historia">' + _orden[x].historia + '</span>';
        }
        
        var buffer = '';
        if ( modo == '0' ) {
            buffer += '<input class="chk_separar_pedido" type="checkbox" value="'+_orden[x].ID_pedido+'" />&nbsp;';
        }
        
        var todo_despachado = true;
        var estado_despacho = "P";
        if (_orden[x].flag_elaborado === '1') estado_despacho = 'E';
        if (_orden[x].flag_despachado === '1') estado_despacho = 'D';
        if (_orden[x].flag_cancelado === '1') estado_despacho = '-';
        
        if (_orden[x].flag_cancelado === "0" && estado_despacho !== "D") todo_despachado = false;
        
        var opcion_cancelar_pedido = '<span class="cancelar_pedido" title="Cancelar este pedido">X</span>&nbsp;';
        if (_orden[x].flag_cancelado === '1') opcion_cancelar_pedido = "-&nbsp;";
        
        
        buffer += '<span class="estado_despacho" title="P = pendiente | E = elaborado | D = despachado">' + estado_despacho + '</span>&nbsp;';
        buffer += opcion_cancelar_pedido;
        buffer += '<span style="color:yellow;" title="' + _orden[x].ID_pedido + '">' + _orden[x].nombre_producto + '</span>&nbsp;';
        buffer += '<span class="editar_pedido">$' + _orden[x].precio_grabado + "</span>";
        if (!$("#ocultar_fechas").is(':checked'))
        {
            try {
                buffer += '&nbsp;<span class="detalle_hora">[' + Date.parse(_orden[x].fechahora_pedido).toString('HH:mm') + hora_entregado  + ']</span>&nbsp;';
            } catch(error){
                console.log(error);
            }
        }
        buffer += eliminado;
        buffer += historial;

        pedido.find('.producto').html(buffer);
                
        if ('adicionales' in _orden[x] && _orden[x].adicionales.length > 0)
        {
            pedido.append('<div class="adicionales" ><ul></ul></div>');
            for (adicional in _orden[x].adicionales)
            {
                pedido.find('.adicionales ul').append('<li id_adicional="'+_orden[x].adicionales[adicional].ID_pedido_adicional+'">' + _orden[x].adicionales[adicional].nombre  + ' <span class="adicionales_precio">$' + _orden[x].adicionales[adicional].precio_grabado + '</span></li>');
                if (_orden[x].flag_cancelado === '0') {
                    total += parseFloat(_orden[x].adicionales[adicional].precio_grabado);
                }
            }
        }

        if ('remociones' in _orden[x] && _orden[x].remociones.length > 0)
        {
            pedido.append('<div class="remociones" ><ul></ul></div>');
            for (remocion in _orden[x].remociones)
            {
                pedido.find('.remociones ul').append('<li>' + _orden[x].remociones[remocion].nombre + '</li>');
            }
        }

        if (_orden[x].flag_cancelado === '0') {
            total += parseFloat(_orden[x].precio_grabado);
        }
        
	if ( ! $("#cuentas_compactas").is(':checked') )
	    orden.append(pedido);   
    }
    
    try {
        if ( _cuenta.info.flag_pagado == '1' )
        {
            notificaciones.append('<div class="vineta" style="background-color:#FFFACD;color:black;text-align:center;">cerrada/pagada ['+_cuenta.info.fechahora_pagado+']</div>');
        }
    } catch(error) {
        
    }   
    
    try {
        if ( _cuenta.domicilio.flag_en_transito == '1' )
        {
            notificaciones.append('<div class="vineta" style="background-color:white;color:blue;text-align:center;">en tránsito ['+_cuenta.domicilio.fechahora_transito+']</div>');
        }
    } catch(error) {
        
    }
    
    if ( todo_despachado )
    {
        notificaciones.append('<div class="vineta" style="background-color:#BBFFFF;color:black;text-align:center;">nada pendiente</div>');
    }

    if ( _cuenta.info.flag_nopropina == '1' )
    {
        notificaciones.append('<div class="vineta" style="background-color:pink;color:red;text-align:center;">sin propina</div>');
    }
    
    if ( _cuenta.info.flag_exento == '1' )
    {
        notificaciones.append('<div class="vineta" style="background-color:yellow;color:black;text-align:center;">sin IVA</div>');
    }   
    
    var precio_sin_iva = (total / 1.13).toFixed(2);
    var iva = (_cuenta.info.flag_exento == '0' ? (total - precio_sin_iva).toFixed(2) : 0);
    var propina = ( _cuenta.info.flag_nopropina == '0' ? ((total * 1.10) - total).toFixed(2) : 0 );
    orden.find('.precio_precalculo').html( '<span style="cursor: not-allowed;" title="Total sin IVA">$' + precio_sin_iva + '</span> + <span class="quitar_iva" style="cursor: pointer;" title="IVA\nClic para quitar IVA">$' + iva + '</span> → <span style="cursor: not-allowed;color:blue;font-weight:bold;" title="Total con IVA sin propina">$' + (parseFloat(precio_sin_iva) + parseFloat(iva)).toFixed(2) + '</span> + <span class="quitar_propina" style="cursor: pointer;color:red;font-weight:bold;" title="Propina\nClic para quitar propina">$' + propina + '</span>' );
    
    total = (parseFloat(precio_sin_iva) + parseFloat(iva) + parseFloat(propina) );
    orden.find('.precio').html( '<span title="Total con IVA y con propina">$' + total.toFixed(2) + '</span>' );
    
    return orden[0].outerHTML;
    
}

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

    if ( _datos[0].flag_exento == '1' )
        orden.find('table.totales').append('<tr><td>IVA</td><td>EXENTO</td></tr>' );
    
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

    general.append('<impuestos>' + ( _datos[0].flag_exento == '0' ? "iva" : "exento" ) + '</impuestos>');
    general.append('<directa>' + (directa ? 'si' : 'no') + '</directa>');
    general.append('<tipo>' + ((tipo == 0) ? 'factura' : 'fiscal') + '</tipo>');
    
    return xml.html();
} // crearXmlParaFacturin()

function cargarEstado() {
    if(typeof(Storage) === "undefined") return;        
    
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
    //$('body').append('<img id="ajax_cargando" src="' + URI_SERVIDOR + '/IMG/cargando.gif" style="position:fixed;top:50%;left:50%;z-index:20;display: none;" />\n');
    
    $('body').append('\
    <div id="ajax_error" style="position:fixed;top:25%;left:25%;z-index:90;display: none;text-align: center;">\
        <img src="' + URI_SERVIDOR + '/IMG/error.png" />\
        <p id="ajax_error_texto" style="color:greenyellow;background: black;font-weight:bold;font-size: 1.2em;padding:6px;"></p>\
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
   
    $(document).ajaxStart(function(){});
    $(document).ajaxStop(function(){});

    $.ajaxSetup({
        cache: false,
        timeout: 5000,
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
            minHeight: '90%',
            minWidth: '90%'
        }); 

        $(document).on('click', '.facebox_cerrar', function(event){
            event.preventDefault();
            $.modal.close();
            return false;
        });
    }
});

function luhn(b, y, t, e, s, u) {
    b = b.replace(/[^0-9]/g, '');     
    s = 0; u = y ? 1 : 2;
    for (t = ( b = b + '').length; t--;) {
        e = b[t] * (u^=3);
        s += e-(e>9?9:0);
    }
    t = 10 - (s % 10 || 10);
    return y ? b + t : !t;
}