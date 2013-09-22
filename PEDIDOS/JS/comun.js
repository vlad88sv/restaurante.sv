_productos = {};
_adicionales = {};

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


function Beep() {
    $('#beep').get(0).play();
}

$(function(){
    
    $.expr[':'].icontains = function (n, i, m) {
        return jQuery(n).text().toUpperCase().indexOf(m[3].toUpperCase()) >= 0;
    };
        
    $('.facebox_cerrar').live('click',function(){
        $("#scroller").show();
        $.modal.close();
    });
    
    rsv_solicitar('producto_ingredientes_y_adicionales',{}, function(datos){
        for (x in datos.aux.adicionables)
        {
            _adicionales[datos.aux.adicionables[x].ID_adicional] = datos.aux.adicionables[x];
        }
    }, true);
});

