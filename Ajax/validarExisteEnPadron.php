<?php
session_start();
require "../_conexion.php";
require "../_conexion250.php";

$response = array('result' => false, 'message' => 'Intentelo nuevamente mas tarde !!');

if (isset($_SESSION['idusuario'])) {

    $cedula = $_POST['cedula'];
    $query1 = "SELECT * FROM padron_datos_socio WHERE cedula='$cedula' AND estado <> 6";
    $query2 = "SELECT * FROM padron_datos_socio WHERE cedula='$cedula'";

    $result  = mysqli_query($mysqli, $query1);
    $result2 = mysqli_query($mysqli250, $query2);

    if (($result) && mysqli_num_rows($result) == 1) {
        $mensaje  = "La persona con la cédula $cedula ya esta en proceso de afiliación";
        $response = array('result' => true, 'existe' => true, 'message' => $mensaje);

    } else if (($result2) && mysqli_num_rows($result2) == 1) {
        $mensaje  = "La persona con la cédula " . $cedula . " ya es socio";
        $response = array('result' => true, 'existe' => true, 'message' => $mensaje);
    } else {
        $response = array('result' => true, 'existe' => false, 'message' => 'No es socio');
    }

} else {
    $response = array('result' => false, 'message' => 'Sin sesion');
}

mysqli_close($mysqli);
mysqli_close($mysqli250);
echo json_encode($response);
