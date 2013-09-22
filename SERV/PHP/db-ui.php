<?PHP
function db_ui_opciones($clave, $valor, $tabla, $cuales="", $orden="", $grupo_ui="", $seleccionada="") {
 $html = NULL;
 //La función es crear un combobox con name=id=$clave y value=$valor y HTML a partir de un SELECT $clave, $valor FROM $tabla
 $c = "SELECT $clave, $valor FROM $tabla $cuales $orden";
 DEPURAR ($c, 0);
 $resultado = db_consultar ($c);
 if ( $grupo_ui ) {
    $html .= "<optgroup label='$grupo_ui'>";
 }
while ($f = mysqli_fetch_assoc($resultado)) {
  $t_clave = $f[$clave];
  $t_valor = $f[$valor];
  if ($t_clave == $seleccionada) {
      $selected = ' selected="selected"';
  } else {
      $selected = "";
  }
  $html .= '<option value="' . $t_clave . '"' . $selected . '>' . $t_valor . '</option>';
}
return $html;
}

function db_ui_tabla($resultado, $opciones="", $Titulos=true, $NoMas = "No hay datos") {
 global $db_link;
 if ( !mysqli_num_rows($resultado) ) {
  return $NoMas;
 }

 mysqli_data_seek($resultado, 0);
 
 $table = "";
 $table .= "<table $opciones>\n";
 $fields = mysqli_fetch_fields($resultado);
 if ($Titulos)
 {
 $table .= "<thead><tr>";
 foreach($fields as $fi => $f) {
  $field = $f->name;
  $table .= "<th>$field</th>\n";
 }
 $table .= "</tr></thead>\n";
 }
 $table .= "<tbody>\n";
 while ($r = mysqli_fetch_row($resultado)) {
 $table .= "<tr>";
 foreach ($r as $column) {
 $table .= "<td>$column</td>";
 }
 $table .= "</tr>\n";
 }
 $table .= "</tbody>\n";
 $table .= "</table>";
 return $table;
 }


function db_ui_checkboxes($guid, $tabla, $valor, $texto, $explicacion, $default = array(), $extra="", $where="1")
{
 $c = "SELECT $valor, $texto, $explicacion FROM $tabla WHERE $where";
 $r = db_consultar($c);
 $html = '';
 if (is_array($default)) $arr = array_flip($default); else $arr = array();
 while ($row = mysqli_fetch_assoc($r)) {
     $strDefault = isset($arr[$row[$valor]]) ? "checked=\"checked\"" : "";
     $html .= "<span title='".$row[$explicacion]."'>" . ui_input($guid, $row[$valor], "checkbox","","",$strDefault. " " . $extra) . $row[$texto] . "</span><br />";
 }
 return $html;
}

function db_ui_checkboxes_auto($guid, $tabla, $valor, $texto, $explicacion, $prueba, $extra="", $where="1")
{
 $c = "SELECT $valor, $texto, $explicacion, $prueba AS 'chequear' FROM $tabla WHERE $where";
 $r = db_consultar($c);
 $html = '';
 while ($row = mysqli_fetch_assoc($r)) {
     $strDefault = $row['chequear'] == 1 ? "checked=\"checked\"" : "";
     $html .= "<span title='".$row[$explicacion]."'>" . ui_input($guid, $row[$valor], "checkbox","","",$strDefault. " " . $extra) . $row[$texto] . "</span><br />";
 }
 return $html;
}
?>
