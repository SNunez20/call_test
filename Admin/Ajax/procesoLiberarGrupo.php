<?php
include('../../_conexion.php');
session_start();
$response 	= array( 'result' => false, 'message' => 'Intentelo nuevamente mas tarde !!');

if(isset($_SESSION['idadmin'])){
    $grupo = $_GET['grupo'];
    $q = "update numeros set no_contesta = 0 WHERE (flag = 'no interesado' or flag = 'no contesta') and (grupo = '$grupo')";
    $result = mysqli_query($mysqli,$q);
    if($result){
        $q2 = "update numeros set flag = 'libre' WHERE (flag = 'no interesado' or flag = 'no contesta') and (grupo = '$grupo')";
        $result2 = mysqli_query($mysqli,$q2);
    }
    if($result && $result2){
        $q3 = "select * from numeros where grupo = '$grupo' and flag = 'libre'";
        $result3 = mysqli_query($mysqli,$q3);
        $total = mysqli_num_rows($result3);
        $response = array( 'result' => true, 'total' => $total);
    }
}else{
    $response 	= array( 'result' => false, 'message' => 'Sin Sesion'); 
}

mysqli_close($mysqli);
echo json_encode( $response );
?>