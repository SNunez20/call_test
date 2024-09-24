<?php
//include('../_conexionVidaShop.php');
session_start();
$response 	= array( 'result' => false, 'message' => 'Intentelo nuevamente mas tarde !!', 'repetido' => false);

if(isset($_SESSION['idusuario'])){
    $idusuario = $_SESSION['idusuario'];
    $data 		= array_map('stripslashes', $_POST );
    $celular = $data['celular'];
    
 /* $cedula = mysqli_real_escape_string($mysqli,$data['cedula']);

    $q = "SELECT cedula FROM usuarios WHERE cedula = '$cedula'";
    $result = mysqli_query($mysqli,$q);
    $ya_registrado = mysqli_num_rows($result);

    if(!$ya_registrado){
        $q2 = "SELECT origen FROM vidapesos_transferidos_pendientes WHERE cedula = '$cedula' AND origen = 'AppCall'";
        $result2 = mysqli_query($mysqli,$q2);
        $ya_recibio = mysqli_num_rows($result2);

        if(!$ya_recibio){
            $q = "INSERT INTO vidapesos_transferidos_pendientes VALUES(null, 'AppCall', '$cedula', 300, '$fecha')";
            $result = mysqli_query($mysqli,$q);

            $id_usuario_vidashop = 0;

            if($result){
                $q2 = "INSERT INTO transferencias VALUES(null, 'AppCall', $idusuario, 0, 300, '$fecha')";
                $result2 = mysqli_query($mysqli,$q2);

                if($result2){*/
                    //mysqli_close($mysqli);
                    include('../_conexion.php');
                    $fecha = date("Y-m-d H:i:s");
                    $cedula = "-";
                    $q = "INSERT INTO vidashop_envio_vidapesos VALUES(null, $idusuario, '$cedula', '$celular', '$fecha')";
                    $result = mysqli_query($mysqli,$q);

                    if($result){
                        $mensaje	= "FELICITACIONES tienes VP:600 para gastar en VIDASHOP el sitio de compras de VIDA, donde tu cuota de afiliacion es GRATIS. Informate en  www.vidashop.com.uy/info2";
                        $servicio="http://192.168.104.6/apiws/1/apiws.php?wsdl";
                        $parametros=array();
                        $a = $parametros['authorizedKey']="9d752cb08ef466fc480fba981cfa44a1";
                        $b = $parametros['msgId']="0";
                        $c = $parametros['msgData']=$mensaje;
                        $d = $parametros['msgRecip']=$celular;
                        $client = new SoapClient($servicio, $parametros);
                        $resultado = $client-> sendSms($a,$b,$c,$d);
                        
                        $response 	= array( 'result' => true, 'message' => 'Correcto');
                     }
                /*}
            }
        }else{
            $response 	= array( 'result' => true, 'message' => 'Ya recibio vidapesos', 'ya_recibio' => true);
        }
    }else{
        $response 	= array( 'result' => true, 'message' => 'Ya registrado', 'ya_registrado' => true); 
    } */

}else{
   $response 	= array( 'result' => false, 'message' => 'Sin Sesion', 'repetido' => false); 
}
mysqli_close($mysqli);
echo json_encode( $response );
?>