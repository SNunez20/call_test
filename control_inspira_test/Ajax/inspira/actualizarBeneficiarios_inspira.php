<?php

$response["result"] = true;
$response["session"] = false;
$response["message"] = "Beneficiarios actualizados con éxito!";


if (isset($_POST["typeAdmin"])) {
    $response["session"] = true;
    $beneficiarios       = json_decode($_POST["beneficiarios"], true);

    foreach ($beneficiarios as $key => $beneficiario) {
        $nombre           = $beneficiario[0];
        $cedula           = $beneficiario[1];
        $telefono         = $beneficiario[2];
        $fecha_nacimiento = $beneficiario[3];
        $edad             = $beneficiario[4];
        
        $update           = modificar_datos_beneficiarios($cedula, $nombre, $telefono, $fecha_nacimiento, $edad);
        if ($update == false) {
            $response["result"]  = false;
            $response["message"] = "Error al actualizar datos de beneficiarios";
        }
    }
}



echo json_encode($response);




function modificar_datos_beneficiarios($cedula, $nombre, $telefono, $fechaDeNacimiento, $edad)
{
    require "../../../_conexion.php";

    $sql = "UPDATE padron_datos_socio SET 
             nombre='$nombre', 
             tel='$telefono', 
             fecha_nacimiento='$fechaDeNacimiento', 
             edad=$edad 
            WHERE 
             cedula='$cedula'";
    $consulta = mysqli_query($mysqli, $sql);

    mysqli_close($mysqli);
    return $consulta;
}
