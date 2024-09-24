<?php

include_once '../app.php';

$id_socio = (int)$_POST['id_socio'];

$conexion = conexion(DB_SISTEMA_RUTAS);

$latLng = query($conexion, "SELECT * FROM lat_lng_socios WHERE id_socio=$id_socio");

$error = mysqli_num_rows($latLng) === 0 ? true : false;

$link = "<a href='sistema_rutas/?socio={$id_socio}&ver_formulario_cobro=true'>Ingresar Dirección</a>";

$mensaje  = mysqli_num_rows($latLng) === 0
    ? "<b>No se encontro ninguna dirección con latitud y longitud en maps, {$link} </b>"
    : 'El usuario cuenta con dirección confirmada';

dieJson($error, $mensaje);
