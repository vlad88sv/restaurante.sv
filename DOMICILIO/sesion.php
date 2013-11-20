<?php

class sesion {
    static $iniciado = false;
    
    function __construct() {
       // Verificar si esta iniciado
        if ( @$_SESSION['sesion']['iniciado'] == '1' )
        {
            
        }
    }
    
    function iniciar($usuario, $contrasena)
    {
        $iniciado = true;
    }
    
    function terminar()
    {
        
    }
}
