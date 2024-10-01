<?php
include('../../_conexion.php');
session_start();
$response 	= array( 'result' => false, 'message' => 'Intentelo nuevamente mas tarde !!');

if(isset($_SESSION['idadmin'])){
    $idadmin = $_SESSION['idadmin'];
    $grupoAdmin = $_SESSION["grupo_usuarios"];
    $tipoAdmin = $_SESSION["tipo_admin"];
    $data 		= array_map('stripslashes', $_POST );
    $numero =str_replace("'","",$data['numQuitar']);
    $observacion =str_replace("'","",$data['obsQuitar']);
    date_default_timezone_set('America/Argentina/Buenos_Aires');
    $fecha = date("Y-m-d H:i:s");

    $q = "select * from listanegra where numero = '$numero'";
    $result = mysqli_query($mysqli,$q);
    $devuelve = mysqli_num_rows($result);
    if($devuelve==0){
        $q2 = "insert into listanegra values(null,'$numero','$observacion','$fecha',0)";
        $result2 = mysqli_query($mysqli,$q2);
        if($result2){
            $q3 = "update numeros set flag = 'borrado permanente' where numero = '$numero'";
            $result3 = mysqli_query($mysqli,$q3);
            if($result3){
                $q4 = "delete from agendados where numero = '$numero'";
                $result4 = mysqli_query($mysqli,$q4);
                if($result4){
                    $q5 = "delete from referidos where numero = '$numero'";
                    $result5 = mysqli_query($mysqli,$q5);
                    if($result5){
                       $q6 = "update session set numero = '' where numero = '$numero'";
                       $result6 = mysqli_query($mysqli,$q6);
                       if($result6){
                            $q7 = "delete from referidoscuaderno where numero = '$numero'";
                            $result7 = mysqli_query($mysqli,$q7);
                            if($result7){
                                $response = array( 'result' => true, 'repetido' => false, 'message' => 'Correcto.'); 
                            }
                       }
                    }
                }
            }
        } 
    }else{
       $response = array( 'result' => true, 'repetido' => true, 'message' => 'Correcto.'); 
    }
    
}else{
    $response 	= array( 'result' => false, 'message' => 'Sin Sesion'); 
}

mysqli_close($mysqli);
echo json_encode( $response );
?>