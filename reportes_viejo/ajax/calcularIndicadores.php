<?php
require '../../_conexion.php';
require '../../_conexion250.php';

$fecha_desde = mysqli_real_escape_string($mysqli, $_POST['fecha_desde']);
$fecha_hasta = mysqli_real_escape_string($mysqli, $_POST['fecha_hasta']);
$res = array('result' =>false);

if ($fecha_desde=='') {
   $fecha_hasta = date('Y-m-d');
   $fecha_desde = date("Y-m-d",strtotime($fecha_hasta."- 1 month"));

//    $qCedulasPadron = "SELECT cedula FROM padron_datos_socio WHERE abmactual = '1'";
   $qCedulasPadron = "SELECT cedula FROM padron_producto_socio WHERE abmactual = '1' GROUP BY cedula";
   $rCedulasPadron = mysqli_query($mysqli250, $qCedulasPadron);

   if (mysqli_num_rows($rCedulasPadron) > 0) {
        mysqli_query($mysqli, "TRUNCATE TABLE cedulas_padron");
        while ($row = mysqli_fetch_assoc($rCedulasPadron)) {
            mysqli_query($mysqli, "INSERT INTO cedulas_padron VALUES (null,'".$row['cedula']."')");
        }
   }

   $where = "AND p.cedula IN (SELECT cedula FROM cedulas_padron)";
   $where2 = "AND pp.cedula IN (SELECT cedula FROM cedulas_padron)";
 
   
}else{
    $fecha_desde = date('Y-m-d', strtotime($fecha_desde));
    $fecha_hasta = date("Y-m-d",strtotime($fecha_hasta));
    $where =" AND CAST(p.fechafil as date) >= '$fecha_desde' AND cast(p.fechafil as date) <= '$fecha_hasta'";
    $where2 =" AND CAST(pp.fecha_registro as date) >= '$fecha_desde' AND cast(pp.fecha_registro as date) <= '$fecha_hasta'";
}

// $qTotalAltas = "SELECT  count(*) AS cantidad_altas, sum(p.total_importe) AS monto_ventas
// FROM padron_datos_socio p 
// WHERE p.estado = 6 AND cast(p.fechafil as date) >= '$fecha_desde' AND cast(p.fechafil as date) <= '$fecha_hasta' AND p.alta ='1'";

$qTotalAltas = "SELECT  count(*) AS cantidad_altas, sum(p.total_importe) AS monto_ventas
FROM padron_datos_socio p 
WHERE p.estado = 6 AND p.alta ='1' $where";
$rTotalAltas= mysqli_query($mysqli, $qTotalAltas);
$totalAltas = 0;
$montoAltas = 0;
if (mysqli_num_rows($rTotalAltas) > 0) {
    $res['result']=true;
    while ($row = mysqli_fetch_assoc($rTotalAltas)) {
        $totalAltas = $row['cantidad_altas'];
        $montoAltas = $row['monto_ventas'];
    }
} 

$res['totalAltas'] = $totalAltas;
$res['montoTotalAltas'] = $montoAltas;

// $qTotalIncrementos = "SELECT sum(pp.importe) AS 'monto_ventas'
// FROM padron_datos_socio p 
// INNER JOIN padron_producto_socio pp on p.cedula = pp.cedula
// WHERE p.estado = 6 AND cast(pp.fecha_registro as date) >= '$fecha_desde' AND cast(pp.fecha_registro as date) <= '$fecha_hasta' AND p.alta ='0' GROUP BY pp.cedula";

$qTotalIncrementos = "SELECT sum(pp.importe) AS 'monto_ventas'
FROM padron_datos_socio p 
INNER JOIN padron_producto_socio pp on p.cedula = pp.cedula
WHERE p.estado = 6 AND p.alta ='0' AND pp.abm='ALTA-PRODUCTO' $where2 GROUP BY pp.cedula";

// var_dump($qTotalIncrementos); exit;

$rTotalIncrementos = mysqli_query($mysqli, $qTotalIncrementos);
$totalIncrementos =0;
$montoVentas =0;
if ($rTotalIncrementos->num_rows !== 0) {
    while ($row = mysqli_fetch_assoc($rTotalIncrementos)) {
        
        $totalIncrementos++;
        $montoVentas+= (int)$row['monto_ventas'];
    }

    
} 
$res['totalIncrementos'] = $totalIncrementos;
$res['montoTotalIncrementos'] = $montoVentas;

$qCantVendedores = "SELECT COUNT(id) as cantidad FROM usuarios WHERE activo='1' AND id NOT IN (48,3113,3329,3115,3165)";

$rCantVendedores = mysqli_query($mysqli, $qCantVendedores);

if ($rCantVendedores->num_rows !== 0) {
    while ($row = mysqli_fetch_array($rCantVendedores)) {
       
        $res['totalVendedoresActivos']= $row['cantidad'];
    }
} 

mysqli_close($mysqli);
echo json_encode($res);