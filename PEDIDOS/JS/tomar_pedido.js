_orden = [];
_b_orden = [];
_meseros = [];
ID_mesero_busqueda = '';

function MostrarRejillaProductos(datos)
{
    
    var buffer = '';
    var indice = 1;
    
    for (x in datos.aux)
    {
        if (datos.aux[x].descontinuado == 0)
        {
            
            _productos[datos.aux[x].ID_producto] = datos.aux[x];
            
            buffer += '<div tabindex="'+indice+'" producto="'+datos.aux[x].ID_producto+'" nombre="' + datos.aux[x].nombre + '" precio="' + datos.aux[x].precio + '" ' + (datos.aux[x].disponible == 0 ? 'style="text-decoration:line-through"' : '') +' class="agregar_producto"><div class="nombre">' + datos.aux[x].nombre + '</div>&nbsp;<div class="precio">$' + parseFloat(datos.aux[x].precio).toFixed(2)  + '</div></div>';
            
            indice++;
        }
    }
    
    $("#scroller").html(buffer);
}

function reiniciarInterfaz() {
    _orden = [];
    _b_orden = [];
    mostrar_grupo_productos('1');
    miniResumenOrden();
    ResumenOrden();
    obtener_lista_meseros();
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
    rsv_solicitar('producto_buscar', {grupo: ID_grupo}, MostrarRejillaProductos, true);
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
 
function intentarProductoEnPedido(str_producto, str_detalle, str_precio)
{
    _b_orden = {timestamp: Math.floor(+new Date() / 1000), ID: str_producto, precio: str_precio, detalle: str_detalle, adicionales: [], ingredientes: []};
    
    var buffer = '';
        
    buffer += '<div style="clear:both;height:45px;text-align:center;border-bottom: 4px solid black;margin-bottom:4px;" class="botones_grandes">';
    buffer += '<button id="agregar_producto_aceptar" class="key" key="65" style="float:left;">[Ctrl+Alt+a] Aceptar</button>';
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
    buffer += '<button class="filtro_adicionales" rel="5">Quesos y sabores</button></div>';
    buffer += '<div style="bottom:0;top: 160px;left:0;right:0;overflow-y: auto;padding: 0 5px;position: absolute;">';
    buffer += '<div id="cpep_adicionables"></div>';
    buffer += '</div>';
    
    $.modal(buffer, {opacity: 0, focus: false} );
    
    $("#busqueda_adicionales").focus()
    
    personalizar_producto_ingredientes_y_adicionales(str_producto);
    
}

function convertirProductoEnPedido(buffer_de_orden)
{
    _orden.push(buffer_de_orden);
    miniResumenOrden();
    $("#buscar_producto").focus();
}

function ResumenOrden()
{
    var buffer = '';
    
    if (_orden.length == 0)
    {
        $("#scroller").html('<p>No hay ningún pedido agregado</p>');
        return;
    }
    
    buffer += '<h1>Resumen de la orden</h1>';
    
    buffer += '<table class="estandar ancha bordes zebra" id="seleccion_producto">';
    for (x in _orden)
    {
        var adicionales = '';
        if (_orden[x].adicionales.length > 0) {
            adicionales += '<b>Agregar:</b>';
            adicionales += '<ul>';
            
            for (y in _orden[x].adicionales) {
                adicionales += '<li>' + _adicionales[_orden[x].adicionales[y]].nombre + '</li>';
            }
            
            adicionales += '</ul>';
        }
        
        var quitar = '';
        if (_orden[x].ingredientes.length > 0) {
            quitar += '<b>Quitar:</b>';
            quitar += '<ul>';
            
            for (y in _orden[x].ingredientes) {
                quitar += '<li>' + _adicionales[_orden[x].ingredientes[y]].nombre + '</li>';
            }
            
            quitar += '</ul>';
        }
        
        buffer += '<tr ID_orden="' + x + '">';
        buffer += '<td>' + (parseInt(x)+1) + '</td>';
        buffer += '<td><div style="color:blue;font-weight:bold;">' + _orden[x].detalle + '</div><div>' + adicionales + '</div><div>' + quitar + '</div></td>';
        buffer += '<td>' + _orden[x].precio + '</td>';
        buffer += '<td><button class="btn_eliminar_pedido">Eliminar</button></td>';
        buffer += '</tr>';
        
    }
    buffer += '</table>';
    
    
    _orden[x].precio
    
    $("#scroller").html(buffer);
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
    
    $("#borrar_orden").click(function(){
        if (confirm('¿Desea borrar por completo esta orden?')) {
            reiniciarInterfaz();
        }
    });
    
    $('.ppia_adicional').live('click',function(){
        $("#busqueda_adicionales").val('').focus().trigger('keyup');
    });
    
    $('#enviar_orden_a_cocina').click(function(){
        
        if (_orden.length == 0)
        {
            alert('No hay pedidos en la orden.');
            return;
        }
        
        ResumenOrden();
        
        var ID_mesa = 0;
        
        while ( ID_mesa == 0 ) {
            ID_mesa = window.prompt('1. Número de MESA','0');
            
            if (!ID_mesa) {
                alert ('Cancelando envío');
                return;
            }

            if (/^[0-9]+$/.test(ID_mesa) == false)
            {
                alert('Número de mesa incorrecto.');
                ID_mesa = 0;
            }
        }
        
        
        var ID_mesero_busqueda = "";
        
        rsv_solicitar('cuenta',{mesa: ID_mesa, pendientes: true}, function(datos){
            if ( typeof datos.aux.pendientes != "undefined" )
            {
                ID_mesero_busqueda = datos.aux.pendientes[Object.keys(datos.aux.pendientes)[0]][0].ID_mesero;
                
                alert('¡Mesa con cuenta abierta!');

                if (datos.aux.pendientes[Object.keys(datos.aux.pendientes)[0]][0].flag_tiquetado == "1") {
                    alert('¡parece que la va a meter donde no debe!');
                }
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
                    alert ('Cancelando envío');
                    return;
                }
    
                if (/^[0-9]+$/.test(ID_mesero) == false)
                {
                    alert('Número de mesero incorrecto.');
                    ID_mesero = 0;
                }
            }
            
            rsv_solicitar('ingresar_orden',{mesa: ID_mesa, mesero: ID_mesero, orden: _orden}, function(){
                reiniciarInterfaz();
                $('#info_principal').html('<div style="color:red;font-size:14px;font-weight:bold;text-align:center;">ORDEN ENVIADA</div>');
            }); 
        });
        
    });
    
    
    $(".agregar_producto").live('keydown', function(event){
        event.preventDefault();
        var keyCode = event.keyCode || event.which;
        
        if (keyCode == 13) {
            var ID_producto = $(this).attr('producto');
            var _b_orden = {ID: ID_producto, precio: $(this).attr('precio'), detalle: $(this).attr('nombre'), adicionales: [], ingredientes: []};
            convertirProductoEnPedido(_b_orden);
        }
        
        if (keyCode == 32) {
            var ID_producto = $(this).attr('producto');
            intentarProductoEnPedido(ID_producto, $(this).attr('nombre'), $(this).attr('precio'));
        }
        
        if (keyCode == 37) {
            var tab = parseInt($(this).attr('tabindex'));
            $(".agregar_producto[tabindex='"+(tab-1)+"']").focus();
        }
        
        if (keyCode == 39) {
            var tab = parseInt($(this).attr('tabindex'));
            $(".agregar_producto[tabindex='"+(tab+1)+"']").focus();
        }
        
        event.stopPropagation();
    });
    
    function agregar_producto_accion_directa(objeto){
        var ID_producto = $(objeto).attr('producto');
        var _b_orden = {ID: ID_producto, precio: $(objeto).attr('precio'), detalle: $(objeto).attr('nombre'), adicionales: [], ingredientes: []};
        convertirProductoEnPedido(_b_orden);
    }
    
    function agregar_producto_accion_indirecta(objeto){
        var ID_producto = $(objeto).attr('producto');
        intentarProductoEnPedido(ID_producto, $(objeto).attr('nombre'), $(objeto).attr('precio'));
    }
    
    $('.agregar_producto').live('click', function(){
        if ($("#modo_tactil").is(':checked')) {
            agregar_producto_accion_directa(this);
        } else {
            agregar_producto_accion_indirecta(this);
        }
        
    });
        
    $('.agregar_producto').live('contextmenu', function(event){
        event.preventDefault();
        if ($("#modo_tactil").is(':checked')) {
            agregar_producto_accion_indirecta(this);
        } else {
            agregar_producto_accion_directa(this);
        }
    });
    
    $('#agregar_producto_aceptar').live('click', function(){

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

        convertirProductoEnPedido(_b_orden);
        $.modal.close();
    });
    
    $('.btn_detalles_pedido').live('click',function(){
        
    });
    
    $(document).on('click','#cpep_adicionables input[type="checkbox"]', function(){
        var grupo = $(this).attr('grupo');
        $('#cpep_adicionables input[type="checkbox"][grupo="'+grupo+'"]:checked').not(this).removeAttr('checked');
    });
    
    $('.btn_eliminar_pedido').live('click', function(){
        
        if (!confirm('¿Desea eliminar este producto?')) return;
        
        _orden.splice($(this).closest('tr').attr('ID_orden'),1);
        miniResumenOrden();
        ResumenOrden();
    });
    
    $('#ver_resumen').click(function(event){
        ResumenOrden();
    });
    
    $(".agregar_producto").live('click', function(event){
        event.preventDefault();            
    });
    
    $(".mp").click(function (event){
        event.preventDefault();
        mostrar_grupo_productos($(this).attr('rel'));
    });
    
    $(".filtro_adicionales").live('click', function (event){
       event.preventDefault();
       var afinidad = $(this).attr('rel');
       if (afinidad == '') {
        $('.contenedor_adicionales tbody tr').show();
        return;
       }
       
       $('.contenedor_adicionales tbody tr').hide();
       $('.contenedor_adicionales tbody tr[rel="'+afinidad+'"]').show();
    });
    
    $("#busqueda_adicionales").live('keydown', function(event){
        //Keydown es antes de keyup
        //event.stopPropagation();
    });
    

    $("#buscar_producto").live('keydown', function(event){
        var keyCode = event.keyCode || event.which;
        
        if (keyCode == 13 || keyCode == 40) {
            $('.agregar_producto:visible').first().focus();
            $("#buscar_producto").val('');
        }
        
    });    
    
    $("#buscar_producto").live('keyup', function(event){
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

    $("#busqueda_adicionales").live('keydown', function(event){
        var keyCode = event.keyCode || event.which;
        
        if (keyCode == 13 || keyCode == 40) {
            $('.contenedor_adicionales tbody tr:visible').first().find('input[type="checkbox"]').first().focus();
            $("#busqueda_adicionales").val('');
        }
        
    });    
    
    $("#busqueda_adicionales").live('keyup', function(event){
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
        
        console.log(keyCode);
        
        var objetivo = $('.key[key="'+keyCode+'"]');
        if ( event.altKey == false || event.ctrlKey == false || objetivo.length == 0) return;
        
        if (objetivo.hasClass('enfocar')) {
            objetivo.focus();
        } else {
            objetivo.click();
        }
        event.stopPropagation();
    });
    
    $("#vaciar_cache").click(function () {
       if (confirm("Esto vaciará el cache y borrará el pedido actual.\nEsto es útil si desea cargar nuevos cambios del sistema o nuevos productos/adicionables.") == false)
        return;
    
        localStorage.clear();
        window.location.reload(true);
       
    });
    
    mostrar_grupo_productos(1);
    obtener_lista_meseros();

});