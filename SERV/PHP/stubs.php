<?php
function numero($numero)
{
    if (!is_numeric($numero))
        return 0.00;
    
    return number_format($numero,2,'.','');
}

function CacheDestruir()
{
    if (extension_loaded('apcu'))
    {
        apc_clear_cache();
    } else {
        $toDelete = new APCIterator('user', '/^'.ID_CACHE.'/' , APC_ITER_VALUE); 
        apc_delete($toDelete); 
    }
    
    
}

function CacheCrear($llave, $valor, $destructivo = false)
{
    if ($destructivo)
        CacheDestruir ();

    apc_store(ID_CACHE .  crc32($llave), $valor, 5);
}

function CacheObtener($llave)
{    
    $cache = apc_fetch(ID_CACHE . crc32($llave));
    return $cache;
}
?>