<?php
include '../_conexion.php';
date_default_timezone_set('America/Argentina/Buenos_Aires');
session_start();

if (isset($_SESSION['idusuario'])) {
    $idusuario = $_SESSION['idusuario'];

    $fecha_desde = $_GET['desde'];
    $fecha_hasta = $_GET['hasta'];

    $where = "";
    if ($fecha_desde != "" || $fecha_hasta != "") {
        #formato unix
        $fecha_desde = strtotime($_GET['desde']);
        $fecha_hasta = strtotime($_GET['hasta']);

        #formato date
        $fecha_desde = date('Y-m-d ', $fecha_desde);
        $fecha_hasta = date('Y-m-d ', $fecha_hasta);
        $where .= " and ";

        if ($fecha_desde != "") {
            $where .= "fecha_agendado >= '$fecha_desde'";
        }
        if ($fecha_hasta != "") {
            $where .= " and fecha_agendado <= '$fecha_hasta'";
        }
    }

    $q         = "select * from agendados where usuarioid = $idusuario $where order by fecha_agendado asc";
    $result    = mysqli_query($mysqli, $q);
    $agendados = array();

    while ($row = mysqli_fetch_array($result)) {
        $numero         = $row['numero'];
        $nombre         = htmlspecialchars($row['nombre']);
        $fecha_agendado = $row['fecha_agendado'];
        $comentario     = htmlspecialchars($row['comentario']);
        $fecha          = $row['fecha'];
        $fechaHoy     = date("Y-m-d");
        $fechaSinHora = date("Y-m-d", strtotime($fecha_agendado));
        if ($fechaHoy > $fechaSinHora) {
            $estado = "vencido";
        } else if ($fechaHoy < $fechaSinHora) {
            $estado = "futuro";
        } else if ($fechaHoy == $fechaSinHora) {
            $estado = "del dia";
        }

        $agendados[] = array('numero' => $numero, 'nombre' => $nombre, 'fecha_agendado' => $fecha_agendado, 'comentario' => $comentario, 'fecha' => $fecha, 'estado' => $estado);
    }
}
mysqli_close($mysqli);
echo json_encode($agendados);
