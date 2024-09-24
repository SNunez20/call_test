<?php
include '../_conexion.php';
session_start();

$response = array(
    'result'  => false,
    'session' => false,
    'message' => 'sin sesion.',
);

if (isset($_SESSION['idusuario'])) {
    $code         = mysqli_real_escape_string($mysqli, $_POST["code"]);
    $idsServicios = mysqli_real_escape_string($mysqli, $_POST["servicios"]);
    $where        = "";
    if ($code == 5) {
        $where .= ' AND ms.id_metodo=2 ';
    }
    $q = "SELECT mp.id, mp.metodo FROM metodos_pago mp
        INNER JOIN medios_servicios ms ON ms.id_metodo = mp.id
        WHERE mp.mostrar='1' AND ms.id_servicio IN ($idsServicios) $where GROUP BY ms.id_metodo";

    if ($result = mysqli_query($mysqli, $q)) {

        $metodos = array();

        while ($row = mysqli_fetch_array($result)) {

            $id     = $row['id'];
            $metodo = $row['metodo'];

            $metodos[] = array('id' => $id, 'metodo' => $metodo);

            $response = array(
                'result'  => true,
                'session' => true,
                'message' => 'Metodos listado correctamente.',
                'metodos' => $metodos,
            );
        }
    } else {
        $response = array(
            'result'  => false,
            'session' => true,
            'message' => 'Ocurrio un error en la consulta.',
        );
    }
}

// if ($_SESSION['cedulaUsuario'] === '50717986') die($q);

mysqli_close($mysqli);
echo json_encode($response);
