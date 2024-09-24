<?php
include('../_conexion.php');
session_start();
$response 	= array( 'result' => false, 'message' => 'Intentelo nuevamente mas tarde !!');
if(isset($_SESSION['idusuario'])){
$idusuario = $_SESSION['idusuario'];
date_default_timezone_set('America/Argentina/Buenos_Aires');
$fecha2 = date("Y-m-d");

$q = "select * from agendados where date(fecha_agendado) = '$fecha2' && usuarioid = $idusuario";
$result = mysqli_query($mysqli,$q);
$devuelve = mysqli_num_rows($result);
$response 	= array( 'result' => true, 'hoy' => $devuelve);
}
mysqli_close($mysqli);
echo json_encode( $response );
?>