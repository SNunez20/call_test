<?php
include('../_conexion.php');
session_start();
$_SESSION = array();
session_destroy();

// session_regenerate_id(true);
session_start();

$data 		= array_map('stripslashes', $_POST );
$response 	= array( 'result' => false, 'message' => 'Intentelo nuevamente mas tarde !!' );

if( $data ){
    $usuario 	= str_replace("'","",$data['usuario']);
    $contrasena 	= str_replace("'","",$data['contrasena']); 
    
    $q = "select * from usuarios where usuario = '$usuario' and contrasena = '$contrasena' and activo = 1";

    $result = mysqli_query($mysqli,$q);
    
    while ($row = mysqli_fetch_assoc($result)) {
        $idusuario = $row['id'];
		$usuario = $row['usuario'];
        $grupoUsuario = $row['idgrupo'];
        $nombreUsuario = $row['nombre'];
	}
    $result2 = mysqli_affected_rows($mysqli);
    
    if($result2>0){
        $_SESSION["idusuario"] = $idusuario;
        $_SESSION["grupoUsuario"] = $grupoUsuario;
        $_SESSION["nombreUsuario"] = $nombreUsuario;
        $_SESSION["cedulaUsuario"] = $usuario;
        $response = array( 'result' => $result2, 'message' => 'Correcto.','idUser' => $idusuario,'nombreUser' => $nombreUsuario,'numeroVendedor' => $usuario);    
    }else{
        $response = array( 'result' => false, 'message' => 'Error de usuario o contraseña' ); 
    }
}
 mysqli_close($mysqli);   
 echo json_encode( $response );
?>