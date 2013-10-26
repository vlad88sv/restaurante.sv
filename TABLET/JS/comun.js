function rsv_solicitar(peticion, data, funcion) {
    var ret_json;
    var objetivo = {TPL: peticion};
    
    
    $.post('/SERV/', $.extend(objetivo,data), function(retorno){funcion(retorno)}, 'json');
    return ret_json;
}


$(function(){
    $(document).on('click', '.facebox_cerrar', function(){$.modal.close();});
});