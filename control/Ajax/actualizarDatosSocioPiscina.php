<?php
session_start();
require_once "../../_conexion.php";
require_once "../../_conexion250.php";
require_once "../../logger.php";
require_once "functions.php";
$response = array('result' => false, 'message' => 'Ha ocurrido un error');

if ( isset($_POST['typeAdmin']) ) {
    $data      = array_map(fn($data) => strip_tags(mysqli_real_escape_string($mysqli, $data)), $_POST);
    $id        = $data['id_socio'];
    $cedula    = $data['cedula'];
    $id_socio  = $data['id_socio'];
    $nombre    = strtoupper($data['nombre']);
    $direccion = strtoupper($data['direccion']);
    $telefono  = $data['telefono'];
    $sucursal  = $data['sucursal'];
    $fecha_nacimiento = $data['fecha_nacimiento'];
    $radio     = $data['radio'];
    $ruta      = $data['ruta'];
    $numtar    = $data['numtar'];
    $nomtit    = strtoupper($data['nomtit']);
    $cedtit    = $data['cedtit'];
    $fechafil  = $data['fechafil'];
    $estado     = $data['estado'];
    $origenVenta  = $data['origenVenta'];
    $metodoPago = $data['metodoPago'];
    $localidad = $data['localidad'];
    $idUser     = $data['idUser'];
    $observaciones = strtoupper($data['observaciones']);
    $fecha      = date('Y-m-d H:i');
    $log_error = "../../logs/errors_panel/error_$fecha.log";
 
    $cedula_old = getCedulaSocio($id);

    if ($cedula!=$cedula_old && validarExisteEnPadron($cedula)) {
        $response['message'] = 'La cédula ingresada pertenece a un socio.';

    }else if ($cedula!=$cedula_old && validarExisteEnPiscina($cedula)) {
        $response['message'] = 'La cédula ingresada ya se encuentra en piscina.';

    }else{
        
    
       // Obtengo el rut de la empresa segun la filial
        $qERut       = "select empresa_rut from aux2 where num_sucursal = $sucursal limit 1";
        $rERut       = mysqli_query($mysqli250, $qERut);
        $empresarut = mysqli_fetch_assoc($rERut)['empresa_rut'];

        $qEmarca       = "select empresa_brand from aux2 where num_sucursal = $sucursal";
        $rEmarca       = mysqli_query($mysqli250, $qEmarca);
        $empresamarca = mysqli_fetch_assoc($rEmarca)['empresa_brand'];
            
        if ($rERut && mysqli_num_rows($rERut)>0) {
                
            if ($metodoPago == '1') {
                $sucursal              = ($sucursal == '18' && $localidad == 140) ? '3' : $sucursal;
                $radio                 = $sucursal;
                $sucursal_cobranza_num = $sucursal;
                $medio_valido          = 1;
                $rutcentralizado       = $empresarut;
            } else if ($metodoPago == '2') {
        
                // Obtener el radio del metodo de pago. NOTA las tarjetas MASTER con bin 504736 se les cambiara el radio al que corresponda segun la tabla de radios
                // $bin                   = substr($numtar, 0, 6);
                // $qMedio                =  (strtolower($tipoTarjeta) == 'master' && ($bin == '504736' || $bin == '589657')) ? "SELECT radio FROM radios_tarjetas WHERE bin like '%$bin%'" : "SELECT radio FROM radios_tarjetas WHERE nombre_vida LIKE '%$tipoTarjeta%'";
                // $rMedio                = mysqli_query($mysqli, $qMedio);
                // $row                   = mysqli_fetch_assoc($rMedio);
                // $radio                 = $row['radio'];
                // $medio_valido          = mysqli_num_rows($rMedio);
                $sucursal_cobranza_num = '99';
                $empresamarca         = '99';
                $rutcentralizado       = '99';
            } 

            $qDatos = "SELECT alta, estado, accion FROM padron_datos_socio WHERE id = $id";
            $rDatos= mysqli_query($mysqli, $qDatos);
        
            if ($rDatos && mysqli_num_rows($rDatos)>0) {
                $row  = mysqli_fetch_assoc($rDatos);
                $alta = $row['alta'];
                $estadoAnterior  = $row['estado'];
                $accionAnterior = $row['accion'];
            }
        
            $devolverAlPanel = ($estadoAnterior =='6' && ($estado =='3' || $estado =='8' || $estado=='2'));
            if ($devolverAlPanel && $alta =='1') {
                $accion = 1;
            }else if($devolverAlPanel && $alta =='0'){
                $accion = 4;
            }else{
                $accion = $accionAnterior;
            }
        
            $query = "UPDATE padron_datos_socio SET cedula = '$cedula', nombre = '$nombre', direccion = '$direccion', tel = '$telefono', sucursal = '$sucursal', radio = '$radio', fecha_nacimiento = '$fecha_nacimiento', ruta = '$ruta', numero_tarjeta = '$numtar', nombre_titular = '$nomtit', cedula_titular = '$cedtit',fechafil ='$fechafil', observaciones = '$observaciones', estado = $estado, origen_venta = '$origenVenta', accion=$accion, idrelacion='$empresarut-$cedula', empresa_rut='$empresarut', empresa_marca='$empresamarca' WHERE id = '$id_socio'";
            $result3= mysqli_query($mysqli, $query);

            if ($result3) {

                if ($cedula != $cedula_old) {
                    $qUpTitular = "UPDATE padron_producto_socio SET cedula_titular_gf = '$cedula'  WHERE cedula_titular_gf = '$cedula_old'";
                    $rUpTitular= mysqli_query($mysqli, $qUpTitular);
                }
        
                $query2 = ($devolverAlPanel) ? "UPDATE padron_producto_socio SET cedula='$cedula',idrelacion='$empresarut-$cedula', accion='1' WHERE cedula = '$cedula_old' AND abm IN ('ALTA','ALTA-PRODUCTO')" : "UPDATE padron_producto_socio SET cedula='$cedula',idrelacion='$empresarut-$cedula'  WHERE cedula = '$cedula_old'";
                $result4 = mysqli_query($mysqli,$query2);

                if ($result4) {

                    $query3 = ($devolverAlPanel) ? "UPDATE padron_producto_socio SET cedula='$cedula',idrelacion='$empresarut-$cedula', accion='3' WHERE cedula = '$cedula_old' AND abm NOT IN ('ALTA','ALTA-PRODUCTO')" : "UPDATE padron_producto_socio SET cedula='$cedula',idrelacion='$empresarut-$cedula'  WHERE cedula = '$cedula_old'";
                    $result5 = mysqli_query($mysqli,$query3);
            
                    if ($result5 && $devolverAlPanel) {
                    $qIdHistorico = "SELECT id FROM historico_reportes WHERE id_cliente = $id_socio ORDER BY id DESC LIMIT 1";
                    $rIdHistorico = mysqli_query($mysqli,$qIdHistorico);
                        if ($rIdHistorico && mysqli_num_rows($rIdHistorico)>0) {
                        $idHistorico = mysqli_fetch_assoc($rIdHistorico)['id'];
                        mysqli_query($mysqli,"DELETE FROM historico_reportes WHERE id = $idHistorico");
                        mysqli_query($mysqli,"DELETE FROM historico_reportes_servicios WHERE id_historico_reporte = $idHistorico");
                        $response = array('result' => true, 'message' => 'Datos actualizados correctamente','cedula' =>$cedula);
                        }
                    }else if(!$result5){
                        $log_content = "[ERROR][$fecha]|ERROR AL ACTUALIZAR PADRON PRODUCTOS EN PISCINA | QUERY: $query3 | ERROR: " . mysqli_error($mysqli).PHP_EOL;
                        file_put_contents($log_error, $log_content, FILE_APPEND);
                    }else{

                        if ($estado == 672) {
                            mysqli_query($mysqli, "DELETE FROM comprobantes_competencia WHERE cedula = '$cedula'");
                            $qHistorico = "INSERT INTO historico_venta VALUES(null,$idUser,$id_socio,683,'$fecha','DEVUELTO A CALIDAD',11)";
                            mysqli_query($mysqli, $qHistorico);

                            $qCodigosPromo = "SELECT id,servicio, cod_promo FROM padron_producto_socio WHERE cedula = '$cedula' AND servicio IN ('01') AND cod_promo NOT IN ('35','2035')";
                            $rCodigos = mysqli_query($mysqli,$qCodigosPromo);
                            if ($rCodigos && mysqli_num_rows($rCodigos)>0) {
                                while ($row = mysqli_fetch_assoc($rCodigos)) {
                                    $idServi  = $row['id'];
                                    $codPromo = $row['cod_promo'];
                                    $codigoUpdated = $codPromo.'35';

                                    $qUpdateCodigos = "UPDATE padron_producto_socio SET cod_promo = $codigoUpdated WHERE id = $idServi";
                                    $rUpdate = mysqli_query($mysqli,$qUpdateCodigos);
                                    
                                }
                            }
                        }

                        $qHistorico = "INSERT INTO historico_venta VALUES(null,$idUser,$id_socio,$estado,'$fecha','ACTUALIZACION DE DATOS',11)";
                        if ($rHistorico = mysqli_query($mysqli, $qHistorico)) {
                            $id_historico = mysqli_insert_id($mysqli);
                            $response['historico'] = 'Historico guardado correctamente';
                        }

                        $response = array('result' => true, 'message' => 'Datos actualizados correctamente','cedula' => $cedula, 'historico' =>$rHistorico);
                    
                    }
                }else{
                    $log_content = "[ERROR][$fecha]|ERROR AL ACTUALIZAR PADRON PRODUCTOS EN PISCINA | QUERY: $query2 | ERROR: " . mysqli_error($mysqli).PHP_EOL;
                    file_put_contents($log_error, $log_content, FILE_APPEND);
                }
            
            }else{
                $log_content = "[ERROR][$fecha]|ERROR AL ACTUALIZAR DATOS EN PISCINA| QUERY: $query | ERROR: " . mysqli_error($mysqli).PHP_EOL;
                file_put_contents($log_error, $log_content, FILE_APPEND);
            }
        }
    }
}
    


function getCedulaSocio($id){
    
    global $mysqli;

    $qCedula = "SELECT cedula FROM padron_datos_socio WHERE id = $id";

    $result1 = mysqli_query ($mysqli,$qCedula) or die(mysqli_error($mysqli));
    while($row = mysqli_fetch_array($result1)){
        $cedula_old      = $row['cedula'];
    }

    return $cedula_old;
}


function getEstadoSocioPiscina($id){
    
    global $mysqli;

    $qCedula = "SELECT estado FROM padron_datos_socio WHERE id = $id";

    $result1 = mysqli_query ($mysqli,$qCedula) or die(mysqli_error($mysqli));
    while($row = mysqli_fetch_array($result1)){
        $estado      = $row['estado'];
    }

    return $estado;
}

mysqli_close($mysqli);
mysqli_close($mysqli250);
echo json_encode($response);
