<script type="text/javascript" src="JS/tomar_pedido.js"></script>
<?php $_html['titulo'] = 'Tomar pedido'; ?>
<table style="width:100%;margin: 0px;border-bottom: 4px solid black;">
    <tr>
        <td style="text-align:left;">Búscar: <input class="key enfocar" key="88" type="text" id="buscar_producto" value="" /></td>
        <td style="text-align: right;vertical-align: middle;">
            <div style="display:none">[ <input type="checkbox" id="modo_tactil" value="1" /> <label style="font-size:10pt" for="modo_tactil">táctil</label> ]&nbsp;</div>
            <button class="key" key="66" id="borrar_orden"><b>B</b>orrar</button>&nbsp;
            <button class="key" key="82" id="ver_resumen"><b>R</b>esumen</button>
            <button class="key" key="69" id="enviar_orden_a_cocina"><b>E</b>nviar</button>
        </td>
    </tr>
</table>

<div id="scroller"></div>
<div id="info_principal"></div>

<table id="menu_productos">
    <tbody><tr>
        <td><a href="#" key="49" rel="1" class="mp key grupo1">1.MAIZ</a></td>
        <td><a href="#" key="50" rel="2" class="mp key grupo2">2.ARROZ</a></td>
        <td><a href="#" key="51" rel="3" class="mp key grupo3">3.ANTOJITOS</a></td>
        <td><a href="#" key="52" rel="4" class="mp key grupo4">4.BEBIDA</a></td>
    </tr>
</tbody></table>