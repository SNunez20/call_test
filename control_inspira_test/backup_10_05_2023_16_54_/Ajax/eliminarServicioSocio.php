<?php
require_once "../../_conexion250.php";
require_once "../../_conexion.php";
$response = array(
    "result"  => false,
    "session" => false,
);

$response["session"] = true;
$id                  = $_POST["id"] ?? '';

$qCedula = "SELECT cedula,servicio,fecha_registro FROM padron_producto_socio WHERE id = $id";
if ($rCedula = mysqli_query($mysqli250, $qCedula)) {
    $row = mysqli_fetch_assoc($rCedula);
    $cedula   = $row['cedula'];
    $fechareg = $row['fecha_registro'];
    $elimServ = $row['servicio'];

    $qEliminar = "DELETE FROM padron_producto_socio WHERE id = $id";

    if ($rEliminar = mysqli_query($mysqli250, $qEliminar)) {

        $qTotal = "SELECT sum(importe) FROM padron_producto_socio WHERE cedula='$cedula'";

        if ($rTotal = mysqli_query($mysqli250, $qTotal)) {
            $total       = mysqli_fetch_array($rTotal)[0];
            $qActualizar = "UPDATE padron_datos_socio SET total_importe=$total WHERE cedula=$cedula";

            if ($rAct = mysqli_query($mysqli250, $qActualizar)) {

                $response["result"]  = true;
                $response["sesion"]  = true;
                $response["message"] = 'Datos eliminados correctamente';

                $qIdServicio = "SELECT id FROM servicios WHERE nro_servicio='$elimServ'";
                $rIdServ= mysqli_query($mysqli, $qIdServicio);
                $idServicio = mysqli_fetch_assoc($rIdServ)['id'];

                #buscamos el id de ese socio en piscina
                $qIdPiscina = "SELECT id FROM padron_datos_socio WHERE cedula = '$cedula'";
                $rId= mysqli_query($mysqli, $qIdPiscina);
                if ($rId && mysqli_num_rows($rId)>0) {
                    $idPiscina = mysqli_fetch_assoc($rId)['id'];

                    #buscamos el id del ultimo historico
                    $qIdHistorico = "SELECT id FROM historico_reportes WHERE id_cliente = $idPiscina AND fecha_carga='$fechareg' ORDER BY id DESC LIMIT 1";
                    $rIdHistorico = mysqli_query($mysqli,$qIdHistorico);
                        if ($rIdHistorico && mysqli_num_rows($rIdHistorico)>0) {
                            $idHistorico = mysqli_fetch_assoc($rIdHistorico)['id'];

                            $qEliminarServ = "DELETE FROM historico_reportes_servicios WHERE id_historico_reporte = $idHistorico AND id_servicio=$idServicio";
                            mysqli_query($mysqli, $qEliminarServ);
                        
                            #calculamos el total del incremento (en tal caso de ser incremento)
                            $qTotalIncremento ="SELECT sum(importe) AS total FROM padron_producto_socio WHERE cedula ='$cedula' AND fecha_registro = '$fechareg'";
                        
                            if ($rTotalInc= mysqli_query($mysqli250,$qTotalIncremento)) {
                            $totalIncremento= mysqli_fetch_assoc($rTotalInc)['total'];
                            }

                            #actualizamos el total importe del historico
                            $qActTotalReport = "UPDATE historico_reportes SET total_importe = '$total' WHERE id=$idHistorico";
                            mysqli_query($mysqli,$qActTotalReport);
                        

                            #actualizamos el total del incremento si se da el caso
                            $qActTotalIncReport = "UPDATE historico_reportes SET total_incremento = '$totalIncremento' WHERE id_cliente = $idPiscina AND fecha_carga = '$fechareg'";
                            mysqli_query($mysqli,$qActTotalIncReport);

                            #calculamos el total del incremento (en tal caso de ser incremento)
                            $qTotalIncremento ="SELECT sum(importe) AS total FROM padron_producto_socio WHERE cedula ='$cedula' AND fecha_registro = '$fechareg'";
                            if ($rTotalInc= mysqli_query($mysqli250,$qTotalIncremento)) {
                            $totalIncremento= mysqli_fetch_assoc($rTotalInc)['total'];
                            }


                            #calculamos el total de cada servicio
                            $qTotalImporteServicio ="SELECT IFNULL(sum(importe),0) AS total, servicio, sum(hora) AS horas FROM padron_producto_socio WHERE cedula ='$cedula' AND fecha_registro = '$fechareg' GROUP BY servicio";

                            if ($rTotalImporteServicio= mysqli_query($mysqli250, $qTotalImporteServicio)) {
                                if (mysqli_num_rows($rTotalImporteServicio)>0) {
                                    while ($row = mysqli_fetch_assoc($rTotalImporteServicio)) {
                                        $nroServ = $row['servicio'];
                                        $totalImporteServicio = $row['total'];
                                        $horas = $row['horas'];
    
                                        $qIdServ ="SELECT id FROM servicios WHERE nro_servicio = '$nroServ'";
                                        $rIdServ = mysqli_query($mysqli, $qIdServ);
                                        $idServ = mysqli_fetch_assoc($rIdServ)['id'];
                                    
    
                                        if ($totalImporteServicio==0) {
                                          
                                            $qEliminar = "DELETE FROM historico_reportes_servicios WHERE id_servicio = $idServ AND id_historico_reporte = $idHistorico";
                                      
                                            mysqli_query($mysqli, $qEliminar);
    
                                            #verificamos si existe otro servicio registrado en el mismo historico
                                            $qVerificarServicio = "SELECT id FROM historico_reportes_servicios WHERE id_historico_reportes = $idHistorico";
                                            $rVericicar = mysqli_query($mysqli, $qVerificarServicio);
                                         
                                            if ($rVerificar && mysqli_num_rows($rVerificar)==0) {
                                                $qEliminar = "DELETE FROM historico_reportes WHERE id = $idHistorico";
                                      
                                                mysqli_query($mysqli, $qEliminar);
                                            }
    
                                        }else{
                                        
                                            $qActualizar = "UPDATE historico_reportes_servicios SET importe='$totalImporteServicio', horas= '$horas' WHERE id_servicio = $idServ AND id_historico_reporte = $idHistorico";
                                         
                                            mysqli_query($mysqli, $qActualizar);
                                        }
    
    
                                    }
                                }else{
                                
                                    $qEliminar = "DELETE FROM historico_reportes WHERE id = $idHistorico";
                                    mysqli_query($mysqli, $qEliminar);
                                    $qEliminar = "DELETE FROM historico_reportes_servicios WHERE id_historico_reporte = $idHistorico";
                                    mysqli_query($mysqli, $qEliminar);

                                }
                        
                                
                                                       
                            }
                        }
                }
            }
        }
    } else {
        $response["result"]  = false;
        $response["sesion"]  = true;
        $response["message"] = 'Ocurrio un error al eliminar';
    }
} else {
    $response["result"]  = false;
    $response["sesion"]  = true;
    $response["message"] = 'Ocurrio un error al consultar la cedula';
}

mysqli_close($mysqli250);
mysqli_close($mysqli);
echo json_encode($response);
