<?php

/* Migración a uso de cuentas completa. */

if ( empty($_POST['cuenta']) )
    return;

$cuenta = db_codex($_POST['cuenta']);

$c = 'UPDATE ordenes SET flag_pagado=1, fechahora_pagado=NOW() WHERE cuenta="'.$cuenta.'"';
db_consultar($c);
?>