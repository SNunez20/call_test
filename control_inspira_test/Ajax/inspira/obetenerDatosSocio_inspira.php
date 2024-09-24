<?php

$response['result']       = false;
$response['session']      = false;
$response['socio']        = [];
$response['filiales']     = [];
$response['metodos_pago'] = [];


if (isset($_POST["typeAdmin"])) {
    $response["session"] = true;
    $id                  = $_POST["id"] ?? '';
    $esRechazoComp       = false; //compe


    $result = obtener_datos_padron($id);

    if ($result) {
        $response["socio"] = mysqli_fetch_assoc($result);

        // Rutas
        $cedula    = $response["socio"]["cedula"];
        $radio     = $response["socio"]["radio"];
        $sucursal  = $response["socio"]["sucursal"];
        $idGrupo   = $response["socio"]["idgrupo"];
        $fechafil  = $response["socio"]["fechafil"];
        $localidad = $response["socio"]["localidad"];
        $fechaHoy  = date('Y-m-d');
        $es_alta   = $response["socio"]["alta"];


        if ($es_alta == '1') { //compe
            $rRechazo = comprobar_rechazo_comp($id, $fechafil, $fechaHoy);
            if ($rRechazo && mysqli_num_rows($rRechazo) > 0)
                $esRechazoComp = true;
        }


        $response['socio']['esRechazoComp'] = $esRechazoComp; //compe
        $response["socio"]['correspondeVidapesos'] = false;
        $response["socio"]['promoVuelveAntes'] = false;
        $response['socio']['convenioEspecial'] = false;
        $response['socio']['promoMadre'] = false;
        $response['socio']['promoVISA'] = false;
        $response['socio']['promoFloreada'] = false;


        //Títular de grupo familiar
        $rGF = obtener_titular_grupo_familiar($cedula);
        $cedula_titular_gf = mysqli_num_rows($rGF) > 0 ? mysqli_fetch_assoc($rGF)['cedula_titular_gf'] : '';
        $response['socio']['cedula_titular_gf'] = $cedula_titular_gf;

        //Rutas
        $result = obtener_rutas();
        $response["socio"]["rutas"] = mysqli_num_rows($result) > 0 ? mysqli_fetch_all($result, MYSQLI_NUM) : '';

        // Sucursal
        $result = obtener_sucursal($response["socio"]["sucursal"]);
        $data = mysqli_fetch_assoc($result);
        $id_filial = $data["id"];
        $response["socio"]["sucursal"] = $data["nombre_filial"];

        //Nombre de localidad
        $rLoc = obtener_nombre_localidad($response["socio"]["localidad"]);
        $loc = mysqli_fetch_assoc($rLoc)['nombre'];
        $response["socio"]["localidad"] = $loc;

        // Filiales
        $result = obtener_filiales();
        $response["filiales"] = mysqli_fetch_all($result, MYSQLI_NUM);

        // Bancos
        $result = obtener_bancos_emisores();
        $response["bancos"] = mysqli_fetch_all($result, MYSQLI_NUM);

        // Métodos de pago
        $result = obtener_metodos_pago();
        $response["metodos_pago"] = mysqli_fetch_all($result, MYSQLI_NUM);

        $response["result"] = true;
    }
}



die(json_encode($response, JSON_INVALID_UTF8_IGNORE));




function obtener_datos_padron($id)
{
    require "../../../_conexion.php";

    $sql = "SELECT 
             ps.nombre, 
             ps.cedula, 
             ps.tel, 
             ps.radio, 
             ps.localidad, 
             ps.sucursal, 
             ps.direccion, 
             ps.observaciones, 
             ps.fecha_nacimiento, 
             ps.numero_tarjeta, 
             ps.tarjeta, 
             ps.tipo_tarjeta, 
             ps.nombre_titular, 
             ps.cedula_titular, 
             ps.telefono_titular, 
             ps.anio_e, 
             ps.mes_e, 
             ps.cvv, 
             ps.cuotas_mercadopago, 
             ps.count, 
             ps.email, 
             ps.email_titular, 
             ps.total_importe, 
             ps.metodo_pago, 
             ps.banco_emisor, 
             b.banco, 
             u.idgrupo, 
             ps.fechafil, 
             ps.id as id_socio, 
             pp.numero_vendedor, 
             pp.nombre_vendedor, 
             ps.count, 
             ps.ruta, 
             ps.tarjeta_vida, 
             ps.id_usuario, 
             ps.alta, 
             ps.estado as id_estado, 
             e.estado, 
             ps.origen_venta
            FROM 
             padron_datos_socio ps 
             LEFT JOIN bancos_emisores b ON ps.banco_emisor = b.id
             INNER JOIN estados e ON ps.estado = e.id
             INNER JOIN metodos_pago mp ON ps.metodo_pago = mp.id
             INNER JOIN filiales f ON ps.sucursal = f.nro_filial
             INNER JOIN padron_producto_socio pp ON ps.cedula = pp.cedula
             LEFT JOIN usuarios u ON ps.id_usuario = u.id
            WHERE 
             ps.id = $id AND 
             pp.accion = '1' 
            GROUP BY 
             ps.id";
    $consulta = mysqli_query($mysqli, $sql);

    mysqli_close($mysqli);
    return $consulta;
}


function comprobar_rechazo_comp($id, $fechafil, $fechaHoy)
{
    require "../../../_conexion.php";

    $sql = "SELECT 
             id_estado 
            FROM 
             historico_venta 
            WHERE 
             id_cliente = $id AND 
             fecha >= '$fechafil' AND 
             fecha <='$fechaHoy' AND 
             id_estado = 675";
    $consulta = mysqli_query($mysqli, $sql);

    mysqli_close($mysqli);
    return $consulta;
}


function obtener_titular_grupo_familiar($cedula)
{
    require "../../../_conexion.php";

    $sql = "SELECT 
             cedula_titular_gf 
            FROM 
             padron_producto_socio 
            WHERE 
             cedula = '$cedula' AND 
             !ISNULL(cedula_titular_gf) AND 
             cedula_titular_gf != '' AND 
             cod_promo != 27";
    $consulta = mysqli_query($mysqli, $sql);

    mysqli_close($mysqli);
    return $consulta;
}


function obtener_rutas()
{
    require "../../../_conexion1310.php";

    $sql = "SELECT ruta FROM rutas_cobrador GROUP BY ruta";
    $consulta = mysqli_query($mysqli1310, $sql);

    mysqli_close($mysqli1310);
    return $consulta;
}


function obtener_sucursal($sucursal)
{
    require "../../../_conexion.php";

    $sql = "SELECT id, nombre_filial FROM filiales WHERE nro_filial = '$sucursal'";
    $consulta = mysqli_query($mysqli, $sql);

    mysqli_close($mysqli);
    return $consulta;
}


function obtener_nombre_localidad($id_localidad)
{
    require "../../../_conexion.php";

    $sql = "SELECT nombre from ciudades where id = '$id_localidad'";
    $consulta = mysqli_query($mysqli, $sql);

    mysqli_close($mysqli);
    return $consulta;
}


function obtener_filiales()
{
    require "../../../_conexion.php";

    $sql = "SELECT * FROM filiales WHERE nro_filial = '1372'";
    $consulta = mysqli_query($mysqli, $sql);

    mysqli_close($mysqli);
    return $consulta;
}


function obtener_bancos_emisores()
{
    require "../../../_conexion.php";

    $sql = "SELECT * FROM bancos_emisores";
    $consulta = mysqli_query($mysqli, $sql);

    mysqli_close($mysqli);
    return $consulta;
}


function obtener_metodos_pago()
{
    require "../../../_conexion1310.php";

    $sql = "SELECT * FROM tipo_medios_pago WHERE mostrar = 1 AND activo = 1";
    $consulta = mysqli_query($mysqli1310, $sql);

    mysqli_close($mysqli1310);
    return $consulta;
}
