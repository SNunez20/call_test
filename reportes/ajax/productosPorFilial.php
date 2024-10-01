<?php
require '../../_conexion.php';
require '../../_conexion250.php';

$fecha_desde = mysqli_real_escape_string($mysqli, $_POST['fecha_desde']);
$fecha_hasta = mysqli_real_escape_string($mysqli, $_POST['fecha_hasta']);
$idFilial = mysqli_real_escape_string($mysqli, $_POST['filial']);
$nombreFilial = mysqli_real_escape_string($mysqli, $_POST['nombreFilial']);
$res = array('result' =>false);

if ($fecha_desde=='') {
 
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
    
}else{
     $fecha_desde = date('Y-m-d', strtotime($fecha_desde));
     $fecha_hasta = date("Y-m-d",strtotime($fecha_hasta));
     $where =" AND CAST(hr.fecha_carga as date) >= '$fecha_desde' AND cast(hr.fecha_carga as date) <= '$fecha_hasta'";
}

$cond = ($idFilial!='0') ? " hr.id_filial = $idFilial $where" : " 1=1 $where";


$qFiliales = "SELECT count(hrs.id_servicio) AS cantidad, sum(hrs.importe) AS monto_venta, s.nombre_servicio, f.nombre_filial
    FROM historico_reportes_servicios hrs
    INNER JOIN servicios s ON hrs.id_servicio = s.id
    INNER JOIN historico_reportes hr ON hr.id = hrs.id_historico_reporte
    INNER JOIN filiales f ON hr.id_filial = f.id
    INNER JOIN padron_datos_socio p ON p.id = hr.id_cliente
    WHERE $cond
    GROUP BY hrs.id_servicio";

    // var_dump($qFiliales); exit;
    //echo $qFiliales;

$rFiliales = mysqli_query($mysqli, $qFiliales);

if (mysqli_num_rows($rFiliales) > 0) {
    $res['result']=true;
    while ($row = mysqli_fetch_assoc($rFiliales)) {
        
        $nombreFilial = ($idFilial!='0') ? $row['nombre_filial'] : 'Todas las filiales';
        $res['productosFilial'][]= array(
            'producto' => $row['nombre_servicio'],
            'filial' => $nombreFilial ,
            'total' => $row['cantidad'],
            'monto_ventas' => $row['monto_venta']
        );
    }  
 

}else{
    $res['result_vendedor'] = false;
    $res['message'] = 'No se encontraron ventas de la filial solicitada';
}
   
mysqli_close($mysqli);
echo json_encode($res);
