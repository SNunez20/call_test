<?php
include('../_conexion.php');
session_start();

if (isset($_SESSION['idusuario'])) {
    $idusuario = $_SESSION['idusuario'];
    $q = "select historico.numero,detalles.integrantes_familia,detalles.direccion,detalles.otro_servicio,detalles.observaciones,historico.fecha from vendidos inner join detalles on vendidos.iddetalle = detalles.id inner join historico on vendidos.idhistorico = historico.id where vendidos.idusuario = $idusuario";
    $result = mysqli_query($mysqli,$q);
    $vendidos = array();

    while($row = mysqli_fetch_array($result)) {
        $numero = $row['numero'];
        $integrantes_familia = $row['integrantes_familia'];
        $direccion = htmlspecialchars($row['direccion']);
        $otro_servicio = $row['otro_servicio'];
        $observaciones = htmlspecialchars($row['observaciones']);
        $fecha = $row['fecha'];
        $vendidos[] = array(
            'numero'=> $numero, 
            'int_familia'=> $integrantes_familia, 
            'direccion'=> $direccion, 
            'otro_servicio'=> $otro_servicio, 
            'observaciones' => $observaciones, 
            'fecha'=> $fecha
        );
    }
}

mysqli_close($mysqli);
echo json_encode($vendidos);
