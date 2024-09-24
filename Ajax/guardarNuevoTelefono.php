<?php
require '../_conexion.php';
session_start();
$response = array(
    'result'  => false,
    'message' => 'Ocurrio un error.',
);

if (isset($_SESSION["idusuario"])) {
    $data   = array_map(fn($d) => mysqli_real_escape_string($mysqli, $d), $_POST);
    $numero = $data["numero"];
    $grupos = ['A','B','C','D','E','F','G','H','I','J'];
    $grupoAsignado = $grupos[mt_rand(0, count($grupos))];


    // Compruebo si existe el número en el sistema
    $query = "SELECT * FROM numeros WHERE numero = '$numero'";
    if ($result = mysqli_query($mysqli, $query)) {
        if (mysqli_num_rows($result) == 0) {

            // guardo el nuevo número
            $query = "INSERT INTO numeros(numero, grupo, flag, no_contesta, dep_localidad) VALUES('$numero', '$grupoAsignado', 'libre', 0, 'Desconocido')";
            if (mysqli_query($mysqli, $query)) {
                $response["result"]  = true;
                $response["message"] = "Número guardado";
            }
        } else {
            $response["result"]  = true;
            $response["message"] = "Número ya existente";
        }
    }
}

mysqli_close($mysqli);
echo json_encode($response);
