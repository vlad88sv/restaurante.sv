<?php
$db_link = NULL;
static $db_contador;
db_conectar(); // Iniciamos la conexión a la base de datos.

/*
$memcache = new Memcached();
$memcache -> addServer('127.0.0.1', 11211);
*/

function db_conectar(){
    global $db_link;
    $db_link = @mysqli_connect(db__host, db__usuario, db__clave, db__db) or die("Fue imposible conectarse a la base de datos.<br /><hr />Detalles del error:<pre>" . @mysqli_error($db_link) . "</pre>");
    mysqli_query($db_link,'SET NAMES utf8');
}

function db_consultar($consulta){
    global $db_link;
    global $db_contador;
    if ( !$db_link ) {
        db_conectar();
    }
    DEPURAR($consulta,0);
    $resultado = @mysqli_query($db_link,$consulta);
    if ( mysqli_error($db_link) ) {
        error_log ('MySQL.Error:' . mysqli_error($db_link));
        error_log ('MySQL.Query: ' . $consulta) ;
    }
    $db_contador++;
    return $resultado;

}

function db_fetch($resultado)
{
    if (!$resultado) return false;
    return mysqli_fetch_assoc($resultado);
}

function db_codex($datos){
    global $db_link;
    if ( !$db_link ) {
        db_conectar();
    }
    if (is_array($datos))
    {
        foreach($datos as $valor)
        {
            $arr[] = db_codex($valor);
        }
        return $arr;
    }
    else
    {
        return mysqli_real_escape_string($db_link,$datos);
    }
}
function db_afectados(){
    global $db_link;
    if ( $db_link ) {
        return mysqli_affected_rows($db_link);
    }
    return -1;
}
function db_crear_tabla($tabla, $campos, $botarPrimero=false){
    $salida = "";
    if ( $botarPrimero ) {
        if ( db_consultar ("DROP TABLE IF EXISTS $tabla") ) {
            $salida .= "Tabla '$tabla' botada"."<br />";
        } else {
            $salida .= "Tabla '$tabla' no pudo ser botada"."<br />";
        }
    }
    if ( db_consultar ("CREATE TABLE IF NOT EXISTS $tabla ($campos)") ) {
        $salida .= "Tabla '$tabla' creada"."<br />";
        $c = "explain $tabla";
        $resultado = db_consultar($c);
        $salida .= db_ui_tabla($resultado,"",true,"¡oops!, ¡parece que no se creó!");
    } else {
        $salida .= "Tabla '$tabla' no pudo ser creada"."<br />";
    }
    return $salida;
}

function db_agregar_datos($tabla, $datos, $desconfiar = true) {
    global $db_link;
    $campos = $valores = NULL;
    foreach ($datos as $clave => $valor) {
        if ($desconfiar)
        {
            $arr_campos[]   = db_codex($clave);
            $arr_valores[]  = db_codex($valor);
        } else {
            $arr_campos[]   = $clave;
            $arr_valores[]  = $valor;
        }
    }
    $campos = implode (",", $arr_campos);
    
    if ($desconfiar)
        $valores = "'".implode ("','", $arr_valores)."'";
    else
        $valores = implode (",", $arr_valores);
        
    $c = "INSERT INTO $tabla ($campos) VALUES ($valores)";
    $resultado = db_consultar ($c);
    $id = @mysqli_insert_id ($db_link);
    DEPURAR ($c, 0);
    return $id;
}

function db_reemplazar_datos($tabla, $datos) {
    global $db_link;
    $campos = $valores = NULL;
    foreach ($datos as $clave => $valor) {
        //echo "clave: $clave; valor: $valor<br />\n";
        $arr_campos[]   = db_codex($clave);
        $arr_valores[]  = db_codex($valor);
    }
    $campos = implode (",", $arr_campos);
    $valores = "'".implode ("','", $arr_valores)."'";
    $c = "REPLACE INTO $tabla ($campos) VALUES ($valores)";
    $resultado = db_consultar ($c);
    $id = @mysqli_insert_id ($db_link);
    DEPURAR ($c, 0);
    return $id;
}

function db_actualizar_datos($tabla, $datos, $donde = "0") {
    global $db_link;
    $DATA = NULL;
    foreach ($datos as $clave => $valor) {
        $arr_DATA[] = db_codex($clave) . "='".db_codex($valor)."'";
    }
    $DATA = join(",",$arr_DATA);
    $c = "UPDATE $tabla SET $DATA WHERE $donde";
    $resultado = db_consultar ($c);
    $id = @mysqli_affected_rows($db_link);
    DEPURAR ($c, 0);
    return $id;
}

function db_contar($tabla,$where="1")
{
    $c = "SELECT COUNT(*) AS cuenta FROM $tabla WHERE $where";
    $r = db_consultar($c);
    $f = mysqli_fetch_assoc($r);
    return $f['cuenta'];
}

function db_obtener($tabla,$campo,$where,$group='')
{
    $c ="SELECT $campo AS 'resultado' FROM $tabla WHERE $where $group LIMIT 1";
    $r = db_consultar($c);
    if ($r && mysqli_num_rows($r) == 1)
    {
        $f = mysqli_fetch_assoc($r);
        return $f['resultado'];
    } else {
        return false;
    }
}

function db_obtener_fila($tabla,$where,$group='')
{
    $c ="SELECT * FROM $tabla WHERE $where $group LIMIT 1";
    $r = db_consultar($c);
    return db_fetch($r);
}


/*
// Usar solamente en scripts que no procesen post
function memcache_iniciar($contexto,$discriminador='')
{
    global $memcache;
    
    if (!MEMCACHE_ACTIVO || isset($_GET['nocache']))
        return;

    $hash = sha1($contexto.serialize(array($_SERVER['SERVER_PORT'],@$_SERVER["SERVER_NAME"],$discriminador)));
    $buffer = $memcache->get($hash);

    if ($buffer)
    {
        echo $buffer;
        echo '<!-- memcache.hash :: '.$hash.' !-->';
        return true;
    }
    
    ob_start(); // Nota: usar memcache_finalizar() o no se veran los datos!
    
    return false;
}

function memcache_finalizar($contexto, $discriminador='', $duracion = '+12 hour')
{
    global $memcache;
    
    if (!MEMCACHE_ACTIVO || isset($_GET['nocache']))
        return;
    
    $contenido = ob_get_clean();
    
    $hash = sha1($contexto.serialize(array($_SERVER['SERVER_PORT'],@$_SERVER["SERVER_NAME"],$discriminador)));
    $memcache -> set($hash, $contenido, strtotime($duracion));
    
    return $contenido;
}
*/
?>
