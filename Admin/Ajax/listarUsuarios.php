<?php
include('../../_conexion.php');
session_start();
if(isset($_SESSION['idadmin'])){
    $tipo_admin = $_SESSION["tipo_admin"];
    $grupo = $_SESSION["grupo_usuarios"];
    if($tipo_admin == 'normal'){
        $where = "where usuarios.idgrupo = $grupo and usuarios.activo != 2";
    }else{
        $where = "where usuarios.activo != 2";
    }
    $q = "select usuarios.id,usuarios.nombre ,usuarios.usuario,usuarios.activo,gruposusuarios.nombre as nomGrupo from usuarios inner join gruposusuarios on usuarios.idgrupo = gruposusuarios.id $where";
    $result = mysqli_query($mysqli,$q);
    $usuarios = array();
    
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
        
        $usuarios[] =  array('id'=> $id, 'nombre'=> $nombre, 'cedula'=> $cedula, 'grupo'=> $grupo, 'activo'=> $activo);
    }
}else{
   $response 	= array( 'result' => false, 'message' => 'Sin Sesion'); 
}
    mysqli_close($mysqli);
    echo json_encode($usuarios);
?>