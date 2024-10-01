<?php
require '../../_conexion.php';
require '../../_conexion250.php';

$fecha_desde = mysqli_real_escape_string($mysqli, $_POST['fecha_desde']);
$fecha_hasta = mysqli_real_escape_string($mysqli, $_POST['fecha_hasta']);
$cedula_vendedor = mysqli_real_escape_string($mysqli, $_POST['cedula_vendedor']);
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

$qIdVendedor = "SELECT id FROM usuarios WHERE usuario = $cedula_vendedor";
$rIdVendedor = mysqli_query($mysqli, $qIdVendedor);

if (mysqli_num_rows($rIdVendedor) > 0) {
    $res['result']=true;
    while ($row = mysqli_fetch_assoc($rIdVendedor)) {
        $id_usuario= $row['id'];
        $res['result_vendedor'] = true;
    
    }

    // $qhistoricoVendedor = "SELECT u.nombre AS 'vendedor',u.usuario AS 'cedula', g.nombre_reporte AS 'call',p.nombre AS socio, p.cedula AS cedula_socio, IF(alta='1','ALTA','INCREMENTO') AS tipo_afiliacion , pp.importe AS 'monto_venta', pp.fecha_registro AS fecha
    // FROM padron_datos_socio p 
    // INNER JOIN usuarios u ON p.id_usuario = u.id
    // INNER JOIN gruposusuarios g ON u.idgrupo = g.id
    // INNER JOIN filiales f ON p.sucursal = f.nro_filial
    // INNER JOIN padron_producto_socio pp ON p.cedula = pp.cedula
    // WHERE p.estado = 6 AND cast(pp.fecha_registro as date) >= '$fecha_desde' AND cast(pp.fecha_registro as date) <= '$fecha_hasta'  GROUP BY p.cedula";

    $qhistoricoVendedor = "SELECT u.nombre AS 'vendedor',u.usuario AS 'cedula', g.nombre_reporte AS 'call',p.nombre AS socio, p.cedula AS cedula_socio, IF(alta='1','ALTA','INCREMENTO') AS tipo_afiliacion , pp.importe AS 'monto_venta', pp.fecha_registro AS fecha
    FROM padron_datos_socio p 
    INNER JOIN usuarios u ON p.id_usuario = u.id
    INNER JOIN gruposusuarios g ON u.idgrupo = g.id
    INNER JOIN filiales f ON p.sucursal = f.nro_filial
    INNER JOIN padron_producto_socio pp ON p.cedula = pp.cedula
    WHERE p.estado = 6 AND u.id = $id_usuario $where2 
    GROUP BY p.cedula";

    // var_dump($qhistoricoVendedor); exit;

    $rHistoricoVendedor = mysqli_query($mysqli, $qhistoricoVendedor);
    $arrCedulas=[];
    if ($rHistoricoVendedor->num_rows !== 0) {
        while ($row = mysqli_fetch_array($rHistoricoVendedor)) {
            $cedulasocio = $row['cedula_socio'];
            $montoVenta=0;
            if (!in_array($cedulasocio,$arrCedulas)) {
                array_push($arrCedulas,$cedulasocio);
                // $qAcumuladores = "SELECT sum(importe) as monto_venta FROM padron_producto_socio WHERE cedula = '$cedulasocio' AND cast(fecha_registro as date) >= '$fecha_desde' AND cast(fecha_registro as date) <= '$fecha_hasta'";
                $qAcumuladores = "SELECT sum(importe) as monto_venta FROM padron_producto_socio WHERE cedula = '$cedulasocio' AND abm <> '0'";
           
                $rAcumuladores = mysqli_query($mysqli, $qAcumuladores);
                while ($row2 = mysqli_fetch_assoc($rAcumuladores)) {
                    $montoVenta+= (int)$row2['monto_venta'];
                }
        
                $res['historicoVendedor'][] = array(
                    'vendedor' => $row['vendedor'],
                    'cedula' => $row['cedula'],
                    'call' => $row['call'],
                    'socio' => $row['socio'],
                    'cedula_socio' => $row['cedula_socio'],
                    'tipo_afiliacion' => $row['tipo_afiliacion'],
                    'monto_venta' => $montoVenta,
                    'fecha' => date('d-m-Y',strtotime($row['fecha']))
                );
            }
        } 
    }

}else{
    $res['result_vendedor'] = false;
    $res['mensaje'] = 'No se encontró historico de ventas del vendedor';
}


// $qTotalAltas = "SELECT  count(*) AS cantidad_altas, sum(p.total_importe) AS monto_ventas
// FROM padron_datos_socio p 
// WHERE p.estado = 6 AND cast(p.fechafil as date) >= '$fecha_desde' AND cast(p.fechafil as date) <= '$fecha_hasta' AND p.alta ='1'";

// $rTotalAltas = mysqli_query($mysqli, $qTotalAltas);


// if (mysqli_num_rows($rTotalAltas) > 0) {
//     $res['result']=true;
//     while ($row = mysqli_fetch_assoc($rTotalAltas)) {
//         $res['totalAltas'] = $row['cantidad_altas'];
//         $res['montoTotalAltas'] = $row['monto_ventas'];
//     }
// } 

// $qTotalIncrementos = "SELECT COUNT(*) AS 'cantidad_incrementos', sum(pp.importe) AS 'monto_ventas'
// FROM padron_datos_socio p 
// INNER JOIN padron_producto_socio pp on p.cedula = pp.cedula
// WHERE p.estado = 6 AND cast(pp.fecha_registro as date) >= '$fecha_desde' AND cast(pp.fecha_registro as date) <= '$fecha_hasta' AND p.alta ='0'";

// $rTotalIncrementos = mysqli_query($mysqli, $qTotalIncrementos);

// if ($rTotalIncrementos->num_rows !== 0) {
//     while ($row = mysqli_fetch_assoc($rTotalIncrementos)) {
//         $res['totalIncrementos'] = $row['cantidad_incrementos'];
//         $res['montoTotalIncrementos'] = $row['monto_ventas'];
//     }
// } 




mysqli_close($mysqli);
echo json_encode($res);
