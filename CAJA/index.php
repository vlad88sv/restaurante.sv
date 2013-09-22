<?php
require_once('configuracion.php');

$_html['titulo'] = '';
if (empty($_GET['TPL'])) $_GET['TPL'] = 'caja';

ob_start();
require_once('TPL/'.$_GET['TPL'].'.php');
$_html['contenido'] = ob_get_clean();
?>
<!DOCTYPE html> 
<html> 
<head> 
    <title><?php echo $_html['titulo']; ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="Content-Style-type" content="text/css" />
    <meta http-equiv="Content-Script-type" content="text/javascript" />
    <meta http-equiv="Content-Language" content="es" />
    <meta http-equiv="refresh" content="3600">
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="CSS/estilo.css" />
    <script type="text/javascript" src="/SERV/JS/jquery.js"></script>
    <script type="text/javascript" src="/SERV/JS/jquery.simplemodal.js"></script>
    <script type="text/javascript" src="/SERV/JS/jquery-jqprint.js"></script>
    <script type="text/javascript" src="/SERV/JS/date.js"></script>
    <script type="text/javascript">
        $(function(){
            $.extend($.modal.defaults, {
                minHeight: '95%',
        	minWidth: '95%'
            });
            
	    $(document).ajaxStart(function(){$("#ajax_cargando").show();});
	    $(document).ajaxStop(function(){$("#ajax_cargando").hide();});
	    
            $.ajaxSetup({
                cache: false,
		timeout: 30000,
		error: function(jqXHR, exception) {
		    if (jqXHR.status === 0) {
			alert('No hay conexión.\nVerificar red.');
		    } else if (jqXHR.status == 404) {
			alert('Página no encontrada [404]');
		    } else if (jqXHR.status == 500) {
			alert('Error interno de servidor [500].');
		    } else if (exception === 'parsererror') {
		    } else if (exception === 'timeout') {
			alert('Error: su conexión esta muy lenta.');
		    } else if (exception === 'abort') {
			alert('Error: petición AJAX abortada.');
		    } else {
			alert('Error desconocido.\nError: ' + jqXHR.responseText);
		    }
		}
            });
        });
    </script>
    <script type="text/javascript" src="/SERV/JS/cuentas.js"></script>
</head> 
<body> 
<div id="page" >
    <div id="content">
        <?php echo $_html['contenido']; ?>
    </div>
</div>

<img id="ajax_cargando" src="IMG/cargando.gif" style="position:fixed;top:50%;left:50%;z-index:20;display: none;" />
</body>
</html>
