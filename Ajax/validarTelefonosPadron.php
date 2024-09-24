<?php
session_start();
include '../_conexion250.php';
date_default_timezone_set('America/Argentina/Buenos_Aires');
$response = array('result' => false, 'message' => 'Ocurrio un error');

if (isset($_SESSION['idusuario'])) {
    $celular  = $_POST['celu'];
    $telefono = $_POST['telefono'];
    $cedula   = $_POST['cedula'];
    $tel      = true;
    $cel      = true;
    $query    = "SELECT tel FROM padron_datos_socio WHERE cedula = '$cedula' AND tel LIKE '%$celular%'";
    $result   = mysqli_query($mysqli250, $query);
    if (mysqli_num_rows($result) == 0) {
        $q1     = "SELECT tel FROM padron_datos_socio WHERE cedula != '$cedula' AND tel LIKE '%$celular%'";
        $result = mysqli_query($mysqli250, $q1);
        $cel    = (mysqli_num_rows($result) > 3) ? false : true;
    }

    if ($telefono != "") {
        $query  = "SELECT tel FROM padron_datos_socio WHERE cedula = '$cedula' AND tel LIKE '%$telefono%'";
        $result = mysqli_query($mysqli250, $query);
        if (mysqli_num_rows($result) == 0) {
            $q2     = "SELECT tel FROM padron_datos_socio WHERE cedula != '$cedula' AND tel LIKE '%$telefono%'";
            $result = mysqli_query($mysqli250, $q2);
            $tel    = (mysqli_num_rows($result) > 3) ? false : true;
        }
    }

    $response = array('result' => true, 'celular' => $cel, 'telefono' => $tel);
} else {
    $response = array('result' => false, 'message' => 'Sin Sesion');
}

mysqli_close($mysqli250);
echo json_encode($response);
