<?php
include('../../_conexion.php');
session_start();
if(isset($_SESSION['idadmin'])){
    $tipo_admin = $_SESSION["tipo_admin"];
    $grupo = $_SESSION["grupo_usuarios"];

    $q = "select admin.id,admin.nombre ,admin.usuario,admin.activo,gruposusuarios.nombre as nomGrupo from admin inner join gruposusuarios on admin.grupo_usuarios = gruposusuarios.id where admin.activo != 2 and admin.grupo_usuarios != 0";
    $result = mysqli_query($mysqli,$q);
    $admins = array();
    
     while($row = mysqli_fetch_array($result)) {
        $id = $row['id'];
        $nombre = $row['nombre'];
        $cedula = $row['usuario'];
        $activo = $row['activo'];
        $grupo = $row['nomGrupo'];
        if($activo == 1){
            $activo = "Si";
        }else{
            $activo = "No";
        }
        
        $admins[] =  array('id'=> $id, 'nombre'=> $nombre, 'cedula'=> $cedula, 'grupo'=> $grupo, 'activo'=> $activo);
    }
    
}else{
   $response 	= array( 'result' => false, 'message' => 'Sin Sesion'); 
}
    mysqli_close($mysqli);
    echo json_encode($admins);
?>