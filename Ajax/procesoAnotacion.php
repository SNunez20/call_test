<?php
include '../_conexion.php';
session_start();
$response = array('result' => false, 'message' => 'Intentelo nuevamente mas tarde !!');

if (isset($_SESSION['idusuario'])) {
    $idusuario = $_SESSION['idusuario'];
    $data      = array_map('stripslashes', $_POST);
    $texto     = mysqli_real_escape_string($mysqli, $data['anotacionActual']);
    date_default_timezone_set('America/Argentina/Buenos_Aires');
    $fecha = date("Y-m-d H:i:s");

    $q      = "insert into anotaciones values(null,$idusuario,'$texto','$fecha')";
    $result = mysqli_query($mysqli, $q);

    if ($result) {
        $response = array('result' => true, 'message' => 'Correcto');
    }
} else {
    $response = array('result' => false, 'message' => 'Sin Sesion');
}
mysqli_close($mysqli);
echo json_encode($response);