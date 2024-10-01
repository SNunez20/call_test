<?php

include_once '_init.php';

$includes = ["id=1 ", 2, 4, 5, 22, 30, 33, 34];

$includes_query = implode(" OR id=", $includes);

$query = "SELECT * FROM servicios   ";

$servicios = query($mysqli, $query);

$totalizador["Otros"] = 0;

while ($servicio = mysqli_fetch_assoc($servicios)) {
    $id_servicio = (int)$servicio['id'];
    $nro_servicio = $servicio['nro_servicio'];
    $nombre_servicio = $servicio['nombre_servicio'];
    $horas_sanatorio = (int)$servicio['hrs_sanatorio'] == 1 ? true : false;
    $horas_servicio = (int) $servicio['hrs_servicio'] == 1 ? true : false;

    if ($id_servicio == 1 || $id_servicio == 2 && ($horas_sanatorio || $horas_servicio)) {
        $servicio_horas = queryTotalizadorServicioConHoras($nro_servicio);

        $totalizador[$nombre_servicio] = [
            "{$nombre_servicio} 8 horas" => $servicio_horas['ocho'],
            "{$nombre_servicio} 16 horas" => $servicio_horas['dieciseis'],
            "{$nombre_servicio} 24 horas" => $servicio_horas['veinticuatro'],
        ];
    } else {
        $totalizador["Otros"] = (int) $totalizador["Otros"] + (int) queryTotalizadorServicioSinHoras($nro_servicio);
    }
}

die(json_encode($totalizador));


function queryTotalizadorServicioConHoras($nro_servicio)
{
    $array = [
        'ocho' => 0,
        'dieciseis' => 0,
        'veinticuatro' => 0
    ];
    global $mysqli;
    $query = query($mysqli, queryTotalizadorServicios($nro_servicio));

    if (mysqli_num_rows($query) > 0) {
        while ($row = mysqli_fetch_assoc($query)) {

            $nro = (int)$row['nro'];
            if ($nro == 1) $array['ocho']++;
            elseif ($nro == 2) $array['dieciseis']++;
            elseif ($nro == 3) $array['veinticuatro']++;
        }
    }
    return  $array;
}

function queryTotalizadorServicioSinHoras($nro_servicio)
{
    global $mysqli;
    $valor = 0;
    $query = query($mysqli, queryTotalizadorServicios($nro_servicio));
    if (mysqli_num_rows($query) > 0) {
        while ($query_row = mysqli_fetch_assoc($query)) {
           
            $valor++;
        }
    }

    return $valor;
}

function queryTotalizadorServicios($servicio,$alta='')
{
    $buscar_fecha = fechaDesdeHasta();
    $query = "SELECT
    COUNT( padron_producto_socio.cedula ) AS nro,
    padron_producto_socio.cedula ,
    padron_producto_socio.servicio
    FROM
        padron_producto_socio,
        padron_datos_socio
    WHERE
        padron_producto_socio.servicio = '{$servicio}' 
        AND padron_datos_socio.cedula = padron_producto_socio.cedula 
        AND padron_datos_socio.activo=1
        AND padron_datos_socio.estado = 6
        AND padron_producto_socio.servicio<>'06'
        AND padron_producto_socio.servicio<>'07'
        {$alta}        
        {$buscar_fecha} 

    GROUP BY padron_producto_socio.servicio,padron_producto_socio.cedula
        
    ORDER BY
        nro DESC
";

 
    return $query;
}
