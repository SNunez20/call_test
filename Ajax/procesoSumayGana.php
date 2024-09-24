<?php
session_start();
$response 	= array( 'result' => false, 'message' => 'Intentelo nuevamente mas tarde !!', 'repetido' => false);

if(isset($_SESSION['idusuario'])){
    $idusuario = $_SESSION['idusuario'];
    $cedusuario = $_SESSION["cedulaUsuario"];
    $data 		= array_map('stripslashes', $_POST );
    $cedula = str_replace("'","",$data['cedSum']);
    $telefono = str_replace("'","",$data['telSum']);
    include('../_conexion.php');
    $qUCall = "select * from usuarios where usuario = '$cedula'";
    $rUCall = mysqli_query($mysqli,$qUCall);
    $usuario_call = mysqli_num_rows($rUCall);

    if($usuario_call == 0){
        mysqli_close($mysqli);
        include('../_conexionDesafio.php');
        $qBloqueo = "select * from bloqueo";
        $rBloqueo = mysqli_query($mysqli,$qBloqueo);
        while ($row = mysqli_fetch_assoc($rBloqueo)) {
            $activo = $row['activo']; 
        }
        if($activo == 1){
            date_default_timezone_set('America/Argentina/Buenos_Aires');
            $fecha = date("Y-m-d H:i:s");
            
            if($cedula != ""){
                $q = "select * from jugadores_referidos where cedula_jugador = '$cedula' and vencido != 2";
                $result = mysqli_query($mysqli,$q);
                $devuelve = mysqli_num_rows($result);
                $q2 = "select * from usuarios where cedula = '$cedula'";
                $result2 = mysqli_query($mysqli,$q2);
                $devuelve2 = mysqli_num_rows($result2);
                $qRefirio = "select * from jugadores_referidos where cedula_jugador = '$cedula' and idusuario_call = $idusuario";
                $rReferido = mysqli_query($mysqli,$qRefirio);
                $devuelve3 = mysqli_num_rows($rReferido);
            }else{
                $devuelve = 0;
                $devuelve2 = 0;
                $devuelve3 = 0;
            }

            if($devuelve == 0 && $devuelve2 == 0 && $devuelve3 == 0){
                $qTel = "select * from jugadores_referidos where telefono_jugador = '$telefono'";
                $rTel = mysqli_query($mysqli,$qTel);
                $devuelve4 = mysqli_num_rows($rTel);
                
                if($devuelve4 == 0){
                    $q3 = "insert into jugadores_referidos values(null,$idusuario,'$cedusuario','$cedula','$telefono',0,0,'$fecha')";
                    $result3 = mysqli_query($mysqli,$q3);
                    if($result3){
                        $prefijo = substr($telefono,0,2);
                        if($prefijo == "09" && strlen($telefono) == 9){
                            //file_get_contents("https://www.eldesafio.com.uy/sms_call.php?celular=".$telefono);
                        }
                        $response 	= array( 'result' => true, 'repetido' => false);
                    } 
                }else{
                   $response 	= array( 'result' => false, 'tel_repetido' => true); 
                }
                
            }else{
                $response 	= array( 'result' => false, 'repetido' => true);
            }
        }else{
            $response 	= array( 'result' => false, 'bloqueo' => true);
        }
    }else{
       $response 	= array( 'result' => false, 'message' => 'Usuario de call', 'usuario_call' => true); 
    }
}else{
   $response 	= array( 'result' => false, 'message' => 'Sin Sesion', 'repetido' => false); 
}
mysqli_close($mysqli);
echo json_encode( $response );
?>