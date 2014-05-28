// Caches
_productos = {};
_adicionales = {};
_meseros = [];

// Volatiles
_orden = [];
_b_orden = [];
ID_mesero_busqueda = '';

function MostrarRejillaProductos(datos)
{
    _productos = {};
    
    var indice = 1;
    
    $("#scroller td").empty();
    
    for (x in datos.aux)
    {
        if (datos.aux[x].descontinuado == 0)
        {
            
            _productos[datos.aux[x].ID_producto] = datos.aux[x];
            
            $("#resultado_" + datos.aux[x].ID_grupo).append('<div tabindex="'+indice+'" producto="'+datos.aux[x].ID_producto+'" nombre="' + datos.aux[x].nombre + '" precio="' + datos.aux[x].precio + '" ' + (datos.aux[x].disponible == 0 ? 'style="text-decoration:line-through"' : '') +' class="agregar_producto"><div class="nombre">' + datos.aux[x].nombre + '</div>&nbsp;<div class="precio">$' + parseFloat(datos.aux[x].precio).toFixed(2)  + '&nbsp;&nbsp;<button ' + (datos.aux[x].complementar == '1' ? 'disabled="disabled"' : '') + ' class="flecha">' + (datos.aux[x].complementar == '1' ? 'X' : '+') + '</button></div></div>');
            
            indice++;
        }
    }
    
    
}

function reiniciarInterfaz() {
    _orden = [];
    _b_orden = [];
    ID_mesero_busqueda = '';
    miniResumenOrden();
    mostrar_grupo_productos('');
}

function personalizar_producto_ingredientes_y_adicionales(str_producto)
{
    rsv_solicitar('producto_ingredientes_y_adicionales',{producto: str_producto}, function(datos){
        var buffer = '';      
        buffer = '<table class="contenedor_adicionales ancha delgada estandar zebra">';
        
        buffer += '<tbody>';
        for (x in datos.aux.adicionables)
        {
            if (datos.aux.adicionables[x].disponible == 1) { 
                buffer += '<tr rel="'+datos.aux.adicionables[x].afinidad+'">';
                buffer += '<td style="text-align:center;"><input title="Agregar ( x1 )" type="checkbox" class="agregar_adicionable ppia_adicional" grupo="G_'+datos.aux.adicionables[x].ID_adicional+'" value="' + datos.aux.adicionables[x].ID_adicional + '" /></td>';
                buffer += '<td style="text-align:center;"><input title="Agregar doble ( x2 )" type="checkbox" grupo="G_'+datos.aux.adicionables[x].ID_adicional+'" class="agregar_doble_adicionable ppia_adicional" value="' + datos.aux.adicionables[x].ID_adicional + '" /></td>';
                buffer += '<td style="text-align:center;">$' + datos.aux.adicionables[x].precio + '</td>';
                buffer += '<td>' + datos.aux.adicionables[x].nombre + '</td>';
                buffer += '<td style="text-align:center;"><input title="quitar" grupo="G_'+datos.aux.adicionables[x].ID_adicional+'" type="checkbox" class="quitar_adicionable ppia_adicional" value="' + datos.aux.adicionables[x].ID_adicional + '" /></td>';
                buffer += '</tr>';
            }
        }
        buffer += '</tbody>';
        
        buffer += '<thead>';
        buffer += '<tr><th style="width:60px;">Añadir</th><th style="width:60px;">Doble</th><th style="width:80px;">Precio</th><th>Descripción</th><th style="width:40px;">Quitar</th></tr>';
        buffer += '</thead>';
        buffer += '</table>';
        
        $("#cpep_adicionables").html(buffer);
    }, true);
}

function mostrar_producto_ingredientes_y_adicionales(str_producto)
{
    rsv_solicitar('producto_ingredientes_y_adicionales',{producto: str_producto}, function(datos){
        var buffer = '';
        
        buffer += '<ul>';
        for (x in datos.aux.ingredientes)
        {
            buffer += '<li>' + datos.aux.ingredientes[x].nombre + '</li>';
        }
        buffer += '</ul>';
        
        $("#cpep_ignredientes").html(buffer);
        buffer = '<ul>';
        for (x in datos.aux.adicionables)
        {
            buffer += '<li>' + datos.aux.adicionables[x].nombre + '</li>';
        }
        buffer += '</ul>';
        
        $("#cpep_adicionables").html(buffer);
    }, true);
}

function mostrar_grupo_productos(ID_grupo)
{
    rsv_solicitar('producto_buscar', {grupo: ID_grupo, agrupar: 1}, MostrarRejillaProductos, true);
}

function obtener_lista_meseros()
{
    rsv_solicitar('extra_meseros', {}, function(datos){
        for (x in datos.aux)
        {
            _meseros[datos.aux[x].ID_usuarios] = datos.aux[x];
        }        
    }, true);
}

function cantidadParaProductoEnPedido(str_producto, str_detalle, str_precio)
{
    _b_orden = {timestamp: Math.floor(+new Date() / 1000), ID: str_producto, precio: str_precio, detalle: str_detalle, adicionales: [], ingredientes: []};
    
    var buffer = '';   
    buffer += '<div style="text-align:center;" class="botones_grandes">';
    buffer += '<button class="agregar_por_cantidad" rel="2">x2</button><br />';
    buffer += '<button class="agregar_por_cantidad" rel="3">x3</button><br />';
    buffer += '<button class="agregar_por_cantidad" rel="4">x4</button><br />';
    buffer += '<button class="agregar_por_cantidad" rel="5">x5</button><br />';
    buffer += '<button class="agregar_por_cantidad" rel="6">x6</button><br />';
    buffer += '<button class="agregar_por_cantidad" rel="7">x7</button><br />';
    buffer += '<button class="agregar_por_cantidad" rel="8">x8</button><br />';
    buffer += '<button class="agregar_por_cantidad" rel="9">x9</button><br />';
    buffer += '<button class="agregar_por_cantidad" rel="10">x10</button><br />';
    buffer += '</div>';
    
    $.modal(buffer, {opacity: 0, focus: false} );

    
}
 
function intentarProductoEnPedido(str_producto, str_detalle, str_precio)
{
    _b_orden = {timestamp: Math.floor(+new Date() / 1000), ID: str_producto, precio: str_precio, detalle: str_detalle, adicionales: [], ingredientes: []};
    
    var buffer = '';
        
    buffer += '<div style="clear:both;height:45px;text-align:center;border-bottom: 4px solid black;margin-bottom:4px;" class="botones_grandes">';
    buffer += '<div style="float:left;"><button id="agregar_producto_aceptar" class="key" key="65">[Ctrl+Alt+a] Aceptar</button> x <input id="agregar_producto_cantidad" style="width:1em" type="text" value="1" /></div>';
    buffer += '<span style="font-size:1.3em;font-weight:bold;margin:0;padding:0;">' + str_detalle + '</span>';
    buffer += '<button style="float:right;" class="facebox_cerrar key" key="67">[Ctrl+Alt+c] Cerrar</button>';
    buffer += '</div>';
    
    buffer += '';    
    buffer += '<div style="border-bottom: 4px solid black;margin-bottom:4px;height:45px;font-size:0.9em;">Búscar: <input type="text" style="width:100px;" id="busqueda_adicionales" value="" /> - ';
    buffer += '<button class="filtro_adicionales" rel="">Todos</button> ';
    buffer += '<button class="filtro_adicionales" rel="1">Especiales</button> ';
    buffer += '<button class="filtro_adicionales" rel="2">Salsas</button> ';
    buffer += '<button class="filtro_adicionales" rel="3">Topping</button> ';
    buffer += '<button class="filtro_adicionales" rel="4">Ingredientes</button> '
    buffer += '<button class="filtro_adicionales" rel="5">Quesos</button>';
    buffer += '<button class="filtro_adicionales" rel="6">Sabores</button>';
    buffer += '</div>';
    buffer += '<div style="bottom:0;top: 160px;left:0;right:0;overflow-y: auto;padding: 0 5px;position: absolute;">';
    buffer += '<div id="cpep_adicionables"></div>';
    buffer += '</div>';
    
    $.modal(buffer, {opacity: 0, focus: false} );
    
    $("#busqueda_adicionales").focus()
    
    personalizar_producto_ingredientes_y_adicionales(str_producto);
    
}

function convertirProductoEnPedido(buffer_de_orden, cantidad)
{
    cantidad = cantidad || 1;
    for (var i=0; i < cantidad; i++) {
        _orden.push(buffer_de_orden);
    }
    miniResumenOrden();
    $("#buscar_producto").focus();
}

function miniResumenOrden()
{
    var ordenador = {};
    var buffer = '';
    
    if (_orden.length == 0)
    {
        $('#info_principal').html(buffer);
        return;
    }
    
    for (x in _orden)
    {
        if (_orden[x].ID in ordenador)
        {
            ordenador[_orden[x].ID].contador++;
        } else {
            ordenador[_orden[x].ID] = {};
            ordenador[_orden[x].ID].producto = _orden[x].detalle;
            ordenador[_orden[x].ID].contador = 1;
        }
    }
    
    buffer += '<ul>';
    for (x in ordenador)
    {
        buffer += '<li>' + ordenador[x].contador + ' x ' + ordenador[x].producto+ '</li>';
    }
    buffer += '</ul>';
    $('#info_principal').html(buffer);
}

$(window).load(function(){
    $("#buscar_producto").focus();
});

$(function(){  
    rsv_solicitar('producto_ingredientes_y_adicionales',{}, function(datos){
        for (x in datos.aux.adicionables)
        {
            _adicionales[datos.aux.adicionables[x].ID_adicional] = datos.aux.adicionables[x];
        }
    }, true);


    $('#buscar_producto').qtip({
        content: {
            text: 'Presione [ENTER] o flecha [ABAJO] para pasar a los resultados.'
        }
    });

    $(document).on('focus mouseover', '.agregar_producto', function(event) {
        $(this).qtip({
            overwrite: true,
            content: '[ENTER] para agregar el producto.<br />[ESPACIO] para personalizar<br />[1] a [9] para agregar x cantidad de veces',
            show: {
                solo: true,
                event: event.type,
                ready: true 
            }
        }, event);
    });
    
    $(document).on('mouseover', '#busqueda_adicionales', function(event) {
        $(this).qtip({
            overwrite: true,
            content: 'Presione [ENTER] o flecha [ABAJO] para pasar a los resultados.',
            show: {
                solo: true,
                event: event.type,
                ready: true 
            }
        }, event);
    });

    $(document).on('focus mouseover', '.ppia_adicional', function(event) {
        $(this).qtip({
            overwrite: true,
            content: '[TAB] para cambiar entre Agregar, Doble y Quitar.<br />[ESPACIO] para chequear esta opción.',
            show: {
                solo: true,
                event: event.type,
                ready: true 
            },
            hide: {
                event: 'unfocus'
            }
        }, event);
    });    
    
    $("#borrar_orden").click(function(){
        if (confirm('¿Desea borrar por completo esta orden?')) {
            reiniciarInterfaz();
        }
    });
    
    $(document).on('click','.ppia_adicional',function(){
        $("#busqueda_adicionales").val('').focus().trigger('keyup');
    });
    
    $('#enviar_orden_a_cocina').click(function(){
        
        $('#notificaciones').empty();
        
        // Chequeemos si esta autorizado para enviar ordenes
        
        rsv_solicitar('aut',{permisos:['ingresar_orden']}, function(retorno){
            if ( typeof(retorno.AUTORIZADO) === "undefined" )
            {   
                alert('HUBO UN ERROR CON EL SERVIDOR DE AUTORIZACIÓN, SU ORDEN NO PUEDE ENVIARSE.');
                return;
            }
            
            if (retorno.AUTORIZADO == 'no') {
                aut_solicitar();
                return;
            }
        
            if (_orden.length == 0)
            {
                alert('No hay pedidos en la orden.');
                return;
            }
            
            var ID_mesa = 0;
            
            while ( ID_mesa == 0 ) {
                ID_mesa = window.prompt('1. Número de MESA','0');
                
                if (!ID_mesa) {
                    $('#notificaciones').html('ENVIO CANCELADO');
                    return;
                }
            }
            
            
            var ID_mesero_busqueda = "";
            
            rsv_solicitar('cuenta',{mesa: ID_mesa, pendientes: true}, function(datos){
                try {
                    if ( typeof datos.aux.pendientes != "undefined" && datos.aux.pendientes != '')
                    {
                        ID_mesero_busqueda = datos.aux.pendientes[Object.keys(datos.aux.pendientes)[0]][0].ID_mesero;
    
                        if (datos.aux.pendientes[Object.keys(datos.aux.pendientes)[0]][0].flag_tiquetado == "1") {
                            alert('¡parece que la va a meter donde no debe!');
                        }
                    }
                } catch (error){
                    ID_mesero_busqueda = 0;
                }
    
                var ID_mesero = 0;
                while ( ID_mesero == 0 ) {
                    var meseros = '';
                    
                    for (x in _meseros)
                    {
                        meseros += " * " + _meseros[x].ID_usuarios + ". " + _meseros[x].usuario + "\n"; 
                        
                    }
                    
                    ID_mesero = window.prompt('2. Número de MESERO.' + "\n" + meseros, ID_mesero_busqueda );
                    
                    if (!ID_mesero) {
                        ID_mesero = 0;
                        ID_mesero_busqueda = 0;
                        $('#notificaciones').html('ENVIO CANCELADO');
                        return;
                    }
        
                    if (/^[0-9]+$/.test(ID_mesero) == false)
                    {
                        alert('Número de mesero incorrecto.');
                        ID_mesero = 0;
                    }
                }
                
                rsv_solicitar('ingresar_orden',{mesa: ID_mesa, mesero: ID_mesero, orden: _orden, GENERAR_IMPRESION_ORDEN_TRABAJO: 1}, function(){
                    reiniciarInterfaz();
                    $('#notificaciones').html('ORDEN ENVIADA');
                }); 
            });
        
        });
        
    });
    
    $(document).on('click', 'button.flecha', function(event){
        event.stopPropagation();
        var producto = $(this).parents('.agregar_producto');
        var ID_producto = producto.attr('producto');
        cantidadParaProductoEnPedido(ID_producto, producto.attr('nombre'), producto.attr('precio'));
    });
    
    $(document).on('keydown', '.agregar_producto', function(event){
        event.preventDefault();
        var keyCode = event.keyCode || event.which;

        var ID_producto = $(this).attr('producto');
        var _b_orden = {ID: ID_producto, precio: $(this).attr('precio'), detalle: $(this).attr('nombre'), adicionales: [], ingredientes: []};

        if (keyCode > 48 && keyCode < 58) {
            convertirProductoEnPedido(_b_orden, parseInt(keyCode) - 48);
        }
        
        if (keyCode > 96 && keyCode < 106) {
            convertirProductoEnPedido(_b_orden, parseInt(keyCode) - 96);
        }
        
        if (keyCode == 13) {
            agregar_producto_accion_directa(this);
        }
        
        if (keyCode == 32) {
            agregar_producto_accion_indirecta(this);
        }        
                
        // Arriba
        if (keyCode == 38) {
            var tab = parseInt($(this).attr('tabindex'));
            $(".agregar_producto[tabindex='"+(tab-1)+"']").focus();
        }
        
        
        // Abajo
        if (keyCode == 40) {
            var tab = parseInt($(this).attr('tabindex'));
            $(".agregar_producto[tabindex='"+(tab+1)+"']").focus();
        }
        
        // Izquierda
        if (keyCode == 37) {
            $(this).parents('td').prev('td').find('.agregar_producto:visible').eq(0).focus();
        }
        
        // Derecha
        if (keyCode == 39) {
            $(this).parents('td').next('td').find('.agregar_producto:visible').eq(0).focus();
        }
        
        event.stopPropagation();
    });
    
    function agregar_producto_accion_directa(objeto){
        
        var ID_producto = $(objeto).attr('producto');
        var _b_orden = {ID: ID_producto, precio: $(objeto).attr('precio'), detalle: $(objeto).attr('nombre'), adicionales: [], ingredientes: []};
        
        if (_productos[$(objeto).attr('producto')].complementar == '1')
        {
            intentarProductoEnPedido(ID_producto, $(objeto).attr('nombre'), $(objeto).attr('precio'));
        } else {
            convertirProductoEnPedido(_b_orden);
        }
    }
    
    function agregar_producto_accion_indirecta(objeto){
        var ID_producto = $(objeto).attr('producto');
        intentarProductoEnPedido(ID_producto, $(objeto).attr('nombre'), $(objeto).attr('precio'));
    }
    
    $(document).on('click', '.agregar_producto', function(){
        if ($("#modo_tactil").is(':checked')) {
            agregar_producto_accion_directa(this);
        } else {
            agregar_producto_accion_indirecta(this);
        }
        
    });
        
    $(document).on('contextmenu', '.agregar_producto', function(event){
        event.preventDefault();
        if ($("#modo_tactil").is(':checked')) {
            agregar_producto_accion_indirecta(this);
        } else {
            agregar_producto_accion_directa(this);
        }
    });
    
    $(document).on('click', '.agregar_por_cantidad', function(){

        _b_orden.ingredientes = [];
        _b_orden.adicionales = [];
        
        convertirProductoEnPedido(_b_orden, $(this).attr('rel'));
        $.modal.close();
    });
    
    $(document).on('click', '#agregar_producto_aceptar', function(){

        _b_orden.ingredientes = [];
        
        $('#cpep_ingredientes input[type="checkbox"]:checked:enabled').each(function(){
            _b_orden.ingredientes.push($(this).val());
        });

        _b_orden.adicionales = [];
        
        $('#cpep_adicionables input.agregar_adicionable[type="checkbox"]:checked:enabled').each(function(){
            _b_orden.adicionales.push($(this).val());
        });

        $('#cpep_adicionables input.agregar_doble_adicionable[type="checkbox"]:checked:enabled').each(function(){
            _b_orden.adicionales.push($(this).val());
            _b_orden.adicionales.push($(this).val());
        });

        
        $('#cpep_adicionables input.quitar_adicionable[type="checkbox"]:checked:enabled').each(function(){
            _b_orden.ingredientes.push($(this).val());
        });

        convertirProductoEnPedido(_b_orden, $("#agregar_producto_cantidad").val());
        $.modal.close();
    });
        
    $(document).on('click','#cpep_adicionables input[type="checkbox"]', function(){
        var grupo = $(this).attr('grupo');
        $('#cpep_adicionables input[type="checkbox"][grupo="'+grupo+'"]:checked').not(this).removeAttr('checked');
    });
    
    $(document).on('click', '.btn_eliminar_pedido', function(){
        
        if (!confirm('¿Desea eliminar este producto?')) return;
        
        _orden.splice($(this).closest('tr').attr('ID_orden'),1);
        miniResumenOrden();
    });
    
    $(document).on('click', '.agregar_producto', function(event){
        event.preventDefault();            
    });
    
    $(document).on('click', '.filtro_adicionales', function (event){
       event.preventDefault();
       var afinidad = $(this).attr('rel');
       if (afinidad == '') {
        $('.contenedor_adicionales tbody tr').show();
        return;
       }
       
       $('.contenedor_adicionales tbody tr').hide();
       $('.contenedor_adicionales tbody tr[rel="'+afinidad+'"]').show();
    });
    
    $(document).on('keydown', '#busqueda_adicionales', function(event){
        //Keydown es antes de keyup
        //event.stopPropagation();
    });
    

    $(document).on('keydown', '#buscar_producto', function(event){
        var keyCode = event.keyCode || event.which;
        
        if (keyCode == 13 || keyCode == 40) {
            $('.agregar_producto:visible').first().focus();
            $("#buscar_producto").val('');
        }
        
    });    
    
    $(document).on('keyup', '#buscar_producto', function(event){
        var keyCode = event.keyCode || event.which;
        if ( event.altKey == true  || event.ctrlKey == true || keyCode == 18){
            return false;
        }
        
        var busqueda = $.trim($(this).val());
        if (busqueda == '') {
            $('.agregar_producto').show();
            return true;
        }
        mostrar_grupo_productos('');
       
        $('.agregar_producto').hide();
        $('.agregar_producto:icontains("'+busqueda+'")').show();
        $('.agregar_producto').removeAttr('tabindex');
        $('.agregar_producto:visible').each(function(index) {
            $(this).attr('tabindex', (index+1))
       });
        return true;
    });    

    $(document).on('keydown', '#busqueda_adicionales', function(event){
        var keyCode = event.keyCode || event.which;
        
        if (keyCode == 13 || keyCode == 40) {
            $('.contenedor_adicionales tbody tr:visible').first().find('input[type="checkbox"]').first().focus();
            $("#busqueda_adicionales").val('');
        }
        
    });    
    
    $(document).on('keyup', '#busqueda_adicionales', function(event){
       event.stopPropagation();
       
        var keyCode = event.keyCode || event.which;
        if ( event.altKey == true  || event.ctrlKey == true){
            return false;
        }

       var busqueda = $(this).val();
       if (busqueda == '') {
        $('.contenedor_adicionales tbody tr').show();
        return true;
       }
       
       $('.contenedor_adicionales tbody tr').hide();
       $('.contenedor_adicionales tbody tr:icontains("'+busqueda+'")').show();
       
       return true;
    });

    
    $(document).keydown(function(event){
        var keyCode = event.keyCode || event.which;
        
        //console.log(keyCode);
        
        var objetivo = $('.key[key="'+keyCode+'"]');
        if ( event.altKey == false || event.ctrlKey == false || objetivo.length == 0) return;
        
        if (objetivo.hasClass('enfocar')) {
            objetivo.focus();
        } else {
            objetivo.click();
        }
        event.stopPropagation();
    });

   
    // Iniciar
    localStorage.clear();
    mostrar_grupo_productos('');
    obtener_lista_meseros();
    
});