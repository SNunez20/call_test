<?php
require_once "../../_conexion.php";

$response = array(
    "result"  => true,
    "session" => false,
    "message" => "Beneficiarios acutalizados cone exito!",
);

if (isset($_POST["typeAdmin"])) {
    $response["session"] = true;
    $beneficiarios       = json_decode($_POST["beneficiarios"], true);

    foreach ($beneficiarios as $key => $beneficiario) {
        $nombre            = $beneficiario[0];
        $cedula            = $beneficiario[1];
        $telefono          = $beneficiario[2];
        $fechaDeNacimiento = $beneficiario[3];
        $edad              = $beneficiario[4];
        $query             = "UPDATE padron_datos_socio SET nombre='$nombre', tel='$telefono', fecha_nacimiento='$fechaDeNacimiento', edad=$edad WHERE cedula='$cedula'";

        if (!mysqli_query($mysqli, $query)) {
            $response["result"]  = false;
            $response["message"] = "Error al actualizar datos de beneficiarios";
        }
    }
}

mysqli_close($mysqli);
echo json_encode($response);
