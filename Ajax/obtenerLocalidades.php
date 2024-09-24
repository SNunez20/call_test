<?php
session_start();
include "../_conexion.php";

$response = array('result' => false, 'message' => 'Intentelo nuevamente mas tarde !!');

if (isset($_SESSION['idusuario'])) {

    $id_dep = $_POST['departamento'];
    // mysqli_select_db($mysqli1310, "motor_de_precios");
    $q           = "select * from ciudades where id_departamento=$id_dep order by nombre";
   
    $result      = mysqli_query($mysqli, $q);
    //  var_dump(mysqli_error($mysqli));
    $localidades = [];

    while ($row = mysqli_fetch_array($result)) {
        $id            = $row['id'];
        $localidad     = $row['nombre'];
        $idFilial      = $row['id_filial'];
        $localidades[] = array(
            'id'        => $id,
            'idFilial'  => $idFilial,
            'localidad' => $localidad
        );

    }
    $response = array('result' => true, 'message' => 'Exito', 'localidades' => $localidades);

} else {
    $response = array('result' => false, 'message' => 'Sin sesion');
}

mysqli_close($mysqli);
echo json_encode($response);
