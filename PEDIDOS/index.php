<?php
require_once('../configuracion.php');
require_once('../inicio.php');

$_html['titulo'] = '';
$_GET['TPL'] =  ( empty($_GET['TPL']) ? 'tomar.pedido' : $_GET['TPL'] );
ob_start();
require_once('TPL/'.$_GET['TPL'].'.php');
$_html['contenido'] = ob_get_clean();?>
<!DOCTYPE html> 
<html> 
<head> 
    <title><?php echo ID_SERVIDOR . ' - ' . $_html['titulo']; ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="Content-Language" content="es" />
    <meta name="viewport" content="width=480, height=800, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" >
    
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="CSS/estilo.css" />
    <link rel="stylesheet" href="<?php echo URI_SERVIDOR; ?>/CSS/jquery.qtip.css" />
    
    <script type="text/javascript" src="<?php echo URI_SERVIDOR; ?>/JS/jquery.js"></script>
    <script type="text/javascript" src="<?php echo URI_SERVIDOR; ?>/JS/jquery.simplemodal.js"></script>
    <script type="text/javascript" src="<?php echo URI_SERVIDOR; ?>/JS/jquery.qtip.js"></script>
    <script type="text/javascript" src="<?php echo URI_SERVIDOR; ?>/JS/comun.php"></script>
    
    <script type="text/javascript">
        $(function(){
            $(window).bind('beforeunload', function(){
                return "¿Esta seguro de salir?. Si sale de esta página perderá todos los datos ingresados.";
            });
        });
    </script>
</head> 
<body>    
<div id="page" >
    <div id="content">
        <?php echo $_html['contenido']; ?>
    </div>
</div>
</body>
</html>
