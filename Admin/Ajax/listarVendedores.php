<?php
include '../../_conexion.php';
session_start();
$response = array('result' => false, 'message' => 'Intentelo nuevamente mas tarde !!');

if ($_SESSION['idadmin']) {
    $usuario = $_SESSION['idadmin'];
    $query = "SELECT id, nombre FROM usuarios WHERE id <> $usuario AND activo=1";

    if ($_SESSION['grupo_usuarios'] != 0) {
        $query = $query . " AND idgrupo=" . $_SESSION["grupo_usuarios"];
    }


    if ($vendedores = mysqli_query($mysqli, $query)) {
        while ($row = mysqli_fetch_assoc($vendedores)) {
            $response['result'][] = array('id' => $row['id'], 'nombre' => $row['nombre']);
        }
    }

} else {
    $response = array('result' => false, 'message' => 'Sin Sesion');
}

if (isset($_POST['json'])){
    file_put_contents('vendedores.json',json_encode($response['result']));
}

mysqli_close($mysqli);
echo json_encode($response);
