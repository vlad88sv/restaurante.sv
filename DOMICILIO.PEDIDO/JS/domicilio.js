var vista = 'cliente';
var vista_estado_pedidos = false;

// Esta función se encarga únicamente de informar si el telefono ingresado
// tiene compras registradas.
function domicilio__buscar_telefono(telefono, funcion){
    rsv_solicitar('domicilio',{buscar_telefono: telefono}, function(datos){
        if (typeof datos.aux.resultado == 'undefined' || datos.aux.rseultado == '' || datos.aux.resultado == '0' || datos.aux.resultado == 'no')
            funcion(false);
        else
            funcion(true); 
   }); 
}

function domicilio__mostrar_registros(telefono, funcion){
    telefono = telefono.replace(/[^0-9\\.]+/g, '');
    rsv_solicitar('domicilio',{info: telefono}, function(datos){
        if (typeof datos.aux.resultado == 'undefined' || datos.aux.rseultado == '' || datos.aux.resultado == '0' || datos.aux.resultado == 'no')
            funcion(false);
        else
            funcion(datos.aux.resultado); 
   });
}

function domicilio__cliente_telefono_change(event){
    miniResumenOrden();
    
    if ($('#cliente_telefono').val() == '')
    {
        $("#buscar_telefono").css('background','none');
        return;
    }
    
    domicilio__buscar_telefono($('#cliente_telefono').val(), function (resultado){
        
        if (resultado) {
            $("#buscar_telefono").css('background-color','pink').removeProp('disabled');
        } else {
            $("#buscar_telefono").css('background','none').prop('disabled','disabled');
        }

    });
}

function domicilio__buscar_telefono_click(){
    domicilio__mostrar_registros($('#cliente_telefono').val(), function (resultado){
        var buffer = "";
        var buffer_nombres = "";
        var buffer_direcciones = "";
        var buffer_pedidos = "";
        
        
        if (typeof resultado.nombres != "undefined")
        {
            buffer_nombres = '<div class="domicilio_div_resultado" id="domicilio_nombres">';
            buffer_nombres += '<ul>';
            for (x in resultado.nombres)
            {
                buffer_nombres += '<li>' + resultado.nombres[x].nombre + '</li>';
            }
            buffer_nombres += '</ul>';
            buffer_nombres += '</div>';
        }

        if (typeof resultado.direcciones != "undefined")
        {
            buffer_direcciones = '<div class="domicilio_div_resultado" id="domicilio_direcciones" style="display:none;">';
            buffer_direcciones += '<ul>';
            for (x in resultado.direcciones)
            {
                buffer_direcciones += '<li>' + resultado.direcciones[x].direccion + '</li>';
            }
            buffer_direcciones += '</ul>';
            buffer_direcciones += '</div>';
        }

        if (typeof resultado.direcciones != "undefined")
        {
            buffer_pedidos = '<div class="domicilio_div_resultado" id="domicilio_pedidos" style="display:none;">';
            buffer_pedidos += '<h2>Últimos pedidos realizado</h2>';            
            buffer_pedidos += '</div>';
        }

        buffer += "<h1>Resultados</h1>";
        buffer += '<div id="resultados_panel">';
        buffer += '<input id="resultado_nombres" class="cambio_resultado" rel="domicilio_nombres" type="radio" name="tipo_resultado" value="nombres" checked="checked" /><label for="resultado_nombres">Nombres</label>&nbsp;';
        buffer += '<input id="resultado_direcciones" class="cambio_resultado" rel="domicilio_direcciones"  type="radio" name="tipo_resultado" value="direcciones" /><label for="resultado_direcciones">Direcciones</label>&nbsp;';
        buffer += '<input id="resultado_pedidos" class="cambio_resultado" rel="domicilio_pedidos"  type="radio" name="tipo_resultado" value="pedidos" /><label for="resultado_pedidos">Pedidos</label>&nbsp;';
        buffer += '</div>';
        
        buffer += buffer_nombres;
        buffer += buffer_direcciones;
        buffer += buffer_pedidos;
        
        $.modal(buffer);
    });
}

function domicilio__procesar_nombres()
{
    $("#cliente_nombre").val($(this).html());
    miniResumenOrden();
}

function domicilio__procesar_direccion()
{
    $("#cliente_direccion").val($(this).html());
    miniResumenOrden();
}

function domicilio__cambiar_vista()
{
    if (vista === 'cliente')
    {
        $('#cambiar_vista').prop('src','IMG/clientes.png');
        $('#vista_pizza').show();
        $('#vista_cliente').hide();
        vista = 'pizza';
    } else {
        $('#cambiar_vista').prop('src','IMG/pizza.png');
        $('#vista_pizza').hide();
        $('#vista_cliente').show();
        vista = 'cliente';
    }
}

function domicilio__mostrar_opciones_pago(opcion)
{
    if (this.id == 'domicilio_metodo_pago_tarjeta')
    {
        $("#metodo_pago_efectivo").hide();
        $("#metodo_pago_tarjeta").show();
    }
    
    if (this.id == 'domicilio_metodo_pago_efectivo')
    {
        $("#metodo_pago_efectivo").show();
        $("#metodo_pago_tarjeta").hide();        
    }
    
    miniResumenOrden();
}

function domicilio__cliente_tarjeta_keyup()
{
    if (luhn($("#cliente_tarjeta").val()))
        $("#cliente_tarjeta").css('background-color', bg_color);
    else
        $("#cliente_tarjeta").css('background-color', 'pink');
    
}

function domicilio__estado_pedidos_click()
{
    vista_estado_pedidos = ! vista_estado_pedidos;
    
    $("#vista_pedidos").toggle(!vista_estado_pedidos);
    $("#cambiar_vista").toggle(!vista_estado_pedidos);
    $("#vista_estado_pedidos").toggle(vista_estado_pedidos);
    
}

function domicilio__actualizar_estado_pedidos()
{
    if (! vista_estado_pedidos ) return;
    
    if (typeof actualizarOrdenesPendientes === 'function')
        actualizarOrdenesPendientes();
    
    
}

function domicilio__cambio_resultado_click()
{
    $('.domicilio_div_resultado').hide();
    $('#' + $(this).attr('rel')).show();
}

$(function(){
    bg_color = $("#cliente_tarjeta").css('background-color');
    
    $('#cliente_telefono').keyup(domicilio__cliente_telefono_change);
    $('#buscar_telefono').click(domicilio__buscar_telefono_click)
    $(document).on('click','.cambio_resultado',domicilio__cambio_resultado_click);
    $(document).on('click','#domicilio_direcciones li',domicilio__procesar_direccion);
    $(document).on('click','#domicilio_nombres li',domicilio__procesar_nombres);
    $("#cambiar_vista").click(domicilio__cambiar_vista)
    $("#estado_pedidos").click(domicilio__estado_pedidos_click)
    $("#cliente_direccion").keyup(miniResumenOrden);
    $("#cliente_nombre").keyup(miniResumenOrden);
    $("#cliente_tarjeta").keyup(domicilio__cliente_tarjeta_keyup);
    $('[name="domicilio_metodo_pago"]').click(domicilio__mostrar_opciones_pago);
    $('[name="domicilio_documento_fiscal"]').click(domicilio__mostrar_opciones_pago);
    
    
    window.setInterval(domicilio__actualizar_estado_pedidos, 1000);
});

$(document).ready(function(){
    $("#fechahora_activacion").datetimepicker({dateFormat: "yy-mm-dd", timeFormat: "HH:mm:00",stepMinute: 5,hourMin: 9, hourMax: 22});
});