<?php
session_start();
require_once "../../_conexion250.php";
$response = array('result' => false, 'message' => 'Ha ocurrido un error');

if ( isset($_POST['typeAdmin']) ) {
    $data      = array_map(fn($data) => strip_tags(mysqli_real_escape_string($mysqli250, $data)), $_POST);
    $id        = $data['id_socio'];
    $cedula    = $data['cedula'];
    $id_socio  = $data['id_socio'];
    $nombre    = strtoupper($data['nombre']);
    $direccion = strtoupper($data['direccion']);
    $telefono  = $data['telefono'];
    $sucursal  = $data['sucursal'];
    $fecha_nacimiento = $data['fecha_nacimiento'];
    $radio     = $data['radio'];
    $ruta      = $data['ruta'];
    $numtar    = $data['numtar'];
    $nomtit    = strtoupper($data['nomtit']);
    $cedtit    = $data['cedtit'];
    $fechafil  = $data['fechafil'];
    $observaciones = strtoupper($data['observaciones']);
    $cedula_titular_gf    = $data['cedula_titular_gf'];

    $qCedula = "SELECT cedula FROM padron_datos_socio WHERE id = $id";
    $result1 = mysqli_query ($mysqli250,$qCedula) or die(mysqli_error($mysqli250));
    while($row = mysqli_fetch_array($result1)){
        $cedula_old = $row['cedula'];
    }
    
    $q = "SELECT * FROM aux2 where ruta='$ruta' and radio='$radio'" ;
	$result2 = mysqli_query ($mysqli250,$q) or die(mysqli_error($mysqli250));
    while($row = mysqli_fetch_array($result2)){
        $empresarut      = $row['empresa_rut'];
        $empresamarca    = $row['empresa_brand'];
        $rutcentralizado = $empresamarca;
    }
    
    $query = "UPDATE padron_datos_socio SET cedula = '$cedula', nombre = '$nombre', direccion = '$direccion', tel = '$telefono', sucursal = '$sucursal', radio = '$radio', fecha_nacimiento = '$fecha_nacimiento', ruta = '$ruta', numero_tarjeta = '$numtar', nombre_titular = '$nomtit', cedula_titular = '$cedtit',fechafil ='$fechafil', observaciones = '$observaciones', idrelacion='$empresarut-$cedula', empresa_rut='$empresarut', empresa_marca='$empresamarca' WHERE id = '$id_socio'";
    $result3= mysqli_query($mysqli250, $query);

    $query2 = "UPDATE padron_producto_socio SET cedula='$cedula',idrelacion='$empresarut-$cedula', cedula_titular_gf = '$cedula_titular_gf'  WHERE cedula = '$cedula_old'";
    $result4 = mysqli_query($mysqli250,$query2);

    if ($result3 && $result4) {
        $response = array('result' => true, 'message' => 'Datos actualizados correctamente','cedula' =>$cedula);
    }else{
        $response = array('result' => false, 'message' => 'Ocurrio un error al actualizar');
    }
}

mysqli_close($mysqli250);
echo json_encode($response);
