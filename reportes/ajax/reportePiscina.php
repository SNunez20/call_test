<?php
require '../../_conexion.php';

$fecha_desde = mysqli_real_escape_string($mysqli, $_POST['fecha_desde']);
$fecha_hasta = mysqli_real_escape_string($mysqli, $_POST['fecha_hasta']);
$res = array('result' =>false);

if ($fecha_desde=='') {
    $fecha_hasta = date('Y-m-d');
    $fecha_desde = date("Y-m-d",strtotime($fecha_hasta."- 1 month"));
}else{
    $fecha_desde = date('Y-m-d', strtotime($fecha_desde));
    $fecha_hasta = date("Y-m-d",strtotime($fecha_hasta));
}

$qTotalAltas = "SELECT  count(*) AS cantidad_altas, sum(p.total_importe) AS monto_ventas
FROM padron_datos_socio p 
WHERE (p.estado <> 6 AND p.estado <> 4 AND p.estado <> 7 AND p.estado <> 9 AND p.estado <> 668 AND p.estado <> 675 AND p.estado <> 676 AND p.estado <> 688) AND cast(p.fechafil as date) >= '$fecha_desde' AND cast(p.fechafil as date) <= '$fecha_hasta' AND p.alta ='1'";

$rTotalAltas = mysqli_query($mysqli, $qTotalAltas);

$cantAltas  = 0;
$montoAltas = 0;

if (mysqli_num_rows($rTotalAltas) > 0) {
    $res['result']=true;
    while ($row = mysqli_fetch_assoc($rTotalAltas)) {
        $cantAltas = $row['cantidad_altas'];
        $montoAltas = $row['monto_ventas'];
    }
} 

$res['totalAltas'] = $cantAltas;
$res['montoTotalAltas'] =$montoAltas;

$qTotalIncrementos = "SELECT sum(pp.importe) AS 'monto_ventas'
FROM padron_datos_socio p 
INNER JOIN padron_producto_socio pp on p.cedula = pp.cedula
WHERE (p.estado <> 6 AND p.estado <> 4 AND p.estado <> 7 AND p.estado <> 9 AND p.estado <> 668 AND p.estado <> 675 AND p.estado <> 676 AND p.estado <> 688) AND cast(pp.fecha_registro as date) >= '$fecha_desde' AND cast(pp.fecha_registro as date) <= '$fecha_hasta' AND p.alta ='0'  AND pp.abm='ALTA-PRODUCTO'
GROUP BY pp.cedula";

$rTotalIncrementos = mysqli_query($mysqli, $qTotalIncrementos);
$totalIncrementos =0;
$montoVentas =0;
if ($rTotalIncrementos->num_rows !== 0) {
    while ($row = mysqli_fetch_assoc($rTotalIncrementos)) {
        $totalIncrementos++;
        $montoVentas+= (int)$row['monto_ventas'];
    }

}

$res['totalIncrementos']      = $totalIncrementos;
$res['montoTotalIncrementos'] = $montoVentas;

$qCantVendedores = "SELECT COUNT(id) as cantidad FROM usuarios WHERE activo='1' AND id NOT IN (48,3113,3329,3115,3165)";

$rCantVendedores = mysqli_query($mysqli, $qCantVendedores);

if ($rCantVendedores->num_rows !== 0) {
    while ($row = mysqli_fetch_array($rCantVendedores)) {
       
        $res['totalVendedoresActivos']= $row['cantidad'];
    }
} 



$qAltasFilial = "SELECT  f.nombre_filial AS 'filial', count(*) AS 'cantidad_altas', sum(p.total_importe) AS 'monto_ventas'
FROM padron_datos_socio p 
INNER JOIN filiales f ON p.sucursal = f.nro_filial
WHERE (p.estado <> 6 AND p.estado <> 4 AND p.estado <> 7 AND p.estado <> 9 AND p.estado <> 668 AND p.estado <> 675 AND p.estado <> 676 AND p.estado <> 688) AND cast(p.fechafil as date) >= '$fecha_desde' AND cast(p.fechafil as date) <= '$fecha_hasta' AND p.alta ='1' 
GROUP BY f.nombre_filial ORDER BY f.nombre_filial ASC";

$rAltasFilial = mysqli_query($mysqli, $qAltasFilial);

if ($rAltasFilial->num_rows !== 0) {
    while ($row = mysqli_fetch_array($rAltasFilial)) {
       
        $res['altasFilial'][] = array(
            'filial' => $row['filial'],
            'cantidad_altas' => $row['cantidad_altas'],
            'monto_ventas' => $row['monto_ventas']
        );
    }
} 

$qIncrementosFilial = "SELECT f.nombre_filial as filial, p.sucursal as sucursal
FROM padron_datos_socio p 
INNER JOIN filiales f ON p.sucursal = f.nro_filial
INNER JOIN padron_producto_socio pp ON p.cedula = pp.cedula
WHERE (p.estado <> 6 AND p.estado <> 4 AND p.estado <> 7 AND p.estado <> 9 AND p.estado <> 668 AND p.estado <> 675 AND p.estado <> 676 AND p.estado <> 688) AND cast(pp.fecha_registro as date) >= '$fecha_desde' AND cast(pp.fecha_registro as date) <= '$fecha_hasta' AND p.alta ='0' 
GROUP BY f.nombre_filial ORDER BY f.nombre_filial ASC";

$rIncrementosFilial = mysqli_query($mysqli, $qIncrementosFilial);

if ($rIncrementosFilial->num_rows !== 0) {
   
    while ($row = mysqli_fetch_array($rIncrementosFilial)) {
        $suc = $row['sucursal'];
        $qAcumuladores ="SELECT sum(pp.importe) AS monto_ventas
        FROM padron_datos_socio p 
        INNER JOIN filiales f ON p.sucursal = f.nro_filial
        INNER JOIN padron_producto_socio pp ON p.cedula = pp.cedula
        where (p.estado <> 6 AND p.estado <> 4 AND p.estado <> 7 AND p.estado <> 9 AND p.estado <> 668 AND p.estado <> 675 AND p.estado <> 676 AND p.estado <> 688) AND cast(pp.fecha_registro as date) >= '$fecha_desde' AND cast(pp.fecha_registro as date) <= '$fecha_hasta' AND p.alta ='0'  AND pp.abm='ALTA-PRODUCTO' AND p.sucursal=$suc
        GROUP BY p.cedula";
        $rAcumuladores = mysqli_query($mysqli, $qAcumuladores);
        $totalIncrementosFilial =0;
        $montoVentas =0;
        if ($rAcumuladores->num_rows !== 0) {
            while ($row2= mysqli_fetch_assoc($rAcumuladores)) {
                $totalIncrementosFilial++;
                $montoVentas+= (int)$row2['monto_ventas'];
            }
    
        } 
       
        $res['incrementosFilial'][] = array(
            'filial' => $row['filial'],
            'cantidad_incrementos' => $totalIncrementosFilial,
            'monto_ventas' => $montoVentas
        );
    }
} 

$qAltasCall = "SELECT g.id,g.nombre_reporte AS 'call', count(*) AS 'cantidad_altas', sum(p.total_importe) AS 'monto_ventas'
FROM padron_datos_socio p 
INNER JOIN usuarios u ON p.id_usuario = u.id
INNER JOIN gruposusuarios g ON u.idgrupo = g.id
INNER JOIN filiales f ON p.sucursal = f.nro_filial
WHERE (p.estado <> 6 AND p.estado <> 4 AND p.estado <> 7 AND p.estado <> 9 AND p.estado <> 668 AND p.estado <> 675 AND p.estado <> 676 AND p.estado <> 688) AND cast(p.fechafil as date) >= '$fecha_desde' AND cast(p.fechafil as date) <= '$fecha_hasta' AND p.alta ='1' 
GROUP BY g.nombre_reporte ORDER BY g.nombre_reporte ASC";

$rAltasCall = mysqli_query($mysqli, $qAltasCall);

if ($rAltasCall->num_rows !== 0) {
    while ($row = mysqli_fetch_array($rAltasCall)) {
        $idGrupo = $row['id'];
        $qCantVendedores = "SELECT count(id) as cant_vendedores FROM usuarios WHERE activo=1 AND  idgrupo = $idGrupo";
        $rCantVendedores = mysqli_query($mysqli,  $qCantVendedores);
        $cantVendedores = mysqli_fetch_assoc($rCantVendedores)['cant_vendedores'];
       
        $res['altasCall'][] = array(
            'call' => $row['call'],
            'cantidad_altas' => $row['cantidad_altas'],
            'monto_ventas' => $row['monto_ventas'],
            'cantidad_vendedores' => $cantVendedores
        );
    }
} 

$qIncrementosCall = "SELECT g.id,g.nombre_reporte AS 'call', g.id as idcall
FROM padron_datos_socio p 
INNER JOIN usuarios u ON p.id_usuario = u.id
INNER JOIN gruposusuarios g ON u.idgrupo = g.id
INNER JOIN filiales f ON p.sucursal = f.nro_filial
INNER JOIN padron_producto_socio pp ON p.cedula = pp.cedula
WHERE (p.estado <> 6 AND p.estado <> 4 AND p.estado <> 7 AND p.estado <> 9 AND p.estado <> 668 AND p.estado <> 675 AND p.estado <> 676 AND p.estado <> 688) AND cast(pp.fecha_registro as date) >= '$fecha_desde' AND cast(pp.fecha_registro as date) <= '$fecha_hasta' AND p.alta ='0' 
GROUP BY g.nombre_reporte ORDER BY g.nombre_reporte ASC";

$rIncrementosCall = mysqli_query($mysqli, $qIncrementosCall);

if ($rIncrementosCall->num_rows !== 0) {
    while ($row = mysqli_fetch_array($rIncrementosCall)) {
        $idcall = $row['idcall'];
        $qCantVendedores = "SELECT count(id) as cant_vendedores FROM usuarios WHERE activo=1 AND  idgrupo = $idcall";
        $rCantVendedores = mysqli_query($mysqli,  $qCantVendedores);
        $cantVendedores = mysqli_fetch_assoc($rCantVendedores)['cant_vendedores'];
        $qAcumuladores ="SELECT  g.id, sum(pp.importe) AS monto_ventas
        FROM padron_datos_socio p 
        INNER JOIN usuarios u on p.id_usuario = u.id
        INNER JOIN gruposusuarios g on u.idgrupo = g.id
        INNER JOIN filiales f on p.sucursal = f.nro_filial
        INNER JOIN padron_producto_socio pp on p.cedula = pp.cedula
        where (p.estado <> 6 AND p.estado <> 4 AND p.estado <> 7 AND p.estado <> 9 AND p.estado <> 668 AND p.estado <> 675 AND p.estado <> 676 AND p.estado <> 688) AND cast(pp.fecha_registro as date) >= '$fecha_desde' AND cast(pp.fecha_registro as date) <= '$fecha_hasta' AND p.alta ='0'  AND pp.abm='ALTA-PRODUCTO' AND g.id=$idcall
        GROUP BY pp.cedula ORDER BY g.nombre_reporte ASC";
        $rAcumuladores = mysqli_query($mysqli, $qAcumuladores);
        $totalIncrementosCall =0;
        $montoVentas =0;
        if ($rAcumuladores->num_rows !== 0) {
            while ($row2= mysqli_fetch_assoc($rAcumuladores)) {
                $totalIncrementosCall++;
                $montoVentas+= (int)$row2['monto_ventas'];
            }
    
        } 
       
        $res['incrementosCall'][] = array(
            'call' => $row['call'],
            'cantidad_incrementos' => $totalIncrementosCall,
            'monto_ventas' => $montoVentas,
            'cantidad_vendedores' => $cantVendedores
        );
    }
}

mysqli_close($mysqli);
echo json_encode($res);
