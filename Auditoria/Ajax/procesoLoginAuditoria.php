<?php
include('../../_conexion.php');
session_start();
$_SESSION = array();
session_destroy();
session_regenerate_id(true);
session_start();

$data 		= array_map('stripslashes', $_POST );
$response 	= array( 'result' => false, 'message' => 'Intentelo nuevamente mas tarde !!' );

if( $data ){
    $usuario 	= str_replace("'","",$data['usuario']);
    $contrasena 	= str_replace("'","",$data['contrasena']); 
    
    $q = "select * from auditoriausuarios where usuario = '$usuario' and contrasena = '$contrasena' and activo = 1";

    $result = mysqli_query($mysqli,$q);
    
    while ($row = mysqli_fetch_assoc($result)) {
        $idauditoria = $row['id'];
		$nombre =  $row['nombre_auditora'];
	}
    $devuelve = mysqli_affected_rows($mysqli);
    
    if($devuelve>0){
        $_SESSION["idauditoria"] = $idauditoria;
        $_SESSION["nombre_auditora"] = $nombre;
        $response = array( 'result' => $devuelve, 'message' => 'Correcto.' );    
    }else{
        $response = array( 'result' => false, 'message' => 'Error de usuario o contraseña' ); 
    }
 
 mysqli_close($mysqli);   
 echo json_encode( $response );
}
?>