<?php
require_once('../configuracion.php');

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
    <meta http-equiv="Content-Language" content="es" />
    <meta http-equiv="refresh" content="3600">
    
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="CSS/estilo.css" />
    <link rel="stylesheet" href="<?php echo URI_SERVIDOR; ?>/CSS/jquery.qtip.css" />
    
    <script type="text/javascript">URI_SERVIDOR = "<?php echo URI_SERVIDOR; ?>";</script>
    <script type="text/javascript" src="<?php echo URI_SERVIDOR; ?>/JS/jquery.js"></script>
    <script type="text/javascript" src="<?php echo URI_SERVIDOR; ?>/JS/jquery.simplemodal.js"></script>
    <script type="text/javascript" src="<?php echo URI_SERVIDOR; ?>/JS/comun.js"></script>
    <script type="text/javascript" src="<?php echo URI_SERVIDOR; ?>/JS/jquery.qtip.js"></script>
    <script type="text/javascript" src="<?php echo URI_SERVIDOR; ?>/JS/date.js"></script>
</head> 
<body> 
<div id="page" >
    <div id="content">
        <?php echo $_html['contenido']; ?>
    </div>
</div>
</body>
</html>
