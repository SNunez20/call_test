<?php
include('../_conexionDesafio.php');
session_start();
if(isset($_SESSION['idusuario'])){
    $idusuario = $_SESSION['idusuario'];
    $q = "select sum(saldo) as saldo from jugadores_referidos where idusuario_call = $idusuario GROUP BY idusuario_call";
    $result = mysqli_query($mysqli,$q);
    while($row = mysqli_fetch_assoc($result)) {
        $saldo = $row['saldo'];
    }
    
    $q2 = "select j.cedula_jugador,t.monto,t.fecha,j.telefono_jugador from jugadores_referidos as j inner join transacciones_vendedores as t on j.id = t.idreferido where idusuario_call = $idusuario";
    $result2 = mysqli_query($mysqli,$q2);

    $suma = array();
    
    while($row = mysqli_fetch_array($result2)) {
        $cedula=$row['cedula_jugador'];
        $monto=$row['monto'];
        $fecha=$row['fecha'];
        $telefono=$row['telefono_jugador'];
       
        
        $suma[] =  array('cedula'=> $cedula,'monto'=>$monto, 'fecha'=> $fecha, 'saldo'=>$saldo, 'telefono' =>$telefono);
    }
}
    mysqli_close($mysqli);
    echo json_encode($suma);
?>