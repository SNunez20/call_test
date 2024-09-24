<?php
session_start();
include "../_conexion.php";

$response = array('result' => false, 'message' => 'Intentelo nuevamente mas tarde !!');

if (isset($_SESSION['idusuario'])) {

    // mysqli_select_db($mysqli, "motor_de_precios");
    $q             = "select * from departamentos order by nombre";
    $result        = mysqli_query($mysqli, $q);
    $departamentos = [];

    while ($row = mysqli_fetch_array($result)) {
        $id              = $row['id'];
        $departamento    = $row['nombre'];
        $departamentos[] = array(
            'id'           => $id,
            'departamento' => $departamento,
        );

    }
    $response = array('result' => true, 'message' => 'Exito', 'departamentos' => $departamentos);

} else {
    $response = array('result' => false, 'message' => 'Sin sesion');
}

mysqli_close($mysqli);
echo json_encode($response);
