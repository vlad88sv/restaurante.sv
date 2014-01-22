<script type="text/javascript" src="JS/nodo.js"></script>
<audio id="beep">
    <source src="/SERV/SND/beep.wav">
    <source src="/SERV/SND/beep.mp3">
</audio>
<div id="pedidos"></div>
<div id="cont_nodo">
    <label for="opcion_despacho_completo">Despacho completo</label>
    <input class="auto_guardar" id="opcion_despacho" type="checkbox" value="1" />
    &nbsp;
    <select class="auto_guardar" id="nodo">
<?php
    foreach($__listado_nodos as $valor => $nodo)
    {
        echo sprintf("\t\t".'<option value="%s">%s</option>', $valor, $nodo)."\n";
    }
?>
    </select>
</div>