<link rel="stylesheet" href="//code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
<script type="" src="//code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
<script type="text/javascript" src="<?php echo URI_SERVIDOR; ?>/JS/jquery.ui.timepicker.js"></script>
<script type="text/javascript" src="JS/tomar_pedido.js"></script>
<script type="text/javascript" src="JS/master.chef.js"></script>
<script type="text/javascript" src="JS/domicilio.js"></script>
<?php $_html['titulo'] = 'Pedido a domicilio'; ?>
<img src="IMG/delivery.png" id="estado_pedidos" class="key" key="0" />
<img src="IMG/pizza.png" id="cambiar_vista" class="key" key="86" />

<div id="vista_pedidos" style="width:80%;padding-top: 10px;position:relative;">
    <div id="botones_principales">
        <button class="key" key="69" id="enviar_orden_a_cocina" style="font-weight: bold;height:30px;margin-bottom:1px;">Enviar</button><br />
        <button class="key" key="66" id="borrar_orden" style="font-size:10pt;">Borrar</button>
    </div>

    <div id="vista_cliente">
        <div id="datos_cliente" >
            <div style="margin:5px 15px;padding:5px;border:3px solid black;border-radius: 10px;">
                <p style="margin-left:25px;">
                    <label class="forma" for="cliente_telefono">Teléfono</label><input type="text" id="cliente_telefono" style="vertical-align: middle;" value="" />
                    <button class="rsv" id="buscar_telefono" disabled="disabled">historial</button>
                    <button class="rsv" id="guardar_datos">guardar</button><br />
                    <label class="forma" for="cliente_nombre">Nombre</label><input type="text" id="cliente_nombre" style="width:360px;" sugge value="" />
                </p>
                <p style="margin-left:10px;">
                    <label for="cliente_direccion">Dirección</label><br />
                    <textarea  id="cliente_direccion"></textarea><br />
                    <label for="cliente_notas">Notas</label><br />
                    <textarea  id="cliente_notas"></textarea>
                </p>
                <p style="margin-left:10px;">
                    <input type="checkbox" id="flag_pausa" name="pedido_pausar_orden" value="1"><label for="flag_pausa">Pausar orden y mostrarla hasta</label>
                    <input type="text" id="fechahora_activacion" name="pedido_fechahora_activacion" value="" />
                </p>
            </div>
        </div>

        <div id="datos_pago" >
            <div style="margin:5px 15px;padding:5px;border:3px solid black;border-radius: 10px;">
                <div style="padding:10px;">
                    Método de pago:&nbsp;
                    <label class="forma" for="domicilio_metodo_pago_tarjeta">Tarjeta</label><input type="radio" name="domicilio_metodo_pago" checked="checked" id="domicilio_metodo_pago_tarjeta" value="tarjeta" />&nbsp;
                    <label class="forma" for="domicilio_metodo_pago_efectivo">Efectivo</label><input type="radio" name="domicilio_metodo_pago" id="domicilio_metodo_pago_efectivo" value="efectivo" />&nbsp;
                </div>
                
                <p id="metodo_pago_tarjeta" style="margin-left:25px;margin-top:0px;">
                    <label class="forma" for="cliente_tarjeta">Número tarjeta</label><input type="text" id="cliente_tarjeta" placeholder="4321-1234-4321-1234" value="" />&nbsp;
                    <label class="forma" for="cliente_tarjeta_expiracion">Expiración (MM/YY)</label><input type="text" id="cliente_tarjeta_expiracion" placeholder="04/13" value="" style="width:65px;" /><br />
                </p>
                
                <p id="metodo_pago_efectivo" style="margin-left:25px;margin-top:0px;display:none;">
                    <label class="forma" for="cliente_vuelto">Cambio para $</label><input type="text" id="cliente_vuelto" placeholder="0.00" value="" style="width:100px;" /><br />
                </p>

                <div style="padding:10px;">
                    Detalle de facturación:&nbsp;
                    <label class="forma" for="domicilio_detalle_facturacion_consumo">Consumo</label><input type="radio" name="domicilio_detalle_facturacion" checked="checked" id="domicilio_detalle_facturacion_consumo" value="consumo" />&nbsp;
                    <label class="forma" for="domicilio_detalle_facturacion_detalle">Detalle</label><input type="radio" name="domicilio_detalle_facturacion" id="domicilio_detalle_facturacion_detalle" value="detalle" />&nbsp;
                </div>

                
                <div style="padding:10px;">
                    Documento físcal:&nbsp;
                    <label class="forma" for="domicilio_documento_fiscal_consumidor_final">Consumidor Final</label><input type="radio" name="domicilio_documento_fiscal" id="domicilio_documento_fiscal_consumidor_final" value="consumidor_final" />&nbsp;
                    <label class="forma" for="domicilio_documento_fiscal_credito_fiscal">Crédito físcal</label><input type="radio" name="domicilio_documento_fiscal" id="domicilio_documento_fiscal_credito_fiscal" value="credito_fiscal" />&nbsp;
                </div>
                
                <div style="margin-left:25px;">
                    <label class="forma" for="datos_facturacion__nombre">Nombre</label><input type="text" name="datos_facturacion__nombre" id="datos_facturacion__nombre" placeholder="" /><br />
                    <label class="forma" for="datos_facturacion__dui">DUI</label><input type="text" name="datos_facturacion__dui" id="datos_facturacion__dui" placeholder="" /><br />
                    <label class="forma" for="datos_facturacion__nit">NIT</label><input type="text" name="datos_facturacion__nit" id="datos_facturacion__nit" placeholder="" /><br />
                    <label class="forma" for="datos_facturacion__nrc">NRC</label><input type="text" name="datos_facturacion__nrc" id="datos_facturacion__nrc" placeholder="" /><br />
                    <label class="forma" for="datos_facturacion__giro">Giro</label><input type="text" name="datos_facturacion__giro" id="datos_facturacion__giro" placeholder="" /><br />
                    <label class="forma" for="datos_facturacion__direccion">Dirección</label><input type="text" name="datos_facturacion__direccion" id="datos_facturacion__direccion" placeholder="" /><br />
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

<div id="vista_estado_pedidos" style="width:80%;padding-top: 10px;display:none;">
    
    <div style="margin:5px 15px;padding:5px;border:3px solid black;border-radius: 10px;">
        <h1>VISTA ESTADO PEDIDOS</h1>
        <p>Filtrar a cuenta <input type="text" id="estado_pedidos_filtro" placeholder="2560-6060" /></p>
        <div id="ajax_estado_pedidos"></div>
    </div>
</div>
<div id="info_principal"></div>