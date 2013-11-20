<script type="text/javascript" src="JS/tomar_pedido.js"></script>
<script type="text/javascript" src="JS/domicilio.js"></script>
<?php $_html['titulo'] = 'Pedido a domicilio'; ?>
<img src="IMG/pizza.png" id="cambiar_vista" class="key" key="86" />

<div id="contenedor_general" style="width:80%;padding-top: 10px;">
    <div id="botones_principales">
        <button class="key" key="69" id="enviar_orden_a_cocina" style="font-weight: bold;">Enviar</button><br />
        <button class="key" key="66" id="borrar_orden" style="font-size:10pt;">Borrar</button>
    </div>

    <div id="vista_cliente">
        <div id="datos_cliente" >
            <div style="margin:5px 15px;padding:5px;border:3px solid black;border-radius: 10px;">
                <p style="margin-left:25px;">
                    <label for="cliente_telefono">Teléfono</label><input type="text" id="cliente_telefono" style="vertical-align: middle;" value="" />
                    <button id="buscar_telefono" style="background: none;" disabled="disabled">historial</button><br />
                    <label for="cliente_nombre">Nombre</label><input type="text" id="cliente_nombre" style="width:350px;" sugge value="" />
                </p>
                <p style="margin-left:10px;">
                    <label for="cliente_direccion">Dirección y notas</label><br />
                    <textarea  id="cliente_direccion"></textarea>
                </p>
            </div>
        </div>

        <div id="datos_pago" >
            <div style="margin:5px 15px;padding:5px;border:3px solid black;border-radius: 10px;">
                <div style="padding:10px;">
                    Método de pago:&nbsp;
                    <label for="domicilio_metodo_pago_tarjeta">Tarjeta</label><input type="radio" name="domicilio_metodo_pago" checked="checked" id="domicilio_metodo_pago_tarjeta" value="tarjeta" />&nbsp;
                    <label for="domicilio_metodo_pago_efectivo">Efectivo</label><input type="radio" name="domicilio_metodo_pago" id="domicilio_metodo_pago_efectivo" value="efectivo" />&nbsp;
                </div>
                
                <p id="metodo_pago_tarjeta" style="margin-left:25px;margin-top:0px;">
                    <label for="cliente_tarjeta">Número tarjeta</label><input type="text" id="cliente_tarjeta" placeholder="4321-1234-4321-1234" value="" />&nbsp;
                    <label for="cliente_tarjeta_expiracion">Expiración (MM/YY)</label><input type="text" id="cliente_tarjeta_expiracion" placeholder="04/13" value="" style="width:65px;" /><br />
                </p>
                
                <p id="metodo_pago_efectivo" style="margin-left:25px;margin-top:0px;display:none;">
                    <label for="cliente_vuelto">Cambio para $</label><input type="text" id="cliente_vuelto" placeholder="0.00" value="" style="width:100px;" /><br />
                </p>

                <div style="padding:10px;">
                    Detalle de facturación:&nbsp;
                    <label for="domicilio_detalle_facturacion_consumo">Consumo</label><input type="radio" name="domicilio_detalle_facturacion" checked="checked" id="domicilio_detalle_facturacion_consumo" value="consumo" />&nbsp;
                    <label for="domicilio_detalle_facturacion_detalle">Detalle</label><input type="radio" name="domicilio_detalle_facturacion" id="domicilio_detalle_facturacion_detalle" value="detalle" />&nbsp;
                </div>

                
                <div style="padding:10px;">
                    Documento físcal:&nbsp;
                    <label for="domicilio_documento_fiscal_tiquete">Tiquete</label><input type="radio" name="domicilio_documento_fiscal" checked="checked" id="domicilio_documento_fiscal_tiquete" value="tiquete" />&nbsp;
                    <label for="domicilio_documento_fiscal_credito_fiscal">Crédito físcal</label><input type="radio" name="domicilio_documento_fiscal" id="domicilio_documento_fiscal_credito_fiscal" value="credito_fiscal" />&nbsp;
                    <label for="domicilio_documento_fiscal_consumidor_final">Consumidor Final</label><input type="radio" name="domicilio_documento_fiscal" id="domicilio_documento_fiscal_consumidor_final" value="consumidor_final" />&nbsp;
                </div>
                
                <div style="margin-left:25px;">
                    <label for="datos_facturacion__nombre">Nombre</label><input type="text" id="datos_facturacion__nombre" placeholder="" /><br />
                    <label for="datos_facturacion__dui">DUI</label><input type="text" id="datos_facturacion__dui" placeholder="" /><br />
                    <label for="datos_facturacion__nit">NIT</label><input type="text" id="datos_facturacion__nit" placeholder="" /><br />
                    <label for="datos_facturacion__nrc">NRC</label><input type="text" id="datos_facturacion__nrc" placeholder="" /><br />
                    <label for="datos_facturacion__giro">Giro</label><input type="text" id="datos_facturacion__giro" placeholder="" /><br />
                    <label for="datos_facturacion__direccion">Dirección</label><input type="text" id="datos_facturacion__direccion" placeholder="" /><br />
                </div>
                
            </div>
        </div>
    </div>

    <div id="vista_pizza" style="display: none;">
        <div >
            <div style="margin:5px 15px;padding:5px;border:3px solid black;border-radius: 10px;">
                <p style="margin-left:25px;"><label for="buscar_producto">Producto</label> <input class="key enfocar" key="88" type="text" id="buscar_producto" value="" /></p>
                <div id="scroller"></div>
            </div>
        </div>

        <div >
            <div id="resumen_completo" ></div>
        </div>
        
        
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
    </div>
</div>

<div id="info_principal"></div>