<?php
require_once('configuracion.php');

$_html['titulo'] = '';
if (empty($_GET['TPL'])) $_GET['TPL'] = 'master.chef';

ob_start();
require_once('TPL/'.$_GET['TPL'].'.php');
$_html['contenido'] = ob_get_clean();
?>
<!DOCTYPE html> 
<html> 
<head> 
    <title><?php echo NOMBRE_RESTAURANTE; ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="Content-Style-type" content="text/css" />
    <meta http-equiv="Content-Script-type" content="text/javascript" />
    <meta http-equiv="Content-Language" content="es" />
    <meta http-equiv="refresh" content="3600">
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <link rel="stylesheet" media="screen" href="CSS/estilo.css" />
    <link rel="stylesheet" media="print" href="CSS/estilo.impresion.css" />
    <script type="text/javascript" src="JS/jquery.js"></script>
    <script type="text/javascript" src="JS/jquery-ui.js"></script>
    <script type="text/javascript" src="JS/jquery-jqprint.js"></script>
    <script type="text/javascript" src="JS/jquery.simplemodal.js"></script>
    <script type="text/javascript">
        $(function(){
            $.extend($.modal.defaults, {
                minHeight: '70%',
        	minWidth: '80%'
            });
            
            $.ajax({
                cache: false,
                beforeSend: function(){
                    $("#ajax_cargando").show();
                },
                complete: function(){
                    $("#ajax_cargando").hide();
                }
            });
        });
    </script>
    <script type="text/javascript" src="JS/comun.js"></script>
</head> 
<body> 
<?php echo $_html['contenido']; ?>
<img id="ajax_cargando" src="IMG/cargando.gif" style="position:fixed;top:50%;left:50%;z-index:20;display: none;" />
</body>
</html>
