<?php
require_once "../../_conexion.php";
require_once "../../_conexion250.php";

$response = array(
    "result"  => false,
    "session" => false,
);

if (isset($_POST["typeAdmin"])) {
    $response["session"] = true;
    $data                = array_map(fn($data) => strip_tags(mysqli_real_escape_string($mysqli, $data)), $_POST);
    $id                  = $data["id"];
    $ruta                = $data["ruta"];
    $response["result"]  = mysqli_query($mysqli, "UPDATE padron_datos_socio SET ruta='$ruta' WHERE id=$id");
}

mysqli_close($mysqli);
echo json_encode($response);
