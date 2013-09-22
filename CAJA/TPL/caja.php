<script type="text/javascript" src="JS/caja.js"></script>
<script type="text/javascript" src="JS/master.chef.js"></script>
<audio id="beep">
    <source src="./SND/tim_tum.ogg">
    <source src="./SND/tim_tum.mp3">
</audio>
<?php $_html['titulo'] = 'Caja'; ?>
<div id="menu" style="z-index:555;padding-left:40px;height:32px;line-height:32px;background-color: grey; position:fixed;left:0px;right:0px;border:2px solid white;display:none;">
    <div>
    <input type="text" style="width:100px;" readonly="readonly" value="<?php echo date('Y-m-d'); ?>" id="fecha_caja" />&nbsp;
    <button id="ver_historial">Historial</button>&nbsp;
    <button id="ver_total">Ver total del día</button>&nbsp;
    <button id="inventario">Inventario</button>&nbsp;
    <button id="compras">Compras</button>&nbsp;
    <button id="historial_cortez" style="display:none;">Tabla Cortes</button>&nbsp;
    <button id="cortes" style="display:none;">Cortes</button>&nbsp;
    </div>
</div>
<img src="IMG/gear.png" id="mostrar_opciones" style="background-color: grey;border:2px solid white;position:fixed;top:0px;left:0px;z-index:999;" />
<table style="width:98%;border-collapse:collapse;margin:auto;table-layout:fixed;">
<tr>
    <td id="pestana_pedido" style="vertical-align: top; border-right: 1px solid whitesmoke;padding-right: 5px;width:75%;">
        <h1>
            CUENTAS ABIERTAS&nbsp;
            [&nbsp;
            Mesa: <input id="id_mesa" type="text" value="" style="width:3.5em;" />&nbsp;|&nbsp;
            <input type="checkbox" style="vertical-align: middle;" class="vaciar_cache_caja" id="ocultar_fechas" value="1" /><label for="ocultar_fechas">Ocultar horas</label>&nbsp;
            <input type="checkbox" style="vertical-align: middle;" class="vaciar_cache_caja" id="cuentas_compactas" value="1" /><label for="cuentas_compactas">Cuentas compactas</label>
            ]

        </h1><hr />
        <div id="pedidos"></div>
    </td>
    <td id="pestana_cocina" style="vertical-align: top; border-left: 1px solid whitesmoke;padding-left: 5px;width:25%;">
        <h1>ORDENES EN COCINA</h1><hr />
        <div id="cocina"></div>
    </td>
</tr>
</table>
<div style="background: grey;border-top: 2px solid whitesmoke;color: black;z-index: 99;position: fixed;bottom: 0;line-height: 20px;left: 0px;right: 0px;font-family: monospace;font-size:12px;">
    <span title="Distrubición de Servicio Normalizado" style="font-weight:bold;">DSN</span>: <span id="dsn"></span>
    <br />
    <span title="Tiempo Promedio de Servicio" style="font-weight:bold;">TPS</span>: <span id="tps"></span>
    &nbsp;|&nbsp;
    <span title="Tiempo Máximo de Servicio" style="font-weight:bold;">TMS</span>: <span id="tms"></span>
</div>