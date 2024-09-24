<?php
include('../_conexion.php');
session_start();

$referidos = [];

if (isset($_SESSION['idusuario'])) {
    $idusuario = $_SESSION['idusuario'];
    $q = "select * from referidoscuaderno_pendientes where idusuario = $idusuario order by fecha asc";
    $result = mysqli_query($mysqli,$q);
    $referidos = array();

    while($row = mysqli_fetch_array($result)) {
        $id = $row['id'];
        $numero = $row['numero'];
        $nombre = $row['nombre'];
        $fecha = $row['fecha'];
        $observacion = htmlspecialchars($row['observacion']);
        $referidos[] = array('id'=> $id, 'numero'=> $numero, 'nombre'=> $nombre, 'fecha'=> $fecha, 'observacion'=> $observacion);
    }
}

mysqli_close($mysqli);
echo json_encode($referidos);
