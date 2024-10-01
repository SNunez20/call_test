<?php
require '../../_conexion.php';
require '../../_conexion250.php';

$fecha_desde = mysqli_real_escape_string($mysqli, $_POST['fecha_desde']);
$fecha_hasta = mysqli_real_escape_string($mysqli, $_POST['fecha_hasta']);
$filial = mysqli_real_escape_string($mysqli, $_POST['filial']);
$nombreFilial = mysqli_real_escape_string($mysqli, $_POST['nombreFilial']);
$res = array('result' =>false);

if ($fecha_desde=='') {
    // $fecha_hasta = date('Y-m-d');
    // $fecha_desde = date("Y-m-d",strtotime($fecha_hasta."- 1 month"));
 
    // $qCedulasPadron = "SELECT cedula FROM padron_datos_socio WHERE abmactual = '1'";
    $qCedulasPadron = "SELECT cedula FROM padron_producto_socio WHERE abmactual = '1' GROUP BY cedula";
    $rCedulasPadron = mysqli_query($mysqli250, $qCedulasPadron);
 
    if (mysqli_num_rows($rCedulasPadron) > 0) {
         mysqli_query($mysqli, "DELETE FROM cedulas_padron");
         while ($row = mysqli_fetch_assoc($rCedulasPadron)) {
             mysqli_query($mysqli, "INSERT INTO cedulas_padron VALUES (null,'".$row['cedula']."')");
         }
    }
 
    $where = "AND p.cedula IN (SELECT cedula FROM cedulas_padron)";
    $where2 = "AND pp.cedula IN (SELECT cedula FROM cedulas_padron)";
    $w2 = "AND cedula IN (SELECT cedula FROM cedulas_padron)";
    
}else{
     $fecha_desde = date('Y-m-d', strtotime($fecha_desde));
     $fecha_hasta = date("Y-m-d",strtotime($fecha_hasta));
     $where =" AND CAST(p.fechafil as date) >= '$fecha_desde' AND cast(p.fechafil as date) <= '$fecha_hasta'";
     $where2 =" AND CAST(pp.fecha_registro as date) >= '$fecha_desde' AND cast(pp.fecha_registro as date) <= '$fecha_hasta'";
     $w2 = " AND CAST(fecha_registro as date) >= '$fecha_desde' AND cast(fecha_registro as date) <= '$fecha_hasta'";
}

$qFiliales = "SELECT p.cedula, p.sucursal
    FROM padron_datos_socio p 
    INNER JOIN usuarios u ON p.id_usuario = u.id
    INNER JOIN gruposusuarios g ON u.idgrupo = g.id
    INNER JOIN filiales f ON p.sucursal = f.nro_filial
    INNER JOIN padron_producto_socio pp ON p.cedula = pp.cedula
    WHERE p.estado = 6 AND p.sucursal = $filial $where2 
    GROUP BY p.cedula";

    // var_dump($qFiliales); exit;

$rFiliales = mysqli_query($mysqli, $qFiliales);
$arrCedulas=[];
if (mysqli_num_rows($rFiliales) > 0) {
    $res['result']=true;
    while ($row = mysqli_fetch_assoc($rFiliales)) {
        $cedulasocio= $row['cedula'];
        array_push($arrCedulas,$cedulasocio);
    }
    $qListServicios = "SELECT servicio FROM padron_producto_socio WHERE cedula IN (".implode(',',$arrCedulas).") AND abm<>'0' 
    GROUP BY servicio";
    // var_dump($qListServicios); exit;
    $rListServicios = mysqli_query($mysqli, $qListServicios);

    if (mysqli_num_rows($rListServicios) > 0) {
        
        while ($row2 = mysqli_fetch_array($rListServicios)) {
            $servicio = $row2['servicio'];
            $qNombreServi = "SELECT nombre_servicio FROM servicios WHERE nro_servicio = '$servicio'";
            $rNombreServi = mysqli_query($mysqli,  $qNombreServi);
            $nombreServi = mysqli_fetch_assoc($rNombreServi)['nombre_servicio'];

            $qAcumuladores = "SELECT sum(importe) as monto_venta FROM padron_producto_socio WHERE servicio = '$servicio' AND abm<>'0' AND cedula IN (".implode(',',$arrCedulas).") GROUP BY cedula";
            // var_dump($qAcumuladores); exit;
            $rAcumuladores = mysqli_query($mysqli, $qAcumuladores);
            $cantidad = 0;
            $montoVentas = 0;
            if (mysqli_num_rows($rAcumuladores) > 0) {
                while ($row3 = mysqli_fetch_array($rAcumuladores)) {
                    $cantidad++;
                    $montoVentas+= (int)$row3['monto_venta'];
                }
            }

            $res['productosFilial'][]= array(
                'producto' => $nombreServi,
                'filial' => $nombreFilial,
                'total' => $cantidad,
                'monto_ventas' => $montoVentas
            );
                
        }
    }  
}else{
    $res['result_vendedor'] = false;
    $res['mensaje'] = 'No se encontraron ventas de la filial solicitada';
}
   

    




mysqli_close($mysqli);
echo json_encode($res);
