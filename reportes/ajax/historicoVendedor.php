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
    $qCedulasPadron = "SELECT cedula FROM padron_producto_socio WHERE abmactual = '1' AND abm IN ('ALTA','ALTA-PRODUCTO') GROUP BY cedula";
    $rCedulasPadron = mysqli_query($mysqli250, $qCedulasPadron);
 
    if (mysqli_num_rows($rCedulasPadron) > 0) {
         mysqli_query($mysqli, "DELETE FROM cedulas_padron");
         while ($row = mysqli_fetch_assoc($rCedulasPadron)) {
             mysqli_query($mysqli, "INSERT INTO cedulas_padron VALUES (null,'".$row['cedula']."')");
         }
    }
 
    $where = "AND p.cedula IN (SELECT cedula FROM cedulas_padron)";
    $w2 = "AND cedula IN (SELECT cedula FROM cedulas_padron)";
    
}else{
     $fecha_desde = date('Y-m-d', strtotime($fecha_desde));
     $fecha_hasta = date("Y-m-d",strtotime($fecha_hasta));
     $where =" AND CAST(hr.fecha_carga as date) >= '$fecha_desde' AND cast(hr.fecha_carga as date) <= '$fecha_hasta'";

}

$qIdVendedor = "SELECT id FROM usuarios WHERE usuario = '$cedula_vendedor'";
$rIdVendedor = mysqli_query($mysqli, $qIdVendedor);

if (mysqli_num_rows($rIdVendedor) > 0) {
    $res['result']=true;
    while ($row = mysqli_fetch_assoc($rIdVendedor)) {
        $id_usuario= $row['id'];
        $res['result_vendedor'] = true;
    
    }

    $qhistoricoVendedor = "SELECT u.nombre AS 'vendedor',u.usuario AS 'cedula', g.nombre_reporte AS 'call',p.nombre AS socio, p.cedula AS cedula_socio, IF(hr.alta='1','ALTA','INCREMENTO') AS tipo_afiliacion , hr.total_incremento AS 'monto_venta', hr.fecha_carga AS fecha
    FROM historico_reportes hr 
    INNER JOIN usuarios u ON hr.id_usuario = u.id
    INNER JOIN gruposusuarios g ON hr.id_grupo = g.id
    INNER JOIN filiales f ON hr.id_filial = f.id
    INNER JOIN padron_datos_socio p  ON p.id = hr.id_cliente
    WHERE hr.id_usuario = $id_usuario $where";

    // var_dump($qhistoricoVendedor); exit;

    $rHistoricoVendedor = mysqli_query($mysqli, $qhistoricoVendedor);
    $arrCedulas=[];
    if ($rHistoricoVendedor->num_rows !== 0) {
        while ($row = mysqli_fetch_array($rHistoricoVendedor)) {
            $cedulasocio = $row['cedula_socio'];
            $montoVenta=0;
            if (!in_array($cedulasocio,$arrCedulas)) {

                $res['historicoVendedor'][] = array(
                    'vendedor' => $row['vendedor'],
                    'cedula' => $row['cedula'],
                    'call' => $row['call'],
                    'socio' => $row['socio'],
                    'cedula_socio' => $row['cedula_socio'],
                    'tipo_afiliacion' => $row['tipo_afiliacion'],
                    'monto_venta' => $row['monto_venta'],
                    'fecha' => date('d-m-Y',strtotime($row['fecha']))
                );
            }
        } 
    }

}else{
    $res['result_vendedor'] = false;
    $res['mensaje'] = 'No se encontró historico de ventas del vendedor';
}


mysqli_close($mysqli);
echo json_encode($res);
