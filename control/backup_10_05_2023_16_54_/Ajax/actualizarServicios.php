<?php
require_once "../../_conexion.php";

$response = array(
    "result"  => false,
    "session" => true,
);

//idProducto, hrServicio, nro_servicio, precio base, precio servicio, total con base, promo, observacion, accion, limit
$servicios = $_POST["servicios"] ?? '';
$cedula    = $_POST["cedula"];
$fechaHistorico = date('Y-m-d H:i');
$observacionHistorico = $_POST['observacion'];
$idUser = $_POST['idUser'];
$id = $_POST['id_socio'];
$historico = false;

for ($i = 0; $i < sizeof($servicios); $i++) {
   
    $nro_servicio = $servicios[$i][2];
    if ($servicios[$i][8] == 1) {

        $qDatos = "SELECT * FROM padron_producto_socio WHERE servicio = '$nro_servicio' AND cedula='$cedula'";

        if ($rDatos = mysqli_query($mysqli, $qDatos)) {
         
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

            $servdecod   = ($nro_servicio == '01' || $nro_servicio == '02' || $nro_servicio == '3') ? $nro_servicio . '8' : $nro_servicio;
            $precio_base = ($nro_servicio == '01') ? $servicios[$i][4] : $servicios[$i][4];
            $observacion = $servicios[$i][4];
            $cod_promo= $servicios[$i][6];
            $hrs  = (int)$servicios[$i][9];
            $cantHoras = $hrs * 8;
     

            for ($j=1; $j <= $hrs ; $j++) { 
                $query = "INSERT INTO padron_producto_socio VALUES(null,'$cedula','$nro_servicio','8','$precio_base','$cod_promo','$fecha_afiliacion','0','$fecha_afiliacion','$nombre_vendedor','$observacion','$lugar_venta',$vendedor_independiente,$activo,'$movimiento',";
                $query .= "'$fecha_inicio_derechos','$numero_vendedor',$precio_base,'$promoactivo',$tipo_de_cobro,$tipo_iva,'$idrelacion',$codigo_precio,'$aumento','$empresa',$nactual,'$servdecod','$count','$version','$abm','$abmactual','$usuario','$usuariod','$extra','$nomodifica', '$precioOriginal','$abitab','$id_padron','$accion','$cedula_titular_gf')";

                $rQuery = (mysqli_query($mysqli, $query)) ? true :false;
            }
           
            if ($rQuery) {

                $qTotal = "SELECT sum(importe) FROM padron_producto_socio WHERE  cedula='$cedula'";

                if ($rTotal = mysqli_query($mysqli, $qTotal)) {
                    $total       = mysqli_fetch_array($rTotal)[0];
                    $qActualizar = "UPDATE padron_datos_socio SET total_importe=$total WHERE cedula=$cedula";

                    if ($rAct = mysqli_query($mysqli, $qActualizar)) {
                        $tipoActualizacion   = "Incremento de $cantHoras hrs al servicio $nro_servicio";
                        $historico           = true;
                        $response["result"]  = true;
                        $response["message"] = 'Datos procesados correctamente';
                    } else {
                        $response["result"]  = false;
                        $response["message"] = 'Error al actualizar el importe total';
                    }
                }
            } else {
                $response["result"]  = false;
                $response["message"] = 'Error al guardar los datos del producto';
            }
        } else {
            $response["result"]  = false;
            $response["message"] = 'Error al obtener los datos';
        }

    } else if ($servicios[$i][8] == 2) {
        $nro_servicio = $servicios[$i][2];
        $limit        = $servicios[$i][9];
        //obtengo el id
        $qId = "SELECT id FROM padron_producto_socio WHERE cedula='$cedula' AND  servicio = '$nro_servicio' ORDER BY id DESC LIMIT $limit";

        if ($rId = mysqli_query($mysqli, $qId)) {
            $cantHoras = mysqli_num_rows($rId) * 8;
            while ($row = mysqli_fetch_assoc($rId)) {
                $id = $row['id'];
                //actualizo la accion de ese servicio
                $qEliminar = "DELETE FROM padron_producto_socio WHERE id =$id";
                mysqli_query($mysqli, $qEliminar);
            }

            //calculo el total
            $qTotal = "SELECT sum(importe) FROM padron_producto_socio WHERE  cedula='$cedula'";

            if ($rTotal = mysqli_query($mysqli, $qTotal)) {
                $total = mysqli_fetch_array($rTotal)[0];

                //actualizo el total en el padron
                $qActualizar = "UPDATE padron_datos_socio SET total_importe=$total WHERE cedula=$cedula";

                if ($rAct = mysqli_query($mysqli, $qActualizar)) {
                    $tipoActualizacion   = "Reducción de $cantHoras hrs al servicio $nro_servicio";
                    $historico           = true;
                    $response["result"]  = true;
                    $response["message"] = 'Datos procesados correctamente';
                } else {
                    $response["result"]  = false;
                    $response["message"] = 'Error al actualizar el importe total';
                }
            }

        } else {
            $response["result"]  = false;
            $response["message"] = 'Error al eliminar producto del padron';
        }
    }else if($servicios[$i][8] == 3 && $servicios[$i][2]=='01'){
     
        $cod_promo    = $servicios[$i][6];
        $nro_servicio = $servicios[$i][2];
    
        $qActualizarProductos = "UPDATE padron_producto_socio SET cod_promo='$cod_promo' WHERE cedula='$cedula' AND servicio='$nro_servicio' AND accion='1'";

            if (mysqli_query($mysqli, $qActualizarProductos)) {
                $tipoActualizacion = "Se actualizo el servicio $nro_servicio a promo $cod_promo";
                $historico = true;
                $response["result"]  = true;
                $response["message"] = 'Datos procesados correctamente';
            } else {
                $response["result"]  = false;
                $response["message"] = 'Error al actualizar los productos';
            }
        
    }

}

if ($historico) {
    $qEstado = "SELECT id, estado from padron_datos_socio WHERE cedula = '$cedula'";
    if ($rEstado = mysqli_query($mysqli, $qEstado)) {
        $row = mysqli_fetch_assoc($rEstado);
        $id_socio = $row['id'];
        $idEstado = $row['estado'];
    }
    $qHistorico = "INSERT INTO historico_venta VALUES(null,$idUser,$id_socio,$idEstado,'$fechaHistorico','$tipoActualizacion',11)";
                
    if (mysqli_query($mysqli, $qHistorico)) {
        $response["result_historico"]  = true;
        $response['message'] = 'Historico guardado correctamente';
    } else {
        $response["result_historico"]  = false;
        $response['message_historico'] = 'Error al guardar el historico';
    }
}else{
    $response["result_historico"]  = false;
    $response['message_historico'] = 'Error al guardar el historico';
}

mysqli_close($mysqli);
echo json_encode($response);
