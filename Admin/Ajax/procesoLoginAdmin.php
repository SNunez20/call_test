<?php
include('../../_conexion.php');
session_start();
$_SESSION = array();
session_destroy();
session_start();
session_regenerate_id(true);

$data 		= array_map('stripslashes', $_POST );
$response 	= array( 'result' => false, 'message' => 'Intentelo nuevamente mas tarde !!' );

if( $data ){
    $usuario 	= str_replace("'","",$data['usuario']);
    $contrasena 	= str_replace("'","",$data['contrasena']); 
    
    $q = "select * from admin where usuario = '$usuario' and contrasena = '$contrasena' and activo = 1";

    $result = mysqli_query($mysqli,$q);
    
    while ($row = mysqli_fetch_assoc($result)) {
        $idadmin = $row['id'];
		$nombre =  $row['nombre'];
        $grupo_usuarios = $row['grupo_usuarios'];
        $tipo_admin = $row['tipo_admin'];
	}
    $devuelve = mysqli_affected_rows($mysqli);
    
    if($devuelve>0){
        $_SESSION["idadmin"] = $idadmin;
        $_SESSION["nombre"] = $nombre;
        $_SESSION["grupo_usuarios"] = $grupo_usuarios;
        $_SESSION["tipo_admin"] = $tipo_admin;
        $response = array( 'result' => $devuelve, 'message' => 'Correcto.' );    
    }else{
        $response = array( 'result' => false, 'message' => 'Error de usuario o contraseña' ); 
    }
 
 mysqli_close($mysqli);   
 echo json_encode( $response );
}
?>