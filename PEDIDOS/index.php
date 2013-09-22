<?php
require_once('configuracion.php');

$_html['titulo'] = '';
if (empty($_GET['TPL'])) $_GET['TPL'] = 'tomar.pedido';

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
    <meta name="viewport" content="width=480, height=800, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" >
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="CSS/estilo.css" />
    <script type="text/javascript" src="JS/jquery.js"></script>
    <script type="text/javascript" src="JS/jquery.simplemodal.js"></script>
    <script type="text/javascript">
        $(function(){
            $.extend($.modal.defaults, {
                minHeight: '90%',
        	minWidth: '90%'
            });
            
            $.ajax({
                cache: false,
		timeout : 2000,
                beforeSend: function(){
                    $("#ajax_cargando").show();
                },
                complete: function(){
                    $("#ajax_cargando").hide();
                },
		error: function(jqXHR, textStatus, errorThrown) {
		    alert('La comunicación ha fallado, posiblemente sus datos no se enviaron');
		}
            });
        });
    </script>
    <script type="text/javascript" src="JS/comun.js"></script>
</head> 
<body>
    
<audio id="beep">
    <source src="./SND/beep.wav">
    <source src="./SND/beep.mp3">
</audio>
    
<div id="page" >
    <div id="content">
        <?php echo $_html['contenido']; ?>
    </div>
</div>

<img id="ajax_cargando" src="IMG/cargando.gif" style="position:fixed;top:50%;left:50%;z-index:20;display: none;" />
</body>
</html>
