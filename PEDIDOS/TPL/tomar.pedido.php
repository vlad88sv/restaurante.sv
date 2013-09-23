<script type="text/javascript" src="JS/tomar_pedido.js"></script>
<?php $_html['titulo'] = 'Tomar pedido'; ?>
<table style="width:100%;margin: 0px;border-bottom: 4px solid black;">
    <tr>
        <td style="text-align:left;">Búscar: <input class="key enfocar" key="88" type="text" id="buscar_producto" value="" /></td>
        <td style="text-align: right;vertical-align: middle;">
            [ <input type="checkbox" id="modo_tactil" value="1" /> <label for="modo_tactil">modo táctil</label> ]&nbsp; 
            <button id="vaciar_cache">Vaciar cache</button>&nbsp;
            <button class="key" key="66" id="borrar_orden">[Ctrl+Alt+b] Borrar</button>&nbsp;
            <button class="key" key="82" id="ver_resumen">[Ctrl+Alt+r] Resumen</button>
            <button class="key" key="69" id="enviar_orden_a_cocina">[Ctrl+Alt+e] Enviar</button>
        </td>
    </tr>
</table>

<div id="scroller"></div>
<div id="info_principal"></div>

<table id="menu_productos">
    <tr>
        <td><a class="mp key" rel="2" key="49" href="#">1.ENTRADAS</a></td>
        <td><a class="mp key" rel="1" key="50" href="#">2.PIZZAS</a></td>
        <td><a class="mp key" rel="3" key="51" href="#">3.PASTAS</a></td>
        <td><a class="mp key" rel="8" key="52" href="#">4.ENSALADAS</a></td>
        <td><a class="mp key" rel="4" key="53" href="#">5.POSTRES</a></td>
        <td><a class="mp key" rel="5" key="54" href="#">6.ESPEC</a></td>
        <td><a class="mp key" rel="6" key="55" href="#">7.BEBIDAS</a></td>
        <td><a class="mp key" rel="7" key="56" href="#">8.CERVEZA</a></td>
        <td><a class="mp key" rel="9" key="57" href="#">9.TINTO</a></td>
        <td><a class="mp key" rel="10" key="48" href="#">0.BLANCO</a></td>
        <td><a class="mp key" rel="11" key="171" href="#">*.CHAMPAGNE</a></td>
    </tr>
</table>