<?php
include('../_conexion.php');
session_start();
if(isset($_SESSION['idusuario'])){
    if(isset($_SESSION["numero"])){
      $numero = $_SESSION["numero"];  
    }else{
      $numero = 'sin numero';
    }
	
    $fecha_actual = date("Y-m-d");
    $fecha_60 = date("Y-m-d",strtotime($fecha_actual."- 60 days")); 
    
    $q = "select historico.estado,historico.fecha,detalles.integrantes_familia,detalles.direccion,detalles.otro_servicio,detalles.observaciones from historico left join detalles on historico.id = detalles.idhistorico where numero = '$numero' and cast(historico.fecha as date) >= '$fecha_60' order by fecha asc";
    $result = mysqli_query($mysqli,$q);
    $info = array();
    while($row = mysqli_fetch_array($result)) {
        $estado=$row['estado'];
        $fecha=$row['fecha'];
        $integrantes=$row['integrantes_familia'];
        $direccion=$row['direccion'];
        $otro_servicio=$row['otro_servicio'];
        $observaciones=$row['observaciones'];
        if($integrantes == ''){
            $integrantes = '-';
        }
        if($direccion==''){
            $direccion = '-';
        }
        if($otro_servicio==''){
            $otro_servicio = '-';
        }
        if($observaciones==''){
           $observaciones = '-'; 
        }
        
        $info[] =  array('estado'=> $estado, 'fecha'=> $fecha, 'integrantes'=> $integrantes, 'direccion'=> $direccion, 'otro_servicio'=> $otro_servicio, 'observaciones'=>$observaciones);
    }
}
    mysqli_close($mysqli);
    echo json_encode($info);
    
?>