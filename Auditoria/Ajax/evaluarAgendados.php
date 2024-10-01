<?php
include('../../_conexion.php');
session_start();
$response 	= array( 'result' => false, 'message' => 'Intentelo nuevamente mas tarde !!');
if(isset($_SESSION['idauditoria'])){
$idauditoria = $_SESSION['idauditoria'];
$data 		= array_map('stripslashes', $_POST );  
date_default_timezone_set('America/Argentina/Buenos_Aires');
$fecha = date("Y-m-d H:i:s");
$idagendado = $data['idAgeEval'];
$nombre_call = $data['nomCall'];
$numero = str_replace("'","",$data['numAge']);
$cedula = str_replace("'","",$data['usuEval']);
$puntaje = str_replace("'","",$data['puntaje']);
$comentario = str_replace("'","",$data['comEval']);
$fecha_agendada = str_replace("'","",$data['fechaAgendada']);
$fecha_de_agendado = str_replace("'","",$data['fechaDeAgendado']);
 
$qCall = "select id from gruposusuarios where nombre = '$nombre_call'";
$rCall =  mysqli_query($mysqli,$qCall);
while($row = mysqli_fetch_array($rCall)) {
    $idcall=$row['id'];
 }
$qUsuario = "select id from usuarios where usuario = '$cedula'";  
$rUsuario = mysqli_query($mysqli,$qUsuario);
 while($row = mysqli_fetch_array($rUsuario)) {
    $idusuario=$row['id'];
 }
 
 $q = "insert into evaluacionagendados values(null,$idagendado,$idcall,'$numero',$idusuario,$puntaje,'$comentario','$fecha_agendada','$fecha_de_agendado','$fecha',$idauditoria)";
 $result = mysqli_query($mysqli,$q);
 if($result){
    $q2 = "update agendados set evaluado = 'Si' where id = $idagendado";
    $result2 = mysqli_query($mysqli,$q2);
    if($result2){
        $response = array( 'result' => true, 'message' => 'Correcto.', 'idagendado' => $idagendado);
    }
 }
 
}else{
   $response 	= array( 'result' => false, 'message' => 'Sin Sesion'); 
}
mysqli_close($mysqli);
echo json_encode( $response );
?>