<?php
session_start();
include('../_conexion250.php');
$response 	= array( 'result' => false, 'message' => 'Intentelo nuevamente mas tarde !!');
if(isset($_SESSION['idusuario'])){
    $data 		= array_map('stripslashes', $_POST );

    $cedula = $data['cedula'];
    date_default_timezone_set('America/Argentina/Buenos_Aires');
    $fecha = date("Y-m-d H:i:s");
    $tRojo = "A esta persona no se le puede vender servicio (tradicional), solo productos acotados con tarjeta.";
    $tRojo2 = "Figura en padron.";
    $tAmarillo = "A esta persona se le puede vender CON tarjeta, pero SIN promo (tradicional) o productos acotados con cualquier medio de pago.";
    $tVerdeClaro = "A esta persona se le puede vender CON tarjeta y CON promo (tradicional) o productos acotados con cualquier medio de pago.";
    $tVerde = "A esta persona se le puede vender con TODO (tradicional) o productos acotados con cualquier medio de pago.";
    $meses = "none";
    
    $q = "select * from padron_datos_socio where cedula = '$cedula'";
    $result = mysqli_query($mysqli250,$q);
    $devuelve = mysqli_num_rows($result);

    if ($devuelve > 0){
        //ACA SI ESTA EN PADRON
        $color = "rojo_claro";
        $color_code = "#edb9b9";
        $font_color = "black";
        $texto = $tRojo2;
    }else{
        mysqli_close($mysqli250);
        include('../_conexion.php');
        $q = "select fecha_baja from bajas where cedula = '$cedula'";
        $result = mysqli_query($mysqli,$q);
        $devuelve = mysqli_num_rows($result);
        if ($devuelve > 0) {
            //ACA SI ESTA EN BAJAS
            while ($row = mysqli_fetch_assoc($result)) {
                $fecha_baja = $row['fecha_baja'];
                $tipo_producto = $row['tipo'];
            }

            $fechaBaja = new DateTime($fecha_baja);
            $fechaHoy = new DateTime($fecha);
            $interval = $fechaHoy->diff($fechaBaja);
            $meses = ($interval->y * 12) + $interval->m;

            if ($meses < 7) {
                $color = "rojo";
                $color_code = "red";
                $font_color = "white";
                $texto = $tRojo;
            } elseif ($meses >= 7 && $meses <= 11) {
                $color = "amarillo";
                $color_code = "yellow";
                $font_color = "black";
                $texto = $tAmarillo;
            } elseif ($meses >= 12 && $meses <= 23) {
                $color = "verde_claro";
                $color_code = "#9FF781";
                $font_color = "black";
                $texto = $tVerdeClaro;
            } elseif ($meses >= 24) {
                $color = "verde";
                $color_code = "green";
                $font_color = "white";
                $texto = $tVerde;
            }
        }else{
            //ACA SI NO ES SOCIO NI ESTA DE BAJA
            $color = "verde";
            $color_code = "green";
            $font_color = "white";
            $texto = $tVerde;
        }
    }
    if ($color != "") {
        $response = array( 'result' => true, 'message' => 'Correcto', 'meses' => $meses, 'color' => $color, 'color_code' => $color_code, 'font_color' => $font_color, 'texto' => $texto);
    }
}else{
    $response 	= array( 'result' => false, 'message' => 'Sin Sesion'); 
}

mysqli_close($mysqli);
echo json_encode($response);
