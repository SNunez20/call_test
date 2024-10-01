<?php

/**
 * VALIDA LA CEDULA EN PADRON DE UN GRUPO DE SOCIOS
 */
session_start();
date_default_timezone_set("America/Argentina/Buenos_Aires");
require_once "../_conexion250.php";
require_once "../_conexion.php";
require_once "../_conexion1310.php";

$response = array(
    "result"   => false,
    "sesion"   => false,
    "message"  => "Ocurrio un error. Intentelo nuevamente más tarde.",
    "errors" => [],
    "data_socios" => []
);

if (isset($_SESSION["idusuario"])) {
    $response["sesion"] = true;
    // $data               = array_map(fn($param) => strip_tags(mysqli_real_escape_string($mysqli, $param["cedulasAfiliados"])), $_POST);
    $afiliados          = $_POST["cedulasAfiliados"];

    foreach ($afiliados as $key => $cedula) {
        $query    = "SELECT * FROM padron_datos_socio WHERE cedula = '$cedula'";
        $result   = mysqli_query($mysqli250, $query);
        $socio    = false;
        $result2  = mysqli_query($mysqli, $query);

        if (($result2) && mysqli_num_rows($result2) > 0) {
            $socio    = true;
            $response["socio"] = false;
            $response["result"] = false;
            $response["message"] = "La cédula $cedula se encuentra en proceso de afiliación";
        } else if (mysqli_num_rows($result) == 1) {
            $socio = true;
            $response["socio"] = false;
            $response["result"] = false;
            $response["message"] = "La cédula $cedula ya se encuentra en padrón";
        }

        ####################################################################################################
        // Compruebo si es SOCIO, para de esta manera tener en cuenta que productos ofrecer
        // Abrimos la sesión cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://localhost/call_test/Ajax/buscarBaja.php");
        // curl_setopt($ch,CURLOPT_URL,"http://localhost/call_prueba/Ajax/buscarBaja.php");
        // indicamos el tipo de petición
        curl_setopt($ch, CURLOPT_POST, true);
        // definimos cada uno de los párametros
        curl_setopt($ch, CURLOPT_POSTFIELDS, "cedula=$cedula&ptbbddvp=true");
        // recibimos la respuesta
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $res = json_decode(curl_exec($ch));
        curl_close($ch);
        ####################################################################################################

        if (!$socio) {
            $response["socio"] = false;
            $response["result"] = true;
            $response["message"] = "";
            $response["data_socios"][$key]["code"] = $res->code;
            $response["data_socios"][$key]["cedula"] = $cedula;
        } else {
            $response["socio"] = true;
            $response["result"] = false;
            break;
        }
    }
} else {
    $response["message"] = "Sin Sesión";
}

mysqli_close($mysqli);
mysqli_close($mysqli250);
echo json_encode($response);
