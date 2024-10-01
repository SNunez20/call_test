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
   $where2 = "AND pp.cedula IN (SELECT cedula FROM cedulas_padron)";

//    $qCedulasPadron = "SELECT cedula FROM padron_datos_socio WHERE abmactual = '1'";
   $qCedulasPadron = "SELECT cedula FROM padron_producto_socio WHERE abmactual = '1' GROUP BY cedula";
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
    $where =" AND CAST(p.fechafil as date) >= '$fecha_desde' AND cast(p.fechafil as date) <= '$fecha_hasta'";
    $where2 =" AND CAST(pp.fecha_registro as date) >= '$fecha_desde' AND cast(pp.fecha_registro as date) <= '$fecha_hasta'";
}

// $qTotalAltas = "SELECT  count(*) AS cantidad_altas, sum(p.total_importe) AS monto_ventas
// FROM padron_datos_socio p 
// WHERE p.estado = 6 AND cast(p.fechafil as date) >= '$fecha_desde' AND cast(p.fechafil as date) <= '$fecha_hasta' AND p.alta ='1'";

// $qTotalAltas = "SELECT  count(*) AS cantidad_altas, sum(p.total_importe) AS monto_ventas
// FROM padron_datos_socio p 
// WHERE p.estado = 6 AND p.alta ='1' $where";
// $rTotalAltas= mysqli_query($mysqli, $qTotalAltas);

// if (mysqli_num_rows($rTotalAltas) > 0) {
//     $res['result']=true;
//     while ($row = mysqli_fetch_assoc($rTotalAltas)) {
//         $res['totalAltas'] = $row['cantidad_altas'];
//         $res['montoTotalAltas'] = $row['monto_ventas'];
//     }
// } 

// $qTotalIncrementos = "SELECT sum(pp.importe) AS 'monto_ventas'
// FROM padron_datos_socio p 
// INNER JOIN padron_producto_socio pp on p.cedula = pp.cedula
// WHERE p.estado = 6 AND cast(pp.fecha_registro as date) >= '$fecha_desde' AND cast(pp.fecha_registro as date) <= '$fecha_hasta' AND p.alta ='0' GROUP BY pp.cedula";

// $qTotalIncrementos = "SELECT sum(pp.importe) AS 'monto_ventas'
// FROM padron_datos_socio p 
// INNER JOIN padron_producto_socio pp on p.cedula = pp.cedula
// WHERE p.estado = 6 AND p.alta ='0' AND pp.abm='ALTA-PRODUCTO' $where2 GROUP BY pp.cedula";

// $rTotalIncrementos = mysqli_query($mysqli, $qTotalIncrementos);
// $totalIncrementos =0;
// $montoVentas =0;
// if ($rTotalIncrementos->num_rows !== 0) {
//     while ($row = mysqli_fetch_assoc($rTotalIncrementos)) {
        
//         $totalIncrementos++;
//         $montoVentas+= (int)$row['monto_ventas'];
//     }

//     $res['totalIncrementos'] = $totalIncrementos;
//     $res['montoTotalIncrementos'] = $montoVentas;
// } 

// $qCantVendedores = "SELECT COUNT(id) as cantidad FROM usuarios WHERE activo='1'";

// $rCantVendedores = mysqli_query($mysqli, $qCantVendedores);

// if ($rCantVendedores->num_rows !== 0) {
//     while ($row = mysqli_fetch_array($rCantVendedores)) {
       
//         $res['totalVendedoresActivos']= $row['cantidad'];
//     }
// } 

// $qAltasFilial = "SELECT  f.nombre_filial AS 'filial', count(*) AS 'cantidad_altas', sum(p.total_importe) AS 'monto_ventas'
// FROM padron_datos_socio p 
// INNER JOIN filiales f ON p.sucursal = f.nro_filial
// WHERE p.estado = 6 AND cast(p.fechafil as date) >= '$fecha_desde' AND cast(p.fechafil as date) <= '$fecha_hasta' AND p.alta ='1' 
// GROUP BY f.nombre_filial ORDER BY f.nombre_filial ASC";

$qAltasFilial = "SELECT  f.nombre_filial AS 'filial', count(*) AS 'cantidad_altas', sum(p.total_importe) AS 'monto_ventas'
FROM padron_datos_socio p 
INNER JOIN filiales f ON p.sucursal = f.nro_filial
WHERE p.estado = 6 AND p.alta ='1' $where  
GROUP BY f.nombre_filial ORDER BY f.nombre_filial ASC";

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

// $qIncrementosFilial = "SELECT f.nombre_filial as filial, p.sucursal as sucursal
// FROM padron_datos_socio p 
// INNER JOIN filiales f ON p.sucursal = f.nro_filial
// INNER JOIN padron_producto_socio pp ON p.cedula = pp.cedula
// WHERE p.estado = 6 AND cast(pp.fecha_registro as date) >= '$fecha_desde' AND cast(pp.fecha_registro as date) <= '$fecha_hasta' AND p.alta ='0' 
// GROUP BY f.nombre_filial ORDER BY f.nombre_filial ASC";

$qIncrementosFilial = "SELECT f.nombre_filial as filial, p.sucursal as sucursal
FROM padron_datos_socio p 
INNER JOIN filiales f ON p.sucursal = f.nro_filial
INNER JOIN padron_producto_socio pp ON p.cedula = pp.cedula
WHERE p.estado = 6 AND p.alta ='0'  $where2  
GROUP BY f.nombre_filial ORDER BY f.nombre_filial ASC";


$rIncrementosFilial = mysqli_query($mysqli, $qIncrementosFilial);

if ($rIncrementosFilial->num_rows !== 0) {
    while ($row = mysqli_fetch_array($rIncrementosFilial)) {
        $suc = $row['sucursal'];
        // $qAcumuladores ="SELECT sum(pp.importe) AS monto_ventas
        // FROM padron_datos_socio p 
        // INNER JOIN filiales f ON p.sucursal = f.nro_filial
        // INNER JOIN padron_producto_socio pp ON p.cedula = pp.cedula
        // where p.estado = 6 AND cast(pp.fecha_registro as date) >= '$fecha_desde' AND cast(pp.fecha_registro as date) <= '$fecha_hasta' AND p.alta ='0' AND p.sucursal=$suc
        // GROUP BY p.cedula";
        $qAcumuladores ="SELECT sum(pp.importe) AS monto_ventas
        FROM padron_datos_socio p 
        INNER JOIN filiales f ON p.sucursal = f.nro_filial
        INNER JOIN padron_producto_socio pp ON p.cedula = pp.cedula
        where p.estado = 6 AND p.alta ='0' AND p.sucursal=$suc AND pp.abm='ALTA-PRODUCTO' $where2 
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



// $qAltasCall = "SELECT g.nombre_reporte AS 'call', count(*) AS 'cantidad_altas', sum(p.total_importe) AS 'monto_ventas'
// FROM padron_datos_socio p 
// INNER JOIN usuarios u ON p.id_usuario = u.id
// INNER JOIN gruposusuarios g ON u.idgrupo = g.id
// INNER JOIN filiales f ON p.sucursal = f.nro_filial
// WHERE p.estado = 6 AND cast(p.fechafil as date) >= '$fecha_desde' AND cast(p.fechafil as date) <= '$fecha_hasta' AND p.alta ='1' 
// GROUP BY g.nombre_reporte ORDER BY g.nombre_reporte ASC";

$qAltasCall = "SELECT g.id,g.nombre_reporte AS 'call', count(*) AS 'cantidad_altas', sum(p.total_importe) AS 'monto_ventas'
FROM padron_datos_socio p 
INNER JOIN usuarios u ON p.id_usuario = u.id
INNER JOIN gruposusuarios g ON u.idgrupo = g.id
INNER JOIN filiales f ON p.sucursal = f.nro_filial
WHERE p.estado = 6 AND p.alta ='1' $where  
GROUP BY g.nombre_reporte ORDER BY g.nombre_reporte ASC";



$rAltasCall = mysqli_query($mysqli, $qAltasCall);
// var_dump(mysqli_num_rows($rAltasCall)); exit;

if (mysqli_num_rows($rAltasCall)>0) {
    while ($row = mysqli_fetch_array($rAltasCall)) {
        $idGrupo = $row['id'];
        $qCantVendedores = "SELECT count(id) as cant_vendedores FROM usuarios WHERE activo=1 AND  idgrupo = $idGrupo";
        // var_dump($qCantVendedores); exit;
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

// $qIncrementosCall = "SELECT g.nombre_reporte AS 'call',g.id AS idcall
// FROM padron_datos_socio p 
// INNER JOIN usuarios u ON p.id_usuario = u.id
// INNER JOIN gruposusuarios g ON u.idgrupo = g.id
// INNER JOIN filiales f ON p.sucursal = f.nro_filial
// INNER JOIN padron_producto_socio pp ON p.cedula = pp.cedula
// WHERE p.estado = 6 AND cast(pp.fecha_registro as date) >= '$fecha_desde' AND cast(pp.fecha_registro as date) <= '$fecha_hasta' AND p.alta ='0' 
// GROUP BY g.nombre_reporte ORDER BY g.nombre_reporte ASC";

$qIncrementosCall = "SELECT g.nombre_reporte AS 'call',g.id AS idcall
FROM padron_datos_socio p 
INNER JOIN usuarios u ON p.id_usuario = u.id
INNER JOIN gruposusuarios g ON u.idgrupo = g.id
INNER JOIN filiales f ON p.sucursal = f.nro_filial
INNER JOIN padron_producto_socio pp ON p.cedula = pp.cedula
WHERE p.estado = 6 AND p.alta ='0' $where2
GROUP BY g.nombre_reporte ORDER BY g.nombre_reporte ASC";

$rIncrementosCall = mysqli_query($mysqli, $qIncrementosCall);

if ($rIncrementosCall->num_rows !== 0) {
    while ($row = mysqli_fetch_array($rIncrementosCall)) {
        $idcall = $row['idcall'];
        $qCantVendedores = "SELECT count(id) as cant_vendedores FROM usuarios WHERE activo=1 AND  idgrupo = $idcall";
        $rCantVendedores = mysqli_query($mysqli,  $qCantVendedores);
        $rCantVendedores = mysqli_fetch_assoc($rCantVendedores)['cant_vendedores'];
      
        $qAcumuladores ="SELECT  g.id, sum(pp.importe) AS monto_ventas
        FROM padron_datos_socio p 
        INNER JOIN usuarios u on p.id_usuario = u.id
        INNER JOIN gruposusuarios g on u.idgrupo = g.id
        INNER JOIN filiales f on p.sucursal = f.nro_filial
        INNER JOIN padron_producto_socio pp on p.cedula = pp.cedula
        where p.estado = 6 AND p.alta ='0'  AND pp.abm='ALTA-PRODUCTO' AND g.id=$idcall $where2
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
            'vendedores_activos' => $rCantVendedores
        );
    }
}

mysqli_close($mysqli);
echo json_encode($res);
