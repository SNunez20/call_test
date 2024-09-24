<?php
include('../_conexion.php');
session_start();

if (isset($_SESSION['idusuario'])) {
    $idusuario = $_SESSION['idusuario'];
    $q = "select * from referidos where usuarioid = $idusuario order by fecha asc";
    $result = mysqli_query($mysqli,$q);
    $referidos = array();

    while($row = mysqli_fetch_array($result)) {
        $numero = $row['numero'];
        $nombre = htmlspecialchars($row['nombre']);
        $fecha = $row['fecha'];
        $observacion = htmlspecialchars($row['observacion']);       
        $referidos[] = array('numero'=> $numero, 'nombre'=> $nombre, 'fecha'=> $fecha, 'observacion'=> $observacion);
    }
}

mysqli_close($mysqli);
echo json_encode($referidos);
