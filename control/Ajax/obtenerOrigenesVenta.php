<?php

include "../../_conexion.php";

$response = array('result' => false, 'message' => 'Intentelo nuevamente mas tarde !!');

if (isset($_POST["typeAdmin"])) {

    $q             = "SELECT * FROM origenes_venta ORDER BY origen";
    $result        = mysqli_query($mysqli, $q);
    $origenes = [];

    while ($row = mysqli_fetch_array($result)) {
        $cod        = $row['cod'];
        $origen     = $row['origen'];
        $origenes[] = array(
            'cod'           => $cod,
            'origen' => $origen,
        );
    }
    $response = array('result' => true, 'message' => 'Exito', 'origenes' => $origenes);

} else {
    $response = array('result' => false, 'message' => 'Sin sesion');
}

mysqli_close($mysqli);
echo json_encode($response);