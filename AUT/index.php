<?php require_once('../configuracion.php'); ?>
<!DOCTYPE html> 
<html> 
<head> 
    <title><?php echo ID_SERVIDOR . ' - Autorización'; ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="Content-Language" content="es" />
    <meta http-equiv="refresh" content="600">
    
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    
    <script type="text/javascript">URI_SERVIDOR = "<?php echo URI_SERVIDOR; ?>";</script>
    <script type="text/javascript" src="<?php echo URI_SERVIDOR; ?>/JS/jquery.js"></script>
    <script type="text/javascript" src="<?php echo URI_SERVIDOR; ?>/JS/comun.js"></script>
    <script type="text/javascript" src="aut.js"></script>
</head> 
<body>
    <h1>Autorización</h1>
    <p>El acceso a funciones de CAJA, TOMA DE PEDIDOS y ESTADISTICAS requiere permisos especiales.</p>
    <hr />
    <form autocomplete="off" action="" method="POST" id="f_aut">
        <input type="text" name="usuario" placeholder="usuario" />&nbsp;
        <input type="password" name="clave" placeholder="Contraseña" />&nbsp;
        <input type="submit" value="Autorizar" />
    </form>
    <hr />
    <p>Sesión iniciada: <span style="font-weight:bold;" id="resultado_aut">no</span></p>
    <hr />
    <button id="terminar_sesion">Terminar sesión</button>
</body>
</html>