<?php
include('../../_conexion.php');
session_start();
if(isset($_SESSION['idadmin'])){
    $tipo_admin = $_SESSION["tipo_admin"];
    $call = $_GET['call'];
    
    $q = "select relacion.id,gruposusuarios.nombre,relacion.idgrupotel from relacion LEFT JOIN gruposusuarios on relacion.idgrupousuarios = gruposusuarios.id where relacion.idgrupousuarios = $call";
    $result = mysqli_query($mysqli,$q);
    $grupos = array();
    
     while($row = mysqli_fetch_array($result)) {
        $id = $row['id'];
        $call = $row['nombre'];
        $grupo = $row['idgrupotel'];

        $grupos[] =  array('id'=> $id, 'call'=> $call, 'grupo'=> $grupo);
    }
    
}else{
   $response 	= array( 'result' => false, 'message' => 'Sin Sesion'); 
}
    mysqli_close($mysqli);
    echo json_encode($grupos);
?>