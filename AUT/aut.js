$(function(){
    rsv_solicitar('aut',{}, function(resultado){
        $("#resultado_aut").html( resultado.AUTENTICADO );
    });
    
    $("#f_aut").submit(function(event){
        event.preventDefault();
        
        $("#resultado_aut").empty();
        
        rsv_solicitar('aut', {usuario:$('input[name="usuario"]').val(), clave:$('input[name="clave"]').val()}, function(resultado){
            $("#resultado_aut").html( resultado.AUTENTICADO );
        });
    });
    
    $("#terminar_sesion").click(function(){
        $("#resultado_aut").empty();
        rsv_solicitar('aut', {terminar: true}, function(resultado){
            $("#resultado_aut").html( resultado.AUTENTICADO );
        });
    });
});