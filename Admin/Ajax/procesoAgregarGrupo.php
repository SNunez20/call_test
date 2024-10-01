<?php
include('../../_conexion.php');
session_start();
$response 	= array( 'result' => false, 'message' => 'Intentelo nuevamente mas tarde !!');

if(isset($_SESSION['idadmin'])){
    $idcall = $_GET['idcall'];
    $grupo = $_GET['grupo']; 
    
    $q = "select * from relacion where idgrupousuarios = $idcall and idgrupotel = '$grupo'";
    $result = mysqli_query($mysqli,$q);
    $devuelve = mysqli_num_rows($result);
    if($devuelve > 0){
        $response 	= array( 'result' => false, 'message' => 'Grupo repetido !!', 'repetido' => true);
    } else{
        $q2 = "insert into relacion values(null,$idcall,'$grupo')";
        $result2 = mysqli_query($mysqli,$q2);
        if($result2){
            $response 	= array( 'result' => true, 'message' => 'Grupo repetido !!', 'repetido' => true);
        }
    } 
}else{
    $response 	= array( 'result' => false, 'message' => 'Sin Sesion'); 
}

mysqli_close($mysqli);
echo json_encode( $response );

?>