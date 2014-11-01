<!DOCTYPE html>
<head>
   <meta charset="utf-8" />
   <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
   <meta name="viewport" content="width=device-width">

   <title>Dashboard</title>

   <link rel="stylesheet" type="text/css" href="<?php echo URI_SERVIDOR; ?>/VENDORS/dashboard/css/bootmetro.css">
   <link rel="stylesheet" type="text/css" href="<?php echo URI_SERVIDOR; ?>/VENDORS/dashboard/css/bootmetro-responsive.css">
   <link rel="stylesheet" type="text/css" href="<?php echo URI_SERVIDOR; ?>/VENDORS/dashboard/css/bootmetro-icons.css">
   <link rel="stylesheet" type="text/css" href="<?php echo URI_SERVIDOR; ?>/VENDORS/dashboard/css/bootmetro-ui-light.css">
   <link rel="stylesheet" type="text/css" href="<?php echo URI_SERVIDOR; ?>/VENDORS/dashboard/css/site.css">
</head>
<body>
   <header id="hero" class="">
         <div class="jumbotron masthead">
            <div class="container-fluid">
               <div class="row-fluid">
                  <div class="inner span7">
                     <h1>CloudCRP</h1>
                     <h2>Solucion de Negocio para cualquier tipo de empresa!</h2>
                     <h3>Bienvenido <i><?php echo $_SESSION['datos']['usuario']; ?></i>. <a href="<?php echo URI_AUT; ?>?logout">Salir</a></h3>
                  </div>
            </div>
         </div>
   </header>
   
   <div id="home-tiles" class="container-fluid metro-responsive-tiles">
      <div class="row-fluid">
         <div class="span4">
            <a class="tile wide imagetext bg-color-orange first" target="_blank" href="<?php echo URI_AUT; ?>PEDIDOS/">
               <div class="image-wrapper">
                  <span class="icon icon-cart"></span>
               </div>
               <div class="column-text">
                  <div class="text-header3">Pedidos</div>
               </div>
            </a>
         </div>
         <div class="span4">
            <a class="tile wide imagetext bg-color-green middle" target="_blank" href="<?php echo URI_AUT; ?>">
               <div class="image-wrapper">
                  <span class="icon icon-users"></span>
               </div>
               <div class="column-text">
                  <div class="text-header2">Usuarios</div>
                  <div class="text4">
                  </div>
               </div>
            </a>
         </div>
         <div class="span4">
            <a class="tile wide imagetext bg-color-blue last" target="_blank" href="<?php echo URI_AUT; ?>DESPACHO/">
               <div class="image-wrapper">
                  <span class="icon icon-screen"></span>
               </div>
               <div class="column-text">
                  <div class="text-header3">Monitor</div>
               </div>
            </a>
         </div>
      </div>
   
      <div class="row-fluid">
         <div class="span4">
             <a class="tile wide imagetext bg-color-greenDark first" target="_blank" href="<?php echo URI_AUT; ?>CAJA/">
               <div class="image-wrapper">
                  <span class="icon icon-calculate"></span>
               </div>
               <div class="column-text">
                  <div class="text-header3">Caja</div>
               </div>
            </a>
         </div>
         <div class="span4">
            <a class="tile wide imagetext bg-color-blueDark middle" target="_blank" href="<?php echo URI_AUT; ?>">
               <div class="image-wrapper">
                  <span class="icon icon-user-6"></span>
               </div>
               <div class="column-text">
                  <div class="text-header3">Admin</div>
               </div>
            </a>
         </div>
         <div class="span4">
             <a class="tile wide imagetext bg-color-orange first" target="_blank" href="<?php echo URI_AUT; ?>NODO/">
               <div class="image-wrapper">
                  <span class="icon icon-screen-2"></span>
               </div>
               <div class="column-text">
                  <div class="text-header2">Nodo</div>
                  <div class="text4">
                  </div>
               </div>
            </a>
         </div>         
      </div>
   </div>
   
</body>
</html>
