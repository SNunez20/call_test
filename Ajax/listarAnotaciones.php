<?php
include '../_conexion.php';
session_start();
$response = array('result' => false, 'message' => 'Intentelo nuevamente mas tarde !!');

if (isset($_SESSION['idusuario'])) {
    $idusuario = $_SESSION['idusuario'];
    $filtro_fecha = $_GET['fecha'];
    $filtro_texto = $_GET['palabra'];

    $where = "";
    if (($filtro_fecha != "" || $filtro_texto != "") && ($filtro_fecha != "undefined" || $filtro_texto != "undefined")) {
        $where .= " and ";

        if ($filtro_fecha != "" && $filtro_fecha != "undefined") {
            $where .= "cast(fecha as date) = '$filtro_fecha'";
        }
        if ($filtro_texto != "" && $filtro_texto != "undefined") {
            if ($filtro_fecha != "") {
                $where .= " and ";
            }
            $where .= "texto like '%$filtro_texto%'";
        }
    }

    $q      = "select * from anotaciones where idusuario = $idusuario $where order by fecha desc";
    $result = mysqli_query($mysqli, $q);

    if ($result) {
        $anotacion = "";
        $fecha     = "";
        while ($row = mysqli_fetch_array($result)) {
            if ($anotacion != "") {
                $anotacion .= "\n\n";
            }
            $anotacion .= " - ";
            $fecha = date("d/m/Y H:i:s", strtotime($row['fecha']));
            $anotacion .= $fecha;
            $anotacion .= " - ";
            $anotacion .= "\n";
            $anotacion .= htmlspecialchars($row['texto']);
            $anotacion .= "\n";
            $anotacion .= "---------------------------";
        }
        $response = array('result' => true, 'anotacion' => $anotacion, 'fecha' => $fecha);
    }
} else {
    $response = array('result' => false, 'message' => 'Sin Sesion');
}

mysqli_close($mysqli);
echo json_encode($response);
