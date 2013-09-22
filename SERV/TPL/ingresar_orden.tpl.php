<?php
// La orden viene en un JSON que es un array que se compone de:
// No. de Mesa
// Array de pedidos

// En el array de pedidos, cada pedido (producto) contiene 3 partes fundamentales:
// 1. ID de producto y precio
// 2. Array de ingredientes que se van a _quitar_
// 3. Array de extras/adicionales que se van a _agregar_


// Antes de ingresar el pedido a la tabla generaremos un UUID (SHA1) que servirá como ID de la orden


if (!is_numeric($_POST['mesa']) || !isset($_POST['orden']) || !is_array($_POST['orden']) || count($_POST['orden']) == 0)
{
    $json['error'][] = 'La orden es inválida. Error 1';
}

ingresar_orden($_POST['orden'], $_POST['mesa'], $_POST['mesero']);
?>