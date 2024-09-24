<?php
require_once "../../../_conexion.php";

$response["result"]  = false;
$response["session"] = true;


$fechaHistorico       = date('Y-m-d H:i');
$servicios            = $_POST["servicios"] ?? '';
$cedula               = $_POST["cedula"];
$observacionHistorico = $_POST['observacion'];
$idUser               = $_POST['idUser'];
$id                   = $_POST['id_socio'];
$historico            = false;


foreach ($servicios as $datos) {
    $nro_servicio = $datos['nro_servicio'];


    if ($datos['accion'] == 1) {

        $rDatos = obtener_productos_actuales($cedula, $nro_servicio);
        if (!$rDatos) {
            $response["result"]  = false;
            $response["message"] = 'Error al obtener los datos';
            die(json_encode($response));
        }


        while ($row = mysqli_fetch_assoc($rDatos)) {
            $fecha_registro         = $row['fecha_registro'];
            $numero_contrato        = $row['numero_contrato'];
            $fecha_afiliacion       = $row['fecha_afiliacion'];
            $nombre_vendedor        = $row['nombre_vendedor'];
            $lugar_venta            = $row['lugar_venta'];
            $vendedor_independiente = $row['vendedor_independiente'];
            $activo                 = $row['activo'];
            $movimiento             = 'ALTA';
            $fecha_inicio_derechos  = $row['fecha_inicio_derechos'];
            $numero_vendedor        = $row['numero_vendedor'];
            $promoactivo            = $row['promoactivo'];
            $tipo_de_cobro          = $row['tipo_de_cobro'];
            $tipo_iva               = $row['tipo_iva'];
            $idrelacion             = $row['idrelacion'];
            $codigo_precio          = $row['codigo_precio'];
            $aumento                = $row['aumento'];
            $empresa                = $row['empresa'];
            $nactual                = $row['nactual'];
            $servdecod              = $row['servdecod'];
            $count                  = $row['count'];
            $version                = $row['version'];
            $abm                    = $row['abm'];
            $abmactual              = $row['abmactual'];
            $usuario                = $row['usuario'];
            $usuariod               = $row['usuariod'];
            $extra                  = $row['extra'];
            $nomodifica             = $row['nomodifica'];
            $precioOriginal         = $row['precioOriginal'];
            $abitab                 = ($row['abitab'] == '') ? '0' : $row['abitab'];
            $id_padron              = $row['id_padron'];
            $accion                 = '1';
            $cedula_titular_gf      = $row['cedula_titular_gf'];
        }



        $servdecod   = in_array($nro_servicio, ["01", "02", "3"]) ? $nro_servicio . '8' : $nro_servicio;
        $indice_array = $datos['indice_array'];
        $precio_base = $datos['array_importes'][$indice_array];
        $observacion = $datos['observacion'];
        $cod_promo   = $datos['cod_promo'];
        $hrs         = (int)$datos['limit'];
        $cantHoras   = $hrs * 8;


        for ($j = 1; $j <= $hrs; $j++) {
            $query = "INSERT INTO padron_producto_socio SET
            id = NULL,
            cedula = '$cedula',
            servicio = '$nro_servicio',
            hora = '8',
            importe = '$precio_base',
            cod_promo = '$cod_promo',
            fecha_registro = '$fecha_afiliacion',
            numero_contrato = '0',
            fecha_afiliacion = '$fecha_afiliacion',
            nombre_vendedor = '$nombre_vendedor',
            observaciones = '$observacion',
            lugar_venta = '$lugar_venta',
            vendedor_independiente = '$vendedor_independiente',
            activo = '$activo',
            movimiento = '$movimiento',
            fecha_inicio_derechos = '$fecha_inicio_derechos',
            numero_vendedor = '$numero_vendedor',
            keepprice1 = '$precio_base',
            promoactivo = '$promoactivo',
            tipo_de_cobro = '$tipo_de_cobro',
            tipo_iva = '$tipo_iva',
            idrelacion = '$idrelacion',
            codigo_precio = '$codigo_precio',
            aumento = '$aumento',
            empresa = '$empresa',
            nactual = '$nactual',
            servdecod = '$servdecod',
            count = '$count',
            `version` = '$version',
            abm = '$abm',
            abmactual = '$abmactual',
            usuario = '$usuario',
            usuariod = '$usuariod',
            extra = '$extra',
            nomodifica = '$nomodifica',
            precioOriginal = '$precioOriginal',
            abitab = '$abitab',
            id_padron = '$id_padron',
            accion = '$accion',
            cedula_titular_gf = '$cedula_titular_gf'";
            $rQuery = mysqli_query($mysqli, $query);
        }

        if (!$rQuery) {
            $response["result"]  = false;
            $response["message"] = 'Error al guardar los datos del producto';
            die(json_encode($response));
        }


        //Calculo el importe total
        $importe_total = sumar_array($datos['array_importes']);

        $qActualizar = modifico_importe_padron_socio($importe_total, $cedula);
        if (!$qActualizar) {
            $response["result"]  = false;
            $response["message"] = 'Error al actualizar el importe total';
            die(json_encode($response));
        }


        $qActualizarProductos = "UPDATE padron_producto_socio SET importe = '$precio_base' WHERE cedula = '$cedula' AND servicio = '$nro_servicio' AND accion = '1'";
        $rActualizarProductos = mysqli_query($mysqli, $qActualizarProductos);

        if (!$rActualizarProductos) {
            $response["result"]  = false;
            $response["message"] = 'Error al actualizar los productos';
            die(json_encode($response));
        }


        $tipoActualizacion   = "Incremento de $cantHoras hrs al servicio $nro_servicio";
        $historico           = true;
        $response["result"]  = true;
        $response["message"] = 'Datos procesados correctamente';
    }


    if ($datos['accion'] == 2) {
        $limit = $datos['limit'];

        //obtengo el id
        $qId = "SELECT id FROM padron_producto_socio WHERE cedula = '$cedula' AND servicio = '$nro_servicio' ORDER BY id DESC LIMIT $limit";
        $rId = mysqli_query($mysqli, $qId);
        if (!$rId) {
            $response["result"]  = false;
            $response["message"] = 'Error al obtener el producto de padrón';
            die(json_encode($response));
        }

        $cantHoras = mysqli_num_rows($rId) * 8;
        while ($row = mysqli_fetch_assoc($rId)) {
            $id = $row['id'];
            //actualizo la acción de ese servicio
            $qEliminar = "DELETE FROM padron_producto_socio WHERE id = $id";
            $rEliminar = mysqli_query($mysqli, $qEliminar);

            if (!$rEliminar) {
                $response["result"]  = false;
                $response["message"] = 'Error al eliminar el producto de padrón';
                die(json_encode($response));
            }
        }

        $indice_array = $datos['indice_array'];
        $nuevo_importe = $datos['array_importes'][$indice_array];

        $qActualizarProductos = "UPDATE padron_producto_socio SET importe = '$nuevo_importe' WHERE cedula = '$cedula' AND servicio = '$nro_servicio' AND accion = '1'";
        $rActualizarProductos = mysqli_query($mysqli, $qActualizarProductos);

        if (!$rActualizarProductos) {
            $response["result"]  = false;
            $response["message"] = 'Error al actualizar los productos';
            die(json_encode($response));
        }


        //calculo el total
        $importe_total = sumar_array($datos['array_importes']);


        //actualizo el total en el padron
        $qActualizar = modifico_importe_padron_socio($importe_total, $cedula);
        if (!$qActualizar) {
            $response["result"]  = false;
            $response["message"] = 'Error al actualizar el importe total';
            die(json_encode($response));
        }

        $tipoActualizacion   = "Reducción de $cantHoras hrs al servicio $nro_servicio";
        $historico           = true;
        $response["result"]  = true;
        $response["message"] = 'Datos procesados correctamente';
    }


    if ($datos['accion'] == 3 && $datos['nro_servicio'] == '01') {
        $cod_promo    = $datos['cod_promo'];
        $nro_servicio = $datos['nro_servicio'];

        $qActualizarProductos = "UPDATE padron_producto_socio SET cod_promo = '$cod_promo' WHERE cedula = '$cedula' AND servicio = '$nro_servicio' AND accion = '1'";
        $rActualizarProductos = mysqli_query($mysqli, $qActualizarProductos);

        if (!$rActualizarProductos) {
            $response["result"]  = false;
            $response["message"] = 'Error al actualizar los productos';
            die(json_encode($response));
        }


        $tipoActualizacion = "Se actualizo el servicio $nro_servicio a promo $cod_promo";
        $historico = true;
        $response["result"]  = true;
        $response["message"] = 'Datos procesados correctamente';
    }
}


if (!$historico) {
    $response["result_historico"]  = false;
    $response['message_historico'] = 'No se puede registrar el historico';
    die(json_encode($response));
}


$qEstado = "SELECT id, estado from padron_datos_socio WHERE cedula = '$cedula'";
$rEstado = mysqli_query($mysqli, $qEstado);
if ($rEstado) {
    $row = mysqli_fetch_assoc($rEstado);
    $id_socio = $row['id'];
    $idEstado = $row['estado'];
}


$rHistorico = registrar_historico_venta($idUser, $id_socio, $idEstado, $fechaHistorico, $tipoActualizacion);
if (!$rHistorico) {
    $response["result_historico"]  = false;
    $response['message_historico'] = 'Error al guardar el historico';
    die(json_encode($response));
}


$response["result_historico"]  = true;
$response['message'] = 'Historico guardado correctamente';




mysqli_close($mysqli);
echo json_encode($response);




function obtener_productos_actuales($cedula, $servicio)
{
    require "../../../_conexion.php";

    $sql = "SELECT * FROM padron_producto_socio WHERE cedula = '$cedula' AND servicio = '$servicio'";
    $consulta = mysqli_query($mysqli, $sql);

    mysqli_close($mysqli);
    return $consulta;
}


function modifico_importe_padron_socio($importe_total, $cedula)
{
    require "../../../_conexion.php";

    $sql = "UPDATE padron_datos_socio SET total_importe = '$importe_total' WHERE cedula = '$cedula'";
    $consulta = mysqli_query($mysqli, $sql);

    mysqli_close($mysqli);
    return $consulta;
}


function registrar_historico_venta($idUser, $id_socio, $idEstado, $fechaHistorico, $tipoActualizacion)
{
    require "../../../_conexion.php";

    $sql = "INSERT INTO historico_venta VALUES(null, $idUser, $id_socio, $idEstado, '$fechaHistorico', '$tipoActualizacion', 11)";
    $consulta = mysqli_query($mysqli, $sql);

    mysqli_close($mysqli);
    return $consulta;
}


function sumar_array($array_numeros)
{
    $total_suma = 0;
    foreach ($array_numeros as $numero) {
        $total_suma += $numero;
    }
    return $total_suma;
}
