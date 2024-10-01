<?php
include('../../_conexion.php');
session_start();
$response 	= array( 'result' => false, 'message' => 'Intentelo nuevamente mas tarde !!' );

if(isset($_SESSION['idadmin'])){
    $idadmin = $_SESSION['idadmin'];
    $nombreAdmin = $_SESSION["nombre"];
    $grupoAdmin = $_SESSION["grupo_usuarios"];
    $tipoAdmin = $_SESSION["tipo_admin"];
    $qGrupo = "select * from gruposusuarios where id = $grupoAdmin";
    $resultGrupo = mysqli_query($mysqli,$qGrupo);
    while ($row = mysqli_fetch_assoc($resultGrupo)) {
        $nomGrupo = $row['nombre'];
    }
    if(!isset($nomGrupo)){
        $nomGrupo = "Usuario Full";
    }
    date_default_timezone_set('America/Argentina/Buenos_Aires');
    $fecha = date("Y-m-d H:i:s");
    
    $q = "update admin set ultimo_acceso = '$fecha' where id = $idadmin";
    $result = mysqli_query($mysqli,$q);
    if($result){
        $response = array( 'result' => true, 'message' => 'Correcto.', 'nombre' => $nombreAdmin, 'grupo' => $grupoAdmin, 'nomGrupo' => $nomGrupo); 
    }
}else{
   $response 	= array( 'result' => false, 'message' => 'Sin Sesion'); 
}
mysqli_close($mysqli);
echo json_encode( $response );
?>