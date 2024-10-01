<?php
require '../../_conexion.php';
require '../../_conexion250.php';

$fecha_desde = mysqli_real_escape_string($mysqli, $_POST['fecha_desde']);
$fecha_hasta = mysqli_real_escape_string($mysqli, $_POST['fecha_hasta']);
$res = array('result' =>false,'message' => 'No se encontraron resultados');


if ($fecha_desde=='') {
    $fecha_hasta = date('Y-m-d');
    $fecha_desde = date("Y-m-d",strtotime($fecha_hasta."- 6 month"));
 
    // $qCedulasPadron = "SELECT cedula FROM padron_datos_socio WHERE abmactual = '1'";
    $qCedulasPadron = "SELECT cedula FROM padron_producto_socio WHERE abmactual = '1' AND abm IN ('ALTA','ALTA-PRODUCTO') GROUP BY cedula";;
    $rCedulasPadron = mysqli_query($mysqli250, $qCedulasPadron);
 
    if (mysqli_num_rows($rCedulasPadron) > 0) {
        mysqli_query($mysqli, "DELETE FROM cedulas_padron");
        while ($row = mysqli_fetch_assoc($rCedulasPadron)) {
            mysqli_query($mysqli, "INSERT INTO cedulas_padron VALUES (null,'".$row['cedula']."')");
        }
    }

    
}else{
     $fecha_desde = date('Y-m-d', strtotime($fecha_desde));
     $fecha_hasta = date("Y-m-d",strtotime($fecha_hasta));

}

$meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
$headers = array('filial' => 'Filial', 'mes1' => '', 'mes2' =>'', 'mes3' => '','mes4' =>'' ,'mes5' =>'', 'mes6' =>'' );


$qFiliales = "SELECT id,nro_filial,nombre_filial FROM filiales WHERE mostrar='1'";
$rFiliales  = mysqli_query($mysqli, $qFiliales);
$arrEstadisticas = array();

while ($row = mysqli_fetch_assoc($rFiliales)) {

    $nombreFilial = $row['nombre_filial'];
    $nroFilial = $row['nro_filial'];
    $idFilial = $row['id'];
    $totalesMeses = [];
    $datetime1 = new DateTime($fecha_desde);
    $datetime2 = new DateTime($fecha_hasta);

    # obtenemos la diferencia entre las dos fechas
    $interval = $datetime2->diff($datetime1);

    # obtenemos la diferencia en meses
    $intervalMeses=$interval->format("%m");

    
    for ($i=1; $i <= $intervalMeses ; $i++) { 
       
        $mes = $meses[$datetime1->format('n')-1];
        $diaIni = ($i==1) ? $datetime1->format('d') : '01';
        // $diaFin = ($i==6) ? $datetime2->format('d') : $datetime1->format('t');
        $diaFin = $datetime1->format('t');

        $fdesde =  $datetime1->format('Y').'-'.$datetime1->format('m').'-'.$diaIni;
        $fhasta =  $datetime1->format('Y').'-'.$datetime1->format('m').'-'.$diaFin;
        // var_dump($fdesde);
        // var_dump($fhasta);

        $where = ($fecha_desde=='') ?  " AND p.cedula IN (SELECT cedula FROM cedulas_padron)" : " AND CAST(hr.fecha_carga as date) >= '$fdesde' AND cast(hr.fecha_carga as date) <= '$fhasta'";
        
        $qAltas = "SELECT COUNT(hr.id) as total FROM historico_reportes hr
        INNER JOIN padron_datos_socio p ON p.id = hr.id_cliente
        WHERE hr.alta='1' AND hr.id_filial =$idFilial $where";

     
        // var_dump($qAltas); exit;
        $rAltas = mysqli_query($mysqli, $qAltas);
        $total = 0;
        while ($row1 = mysqli_fetch_assoc($rAltas)) {
            $total= $row1['total'];
        }
        // var_dump($datetime1->format('Y'));
       
        $headers['mes'.$i] = $mes;
        $totalesMeses[$i-1]= $total;
        $datetime1->modify('+ 1 month');

    }
    // exit;

    $arrEstadisticas[] = array('filial'=> $nombreFilial, 'mes1' => $totalesMeses[0], 'mes2' => $totalesMeses[1], 'mes3' => $totalesMeses[2], 'mes4' => $totalesMeses[3], 'mes5' => $totalesMeses[4], 'mes6' => $totalesMeses[5]);
    

}

$res= array('result' => true, 'headers' => $headers, 'estadisticas' => $arrEstadisticas);


// var_dump(mysqli_error($mysqli)); exit;
 
mysqli_close($mysqli);
echo json_encode($res);
