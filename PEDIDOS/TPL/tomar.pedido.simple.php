<link rel="stylesheet" href="CSS/estilo.simple.css" />
<script type="text/javascript" src="JS/tomar_pedido_simple.js"></script>
<?php $_html['titulo'] = 'Tomar pedido'; ?>
<table style="width:100%;margin: 0px;border-bottom: 4px solid black;">
    <tr>
        <td style="text-align:left;">Búscar: <input class="key enfocar" key="88" type="text" id="buscar_producto" value="" /></td>
        <td id="notificaciones" style="text-align:left;color:red;font-size:14px;font-weight:bold;text-align:center;"></td>
        <td style="text-align: right;vertical-align: middle;">
            [ <input type="checkbox" id="modo_tactil" checked="checked" value="1" /> <label style="font-size:10pt" for="modo_tactil">simple</label> ]&nbsp; 
            <button class="key" key="66" id="borrar_orden"><b>B</b>orrar</button>&nbsp;
            <button class="key" key="69" id="enviar_orden_a_cocina"><b>E</b>nviar</button>
        </td>
    </tr>
</table>

<div id="scroller">
    <table id="resultados">
        <tr>
            <th>Maíz</th>
            <th>Arroz</th>
            <th>Antojitos</th>
            <th>Bebidas</th>
        </tr>
        
        <tr>
            <td id="resultado_1"></td>
            <td id="resultado_2"></td>
            <td id="resultado_3"></td>
            <td id="resultado_4"></td>
        </tr>
        
    </table>
</div>
<div id="info_principal"></div>
