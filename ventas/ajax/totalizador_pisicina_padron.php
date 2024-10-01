<?php

include_once '_init.php';

$buscar_fecha = fechaDesdeHasta();

function queryTotalizadorPiscina($estado, $alta = '')
{
    $buscar_fecha = fechaDesdeHasta();
    $query = "SELECT 
                count(padron_datos_socio.cedula) as socio 
            FROM 
                padron_datos_socio ,
                padron_producto_socio
            WHERE 
                padron_datos_socio.estado = $estado 
                AND padron_datos_socio.activo = 1
                AND padron_producto_socio.cedula = padron_datos_socio.cedula 
                AND padron_producto_socio.servicio <> '06'
                AND padron_producto_socio.servicio<>'07'
                {$alta}
                {$buscar_fecha} 
            GROUP BY padron_producto_socio.cedula
             ";

   return $query;
}
 
$padron = query($mysqli, queryTotalizadorPiscina(6));
$count_padron = (int) mysqli_num_rows($padron);

$bienvenida = query($mysqli, queryTotalizadorPiscina(3));
$count_bienvenida = (int) mysqli_num_rows($bienvenida);

$pendiente = query($mysqli, queryTotalizadorPiscina(1));
$count_pendiente = (int) mysqli_num_rows($pendiente);

$rechazados = query($mysqli, queryTotalizadorPiscina(678));
$count_rechazados = (int) mysqli_num_rows($rechazados);

$eliminados = query($mysqli, queryTotalizadorPiscina(692));
$count_eliminados = (int)mysqli_num_rows($eliminados);

$rechazado_morosidad = query($mysqli, queryTotalizadorPiscina(7));
$count_rechazado_morosidad = (int) mysqli_num_rows($rechazado_morosidad);

$pendiente_morosidad = query($mysqli, queryTotalizadorPiscina(2));
$count_pendiente_morosidad = (int)mysqli_num_rows($pendiente_morosidad);

$rechazado_bienvenida = query($mysqli, queryTotalizadorPiscina(4));
$count_rechazado_bienvenida = (int)mysqli_num_rows($rechazado_bienvenida);
 

$total_socios = (int) $count_padron + $count_bienvenida + $count_pendiente + $count_rechazados  + $count_eliminados + $count_rechazado_morosidad + $count_pendiente_morosidad +
    $count_rechazado_bienvenida;

$totalizdor = [
    'padron' => $count_padron,
    'bienvenida' => $count_bienvenida,
    'pendiente' => $count_pendiente,
    'rechazados' => $count_rechazados,
    'eliminados' => $count_eliminados,
    'rechazado_morosidad' => $count_rechazado_morosidad,
    'pendiente_morisidad' => $count_pendiente_morosidad,
    'rechazado_bienvenida' => $count_rechazado_bienvenida,
    'total_socios' => $total_socios
];


die(json_encode($totalizdor));
