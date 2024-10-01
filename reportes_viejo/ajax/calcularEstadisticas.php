<?php
require '../../_conexion.php';
require '../../_conexion250.php';

$fecha_desde = mysqli_real_escape_string($mysqli, $_POST['fecha_desde']);
$fecha_hasta = mysqli_real_escape_string($mysqli, $_POST['fecha_hasta']);
$res = array('result' =>true,'message' => 'No se encontraron resultados');


if ($fecha_desde=='') {
    $fecha_hasta = date('Y-m-d');
    $fecha_desde = date("Y-m-d",strtotime($fecha_hasta."- 6 month"));
 
    // $qCedulasPadron = "SELECT cedula FROM padron_datos_socio WHERE abmactual = '1'";
    $qCedulasPadron = "SELECT cedula FROM padron_producto_socio WHERE abmactual = '1' GROUP BY cedula";
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
$headers = array('mes1' => '', 'mes2' => '', 'mes3' => '', 'mes4' => '' , 'mes5' => '', 'mes6' => '' );
$arrEstadisticas = array('altas' => '', 'incrementos' => '', 'bajas' => '');

$fillHeaders = false;
// var_dump($intervalMeses);exit;
 foreach ($arrEstadisticas as $key => $val) {
    $arrTotales = [];
    $datetime1 = new DateTime($fecha_desde);
    $datetime2 = new DateTime($fecha_hasta);

    # obtenemos la diferencia entre las dos fechas
    $interval = $datetime2->diff($datetime1);

    # obtenemos la diferencia en meses
    $intervalMeses=$interval->format("%m");

    
    for ($i=1; $i <= $intervalMeses ; $i++) { 
       
        $mes = $meses[$datetime1->format('n')-1];
        if (!$fillHeaders) {
            $headers['mes'.$i] = $mes;
        }
        $diaIni = ($i==1) ? $datetime1->format('d') : '01';
        $diaFin = ($i==6) ? $datetime2->format('d') : $datetime1->format('t');
        // var_dump($datetime1->format('m'));

        $fdesde = $datetime1->format('Y').'-'.$datetime1->format('m').'-'.$diaIni;
        $fhasta = $datetime1->format('Y').'-'.$datetime1->format('m').'-'.$diaFin;


        $where = ($fecha_desde=='') ?  " AND p.cedula IN (SELECT cedula FROM cedulas_padron)" : " AND CAST(h.fecha as date) >= '$fdesde' AND cast(h.fecha as date) <= '$fhasta'";

        if ($key=='altas') {
            $q = "SELECT COUNT(h.id) as total FROM historico_venta h
            INNER JOIN padron_datos_socio p ON p.id = h.id_cliente
            WHERE h.observacion LIKE '%ALTA%'AND p.estado = 6 $where";
           
        }else if($key=='incrementos'){
            $q = "SELECT COUNT(h.id) as total FROM historico_venta h
            INNER JOIN padron_datos_socio p ON p.id = h.id_cliente
            WHERE h.observacion LIKE '%INCREMENTO%' AND p.estado = 6 $where";
        }else if($key =='bajas'){
            $q = "SELECT COUNT(id) as total FROM crm.bajas WHERE estado = 'Otorgada' AND CAST(fecha_fin_gestion as date) >= '$fdesde' AND cast(fecha_fin_gestion as date) <= '$fhasta'";
   
        }
        
        
    //    if ($key=='bajas') {
    //      var_dump($fdesde);
    //      var_dump($q); 
    //    }
        
      
        $rquery = ($key=='bajas') ? mysqli_query($mysqli250, $q) : mysqli_query($mysqli, $q);
        $total =0;
        while ($row1 = mysqli_fetch_assoc($rquery)) {
            $total= $row1['total'];
        }
        
        // var_dump($datetime1->format('Y'));
        array_push($arrTotales,$total);
       
      
        $datetime1->modify('+ 1 month');
        // var_dump($arrTotales);
    }

    $fillHeaders = true;
    $arrEstadisticas[$key] = $arrTotales;
   
  
 }
//  exit;


    

  
    



$res= array('result' => true,'headers' =>$headers, 'estadisticas' =>$arrEstadisticas);


// var_dump(mysqli_error($mysqli)); exit;
 
mysqli_close($mysqli);
echo json_encode($res);
