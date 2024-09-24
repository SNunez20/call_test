<?php

$response['result'] = false;
$response['session'] = false;


if (isset($_POST["typeAdmin"])) {
    $response["session"] = true;
    $cedulaSocio         = $_POST["id"] ?? '';


    // QUERY PARA EL RESUMEN DE PRODUCTO ,
    $result = obtener_productos_resumen($cedulaSocio);

    if ($result) {
        $total = 0;
        while ($row = mysqli_fetch_assoc($result)) {
            $promo        = $row['promo']; //compe
            $alta         = $row['alta']; //compe
            $nroServicio  = $row['servicio'];
            $importe      = obtener_total_importe($row['id'])['total_importe'];
            $accion       = $row['accion'];
            if ($accion == 1) $total += $importe;

            $response['productosResumen'][] = [
                'id'              => $row["id"],
                'horas'           => $row["horas"],
                'accion'          => $row["accion"],
                'id_servicio'     => $row["id_servicio"],
                'nro_servicio'    => $nroServicio, //compe
                'nombre_servicio' => $row["nombre_servicio"],
                'importe'         => $importe, //compe
                'accion'          => $row['accion'],
                'promo'           => $promo,
                'dato_extra'      => $row['dato_extra'],
            ];
        }
    }


    // QUERY PARA EL PANEL DE EDICION DE PRODUCTOS
    $result = obtener_productos_editar($cedulaSocio);

    if ($result) {
        $result2          = obtener_productos_editar($cedulaSocio);
        $row              = mysqli_fetch_array($result2);
        $suc              = ($row['sucursal'] == 3 || $row['sucursal'] == 7 || $row['sucursal'] == 8) ? '0' . $row['sucursal'] : $row['sucursal'];
        $cedula_cliente   = $row['cedula'];
        $nombre_cliente   = $row['nombre'];
        $fecha_nacimiento = $row['fecha_nacimiento'];
        $rloc             = obtener_localidad($suc);

        $localidad = mysqli_fetch_assoc($rloc)['id'];

        while ($row = mysqli_fetch_assoc($result)) {
            $id              = $row['id'];
            $id_servicio     = $row['id_servicio'];
            $nro_servicio    = $row['servicio'];
            $cod_promo       = $row['cod_promo'];
            $nombre_servicio = $row['nombre_servicio'];
            $horas           = $row['horas'];
            $alta            = $row['alta'];

            $productosSocio[] = [
                'id'              => $id,
                'id_servicio'     => $id_servicio,
                'nro_servicio'    => $nro_servicio,
                'nombre_servicio' => $nombre_servicio,
                'horas'           => $horas,
                'total_importe'   => obtener_total_importe($id)['total_importe'],
                'cod_promo'       => $cod_promo,
                'alta'            => $alta,
            ];
        }


        for ($i = 0; $i < count($productosSocio); $i++) {
            $numero_servicio                   = $productosSocio[$i]['nro_servicio'];
            $rTotalHoras                       = obtener_suma_horas($cedulaSocio, $numero_servicio);
            $productosSocio[$i]['total_horas'] = mysqli_fetch_assoc($rTotalHoras)['horas'];
        }


        $response['localidad']                     = $localidad;
        $response['fecha_nacimiento']              = $fecha_nacimiento;
        $response['cedula']                        = $cedula_cliente;
        $response['nombre_cliente']                = $nombre_cliente;
        $response['productos']                     = $productosSocio;
        $response['total']                         = $total;
        $response['totalDtoCompetencia']           = 0; //compe
        $response['dtoCompetencia']                = 0.10; //compe
        $response['totalDtoCompetenciaVeintitres'] = 0; //compe
        $response['dtoCompetenciaVeintitres']      = 0.50; //compe
        $response['aplicaPromoCr']                 = false; //conva
        $response["result"]                        = true;
        $response["sesion"]                        = true;
        $response['omt']                           = false;
        $response['datosOmt']                      = null;
    } else {
        $response["result"]  = false;
        $response["sesion"]  = true;
        $response["message"] = 'Ocurrió un error en la consulta a la base de datos';
    }
}



echo json_encode($response);




function obtener_productos_resumen($cedula)
{
    require "../../../_conexion.php";

    $where = "pd.servicio NOT IN (08, 110) AND";

    $sql = "SELECT
             pd.id, 
             pd.cedula, 
             pd.servicio, 
             pd.cod_promo, 
             s.nombre_servicio, 
             s.id as id_servicio, 
             sum(hora) as horas, 
             importe,
             pd.accion as accion, 
             ps.total_importe as total, 
             pd.cod_promo as promo, 
             ps.dato_extra, 
             ps.alta,
             ps.nombre,
             ps.sucursal,
             ps.fecha_nacimiento
            FROM 
             padron_producto_socio pd
             INNER JOIN servicios s ON pd.servicio = s.nro_servicio
             INNER JOIN padron_datos_socio ps ON ps.cedula = pd.cedula
            WHERE 
            $where
             pd.cedula = '$cedula'
            GROUP BY 
             pd.accion,
             pd.servicio 
            ORDER BY pd.id ASC";
    $consulta = mysqli_query($mysqli, $sql);

    mysqli_close($mysqli);
    return $consulta;
}


function obtener_productos_editar($cedula)
{
    require "../../../_conexion.php";

    $where = "pd.servicio NOT IN (08, 110) AND";

    $sql = "SELECT 
             pd.id, 
             pd.servicio,
             ps.nombre, 
             pd.cedula, 
             pd.cod_promo, 
             s.nombre_servicio, 
             s.id as id_servicio, 
             sum(hora) as horas, 
             importe,
             ps.sucursal, 
             ps.fecha_nacimiento, 
             ps.alta
            FROM 
             padron_producto_socio pd
             INNER JOIN servicios s ON pd.servicio = s.nro_servicio
             INNER JOIN padron_datos_socio ps ON ps.cedula = pd.cedula
            WHERE 
             $where
             pd.cedula='$cedula' AND 
             pd.accion='1'
            GROUP BY pd.servicio 
            ORDER BY pd.id ASC";
    $consulta = mysqli_query($mysqli, $sql);

    mysqli_close($mysqli);
    return $consulta;
}


function obtener_localidad($sucursal)
{
    require "../../../_conexion.php";

    $sql = "SELECT id FROM filiales WHERE nro_filial = $sucursal";
    $consulta = mysqli_query($mysqli, $sql);

    mysqli_close($mysqli);
    return $consulta;
}


function obtener_suma_horas($cedula, $servicio)
{
    require "../../../_conexion.php";

    $where = "servicio NOT IN (08, 110) AND";

    $sql = "SELECT 
             SUM(hora) as horas 
            FROM 
             padron_producto_socio 
            WHERE 
             $where 
             accion = '3' AND 
             cedula = '$cedula' AND 
             servicio = '$servicio'";
    $consulta = mysqli_query($mysqli, $sql);

    mysqli_close($mysqli);
    return $consulta;
}


function obtener_total_importe($id)
{
    require "../../../_conexion.php";

    $sql = "SELECT SUM(importe) AS 'total_importe' FROM padron_producto_socio WHERE id = '$id' GROUP BY servicio";
    $consulta = mysqli_query($mysqli, $sql);

    $resultados = $consulta != false ? mysqli_fetch_assoc($consulta) : false;

    mysqli_close($mysqli);
    return $resultados;
}
