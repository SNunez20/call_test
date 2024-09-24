<?php
require_once "../../_conexion.php";

$response = array(
    "result"  => true,
    "session" => false,
    "message" => "Beneficiarios guardados con exito!",
);

if (isset($_POST["typeAdmin"])) {
    $response["session"] = true;
    $beneficiarios       = json_decode($_POST["beneficiarios"], true);
    $cedulaTitular       = mysqli_real_escape_string($mysqli,$_POST["cedulaTitular"]);
    $numServi             = mysqli_real_escape_string($mysqli,$_POST["numServi"]);

    // var_dump($beneficiarios); exit;

    foreach ($beneficiarios as $key => $beneficiario) {
        $nombre            = $beneficiario[0];
        $cedula            = $beneficiario[1];
        $telefono          = $beneficiario[2];
        $fechaDeNacimiento = $beneficiario[3];
        $edad              = $beneficiario[4];
        $idBen             = $beneficiario[5];

        $q = "SELECT id FROM beneficiarios_servicios WHERE cedula_titular = '$cedulaTitular' AND cedula='$cedula' AND concretado = 0";
        $r = mysqli_query($mysqli, $q);
        if ($r && mysqli_num_rows($r)>0) {
            $query = "UPDATE beneficiarios_servicios SET nombre =  '$nombre', cedula= '$cedula',fecha_nacimiento = '$fechaDeNacimiento',telefono = '$telefono', num_servicio = $numServi WHERE id = $idBen";
        }else{
            $query = "INSERT INTO beneficiarios_servicios VALUES (null, '$nombre','$cedula','$fechaDeNacimiento','$telefono','$cedulaTitular','$numServi',0)";
        }
 

        if (!mysqli_query($mysqli, $query)) {
            $response["result"]  = false;
            $response["message"] = "Error al guardar datos de beneficiarios";
        }
    }
}

mysqli_close($mysqli);
echo json_encode($response);
