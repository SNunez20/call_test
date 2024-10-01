<?php

include_once '_init.php';
 
$buscar_fecha=fechaDesdeHasta();

function queryTotalizadorPiscina($estado){
    $buscar_fecha=fechaDesdeHasta();
    $query="SELECT 
                count(padron_datos_socio.cedula) as socio 
            FROM 
                padron_datos_socio ,
                usuarios
            WHERE 
                padron_datos_socio.estado = $estado 
                AND usuarios.activo=1
                AND usuarios.id = padron_datos_socio.id_usuario
                {$buscar_fecha} 
            GROUP BY 
                padron_datos_socio.cedula"
            ;
        return $query;
} 



die(json_encode($totalizdor));
