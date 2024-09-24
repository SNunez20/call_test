<?php
include('../_conexionDesafio.php');
session_start();
if(isset($_SESSION['idusuario'])){
    $idusuario = $_SESSION['idusuario'];
    $q = "select * from jugadores_referidos where idusuario_call = $idusuario";
    $result = mysqli_query($mysqli,$q);
    $suma2 = array();
    
    while($row = mysqli_fetch_assoc($result)) {
        $cedula = $row['cedula_jugador'];
        $vencido = $row['vencido'];
        if($vencido == 0){
            $vencido = "NO";
        }else{
            $vencido = "SI";
        }
        $fecha = $row['fecha'];
        $telefono=$row['telefono_jugador'];
        $suma2[] =  array('cedula'=> $cedula, 'vencido'=> $vencido, 'fecha'=> $fecha, 'telefono' =>$telefono);
    }
}
    mysqli_close($mysqli);
    echo json_encode($suma2);
?>