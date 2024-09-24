<?php
require_once "../../_conexion.php";
require_once "../../_conexion250.php";
$response = array(
    "result"  => false,
    "session" => false,
);

if (isset($_POST["typeAdmin"])) {
    $response["session"] = true;
    $cedulaSocio         = $_POST["id"] ?? '';
    $omt                 = false;
    $esPromoCompetencia = false; //compe
    $totalConDtoCompetencia = 0; //compe
    $dtoCompetencia = 0.10; //compe
    $aplicaPromoCr = false; //conva

    // QUERY PARA EL RESUMEN DE PRODUCTO ,
    $query = "SELECT pd.id, pd.servicio, s.nombre_servicio, s.id as id_servicio, sum(hora) as horas, sum(importe) as total_importe, pd.accion as accion, ps.total_importe as total, pd.cod_promo as promo, ps.dato_extra, ps.alta
            FROM padron_producto_socio pd
            INNER JOIN servicios s
            ON pd.servicio = s.nro_servicio
            INNER JOIN padron_datos_socio  ps
            ON ps.cedula = pd.cedula
            WHERE pd.cedula='$cedulaSocio'
            GROUP BY pd.accion, pd.servicio ORDER BY pd.id";

    $productosResumen = [];
    if ($result = mysqli_query($mysqli, $query)) {
        $c= 0;
        while ($row = mysqli_fetch_assoc($result)) {
            
            $total = $row['total'];
            $promo = $row['promo'];//compe
            $alta = $row['alta'];//compe
            $nroServicio = $row['servicio'];
            $totalImporte = $row['total_importe'];
            $esPromoCompetencia = (!$esPromoCompetencia && ($promo =='35' || $promo=='2035' || $promo=='3335')) ? true : $esPromoCompetencia; //compe
            $totalConDtoCompetencia += ($esPromoCompetencia && $nroServicio=='01') ? round($totalImporte*$dtoCompetencia) : $totalImporte; //compe
           
            if ($nroServicio=='101') {
                // var_dump($productosResumen); exit;
                $productosResumen[$c-1]['importe'] = (int)$productosResumen[$c-1]['importe'] + (int)$totalImporte;
            }else{
                $productosResumen[] = array(
                    'id'              => $row["id"],
                    'horas'           => $row["horas"],
                    'accion'          => $row["accion"],
                    'id_servicio'     => $row["id_servicio"],
                    'nro_servicio'    => $nroServicio, //compe
                    'nombre_servicio' => $row["nombre_servicio"],
                    'importe'         => $totalImporte, //compe
                    'accion'          => $row['accion'],
                    'promo'           => $row["promo"],
                    'dato_extra'      => $row['dato_extra'],
                );
            }
            

            $c++;
        }
    }

    $response['productosResumen'] = $productosResumen;

    // QUERY PARA EL PANEL DE EDICION DE PRODUCTOS
    $query = "SELECT pd.id, pd.servicio,ps.nombre, pd.cedula, pd.cod_promo, s.nombre_servicio, s.id as id_servicio, sum(hora) as horas, sum(importe) as total_importe,
    ps.sucursal, ps.fecha_nacimiento, ps.alta
            FROM padron_producto_socio pd
            INNER JOIN servicios s
            ON pd.servicio = s.nro_servicio
            INNER JOIN padron_datos_socio  ps
            ON ps.cedula = pd.cedula
            WHERE pd.cedula='$cedulaSocio' and pd.accion='1'
            GROUP BY pd.servicio ORDER BY pd.id ASC";

    $productosSocio = [];
    if ($result = mysqli_query($mysqli, $query)) {
        $result2          = mysqli_query($mysqli, $query);
        $row              = mysqli_fetch_array($result2);
        $suc              = ($row['sucursal'] == 3 || $row['sucursal'] == 7 || $row['sucursal'] == 8) ? '0' . $row['sucursal'] : $row['sucursal'];
        $cedula_cliente   = $row['cedula'];
        $nombre_cliente   = $row['nombre'];
        $fecha_nacimiento = $row['fecha_nacimiento'];
        $qlocalidad       = "SELECT id FROM filiales WHERE nro_filial= $suc";
        $rloc             = mysqli_query($mysqli, $qlocalidad);

        $localidad = mysqli_fetch_assoc($rloc)['id'];

        $c = 0;
        while ($row = mysqli_fetch_assoc($result)) {
            $id              = $row['id'];
            $id_servicio     = $row['id_servicio'];
            $nro_servicio    = $row['servicio'];
            $cod_promo       = $row['cod_promo'];
            $nombre_servicio = $row['nombre_servicio'];
            $horas           = $row['horas'];
            $total_importe   = $row['total_importe'];
            $alta            = $row['alta'];
         
            if ($nro_servicio=='101') {
                $productosSocio[$c-1]['total_importe'] = (int)$productosSocio[$c-1]['total_importe'] + (int)$total_importe;
            }else{
                $productosSocio[] = array(
                'id'              => $id,
                'id_servicio'     => $id_servicio,
                'nro_servicio'    => $nro_servicio,
                'nombre_servicio' => $nombre_servicio,
                'horas'           => $horas,
                'total_importe'   => $total_importe,
                'cod_promo'       => $cod_promo,
                'alta'            => $alta);
            }
           
           $c++;
        }

    
   
        // var_dump($productosResumen);
        // var_dump($productosSocio);exit;
        

        for ($i = 0; $i < count($productosSocio); $i++) {
            $qTotalHoras                       = "SELECT SUM(hora) as horas FROM padron_producto_socio WHERE accion ='3' AND cedula='$cedulaSocio' AND servicio='" . $productosSocio[$i]['nro_servicio'] . "'";
            $rTotalHoras                       = mysqli_query($mysqli, $qTotalHoras);
            $totalHoras                        = mysqli_fetch_assoc($rTotalHoras)['horas'];
            $productosSocio[$i]['total_horas'] = $totalHoras;
        }

        $datosOmt =null;
        $qOmt = "SELECT cedula,importe from padron_producto_socio WHERE servicio='70' AND cedula_titular_gf = '$cedulaSocio'";
        
        if ($resultomt = mysqli_query($mysqli,$qOmt)) {
           if (mysqli_num_rows($resultomt)>0) {
               $row = mysqli_fetch_assoc($resultomt);
                $cedulaomt = $row['cedula'];
                $importeomt = $row['importe'];
                $total+=$importeomt;
               $omt = true;
               $qDatosOmt = "SELECT nombre, tel, direccion from padron_datos_socio WHERE cedula = '$cedulaomt'";
               if ($rDatosOmt = mysqli_query($mysqli,$qDatosOmt)) {
             
                if (mysqli_num_rows($rDatosOmt)>0) {
                    $r =mysqli_fetch_assoc($rDatosOmt);
                     $nombreOmtBen = $r['nombre'];
                     $telOmtBen = $r['tel'];
                     $dirOmtBen = $r['direccion'];
                    $omt = true;
                  
                }
                $datosOmt= array('cedula_omtben'=> $cedulaomt,'nombre_omtben' => $nombreOmtBen,'telefono_omtben'=> $telOmtBen,'importeomt' =>$importeomt);
             }
           }
        }

         //conva comprobamos si tienen convalecencia de regalo
         $qPromoCr = "SELECT id FROM padron_producto_socio WHERE abm='ALTA' AND cedula = '$cedulaSocio' AND cod_promo='24'";
         $rPromoCr = mysqli_query($mysqli, $qPromoCr);
         if ($rPromoCr && mysqli_num_rows($rPromoCr)>0) {
             $aplicaPromoCr = true;
         }

        $response['localidad']        = $localidad;
        $response['fecha_nacimiento'] = $fecha_nacimiento;
        $response['cedula']           = $cedula_cliente;
        $response['nombre_cliente']   = $nombre_cliente;
        $response['productos']        = $productosSocio;
        $response['total']            = $total;
        $response['totalDtoCompetencia'] = $totalConDtoCompetencia; //compe
        $response['dtoCompetencia']   = $esPromoCompetencia;//compe
        $response['aplicaPromoCr']    = $aplicaPromoCr;//conva
        $response["result"]           = true;
        $response["sesion"]           = true;
        $response['omt']              = $omt;
        $response['datosOmt']         = $datosOmt;
    } else {
        $response["result"]  = false;
        $response["sesion"]  = true;
        $response["message"] = 'Ocurrio un error en la consulta a la base de datos';

    }
}

mysqli_close($mysqli);
mysqli_close($mysqli250);
echo json_encode($response);
