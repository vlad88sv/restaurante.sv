<?php
if ( !USAR_AUT ) return;

require_once('SERV/PHP/vital.php');

// Si esta intentando terminar sesion
if (isset($_REQUEST['logout']))
{
    sesion::terminar();
    header('Location: ' . URI_AUT);
    exit();
}

// Si esta intentando iniciar sesion
if (!empty($_REQUEST['login']) && !empty($_REQUEST['password']))
{    
    sesion::iniciar($_REQUEST['login'], $_REQUEST['password']);
}

// Si ha iniciado sesion entonces todo bien
if ( sesion::$autenticado ) return;
?>
<!DOCTYPE html>
<!--[if lt IE 7]> <html class="lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]> <html class="lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]> <html class="lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html lang="en"> <!--<![endif]-->
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <title><?php echo NOMBRE_RESTAURANTE; ?></title>
  <link rel="stylesheet" href="<?php echo URI_SERVIDOR; ?>/CSS/inicio.css">
  <!--[if lt IE 9]><script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
</head>
<body>
  <form method="post" action="" class="login">
    <p>
      <label for="login">Usuario</label>
      <input type="text" name="login" id="login">
    </p>

    <p>
      <label for="password">Contraseña</label>
      <input type="password" name="password" id="password">
    </p>

    <p class="login-submit">
      <button type="submit" class="login-button">Iniciar</button>
    </p>

    <!--<p class="forgot-password"><a href="index.html">Forgot your password?</a></p>!-->
  </form>

  <section class="about">
    <p class="about-links">
      <a href="http://logicalsdts.com/" target="_parent">LogicalSDTS.com</a>
      <a href="http://logicalsdts.com/contacto.html" target="_parent">Contacto</a>
    </p>
    <p class="about-author">
        <?php echo SUCURSAL_EMPRESA; ?>
    </p>
      
  </section>
</body>
</html>
<?php
exit ('asdksad');