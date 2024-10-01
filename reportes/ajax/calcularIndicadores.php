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
   $qCedulasPadron = "SELECT cedula FROM padron_producto_socio WHERE abmactual = '1' AND abm IN ('ALTA','ALTA-PRODUCTO') GROUP BY cedula";
   $rCedulasPadron = mysqli_query($mysqli250, $qCedulasPadron);

    if (mysqli_num_rows($rCedulasPadron) > 0) {
        mysqli_query($mysqli, "TRUNCATE TABLE cedulas_padron");
        while ($row = mysqli_fetch_assoc($rCedulasPadron)) {
            mysqli_query($mysqli, "INSERT INTO cedulas_padron VALUES (null,'".$row['cedula']."')");
        }
    }

   $where = "WHERE p.cedula IN (SELECT cedula FROM cedulas_padron)";
   $tableHistorico = "( SELECT * FROM historico_reportes WHERE id IN ( SELECT MAX( id ) FROM historico_reportes GROUP BY id_cliente ) ) AS hr";
 
   
}else{
    $fecha_desde = date('Y-m-d', strtotime($fecha_desde));
    $fecha_hasta = date("Y-m-d", strtotime($fecha_hasta));
    $where =" WHERE CAST(hr.fecha_carga as date) >= '$fecha_desde' AND cast(hr.fecha_carga as date) <= '$fecha_hasta'";
    $tableHistorico = " historico_reportes hr";
}


$qTotalIndicadores = "SELECT
SUM(IF(hr.alta = '1', 1, 0)) AS cantidad_altas, 
SUM(IF(hr.alta = '1', hr.total_importe, 0)) AS monto_ventas,
SUM(IF(hr.alta = '0', 1, 0)) AS cantidad_incrementos, 
SUM(IF(hr.alta = '0', hr.total_incremento, 0)) AS monto_incrementos
FROM
padron_datos_socio AS p
INNER JOIN $tableHistorico ON p.id = hr.id_cliente $where";

$rTotalIndicadores= mysqli_query($mysqli, $qTotalIndicadores);
$totalAltas = 0;
$montoAltas = 0;
$totalIncrementos = 0;
$montoVentas = 0;
if (mysqli_num_rows($rTotalIndicadores) > 0) {
    $res['result']=true;
    while ($row = mysqli_fetch_assoc($rTotalIndicadores)) {
        $totalAltas = $row['cantidad_altas'];
        $montoAltas = $row['monto_ventas'];
        $totalIncrementos = $row['cantidad_incrementos'];
        $montoVentas = ($row['monto_ventas'] !=null) ? $row['monto_incrementos'] : 0;
    }
} 

$res['totalAltas'] = $totalAltas;
$res['montoTotalAltas'] = $montoAltas;
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