<?php
require_once('../configuracion.php');

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
    <meta http-equiv="Content-Language" content="es" />
    <meta http-equiv="refresh" content="43200">
    
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <link rel="stylesheet" media="screen" href="CSS/estilo.css" />
    
    <script type="text/javascript">URI_SERVIDOR = "<?php echo URI_SERVIDOR; ?>";</script>
    <script type="text/javascript" src="<?php echo URI_SERVIDOR; ?>/JS/jquery.js"></script>
    <script type="text/javascript" src="<?php echo URI_SERVIDOR; ?>/JS/comun.php"></script>
</head> 
<body> 
<?php echo $_html['contenido']; ?>
</body>
</html>
