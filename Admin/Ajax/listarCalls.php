<?php
include('../../_conexion.php');
session_start();
if(isset($_SESSION['idadmin'])){
    $tipo_admin = $_SESSION["tipo_admin"];
    
    $q = "select gruposusuarios.id, gruposusuarios.nombre,SUM(usuarios.activo = 1) as vendedor from gruposusuarios left join usuarios on gruposusuarios.id = usuarios.idgrupo GROUP BY gruposusuarios.nombre";
    $result = mysqli_query($mysqli,$q);
    $calls = array();
    $grupos = array();
     while($row = mysqli_fetch_array($result)) {
        $id = $row['id'];
        $nombre = $row['nombre'];
        $vendedor = $row['vendedor'];
        if($vendedor == null){
            $vendedor = 0;
        }
        $q2 = "select * from relacion where idgrupousuarios = $id order by idgrupotel";
        $result2 = mysqli_query($mysqli,$q2);
        
        while($row = mysqli_fetch_array($result2)) {
            $grupos[] = $row['idgrupotel'];
        }
        if($grupos == null){
            $grupos = "(Sin Grupos)";
        }
        $calls[] =  array('id'=> $id, 'nombre'=> $nombre, 'vendedor'=> $vendedor,'grupos' => $grupos);
        $grupos = null;
    }
    
}else{
   $response 	= array( 'result' => false, 'message' => 'Sin Sesion'); 
}
    mysqli_close($mysqli);
    echo json_encode($calls);
?>