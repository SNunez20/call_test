<?php
session_start();
include '../_conexion250.php';
include '../_conexion.php';
$response = array('result' => false, 'message' => 'Intentelo nuevamente mas tarde !!');

if (isset($_SESSION['idusuario']) || (isset($_POST['ptbbddvp']) && $_POST['ptbbddvp'] == true)) {
    $data = array_map('stripslashes', $_POST);

    $cedula = $data['cedula'];
    date_default_timezone_set('America/Argentina/Buenos_Aires');
    $fecha        = date("Y-m-d H:i:s");
    $tRojo        = "No autorizado, consultar con Comercial";
    $tRojo2       = "Figura en padrón.";
    $tVerdeClaro  = "A esta persona se le puede vender SOLO productos acotados con cualquier medio de pago.";
    $tVerdeClaro2 = "A esta persona se le puede vender SOLO productos acotados SOLO con tarjeta de crédito.";
    $tVerde       = "A esta persona se le puede vender con TODO (tradicional) o productos acotados con cualquier medio de pago.";
    $meses        = "none";

    $q        = "select * from padron_datos_socio where cedula = '$cedula'";
    $result   = mysqli_query($mysqli250, $q);
    $devuelve = mysqli_num_rows($result);

    /**
     * 0 - Figura en padrón
     * 1 - No es socio ni esta en baja
     * 2 - Clearing
     * 3 - Todos los productos con cualquier medio de pago
     * 4 - Solo productos acotados con culaquier medio de pago
     * 5 - Solo productos actotados SOLO con tarjeta de crédito
     */
    $code = 0;
    $vuelve_antes = false;

    if ($devuelve > 0) {
        //ACA SI ESTA EN PADRON
        $color      = "rojo_claro";
        $color_code = "#edb9b9";
        $font_color = "black";
        $texto      = $tRojo2;
    } else {
        $q        = "select fecha_baja,tipo_producto,clearing,`count` from bajas where cedula = '$cedula'";
        $result   = mysqli_query($mysqli, $q);
        $devuelve = mysqli_num_rows($result);

        if ($devuelve > 0) {
            //ACA SI ESTA EN BAJAS
            while ($row = mysqli_fetch_assoc($result)) {
                $fecha_baja    = $row['fecha_baja'];
                $tipo_producto = $row['tipo_producto'];
                $clearing      = $row['clearing'];
                $count         = $row['count'];
            }

            if ($clearing == 1) {
                $color      = "rojo";
                $color_code = "red";
                $font_color = "white";
                $texto      = $tRojo;
                $code       = 2;
            } else {
                $fechaBaja = new DateTime($fecha_baja);
                $fechaHoy  = new DateTime($fecha);
                $interval  = $fechaHoy->diff($fechaBaja);
                $meses     = ($interval->y * 12) + $interval->m;

                if ($meses >= 7 || ($count >= 36 && $meses >= 3)) {
                    //ACA SI ESTA EN BAJA PERO YA PASO LAS 7 EMISIONES
                    $color      = "verde";
                    $color_code = "green";
                    $font_color = "white";
                    $texto      = $tVerde;
                    $code       = 3;
                    $vuelve_antes = ($meses < 7) ? true : false;
                } elseif ($tipo_producto == "T") {
                    //ACA SI ES BAJA HACE MENOS DE 7 EMISIONES DE UN PRODUCTO TRADICIONAL
                    $color      = "verde_claro";
                    $color_code = "#9FF781";
                    $font_color = "black";
                    $texto      = $tVerdeClaro;
                    $code       = 4;
                } elseif ($tipo_producto == "A") {
                    //ACA SI ES BAJA HACE MENOS DE 7 EMISIONES DE UN PRODUCTO ACOTADO
                    $color      = "verde_claro2";
                    $color_code = "#cef5c1";
                    $font_color = "black";
                    $texto      = $tVerdeClaro2;
                    $code       = 5;
                }
            }
        } else {
            //ACA SI NO ES SOCIO NI ESTA DE BAJA
            $color      = "verde";
            $color_code = "green";
            $font_color = "white";
            $texto      = $tVerde;
            $code       = 1;
        }
    }
    if ($color != "") {
        $response = array('result' => true, 'message' => 'Correcto', 'meses' => $meses, 'color' => $color, 'color_code' => $color_code, 'font_color' => $font_color, 'texto' => $texto, "code" => $code, "vuelve_antes" => $vuelve_antes);
    }
} else {
    $response = array('result' => false, 'message' => 'Sin Sesion');
}

mysqli_close($mysqli250);
mysqli_close($mysqli);
echo json_encode($response);
