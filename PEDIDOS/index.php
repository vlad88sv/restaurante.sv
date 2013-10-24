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
    <link rel="stylesheet" href="http://cdnjs.cloudflare.com/ajax/libs/qtip2/2.1.1/jquery.qtip.min.css" />
    <script type="text/javascript" src="/SERV/JS/jquery.js"></script>
    <script type="text/javascript" src="/SERV/JS/jquery.simplemodal.js"></script>
    <script type="text/javascript" src="/SERV/JS/comun.js"></script>
    <script type="text/javascript" src="http://cdnjs.cloudflare.com/ajax/libs/qtip2/2.1.1/jquery.qtip.min.js"></script>
</head> 
<body>    
<div id="page" >
    <div id="content">
        <?php echo $_html['contenido']; ?>
    </div>
</div>
</body>
</html>
