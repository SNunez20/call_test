<?php
require_once "../../_conexion.php";
require_once "../funciones/cobrar.php";

$response = array(
    "result"     => false,
    "session"    => false,
    "mensaje"    => "Ocurrio un error, intenta más tarde.",
    "approved"   => false,
    "rejected"   => false,
    "in_process" => false,
);

if (isset($_POST["typeAdmin"])) {
    $id_afiliado   = mysqli_real_escape_string($mysqli, $_POST["id_afiliado"]);
    $cedula        = mysqli_real_escape_string($mysqli, $_POST["cedulaBeneficiario"]);
    $cedulaTitular = mysqli_real_escape_string($mysqli, $_POST["cedulaTitular"]);
    $tipoTarjeta   = mysqli_real_escape_string($mysqli, $_POST["tipoTarjeta"]);
    $total_importe = mysqli_real_escape_string($mysqli, $_POST["total_importe"]);
    $mailTitular   = mysqli_real_escape_string($mysqli, $_POST["email"]);
    $token         = mysqli_real_escape_string($mysqli, $_POST["token"]);
    $observacion   = mysqli_real_escape_string($mysqli, $_POST["observacion"]);
    $idUser        = mysqli_real_escape_string($mysqli, $_POST["id_usuario"]);
    $cvv           = mysqli_real_escape_string($mysqli, $_POST["cvv"]);
    $cuotas        = isset($_POST["cuotas_mercadopago"]) ? $_POST["cuotas_mercadopago"] : 1;
    $fecha         = date('Y-m-d');
    if ($mailTitular == '') {$mailTitular = "sin_mail_" . rand(0, 20000) . "@gmail.com";}

    $getdata = http_build_query(
        array(
            "id_afiliado"   => $id_afiliado,
            "cedulaTitular" => $cedulaTitular,
            "emailTitular"  => $mailTitular,
            "totalImporte"  => $total_importe,
            "tipoTarjeta"   => $tipoTarjeta,
            "token"         => $token,
            "cuotas"        => $cuotas,
            "cvv"           => $cvv,
        ));

    $opts = array("http" => array(
        "header"  => "Content-Type: application/x-www-form-urlencoded\r\n" .
        "Content-Length: " . strlen($getdata) . "\r\n" .
        "User-Agent:MyAgent/1.0\r\n",
        "method"  => "POST",
        "content" => $getdata,
    ));

    $context         = stream_context_create($opts);
    $resultado       = file_get_contents("https://vida-apps.com/call_pagos/funciones/cobrar.php?" . $getdata, false, $context);
    $resultado_cobro = json_decode($resultado);

    $observacion = strtoupper($observacion);
    if ($resultado_cobro->result) {
        if ($resultado_cobro->approved) {
            $response["approved"] = true;
            $qHistrocio           = "INSERT INTO historico_venta VALUES(null,$idUser,$id_afiliado,6,'$fecha','$observacion',11)";
            $rHistorico           = mysqli_query($mysqli, $qHistrocio);
            if ($rHistorico) {
                $response['historico'] = 'Historico guardado correctamente';
            } else {
                $response['historico'] = 'Error al guardar el historico';
            }
        } else if ($resultado_cobro->rejected) {
            $response["rejected"] = true;
        } else if ($resultado_cobro->in_process) {
            $response["in_process"] = true;
        }

        $response["result"]         = true;
        $response["error"]          = $resultado_cobro->error;
        $response["mensaje"]        = $resultado_cobro->mensaje;
        $response["tipo_mensaje"]   = $resultado_cobro->tipo_mensaje;
        $response["titulo_mensaje"] = $resultado_cobro->titulo_mensaje;
        $response["comprobante"]    = $resultado_cobro->comprobante;
        $response["rCobro"]         = $resultado_cobro;
        $response["getData"]         = $getdata;
    
    } else {
        $response = array(
            "result"  => false,
            "message" => 'Error al procesar el pago',
        );
    }
}
mysqli_close($mysqli);
echo json_encode($response);
