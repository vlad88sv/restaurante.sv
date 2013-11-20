var vista = 'cliente';

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
        var buffer = "<h1>Resultados</h1>";
        var buffer_nombres = "";
        var buffer_direcciones = "";
        
        if (typeof resultado.nombres != "undefined")
        {
            buffer_nombres = '<ul id="domicilio_nombres">';
            for (x in resultado.nombres)
            {
                buffer_nombres += '<li>' + resultado.nombres[x].cliente + '</li>';
            }
            buffer_nombres += '</ul>';
        }

        if (typeof resultado.direcciones != "undefined")
        {
            buffer_direcciones = '<ul id="domicilio_direcciones">';
            for (x in resultado.direcciones)
            {
                buffer_direcciones += '<li>' + resultado.direcciones[x].direccion + '</li>';
            }
            buffer_direcciones += '</ul>';
        }
        
        var tabla = '<table id="info_domicilio"><tr><td>'+buffer_nombres+'</td><td>'+buffer_direcciones+'</td></tr></table>';
        
        buffer = buffer + tabla;
        
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

$(function(){
    bg_color = $("#cliente_tarjeta").css('background-color');
    
    $('#cliente_telefono').keyup(domicilio__cliente_telefono_change);
    $('#buscar_telefono').click(domicilio__buscar_telefono_click)
    $(document).on('click','#domicilio_direcciones li',domicilio__procesar_direccion);
    $(document).on('click','#domicilio_nombres li',domicilio__procesar_nombres);
    $("#cambiar_vista").click(domicilio__cambiar_vista)
    $("#cliente_direccion").keyup(miniResumenOrden);
    $("#cliente_nombre").keyup(miniResumenOrden);
    $("#cliente_tarjeta").keyup(domicilio__cliente_tarjeta_keyup);
    $('[name="domicilio_metodo_pago"]').click(domicilio__mostrar_opciones_pago);
    $('[name="domicilio_documento_fiscal"]').click(domicilio__mostrar_opciones_pago);
});