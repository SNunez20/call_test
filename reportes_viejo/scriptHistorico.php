<?php
require '../_conexion.php';
require '../_conexion250.php';


$qDatosPiscina = "SELECT id_usuario, id, cedula, alta, total_importe, sucursal, metodo_pago, observaciones, radio, origen_venta
                  FROM padron_datos_socio where estado=6";

$rDatosPiscina = mysqli_query($mysqli,$qDatosPiscina);
$totalRegistros = 0;
while($row = mysqli_fetch_assoc($rDatosPiscina)){
    $id_cliente = $row['id'];
    $cedula = $row['cedula'];
    $id_usuario = $row['id_usuario'];
    $alta = $row['alta'];
    $total_importe = $row['total_importe'];
    $sucursal = $row['sucursal'];
    $metodo_pago = $row['metodo_pago'];
    $observaciones  = $row['observaciones'];
    $radio = $row['radio'];
    $origen_venta = $row['origen_venta'];

    $qIdSucursal = "SELECT id FROM filiales WHERE nro_filial =$sucursal";
    $rIdSucursal = mysqli_query($mysqli,$qIdSucursal);
    $idSucursal = mysqli_fetch_assoc($rIdSucursal)['id'];

    $qIdGrupo = "SELECT idgrupo FROM usuarios WHERE id = $id_usuario";
    $rIdGrupo = mysqli_query($mysqli,$qIdGrupo);
    $IdGrupo = mysqli_fetch_assoc($rIdGrupo)['idgrupo'];

    $qFechaRegistro = "SELECT fecha_registro FROM padron_producto_socio WHERE cedula = $cedula ORDER BY id DESC LIMIT 1";
    $rFechaRegistro = mysqli_query($mysqli,$qFechaRegistro);
    
    $fechaRegistro = ($rFechaRegistro) ? mysqli_fetch_assoc($rFechaRegistro)['fecha_registro'] : false;

    $qTotalIncremento = "SELECT sum(importe) AS total_incremento FROM padron_producto_socio WHERE cedula = $cedula AND fecha_registro = '$fechaRegistro'";
    $rTotalIncremento = mysqli_query($mysqli,$qTotalIncremento);
    $totalIncremento = mysqli_fetch_assoc($rTotalIncremento)['total_incremento'];
        
    $qGuardarHistorico = "INSERT INTO historico_reportes VALUES (null, $id_usuario,$id_cliente,'$alta','$fechaRegistro','$total_importe','$totalIncremento',$idSucursal, $IdGrupo, $metodo_pago, '$observaciones', '$radio','$origen_venta')";

    $rInsert = mysqli_query($mysqli,$qGuardarHistorico);

    if ($rInsert) {
        $totalRegistros++;

        $idHistorico = mysqli_insert_id($mysqli);
    
        $qServicios = "SELECT servicio, sum(hora) AS horas, sum(importe) AS importe FROM padron_producto_socio WHERE cedula = '$cedula' AND fecha_registro  = '$fechaRegistro' GROUP BY servicio";
        $rServicios = mysqli_query($mysqli,$qServicios);
        if ($rServicios) {
            
            while($row = mysqli_fetch_assoc($rServicios)){
                $nroServicio = $row['servicio'];
                $importe = $row['importe'];
                $horas = $row['horas'];
        
                $qIdServicio = "SELECT id FROM servicios WHERE nro_servicio = '$nroServicio'";
                $rIdServicio = mysqli_query($mysqli,$qIdServicio);
                if (mysqli_num_rows($rIdServicio)==0) {
                    echo "id $nroServicio cedula : $cedula "; exit;
                }
                $idServicio = mysqli_fetch_assoc($rIdServicio)['id'];
                $qGuardarServicio = "INSERT INTO historico_reportes_servicios VALUES (null, $idHistorico, $idServicio, $importe, $horas)";
                $rGuardarServicio = mysqli_query($mysqli,$qGuardarServicio);

                if (!$rGuardarServicio) {
                    echo $nroServicio; exit;
                }
        
        
            }
        }else{
            echo mysqli_error($mysqli);exit;
        }
    
    }else{
        echo $sucursal. "\n";
        echo $fechaRegistro. "\n";
        echo $qGuardarHistorico ."\n";
        echo mysqli_error($mysqli);
        exit;
    }
   


}

echo "listo Total registros en historico $totalRegistros";

mysqli_close($mysqli);
mysqli_close($mysqli250);