<?php
require '../../_conexion.php';
require '../../_conexion250.php';

$fecha_desde = mysqli_real_escape_string($mysqli, $_POST['fecha_desde']);
$fecha_hasta = mysqli_real_escape_string($mysqli, $_POST['fecha_hasta']);
$sueldo = mysqli_real_escape_string($mysqli, $_POST['sueldo']);
$res = array('result' =>false,'message' => 'No se encontraron resultados');


if ($fecha_desde=='') {
    $fecha_hasta = date('Y-m-d');
    $fecha_desde = date("Y-m-d",strtotime($fecha_hasta."- 1 month"));
 
    // $qCedulasPadron = "SELECT cedula FROM padron_datos_socio WHERE abmactual = '1'";
    $qCedulasPadron = "SELECT cedula FROM padron_producto_socio WHERE abmactual = '1' GROUP BY cedula";
    $rCedulasPadron = mysqli_query($mysqli250, $qCedulasPadron);
 
    if (mysqli_num_rows($rCedulasPadron) > 0) {
        mysqli_query($mysqli, "DELETE FROM cedulas_padron");
        while ($row = mysqli_fetch_assoc($rCedulasPadron)) {
            mysqli_query($mysqli, "INSERT INTO cedulas_padron VALUES (null,'".$row['cedula']."')");
        }
    }
 
    $where = " AND p.cedula IN (SELECT cedula FROM cedulas_padron)";
    $where2 = " AND pp.cedula IN (SELECT cedula FROM cedulas_padron)";
    $w2 = " AND cedula IN (SELECT cedula FROM cedulas_padron)";
    
}else{
     $fecha_desde = date('Y-m-d', strtotime($fecha_desde));
     $fecha_hasta = date("Y-m-d",strtotime($fecha_hasta));
     $where =" AND CAST(p.fechafil as date) >= '$fecha_desde' AND cast(p.fechafil as date) <= '$fecha_hasta'";
     $where2 =" AND CAST(pp.fecha_registro as date) >= '$fecha_desde' AND cast(pp.fecha_registro as date) <= '$fecha_hasta'";
     $w2 = " AND cedula IN (SELECT cedula FROM cedulas_padron)";
}


$qVendedores = "SELECT u.id, u.usuario as cedula, u.nombre as vendedor, g.nombre_reporte as 'call'
FROM usuarios u
INNER JOIN gruposusuarios g ON u.idgrupo = g.id
WHERE u.activo = '1' AND  u.id NOT IN (48,3113,3329,3115,3165) ORDER BY vendedor ASC";

$rVendedores  = mysqli_query($mysqli, $qVendedores);

if ($rVendedores->num_rows !== 0) {
    $res['result']=true;

    while ($row = mysqli_fetch_assoc($rVendedores)) {
        $idVendedor = $row['id'];
        $call = $row['call'];
        $cedulaVen = $row['cedula'];
        $nombreVendedor = $row['vendedor'];

        $qAltasVendedores = "SELECT count(id) AS 'cantidad_altas', IFNULL(sum(p.total_importe),0) AS 'monto_ventas', IFNULL((sum(p.total_importe)/$sueldo)*100,0) AS 'target'
        FROM padron_datos_socio p 
        WHERE p.estado = 6 AND p.alta='1' AND p.id_usuario=$idVendedor $where
        ";

        $rAltasVendedores  = mysqli_query($mysqli, $qAltasVendedores );
        $totalAltas = 0;
        $montoVentas = 0;
        $targe = 0;
        if ($rAltasVendedores->num_rows !== 0){
            $row2 = mysqli_fetch_assoc($rAltasVendedores);
            $montoVentas = $row2['monto_ventas'];
            $totalAltas = $row2['cantidad_altas'];
            $target = $row2['target'];
       

        }

        $res['altasVendedores'][] = array(
            'vendedor' => mb_convert_encoding($nombreVendedor, 'UTF-8', 'UTF-8'),
            'cedula' => $cedulaVen,
            'call'  => $call,
            'cantidad_altas' => $totalAltas,
            'monto_ventas' => $montoVentas,
            'target' => round( $target,2)
        );

        // var_dump($res['incrementosVendedores']);


        $qIncrementosVendedores = "SELECT p.cedula 
        FROM padron_datos_socio p 
        INNER JOIN usuarios u ON p.id_usuario = u.id
        INNER JOIN gruposusuarios g ON u.idgrupo = g.id
        INNER JOIN filiales f ON p.sucursal = f.nro_filial
        INNER JOIN padron_producto_socio pp ON p.cedula = pp.cedula
        WHERE p.estado = 6 AND p.alta='0' AND p.id_usuario =$idVendedor $where2
        GROUP BY p.cedula";
        // var_dump($qIncrementosVendedores); exit;

        $arrCedulas=[];

        $rIncrementosVendedores = mysqli_query($mysqli, $qIncrementosVendedores);
        $cantIncrementos = 0;
        $target = 0;
        $montoVentas = 0;
        if ($rIncrementosVendedores->num_rows !== 0) {
        
            while ($row3 = mysqli_fetch_assoc($rIncrementosVendedores)) {
                $cantIncrementos++;
                $cedulaSocio = $row3['cedula'];
              
             
                if (!in_array($cedulaSocio,$arrCedulas)) {
                    array_push($arrCedulas,$cedulaSocio);
                
                    $qAcumuladores = "SELECT count(id) AS cantidad_incrementos, sum(importe) AS monto_venta, IFNULL((sum(importe)/$sueldo)*100,0) AS 'target' FROM padron_producto_socio WHERE cedula  = '$cedulaSocio' AND abm ='ALTA-PRODUCTO' AND  accion='5' $w2 GROUP BY cedula";
                
                    $rAcumuladores = mysqli_query($mysqli, $qAcumuladores);
                    while ($row2 = mysqli_fetch_assoc($rAcumuladores)) {
                        $monto = (int)$row2['monto_venta'];
                        $montoVentas+= $monto;
                        // $target = ($sueldo == 0 ) ? 0 : (int)$row2['target'];
                    }

                    $target = ($sueldo == 0 ) ? 0 : ($montoVentas/$sueldo)*100;
                    $res['incrementosVendedores'][] = array(
                        'vendedor' => mb_convert_encoding($nombreVendedor, 'UTF-8', 'UTF-8'),
                        'cedula' => $cedulaVen,
                        'call'  => $call,
                        'cantidad_incrementos' => $cantIncrementos,
                        'monto_ventas' => $montoVentas,
                        'target' => round($target,2)
                    );

       

                   
                }
            
            
            }
        }else{
            $res['incrementosVendedores'][] = array(
                'vendedor' => mb_convert_encoding($nombreVendedor, 'UTF-8', 'UTF-8'),
                'cedula' => $cedulaVen,
                'call'  => $call,
                'cantidad_incrementos' => $cantIncrementos,
                'monto_ventas' => $montoVentas,
                'target' => round($target,2)
            );
        }
    }

    $qAltasVendedores = "SELECT u.nombre AS 'vendedor',u.usuario AS 'cedula', g.nombre_reporte AS 'call', count(*) AS 'cantidad_altas', sum(p.total_importe) AS 'monto_ventas', IFNULL((sum(p.total_importe)/$sueldo)*100,0) AS 'target'
    FROM padron_datos_socio p 
    INNER JOIN usuarios u ON p.id_usuario = u.id
    INNER JOIN gruposusuarios g ON u.idgrupo = g.id
    INNER JOIN filiales f ON p.sucursal = f.nro_filial
    WHERE p.estado = 6 AND p.alta='1' AND u.activo ='2' $where  
    GROUP BY u.usuario ORDER BY 'vendedor' ASC";

    $rAltasVendedores = mysqli_query($mysqli, $qAltasVendedores);

    if ($rAltasVendedores->num_rows !== 0) {
        $res['result']=true;
        while ($row = mysqli_fetch_array($rAltasVendedores)) {
        
            $res['altasVendedoresInactivos'][] = array(
                'vendedor' => mb_convert_encoding($row['vendedor'], 'UTF-8', 'UTF-8'),
                'cedula' => $row['cedula'],
                'call'  => $row['call'],
                'cantidad_altas' => $row['cantidad_altas'],
                'monto_ventas' => $row['monto_ventas'],
                'target' => round($row['target'],2)
            );
        }
    } 



    $qIncrementosVendedores = "SELECT u.nombre AS 'vendedor',u.usuario AS 'cedula', g.nombre_reporte AS 'call'
    FROM padron_datos_socio p 
    INNER JOIN usuarios u ON p.id_usuario = u.id
    INNER JOIN gruposusuarios g ON u.idgrupo = g.id
    INNER JOIN filiales f ON p.sucursal = f.nro_filial
    INNER JOIN padron_producto_socio pp ON p.cedula = pp.cedula
    WHERE p.estado = 6 AND p.alta='0' AND u.activo IN ('2','0') $where2
    GROUP BY u.usuario ORDER BY 'vendedor' ASC";

    $arrCedulas=[];

    $rIncrementosVendedores = mysqli_query($mysqli, $qIncrementosVendedores);

    if ($rIncrementosVendedores->num_rows !== 0) {
        while ($row = mysqli_fetch_assoc($rIncrementosVendedores)) {
            $cedulaVen = $row['cedula'];
            $montoVentas = 0;
            $cantIncrementos = 0;
            $target = 0;
            if (!in_array($cedulaVen,$arrCedulas)) {
                array_push($arrCedulas,$cedulaVen);
                
                $qAcumuladores = "SELECT count(id) AS cantidad_incrementos, sum(importe) AS monto_venta FROM padron_producto_socio WHERE numero_vendedor = '$cedulaVen' AND abm ='ALTA-PRODUCTO' AND  accion='5' $w2 
                GROUP BY cedula";
               
            
                $rAcumuladores = mysqli_query($mysqli, $qAcumuladores);
                while ($row2 = mysqli_fetch_assoc($rAcumuladores)) {

                    $montoVentas+= (int)$row2['monto_venta'];
                    $cantIncrementos++;
                    
                }

                $target = ($sueldo == 0 ) ? 0 : ($montoVentas/$sueldo)*100;

                $res['incrementosVendedoresInactivos'][] = array(
                    'vendedor' => mb_convert_encoding($row['vendedor'], 'UTF-8', 'UTF-8'),
                    'cedula' => $cedulaVen,
                    'call'  => $row['call'],
                    'cantidad_incrementos' => $cantIncrementos,
                    'monto_ventas' => $montoVentas,
                    'target' => round($target,2)
                );
            }
        
        }
    }

}else{
    $res['result'] = false;
    $res['message'] = 'No se econtraron vendedores activos';
}




 
mysqli_close($mysqli);
echo json_encode($res);
