
<?php
header('Access-Control-Allow-Origin: *');
include '../_conexion250.php';
mysqli_select_db($mysqli1310, "motor_de_precios");
$data     = array_map('stripslashes', $_POST);
$response = array('result' => false, 'message' => 'Intentelo nuevamente mas tarde !!');

$producto = mysqli_real_escape_string($mysqli250, $data['producto']);

$cant_horas_sanatorio = false;
$cant_horas_servicio  = false;

$q               = "select * from servicios where id = $producto";
$result          = mysqli_query($mysqli1310, $q);
$horas_sanatorio = 0;
$horas_servicio  = 0;

while ($row = mysqli_fetch_assoc($result)) {
    // $precio_base = $row['precio_base'];
    // $nombre_servicio = $row['nombre_servicio'];
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

mysqli_close($mysqli1310);
echo json_encode($response);
