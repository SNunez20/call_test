<?php
include('../_conexion.php');
session_start();
$response 	= array( 'result' => false, 'message' => 'Intentelo nuevamente mas tarde !!', 'repetido' => false);

if(isset($_SESSION['idusuario'])){
    $idusuario = $_SESSION['idusuario'];
    $data 		= array_map('stripslashes', $_POST );
    $numeroReferido = str_replace("'","",$data['numRefCuaderno']);
    $qEstaRef = "select * from referidos where numero = '$numeroReferido'";
    $resultEstaRef = mysqli_query($mysqli,$qEstaRef);
    $devuelveRef = mysqli_num_rows($resultEstaRef);
    $qEstaAge = "select * from agendados where numero = '$numeroReferido'";
    $resultEstaAge = mysqli_query($mysqli,$qEstaAge);
    $devuelveAge = mysqli_num_rows($resultEstaAge);
    $qEstaEnUso = "select * from session where numero = '$numeroReferido'";
    $resultEstaEnUso = mysqli_query($mysqli,$qEstaEnUso);
    $devuelveEstaEnUso = mysqli_num_rows($resultEstaEnUso);
    $qListaNegra = "select * from listanegra where numero = '$numeroReferido'";
    $resultListaNegra = mysqli_query($mysqli,$qListaNegra);
    $devuelveListaNegra = mysqli_num_rows($resultListaNegra);
    $qEstaRefCuaderno = "select * from referidoscuaderno where numero = '$numeroReferido'";
    $resultEstaRefCuaderno = mysqli_query($mysqli,$qEstaRefCuaderno);
    $devuelveEstaRefCuaderno = mysqli_num_rows($resultEstaRefCuaderno);
    $nombre = str_replace("'","",$data['nomRefCuaderno']);
    $observacion = str_replace("'","",$data['obsRefCuaderno']);
    date_default_timezone_set('America/Argentina/Buenos_Aires');
    $fecha = date("Y-m-d H:i:s");
    $repetido = false;
    if($devuelveRef==0 and $devuelveAge==0 and $devuelveEstaEnUso==0 and $devuelveListaNegra==0 and $devuelveEstaRefCuaderno==0){
        $qSeUso = "select * from numeros where numero = '$numeroReferido'";
        $resultSeUso = mysqli_query($mysqli,$qSeUso);
        $devuelveSeUso = mysqli_num_rows($resultSeUso);
        while ($row = mysqli_fetch_assoc($resultSeUso)) {
            $flag = $row['flag']; 
        }
        if(($devuelveSeUso>0 && $flag=='libre')or ($devuelveSeUso==0)){
            $chk = '';
            $q = "update numeros set flag = 'referido' where numero = '$numeroReferido'";
            $result = mysqli_query($mysqli,$q);
            if($result){
                $q2 = "insert into historico values(null,'$numeroReferido','referido cuaderno',$idusuario,'$fecha','$chk')";
                $result2 = mysqli_query($mysqli,$q2);
                $idhistorico = mysqli_insert_id($mysqli);
                if($result2){
                    $q3 = "insert into referidoscuaderno values(null,$idhistorico,$idusuario,'$numeroReferido','$nombre','$observacion','$fecha')";
                    $result3 = mysqli_query($mysqli,$q3);
                    if($result3){
                    if($devuelveSeUso == 0){//AGREGAR EL NUMEROS A LA LISTA DE NUMEROS REFERIDOS QUE NO ESTAN EN LA TABLA GENERAL DE NUMEROS
                        $qRepetido = "select * from numerosreferidos where numero = '$numeroReferido'";
                        $rRepetido = mysqli_query($mysqli,$qRepetido);
                        $devuelveRepetido = mysqli_num_rows($rRepetido);
                        if($devuelveRepetido == 0){
                            $qAgregarNumero = "insert into numerosreferidos values(null,$idhistorico,$idusuario,'$numeroReferido','$nombre','$observacion','$fecha')";
                            $rAgregarNumero = mysqli_query($mysqli,$qAgregarNumero);
                            if($rAgregarNumero){//AGREGAR NUMERO A LISTA DE NUMEROS GENERAL
                                $a1 = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J");
                                $grupo = $a1[array_rand($a1)];
                                $qAgregarNumero2 = "insert into numeros values(null,'$numeroReferido','$grupo', 'referido', 0, 'Localidad Desconocida')";
                                $rAgregarNumero2 = mysqli_query($mysqli,$qAgregarNumero2);
                            }
                        }
                    }
                        $resetNoContesta = "update numeros set no_contesta = 0 where numero = '$numeroReferido'";
                        $resultReset = mysqli_query($mysqli,$resetNoContesta);
                        $response = array( 'result' => 'NumeroDespuesReferido', 'message' => 'Correcto.', 'repetido' => false, 'age' => $devuelveAge, 'ref' => $devuelveRef, 'seuso' => $devuelveSeUso, 'enuso'=> $devuelveEstaEnUso);
                    }
                }   
            }
        }else{
           $repetido = true;
           $response 	= array( 'result' => false, 'repetido' => true, 'age' => $devuelveAge, 'ref' => $devuelveRef, 'enuso'=> $devuelveEstaEnUso, 'seuso' => $devuelveSeUso);
        }
    }else{
        $repetido = true;
        $response 	= array( 'result' => false, 'repetido' => true, 'age' => $devuelveAge, 'ref' => $devuelveRef, 'enuso'=> $devuelveEstaEnUso);
    }
}else{
   $response 	= array( 'result' => false, 'message' => 'Sin Sesion', 'repetido' => false); 
}

if($repetido == true){
    $qrefCuaderno = "select * from referidoscuaderno_pendientes where numero = '$numeroReferido' and idusuario = $idusuario";
    $rrefCuaderno = mysqli_query($mysqli,$qrefCuaderno);
    $devuelveRefCuaderno = mysqli_num_rows($rrefCuaderno);
    if ($devuelveRefCuaderno == 0) {
        $qAgregarNumeroReservado = "insert into referidoscuaderno_pendientes values(null,$idusuario,'$numeroReferido','$nombre','$observacion','$fecha')";
        $rAgregarNumeroReservado = mysqli_query($mysqli, $qAgregarNumeroReservado);
        if(!$rAgregarNumeroReservado){
            $response 	= array( 'result' => false, 'message' => 'Error al agregar numero referido reservado', 'repetido' => false);
        }
    }else{
        $response 	= array( 'result' => false, 'message' => 'Usted ya tiene ese numero.', 'repetido_cuaderno_pendiente' => true);
    }
}

mysqli_close($mysqli);
echo json_encode( $response );
?>