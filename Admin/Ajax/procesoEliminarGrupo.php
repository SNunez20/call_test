<?php
include('../../_conexion.php');
session_start();
$response 	= array( 'result' => false, 'message' => 'Intentelo nuevamente mas tarde !!');

if(isset($_SESSION['idadmin'])){
    $idgrupo = $_GET['idgrupo'];
    $q = "delete from relacion where id = $idgrupo";
    $result = mysqli_query($mysqli,$q);
    if($result){
        $response 	= array( 'result' => true, 'message' => 'Correcto !!');
    }
}else{
    $response 	= array( 'result' => false, 'message' => 'Sin Sesion'); 
}

mysqli_close($mysqli);
echo json_encode( $response );
?>