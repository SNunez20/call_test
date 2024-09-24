<?php
require_once "../../_conexion.php";
$response = array("datos" => [], "result" => false);

if (isset($_POST["typeAdmin"])) {
    $data = array_map(fn($data) => strip_tags(mysqli_real_escape_string($mysqli, $data)), $_POST);
    $cedula = $data['cedula'];
    $esCompetencia = false;
    $fechaHoy = date('Y-m-d H:i');

    $query = "SELECT id, cedula, nombre, direccion, tel, sucursal, fecha_nacimiento, radio, ruta, numero_tarjeta, nombre_titular,cedula_titular,abm, total_importe,fechafil,observaciones, origen_venta, estado, alta, metodo_pago, tipo_tarjeta, localidad FROM padron_datos_socio WHERE cedula = '$cedula'";

    $queryFecha = "SELECT fecha_registro FROM padron_producto_socio WHERE cedula = '$cedula' AND abm IN ('ALTA','ALTA-PRODUCTO') LIMIT 1";
    $rFecha =  mysqli_query($mysqli, $queryFecha);
    $fechaRegistro = mysqli_fetch_assoc($rFecha)['fecha_registro'];
    $fechaRegistro = date('Y-m-d H:i', strtotime($fechaRegistro));
    
    if ($result = mysqli_query($mysqli, $query)) {

        if (mysqli_num_rows($result)>0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $idSocio = $row['id'];
                $alta    = $row['alta'];
                if ($alta == '1') {
                    $qCompe        = "SELECT id as id FROM historico_venta WHERE id_estado = 672 AND id_cliente = $idSocio AND fecha >='$fechaRegistro' AND fecha <='$fechaHoy' order by id desc limit 1";
                    $rCompe        = mysqli_query($mysqli, $qCompe);
                    $esCompetencia = ($rCompe && mysqli_num_rows($rCompe) > 0) ? true : $esCompetencia;
                }
               

                $response["datos"]= array(
                    'cedula'            => $row["cedula"],
                    'nombre'            => $row["nombre"],
                    'direccion'         => $row["direccion"],
                    'tel'               => $row['tel'],
                    'sucursal'          => $row["sucursal"],
                    'fecha_nacimiento'  => $row["fecha_nacimiento"],
                    'radio'             => $row["radio"],
                    'ruta'              => $row["ruta"],
                    'numero_tarjeta'    => $row["numero_tarjeta"],
                    'nombre_titular'    => $row["nombre_titular"],
                    'cedula_titular'    => $row["cedula_titular"],
                    'abm'               => $row["abm"],
                    'fechafil'          => ($row['fechafil']=='') ? '0000-00-00' : $row['fechafil'],
                    'observaciones'     => $row["observaciones"],
                    'total_importe'     => $row["total_importe"],
                    'origen_venta'      => $row['origen_venta'],
                    'estado'            => $row['estado'],
                    'metodo_pago'       => $row['metodo_pago'],
                    'localidad'         => $row['localidad'],
                    'tipoTarjeta'       => $row['tipo_tarjeta'],
                    'id'                => $idSocio,
                    'esCompetencia'     => $esCompetencia
                );
            }
            $response["result"]=true;
        }else{
            $response["result"]=false;
            $response["message"]='No hay socio afiliado con éste número de cédula';
        }

    }else{
        $response["result"]=false;
        $response["message"]='Ha ocurrido un error';
    }
}

mysqli_close($mysqli);
echo json_encode($response);