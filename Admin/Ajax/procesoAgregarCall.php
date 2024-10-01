<?php
include('../../_conexion.php');
session_start();
$response 	= array( 'result' => false, 'message' => 'Intentelo nuevamente mas tarde !!');

if(isset($_SESSION['idadmin'])){
    $tipo_admin = $_SESSION['tipo_admin']; 
    if($tipo_admin == 'full'){
        $idadmin = $_SESSION['idadmin'];
        $data 		= array_map('stripslashes', $_POST );
        $nombre =str_replace("'","",$data['nomCall']);
        $qRepetido = "select * from gruposusuarios where nombre = '$nombre'";
        $resultRepetido = mysqli_query($mysqli,$qRepetido);
        $devuelve = mysqli_num_rows($resultRepetido);
        if($devuelve > 0){
            $response 	= array( 'result' => false, 'message' => 'Grupo repetido !!', 'repetido' => true);
        }else{
            $q = "insert into gruposusuarios(nombre) values('$nombre')";
            $result = mysqli_query($mysqli,$q);
            $idcall = mysqli_insert_id($mysqli);
            if($result){
                $response = array( 'result' => true, 'message' => 'Correcto.', 'idcall' => $idcall, 'nombrecall' => $nombre);
            }
        }
    }
}else{
    $response 	= array( 'result' => false, 'message' => 'Sin Sesion'); 
}
mysqli_close($mysqli);
echo json_encode( $response );
?>