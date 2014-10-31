<?php
require_once('../configuracion.php');
require_once('../inicio.php');

$_GET['TPL'] =  ( empty($_GET['TPL']) ? 'caja' : $_GET['TPL'] );
$_html['titulo'] = '';
ob_start();
require_once('TPL/'.$_GET['TPL'].'.php');
$_html['contenido'] = ob_get_clean();
?>
<!DOCTYPE html> 
<html> 
<head> 
    <title><?php echo ID_SERVIDOR . ' - ' . $_html['titulo']; ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="Content-Language" content="es" />
    <meta http-equiv="refresh" content="6000">
    
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="CSS/estilo.css" />
    <link rel="stylesheet" href="<?php echo URI_SERVIDOR; ?>/CSS/jquery.qtip.css" />
    
    <script type="text/javascript" src="<?php echo URI_SERVIDOR; ?>/JS/jquery.js"></script>
    <script type="text/javascript" src="<?php echo URI_SERVIDOR; ?>/JS/jquery.simplemodal.js"></script>
    <script type="text/javascript" src="<?php echo URI_SERVIDOR; ?>/JS/jquery.qtip.js"></script>
    <script type="text/javascript" src="<?php echo URI_SERVIDOR; ?>/JS/date.js"></script>
    <script type="text/javascript" src="<?php echo URI_SERVIDOR; ?>/JS/comun.php"></script>
</head> 
<body> 
<div id="page" >
    <div id="content">
        <?php echo $_html['contenido']; ?>
    </div>
</div>
</body>
</html>
