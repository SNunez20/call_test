<?php
session_start();
require_once "../../_conexion250.php";
require_once "../../_conexion.php";
$response = array('result' => false, 'message' => 'Ha ocurrido un error');

if ( isset($_POST['typeAdmin']) ) {
    $data            = array_map(fn($data) => strip_tags(mysqli_real_escape_string($mysqli, $data)), $_POST);
    $id              = $data['id'];
    $servicio        = $data['servicio'];
    $horas           = $data['horas'];
    $importe         = $data['importe'];
    $codpromo        = $data['codpromo'];
    $abm             = $data['abm'];
    $observacion     = $data['observacion'];
    $fechafil        = $data['fechafil'];
    $fechareg        = $data['fechareg'];
    $numero_vendedor = $data['numero_vendedor'];
    $cedula_socio    = $data['cedula'];
    $keepprice       = $data['keepprice'];
   

    $query = "UPDATE padron_producto_socio SET servicio = '$servicio', hora = '$horas', importe = '$importe', fecha_registro ='$fechareg', fecha_afiliacion = '$fechafil', cod_promo = '$codpromo', abm = '$abm', observaciones = '$observacion', numero_vendedor = '$numero_vendedor', keepprice1 =$keepprice WHERE id = $id";

    if ($result= mysqli_query($mysqli, $query)) {

        $qTotal = "SELECT sum(importe) AS total FROM padron_producto_socio WHERE cedula ='$cedula_socio'";
        if ($rAct= mysqli_query($mysqli, $qTotal)) {
            $row =mysqli_fetch_assoc($rAct);
            $totalImporte = $row['total'];
        }

        $qTotal = "UPDATE padron_datos_socio SET total_importe = $totalImporte WHERE cedula = $cedula_socio";
        if ($rTotal= mysqli_query($mysqli, $qTotal)) {
            $response = array('result' => true, 'message' => 'Datos actualizados correctamente');
        }

    }else{
       
        $response = array('message' => 'Fallo la actualización');
    }
}

mysqli_close($mysqli);
echo json_encode($response);
