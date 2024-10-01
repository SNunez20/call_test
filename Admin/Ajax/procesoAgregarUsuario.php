<?php
include('../../_conexion.php');
session_start();
$response 	= array( 'result' => false, 'message' => 'Intentelo nuevamente mas tarde !!');

if(isset($_SESSION['idadmin'])){
    $idadmin = $_SESSION['idadmin'];
    $grupoAdmin = $_SESSION["grupo_usuarios"];
    $tipoAdmin = $_SESSION["tipo_admin"];
    $data 		= array_map('stripslashes', $_POST );
    $nombre =str_replace("'","",$data['nomUsu']);
    $cedula =str_replace("'","",$data['cedUsu']);
    if(isset($data['grupoUsu'])){
        $grupo = str_replace("'","",$data['grupoUsu']);
    }else{
        $grupo = $grupoAdmin;
    }
    $q = "select * from usuarios where usuario = '$cedula'";
    $result = mysqli_query($mysqli,$q);
    $devuelve = mysqli_num_rows($result);
    if($devuelve==0){
        $q2 = "insert into usuarios values(null,'$nombre','$cedula','$cedula',$grupo,1,'','')";
        $result2 = mysqli_query($mysqli,$q2);
        if($result2){
            $response = array( 'result' => true, 'repetido' => false, 'message' => 'Correcto.', 'grupo' => $grupo);
        } 
    }else{
       $response = array( 'result' => true, 'repetido' => true, 'message' => 'Correcto.', 'grupo' => $grupo); 
    }
    
}else{
    $response 	= array( 'result' => false, 'message' => 'Sin Sesion'); 
}

mysqli_close($mysqli);
echo json_encode( $response );
?>