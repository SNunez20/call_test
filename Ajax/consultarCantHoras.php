<?php
header('Access-Control-Allow-Origin: *');
include '../_conexion250.php';
mysqli_select_db($mysqli, "motor_de_precios");
$data     = array_map('stripslashes', $_POST);
$response = array('result' => false, 'message' => 'Intentelo nuevamente mas tarde !!');

$producto = mysqli_real_escape_string($mysqli, $data['producto']);

$cant_horas_sanatorio = false;
$cant_horas_servicio  = false;

$q      = "select * from servicios where id = $producto";
$result = mysqli_query($mysqli250, $q);

while ($row = mysqli_fetch_assoc($result)) {
    $horas_sanatorio = $row['hrs_sanatorio'];
    $horas_servicio  = $row['hrs_servicio'];
}

if ($horas_sanatorio == 1) {
    $cant_horas_sanatorio = true;
}

if ($horas_servicio == 1) {
    $cant_horas_servicio = true;
}

$response = array('result' => true, 'cant_horas_sanatorio' => $cant_horas_sanatorio, 'cant_horas_servicio' => $cant_horas_servicio);

mysqli_close($mysqli250);
echo json_encode($response);
