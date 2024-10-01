<?php
require '../../_conexion.php';
require '../../_conexion250.php';

$fecha_desde = mysqli_real_escape_string($mysqli, $_POST['fecha_desde']);
$fecha_hasta = mysqli_real_escape_string($mysqli, $_POST['fecha_hasta']);
$res = array('result' =>false);

if ($fecha_desde=='') {
   $fecha_hasta = date('Y-m-d');
   $fecha_desde = date("Y-m-d",strtotime($fecha_hasta."- 1 month"));
   $where = "AND p.cedula IN (SELECT cedula FROM cedulas_padron)";

//    $qCedulasPadron = "SELECT cedula FROM padron_datos_socio WHERE abmactual = '1'";
   $qCedulasPadron = "SELECT cedula FROM padron_producto_socio WHERE abmactual = '1' AND abm IN ('ALTA','ALTA-PRODUCTO') GROUP BY cedula";
   $rCedulasPadron = mysqli_query($mysqli250, $qCedulasPadron);

    if (mysqli_num_rows($rCedulasPadron) > 0) {
        $rCedulas = mysqli_query($mysqli, "DELETE FROM cedulas_padron");
        if ($rCedulas) {
            while ($row = mysqli_fetch_assoc($rCedulasPadron)) {
                mysqli_query($mysqli, "INSERT INTO cedulas_padron VALUES (null,'".$row['cedula']."')");
            }
        }   
    }

}else{
    $fecha_desde = date('Y-m-d', strtotime($fecha_desde));
    $fecha_hasta = date("Y-m-d",strtotime($fecha_hasta));
    $where =" AND CAST(hr.fecha_carga as date) >= '$fecha_desde' AND cast(hr.fecha_carga as date) <= '$fecha_hasta'";
}


$qAltasFilial = "SELECT  f.nombre_filial AS 'filial', count(hr.id) AS 'cantidad_altas', sum(hr.total_importe) AS 'monto_ventas'
FROM historico_reportes hr
INNER JOIN filiales f ON hr.id_filial = f.id
INNER JOIN padron_datos_socio p ON hr.id_cliente = p.id
WHERE hr.alta ='1' $where  
GROUP BY f.nombre_filial ORDER BY f.nombre_filial ASC";

// var_dump($qAltasFilial);exit;

$rAltasFilial = mysqli_query($mysqli, $qAltasFilial);

if ($rAltasFilial->num_rows !== 0) {
    $res['result'] = true;
    while ($row = mysqli_fetch_array($rAltasFilial)) {
       
        $res['altasFilial'][] = array(
            'filial' => $row['filial'],
            'cantidad_altas' => $row['cantidad_altas'],
            'monto_ventas' => $row['monto_ventas']
        );
    }
} 


$qIncrementosFilial = "SELECT  f.nombre_filial AS 'filial', count(hr.id) AS 'cantidad_incrementos', sum(hr.total_incremento) AS 'monto_ventas'
FROM historico_reportes hr
INNER JOIN filiales f ON hr.id_filial = f.id
INNER JOIN padron_datos_socio p ON hr.id_cliente = p.id
WHERE hr.alta ='0' $where  
GROUP BY f.nombre_filial ORDER BY f.nombre_filial ASC";


//  var_dump($qIncrementosFilial);exit;

$rIncrementosFilial = mysqli_query($mysqli, $qIncrementosFilial);

if ($rIncrementosFilial->num_rows !== 0) {
    $res['result'] = true;
    while ($row = mysqli_fetch_array($rIncrementosFilial)) {
       
        $res['incrementosFilial'][] = array(
            'filial' => $row['filial'],
            'cantidad_incrementos' => $row['cantidad_incrementos'],
            'monto_ventas' => $row['monto_ventas']
        );
    }
} 


$qAltasCall = "SELECT g.id,g.nombre_reporte AS 'call', count(hr.id) AS 'cantidad_altas', sum(hr.total_importe) AS 'monto_ventas'
FROM historico_reportes hr
INNER JOIN padron_datos_socio p  ON p.id = hr.id_cliente
INNER JOIN gruposusuarios g ON hr.id_grupo = g.id
WHERE hr.alta ='1' $where  
GROUP BY g.nombre_reporte ORDER BY g.nombre_reporte ASC";

$rAltasCall = mysqli_query($mysqli, $qAltasCall);
// var_dump(mysqli_num_rows($rAltasCall)); exit;

if (mysqli_num_rows($rAltasCall)>0) {
    while ($row = mysqli_fetch_array($rAltasCall)) {
        $idGrupo = $row['id'];
        $qCantVendedores = "SELECT count(id) as cant_vendedores FROM usuarios WHERE activo=1 AND  idgrupo = $idGrupo";

        $rCantVendedores = mysqli_query($mysqli,  $qCantVendedores);
        $rCantVendedores = mysqli_fetch_assoc($rCantVendedores)['cant_vendedores'];
        $res['altasCall'][] = array(
            'call' => $row['call'],
            'cantidad_altas' => $row['cantidad_altas'],
            'monto_ventas' => $row['monto_ventas'],
            'vendedores_activos' => $rCantVendedores
        );
    }
} 


$qIncrementosCall = "SELECT g.id,g.nombre_reporte AS 'call', count(hr.id) AS 'cantidad_incrementos', sum(hr.total_incremento) AS 'monto_ventas'
FROM historico_reportes hr
INNER JOIN padron_datos_socio p  ON p.id = hr.id_cliente
INNER JOIN gruposusuarios g ON hr.id_grupo = g.id
WHERE hr.alta ='0' $where  
GROUP BY g.nombre_reporte ORDER BY g.nombre_reporte ASC";

$rIncrementosCall = mysqli_query($mysqli, $qIncrementosCall);

if ($rIncrementosCall->num_rows !== 0) {
    while ($row = mysqli_fetch_array($rIncrementosCall)) {
        $idcall = $row['id'];
        $qCantVendedores = "SELECT count(id) as cant_vendedores FROM usuarios WHERE activo=1 AND  idgrupo = $idcall";
        $rCantVendedores = mysqli_query($mysqli,  $qCantVendedores);
        $rCantVendedores = mysqli_fetch_assoc($rCantVendedores)['cant_vendedores'];
        $totalIncrementosCall =0;
        $montoVentas =0;
        $totalIncrementosCall = $row['cantidad_incrementos'];
        $montoVentas= (int)$row['monto_ventas'];

        $res['incrementosCall'][] = array(
            'call' => $row['call'],
            'cantidad_incrementos' => $totalIncrementosCall,
            'monto_ventas' => $montoVentas,
            'vendedores_activos' => $rCantVendedores
        );
    }
}

mysqli_close($mysqli);
echo json_encode($res);
