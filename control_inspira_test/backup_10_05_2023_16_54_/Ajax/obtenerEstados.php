<?php

include "../../_conexion.php";

$response = array('result' => false, 'message' => 'Intentelo nuevamente mas tarde !!');

if (isset($_POST["typeAdmin"])) {

    $esCompetencia = mysqli_real_escape_string($mysqli,$_POST['esCompetencia']);
    $where = ($esCompetencia=='false') ? " AND id NOT IN (672,673,674,675,676,683)" : "" ;
    $q             = "SELECT * FROM estados WHERE mostrar = 1 $where ORDER BY estado";
    $result        = mysqli_query($mysqli, $q);
    $estados       = [];
    while ($row = mysqli_fetch_array($result)) {
        $id        = $row['id'];
        $estado    = $row['estado'];
        $estados[] = array(
        'id'     => $id,
        'estado' => $estado,
        );
    
    }
    $response = array('result' => true, 'message' => 'Exito', 'estados' => $estados);

} else {
    $response = array('result' => false, 'message' => 'Sin sesion');
}

mysqli_close($mysqli);
echo json_encode($response);