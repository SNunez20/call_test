<?php
session_start();
require_once "../../_conexion250.php";
require_once "../../_conexion.php";
$response = array('result' => false, 'message' => 'Ha ocurrido un error');

if ( isset($_POST['typeAdmin']) ) {
    $data            = array_map(fn($data) => strip_tags(mysqli_real_escape_string($mysqli250, $data)), $_POST);
    $id              = $data['id'];
    $servicio        = $data['servicio'];
    $horas           = $data['horas'];
    $importe         = $data['importe'];
    $codpromo        = $data['codpromo'];
    $abm             = $data['abm'];
    $observacion     = $data['observacion'];
    $fechafil        = $data['fechafil'];
    $fechareg        = $data['fechareg'];
    $numero_vendedor = $data['numero_vendedor'];
    $cedula_socio    = $data['cedula'];
    $keepprice       = $data['keepprice'];
   

    $query = "UPDATE padron_producto_socio SET servicio = '$servicio', hora = '$horas', importe = '$importe', fecha_registro ='$fechareg', fecha_afiliacion = '$fechafil', cod_promo = '$codpromo', abm = '$abm', observaciones = '$observacion', numero_vendedor = '$numero_vendedor',keepprice1=$keepprice WHERE id = $id";

    if ($result= mysqli_query($mysqli250, $query)) {

        $qTotal = "SELECT sum(importe) AS total FROM padron_producto_socio WHERE cedula ='$cedula_socio'";
        if ($rAct= mysqli_query($mysqli250, $qTotal)) {
            $row =mysqli_fetch_assoc($rAct);
            $totalImporte = $row['total'];
        }

        $qTotal = "UPDATE padron_datos_socio SET total_importe = $totalImporte WHERE cedula = $cedula_socio";
        if ($rTotal= mysqli_query($mysqli250, $qTotal)) {
            $response = array('result' => true, 'message' => 'Datos actualizados correctamente');
        }

        #buscamos el id de ese socio en piscina
        $qIdPiscina = "SELECT id FROM padron_datos_socio WHERE cedula = '$cedula_socio'";
        $rId= mysqli_query($mysqli, $qIdPiscina);
        if ($rId && mysqli_num_rows($rId)>0) {
            $idPiscina = mysqli_fetch_assoc($rId)['id'];

            #buscamos el id del ultimo historico
            $qIdHistorico = "SELECT id FROM historico_reportes WHERE id_cliente = $idPiscina ORDER BY id DESC LIMIT 1";
            $rIdHistorico = mysqli_query($mysqli,$qIdHistorico);
            if ($rIdHistorico && mysqli_num_rows($rIdHistorico)>0) {
                $idHistorico = mysqli_fetch_assoc($rIdHistorico)['id'];

 
                #calculamos el total del incremento (en tal caso de ser incremento)
                $qTotalIncremento ="SELECT sum(importe) AS total FROM padron_producto_socio WHERE cedula ='$cedula_socio' AND fecha_registro = '$fechareg'";
            
                if ($rTotalInc= mysqli_query($mysqli250,$qTotalIncremento)) {
                   $totalIncremento= mysqli_fetch_assoc($rTotalInc)['total'];
                }

                #actualizamos el total importe del historico
                $qActTotalReport = "UPDATE historico_reportes SET total_importe = '$totalImporte' WHERE id=$idHistorico";
                mysqli_query($mysqli,$qActTotalReport);
             

                #actualizamos el total del incremento si se da el caso
                $qActTotalIncReport = "UPDATE historico_reportes SET total_incremento = '$totalIncremento' WHERE id_cliente = $idPiscina AND fecha_carga = '$fechareg'";
                mysqli_query($mysqli,$qActTotalIncReport);

                #calculamos el total del incremento (en tal caso de ser incremento)
                $qTotalIncremento ="SELECT sum(importe) AS total FROM padron_producto_socio WHERE cedula ='$cedula_socio' AND fecha_registro = '$fechareg'";
                if ($rTotalInc= mysqli_query($mysqli250,$qTotalIncremento)) {
                   $totalIncremento= mysqli_fetch_assoc($rTotalInc)['total'];
                }


                #calculamos el total de cada servicio
                $qTotalImporteServicio ="SELECT IFNULL(sum(importe),0) AS total, servicio, sum(hora) AS horas FROM padron_producto_socio WHERE cedula ='$cedula_socio' AND fecha_registro = '$fechareg' GROUP BY servicio";

                if ($rTotalImporteServicio= mysqli_query($mysqli250, $qTotalImporteServicio)) {
               
                    while ($row = mysqli_fetch_assoc($rTotalImporteServicio)) {
                        $nroServ = $row['servicio'];
                        $totalImporteServicio = $row['total'];
                        $horas = $row['horas'];

                        $qIdServ ="SELECT id FROM servicios WHERE nro_servicio = '$nroServ'";
                        $rIdServ = mysqli_query($mysqli, $qIdServ);
                        $idServ = mysqli_fetch_assoc($rIdServ)['id'];

                        $qActualizar = "UPDATE historico_reportes_servicios SET importe='$totalImporteServicio', horas= '$horas' WHERE id_servicio = $idServ AND id_historico_reporte = $idHistorico";
                    //    var_dump( $qActualizar); exit;
                        mysqli_query($mysqli, $qActualizar);
                      

                    }
                  
                   
                }
            }
        }


    }else{
        // var_dump(mysqli_error($mysqli250));
        $response = array('message' => 'Fallo la actualización');
    }
}

mysqli_close($mysqli250);
mysqli_close($mysqli);
echo json_encode($response);
