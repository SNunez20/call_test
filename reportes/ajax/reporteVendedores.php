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
    $qCedulasPadron = "SELECT cedula FROM padron_producto_socio WHERE abmactual = '1' AND abm IN ('ALTA','ALTA-PRODUCTO') GROUP BY cedula";
    $rCedulasPadron = mysqli_query($mysqli250, $qCedulasPadron);
 
    if (mysqli_num_rows($rCedulasPadron) > 0) {
        mysqli_query($mysqli, "DELETE FROM cedulas_padron");
        while ($row = mysqli_fetch_assoc($rCedulasPadron)) {
            mysqli_query($mysqli, "INSERT INTO cedulas_padron VALUES (null,'".$row['cedula']."')");
        }
    }
    $where = " AND p.cedula IN (SELECT cedula FROM cedulas_padron)";

}else{
     $fecha_desde = date('Y-m-d', strtotime($fecha_desde));
     $fecha_hasta = date("Y-m-d",strtotime($fecha_hasta));
     $where =" AND CAST(hr.fecha_carga as date) >= '$fecha_desde' AND cast(hr.fecha_carga as date) <= '$fecha_hasta'";

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

        $qAltasVendedores = "SELECT count(hr.id) AS 'cantidad_altas', IFNULL(sum(hr.total_importe),0) AS 'monto_ventas', IFNULL((sum(hr.total_importe)/$sueldo)*100,0) AS 'target'
        FROM historico_reportes hr
        INNER JOIN  padron_datos_socio p ON p.id = hr.id_cliente
        WHERE hr.alta='1' AND hr.id_usuario = $idVendedor $where";

        $rAltasVendedores  = mysqli_query($mysqli, $qAltasVendedores);
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
            'target' => round($target,2)
        );


        $qIncrementosVendedores = "SELECT count(hr.id) AS 'cantidad_incrementos', IFNULL(sum(hr.total_incremento),0) AS 'monto_ventas', IFNULL((sum(hr.total_incremento)/$sueldo)*100,0) AS 'target'
        FROM historico_reportes hr
        INNER JOIN  usuarios u ON u.id = hr.id_usuario
        INNER JOIN padron_datos_socio p ON p.id = hr.id_cliente
        WHERE hr.alta='0' AND hr.id_usuario = $idVendedor  $where";


        // var_dump($qIncrementosVendedores); exit;

        $rIncrementosVendedores = mysqli_query($mysqli, $qIncrementosVendedores);
        $cantIncrementos = 0;
        $target = 0;
        $montoVentas = 0;

        if ($rIncrementosVendedores->num_rows !== 0) {
        
            while ($row3 = mysqli_fetch_assoc($rIncrementosVendedores)) {
                $cantIncrementos = $row3['cantidad_incrementos'];
                $montoVentas = (int)$row3['monto_ventas'];
                
                // $target = ($sueldo == 0 ) ? 0 : ($montoVentas/$sueldo)*100;
                $target = ($sueldo == 0 ) ? 0 : $row3['target'];

                $res['incrementosVendedores'][] = array(
                    'vendedor' => mb_convert_encoding($nombreVendedor, 'UTF-8', 'UTF-8'),
                    'cedula' => $cedulaVen,
                    'call'  => $call,
                    'cantidad_incrementos' => $cantIncrementos,
                    'monto_ventas' => $montoVentas,
                    'target' => round($target,2)
                );
            
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

    $qAltasVendedores = "SELECT count(hr.id) AS 'cantidad_altas', IFNULL(sum(hr.total_importe),0) AS 'monto_ventas', IFNULL((sum(hr.total_importe)/$sueldo)*100,0) AS 'target', g.nombre_reporte AS 'call', u.usuario AS cedula, u.nombre AS vendedor
    FROM historico_reportes hr
    INNER JOIN usuarios u ON u.id = hr.id_usuario
    INNER JOIN gruposusuarios g ON g.id = hr.id_grupo
    INNER JOIN padron_datos_socio p ON hr.id_cliente = p.id
    WHERE hr.alta='1'  AND u.activo IN ('2','0') $where  
    GROUP BY hr.id_usuario ORDER BY 'vendedor' ASC";

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

    $qIncrementosVendedores = "SELECT count(hr.id) AS 'cantidad_incrementos', IFNULL(sum(hr.total_incremento),0) AS 'monto_ventas', IFNULL((sum(hr.total_incremento)/$sueldo)*100,0) AS 'target', g.nombre_reporte AS 'call',  u.nombre as vendedor, u.usuario AS cedula
    FROM historico_reportes hr
    INNER JOIN  usuarios u ON u.id = hr.id_usuario
    INNER JOIN gruposusuarios g ON g.id = hr.id_grupo
    INNER JOIN padron_datos_socio p ON hr.id_cliente = p.id
    WHERE hr.alta='0' AND u.activo IN ('2','0')  $where
    GROUP BY hr.id_usuario ORDER BY 'vendedor' ASC";

    $rIncrementosVendedores = mysqli_query($mysqli, $qIncrementosVendedores);
    $montoVentas = 0;
    $cantIncrementos = 0;
    $target = 0;

    if ($rIncrementosVendedores->num_rows !== 0) {

        while ($row = mysqli_fetch_assoc($rIncrementosVendedores)) {

            $cedulaVen = $row['cedula'];
            $montoVentas = (int)$row['monto_ventas'];
            $cantIncrementos = $row['cantidad_incrementos'];
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

}else{
    $res['result'] = false;
    $res['message'] = 'No se econtraron vendedores activos';
}



echo json_encode($res);
mysqli_close($mysqli);
