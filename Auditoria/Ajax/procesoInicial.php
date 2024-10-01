<?php
include('../../_conexion.php');
session_start();
$response 	= array( 'result' => false, 'message' => 'Intentelo nuevamente mas tarde !!' );

if(isset($_SESSION['idauditoria'])){
    $idauditoria = $_SESSION['idauditoria'];
    $nombreAuditoria = $_SESSION["nombre_auditora"];
    date_default_timezone_set('America/Argentina/Buenos_Aires');
    $fecha = date("Y-m-d H:i:s");
    
    $q = "update auditoriausuarios set ultimo_acceso = '$fecha' where id = $idauditoria";
    $result = mysqli_query($mysqli,$q);
    if($result){
        $response = array( 'result' => true, 'message' => 'Correcto.', 'nombre' => $nombreAuditoria, 'grupo' => 'Auditoria', 'nomGrupo' => 'Auditoria'); 
    }
}else{
   $response 	= array( 'result' => false, 'message' => 'Sin Sesion'); 
}
mysqli_close($mysqli);
echo json_encode( $response );
?>