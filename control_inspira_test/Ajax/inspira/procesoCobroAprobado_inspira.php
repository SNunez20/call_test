<?php
header('Access-Control-Allow-Origin: *');

require_once "../../../_conexion.php";
require_once "../../../_conexion250.php";
require_once "../../../_conexion1310.php";
require_once "../../../_conexion250_TOCS.php";
require_once "guardarPadron_inspira.php";
require_once "../functions.php";

$response['result'] = false;
$response['session'] = false;
$response['icon'] = "error";
$response['title'] = "Error";
$response['guardado_padron'] = false;
$response['datosVidaShop'] = [];
$response['preguntarEmail'] = false;
$response['registroVidaShop'] = false;


if (isset($_POST["typeAdmin"])) {
    $response["session"] = true;
    $data                = array_map(fn($data) => strip_tags(mysqli_real_escape_string($mysqli, $data)), $_POST);
    $id                  = $data['id'];
    $isMercadoPago       = $data['isMercadoPago'];
    $idUser              = $data['idUser'];
    $tipoAdmin           = $data['typeAdmin'];
    $cedulaAfiliado      = $data['cedulaAfiliado'];
    $observacionCobro    = strtoupper($data['observacionCobro']);
    $origen              = $data['origen'];
    $datosAdelantoCobro  = []; //compe
    $esPromoComp         = false; //compe
    logger("COBRO SOLICITADO PARA[CI]: $cedulaAfiliado ORIGEN $origen", false);
    $fecha = date('Y-m-d H:i');

    try {
        $query = "SELECT `alta` FROM padron_datos_socio WHERE id = $id";
        $result = mysqli_query($mysqli, $query);
    } catch (\Throwable $errores) {
        registrar_errores($query, "procesoCobroAprobado_inspira.php", $errores);
    }

    if ($result) {
        $esAlta      = mysqli_fetch_assoc($result)["alta"];
        $error       = false;
        $origenVenta = obtenerOrigenVenta($cedulaAfiliado); //web

        if ($esAlta == "1") {

            if (validarExisteEnPadron($cedulaAfiliado)) {
                $response['message'] = "La persona con cédula $cedulaAfiliado ya se encuentra en padron";
                $response['result']  = true;
                $error               = true;
            } else if (validarExistenBeneficiariosUdemmSura($cedulaAfiliado)) { //newform
                $response['message'] = "Debe ingresar los datos de los beneficiarios UDEMM";
                $response['result']  = true;
                $error               = true;
            } else if ($msj = validarBeneficiariosEnPadron($cedulaAfiliado)) {
                $response['message'] = $msj;
                $response['result']  = true;
                $error               = true;
            } else if (validarCamposVacios($cedulaAfiliado)) { //web
                $response['message'] = 'Debe llenar la dirección del socio, dando click en el boton "Ver datos" luego haga click en el botón "Ingresar direccion" y complete el formulario.';
                $response['result']  = true;
                $error               = true;
            } else {
                // ALTA CON MERCADOPAGO
                if ($isMercadoPago == "true" || $origenVenta == '6') {
                    // Actualizo el count
                    try {
                        $q1 = "UPDATE padron_datos_socio SET `count` = `count` + 1, estado = 6 WHERE id = $id";
                        $rq1 = mysqli_query($mysqli, $q1);
                    } catch (\Throwable $errores) {
                        registrar_errores($q1, "procesoCobroAprobado_inspira.php", $errores);
                    }

                    try {
                        $q2 = "UPDATE padron_producto_socio SET `count` = `count` + 1 WHERE cedula = '$cedulaAfiliado'";
                        $rq2 = mysqli_query($mysqli, $q2);
                    } catch (\Throwable $errores) {
                        registrar_errores($q2, "procesoCobroAprobado_inspira.php", $errores);
                    }

                    // GRUPO FAMILIAR
                    // ACTUALIZA TODOS LOS BENEFICIARIOS
                    try {
                        $query = "SELECT cedula FROM padron_producto_socio WHERE cedula_titular_gf = '$cedulaAfiliado' AND  accion = '1' GROUP BY cedula";
                        $result = mysqli_query($mysqli, $query);
                    } catch (\Throwable $errores) {
                        registrar_errores($query, "procesoCobroAprobado_inspira.php", $errores);
                    }

                    if ($result) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            // Actualizo el count de los beneficiarios
                            $ci = $row["cedula"];
                            try {
                                $q1 = "UPDATE padron_datos_socio SET `count` = `count` + 1, estado = 6 WHERE cedula = '$ci'";
                                $rq1 = mysqli_query($mysqli, $q1);
                            } catch (\Throwable $errores) {
                                registrar_errores($q1, "procesoCobroAprobado_inspira.php", $errores);
                            }

                            try {
                                $q2 = "UPDATE padron_producto_socio SET `count` = `count` + 1 WHERE cedula = '$ci'";
                                $rq2 = mysqli_query($mysqli, $q2);
                            } catch (\Throwable $errores) {
                                registrar_errores($q2, "procesoCobroAprobado_inspira.php", $errores);
                            }
                        }
                    }
                } else {

                    try {
                        $query = "UPDATE padron_datos_socio SET estado = 6 WHERE id = $id";
                        $rquery = mysqli_query($mysqli, $query);
                    } catch (\Throwable $errores) {
                        registrar_errores($query, "procesoCobroAprobado_inspira.php", $errores);
                    }

                    try {
                        $qPromoCompetencia = "SELECT importe, cod_promo FROM padron_producto_socio WHERE abm = 'ALTA' AND cedula = '$cedulaAfiliado' AND cod_promo IN ('35','2035','3335')"; //compe
                        $rPromoComp = mysqli_query($mysqli, $qPromoCompetencia);
                    } catch (\Throwable $errores) {
                        registrar_errores($qPromoCompetencia, "procesoCobroAprobado_inspira.php", $errores);
                    }

                    if ($rPromoComp && mysqli_num_rows($rPromoComp) > 0) {
                        $esPromoComp   = true;
                        $total_importe = 0;

                        try {
                            $query  = "SELECT `id`,`email`,`count` FROM padron_datos_socio WHERE cedula = '$cedulaAfiliado'"; //compe
                            $result = mysqli_query($mysqli, $query);
                        } catch (\Throwable $errores) {
                            registrar_errores($query, "procesoCobroAprobado_inspira.php", $errores);
                        }

                        if ($result && mysqli_num_rows($result) > 0) {
                            $row         = mysqli_fetch_assoc($result);
                            $email_socio = ($row['email'] != '' && $row['email'] != null && $row['email'] != 'null') ? $row['email'] : 'adelantocobro@vida.com.uy';
                            $count_socio = $row['count'];
                            $idSocio     = $row['id'];
                        }

                        $datosAdelantoCobro = array(
                            'id_socio'    => $idSocio,
                            'cedulaSocio' => $cedulaAfiliado,
                            'emailSocio'  => $email_socio,
                            'countSocio'  => $count_socio,
                        ); //compe

                        $response['datosAdelantoCobro'] = $datosAdelantoCobro;
                        $response['esPromoComp']        = $esPromoComp;
                    }

                    try {
                        $query = "SELECT cedula FROM padron_producto_socio WHERE cedula_titular_gf = '$cedulaAfiliado' GROUP BY cedula";
                        $result = mysqli_query($mysqli, $query);
                    } catch (\Throwable $errores) {
                        registrar_errores($query, "procesoCobroAprobado_inspira.php", $errores);
                    }

                    if ($result) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            $ci = $row["cedula"];
                            try {
                                $query = "UPDATE padron_datos_socio SET estado = 6 WHERE cedula = '$ci'";
                                $result = mysqli_query($mysqli, $query);
                            } catch (\Throwable $errores) {
                                registrar_errores($query, "procesoCobroAprobado_inspira.php", $errores);
                            }
                        }
                    }
                }

                ########################################################################################################################
                #### VIDASHOP
                // #####################################################################################################################
                // $response['registroVidaShop'] = false;
                // $query = "SELECT nombre, tel, email, `count`, total_importe
                //         FROM padron_datos_socio
                //         WHERE cedula = '$cedulaAfiliado'
                //             AND alta IN ('1', 1)
                //             AND (
                //                 (fechafil >= '2021-10-10' AND sucursal = 7)
                //                 OR
                //                 (fechafil >= '2021-12-13' AND localidad = 172)
                //                 OR
                //                 (fechafil >= '2022-03-23' AND sucursal = 115)
                //                 OR
                //                 (fechafil >= '2022-04-11' AND localidad = 288)
                //             )";
                // $result = mysqli_query($mysqli, $query);
                // if ($result && mysqli_num_rows($result) > 0) {
                //     $row             = mysqli_fetch_assoc($result);
                //     $nombre_socio    = $row['nombre'];
                //     $telefonos_socio = $row['tel'];
                //     $email_socio     = $row['email'];
                //     $count_socio     = $row['count'];
                //     $total_importe   = $row['total_importe'];

                //     $response['registroVidaShop'] = true;
                //     $telefonos_split              = explode(' ', $telefonos_socio);
                //     $telefono_socio               = $telefonos_split[0] != '' ? $telefonos_split[0] : $telefonos_split[1];
                //     $vida_pesos                   = $isMercadoPago == "true" ? $total_importe : 0;
                //     $response['preguntarEmail']   = empty($email_socio) || is_null($email_socio) || strtolower($email_socio) == 'null' ? true : false;

                //     $response['datosVidaShop'] = array(
                //         'id_socio'      => $id,
                //         'nombreSocio'   => $nombre_socio,
                //         'cedulaSocio'   => $cedulaAfiliado,
                //         'telefonoSocio' => $telefono_socio,
                //         'vidaPesos'     => $vida_pesos,
                //         'emailSocio'    => $email_socio,
                //         'countSocio'    => $count_socio,
                //     );
                // }
                #####################################################################################################################
                #####################################################################################################################
            }
        } else {

            try {
                $query = "UPDATE padron_datos_socio SET estado = 6 WHERE id = $id";
                $result = mysqli_query($mysqli, $query);
            } catch (\Throwable $errores) {
                registrar_errores($query, "procesoCobroAprobado_inspira.php", $errores);
            }
        }

        if (!$error) {
            try {
                $qHistorico = "INSERT INTO historico_venta VALUES(null, $idUser, $id, 6, '$fecha', '$observacionCobro', 11)";
                $rHistorico = mysqli_query($mysqli, $qHistorico);
            } catch (\Throwable $errores) {
                registrar_errores($qHistorico, "procesoCobroAprobado_inspira.php", $errores);
            }

            if ($rHistorico) {
                if ($esAlta == "1") {
                    try {
                        $query = "SELECT * FROM padron_datos_socio WHERE cedula = '$cedulaAfiliado'";
                        $result = mysqli_query($mysqli250, $query);
                    } catch (\Throwable $errores) {
                        registrar_errores($query, "procesoCobroAprobado_inspira.php", $errores);
                    }

                    if ($result) {
                        if (mysqli_num_rows($result) > 0) {
                            $response['message']         = "La persona con cédula $cedulaAfiliado ya se encuentra en padron";
                            $response["guardado_padron"] = false;
                        } else {

                            $response["guardado_padron"] = guardarPadron($id, $mysqli, $mysqli250, $mysqli1310, $cedulaAfiliado);
                        }
                    }
                } else {
                    $response["guardado_padron"] = guardarPadron($id, $mysqli, $mysqli250, $mysqli1310, $cedulaAfiliado);
                }

                if ($response["guardado_padron"]) {
                    $response['message'] = 'Guardado en padron con éxito!';
                    $response["title"]   = "Éxito!";
                    $response["icon"]    = "success";
                } else {
                    $response['seba'] = 'SI ACA';
                    $response['message'] = 'Error al guardar en padrón';
                }

                $response["result"] = true;
            } else {
                $response["result"]  = false;
                $response['message'] = 'Error al guardar el histórico';
            }
        }
    } else {
        $response['message'] = 'Error al actualizar el estado';
    }
}



function validarExistenBeneficiariosUdemmSura($cedulaAfiliado)
{
    global $mysqli;

    $faltaUdemm = false;
    try {
        $qServi     = "SELECT servicio FROM padron_producto_socio WHERE cedula = '$cedulaAfiliado' AND servicio IN ('87','88')"; //newform
        $rServi     = mysqli_query($mysqli, $qServi);
    } catch (\Throwable $errores) {
        registrar_errores($qServi, "procesoCobroAprobado_inspira.php", $errores);
    }

    if (mysqli_num_rows($rServi) > 0) {
        try {
            $qBS        = "SELECT * FROM beneficiarios_servicios WHERE cedula_titular = '$cedulaAfiliado'";
            $rBS        = mysqli_query($mysqli, $qBS);
        } catch (\Throwable $errores) {
            registrar_errores($qBS, "procesoCobroAprobado_inspira.php", $errores);
        }

        $faltaUdemm = (mysqli_num_rows($rBS) == 0) ? true : false;
    }

    return $faltaUdemm;
}


function validarBeneficiariosEnPadron($cedulaAfiliado)
{
    $error = false;
    global $mysqli;

    try {
        $query = "SELECT cedula FROM padron_producto_socio WHERE cedula_titular_gf = '$cedulaAfiliado' AND  accion='1' GROUP BY cedula";
        $result = mysqli_query($mysqli, $query);
    } catch (\Throwable $errores) {
        registrar_errores($query, "procesoCobroAprobado_inspira.php", $errores);
    }

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            // Valido cada beneficiario en padron
            $ci = $row["cedula"];
            $r  = validarExisteEnPadron($ci);
            if ($r) {
                $error = "El beneficiario con la cédula $ci se encuentra en el padrón.";
                break;
            }
        }
    }

    return $error;
}


function obtenerOrigenVenta($cedulaAfiliado)
{ //web
    $origenVenta = false;
    global $mysqli;

    try {
        $query = "SELECT origen_venta FROM padron_datos_socio WHERE cedula = '$cedulaAfiliado'";
        $result = mysqli_query($mysqli, $query);
    } catch (\Throwable $errores) {
        registrar_errores($query, "procesoCobroAprobado_inspira.php", $errores);
    }

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            // Valido cada beneficiario en padron
            $origenVenta = $row["origen_venta"];
        }
    }

    return $origenVenta;
}


function validarCamposVacios($cedulaAfiliado)
{
    $error = false;
    global $mysqli;

    try {
        $query = "SELECT direccion FROM padron_datos_socio WHERE cedula = '$cedulaAfiliado'";
        $result = mysqli_query($mysqli, $query);
    } catch (\Throwable $errores) {
        registrar_errores($query, "procesoCobroAprobado_inspira.php", $errores);
    }

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            // Valido cada beneficiario en padron
            $dir = $row["direccion"];
        }
        $error = ($dir == '') ? true : $error;
    }

    return $error;
}



mysqli_close($mysqli);
echo json_encode($response);
