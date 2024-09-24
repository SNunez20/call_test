<?php
session_start();

$data                = array_map('stripslashes', $_POST);
$response['result']  = false;
$response['message'] = 'Inténtelo nuevamente mas tarde !!';


$id_producto      = $data['id_producto'];
$servicio         = $data['servicio'];
$hrs_sanatorio    = $data['hrs_sanatorio'];
$hrs_servicio     = $data['hrs_servicio'];
$localidad        = $data['localidad'];
$fecha_nacimiento = $data['fecha_nacimiento'];
$edad             = date("Y") - date("Y", strtotime($fecha_nacimiento));
$base             = $data['base'];

$cantidad_horas = $hrs_sanatorio != "" ? $hrs_sanatorio : $hrs_servicio;

if ($hrs_servicio == "null" || $hrs_servicio == "" || $hrs_servicio == 0)
    $hrs_servicio = 8;


$promo_estaciones = false;

if ($servicio == 1) {
    $obtener_cedula = obtener_cedula_productos($id_producto);
    if ($obtener_cedula == false) {
        $response['result']  = false;
        $response['message'] = 'No se encontró la cédula en padrón.';
        die(json_encode($response));
    }
    $cedula = $obtener_cedula['cedula'];


    $consultar_precio_sanatorio = obtener_precio_actual_sanatorio($cedula);
    if ($consultar_precio_sanatorio == false) {
        $response['result']  = false;
        $response['message'] = 'No se encontró el precio del servicio en padrón.';
        die(json_encode($response));
    }
    $precio_sanatorio = mysqli_fetch_assoc($consultar_precio_sanatorio)['importe'];

    $lista_precios_promo_estaciones = obtener_lista_precios_promo_estaciones();
    if ($lista_precios_promo_estaciones == false) {
        $response['result']  = false;
        $response['message'] = 'No se encontró la lista de precios.';
        die(json_encode($response));
    }

    while ($row = mysqli_fetch_assoc($lista_precios_promo_estaciones)) {
        $precio_lista_promo_estaciones = $row['precio'];

        if ($precio_sanatorio == $precio_lista_promo_estaciones) $promo_estaciones = true;
    }
}


$precio = calcular_precio_servicio($edad, $servicio, $cantidad_horas, $promo_estaciones);


$response = [
    'result'            => true,
    'precio'            => $precio,
    'tiene_precio_base' => false,
    'precio_base'       => 0,
    'es_sanatorio'      => false,
    'precio_sanatorio'  => 0,
    'precio_servicio'   => 0,
];



echo json_encode($response);




function calcular_precio_servicio($edad, $id_servicio, $cantidad_horas, $promo_estaciones)
{
    if ($promo_estaciones != false) {
        $obtener_datos_motor_precios = obtener_precio_promo_estacion($cantidad_horas);
    } else {
        $obtener_datos_motor_precios = $cantidad_horas != "" ?
            calcular_precios($id_servicio, $cantidad_horas, $edad) :
            obtener_precio_servicio($id_servicio, $cantidad_horas);
    }

    return $obtener_datos_motor_precios['precio'];
}


function calcular_precios($numero_servicio, $cantidad_horas, $edad)
{
    require '../../../_conexion1310.php';

    $where = "";
    if (in_array($numero_servicio, [1, 2]))
        $where = $edad < 65 ? "AND $edad BETWEEN edad_desde AND edad_hasta" : "AND edad_desde > 65";


    $sql = "SELECT precio FROM lista_de_precios WHERE id_servicio = '$numero_servicio' AND horas = '$cantidad_horas' $where AND activo = 1";
    $consulta = mysqli_query($mysqli1310, $sql);

    $resultado = $consulta != false ? mysqli_fetch_assoc($consulta) : false;

    mysqli_close($mysqli1310);
    return $resultado;
}


function obtener_precio_servicio($numero_servicio, $cantidad_horas)
{
    require '../../../_conexion1310.php';

    $sql = "SELECT precio FROM lista_de_precios WHERE id_servicio = '$numero_servicio' AND horas = '$cantidad_horas' AND activo = 1";
    $consulta = mysqli_query($mysqli1310, $sql);

    $resultado = $consulta != false ? mysqli_fetch_assoc($consulta) : false;

    mysqli_close($mysqli1310);
    return $resultado;
}


function obtener_precio_promo_estacion($cantidad_horas)
{
    require '../../../_conexion1310.php';

    $sql = "SELECT precio FROM precios_promo_estaciones WHERE horas = '$cantidad_horas' AND activo = 1";
    $consulta = mysqli_query($mysqli1310, $sql);

    $resultado = $consulta != false ? mysqli_fetch_assoc($consulta) : false;

    mysqli_close(mysql: $mysqli1310);
    return $resultado;
}


function obtener_cedula_productos($id_producto)
{
    require '../../../_conexion.php';

    $sql = "SELECT cedula FROM padron_producto_socio WHERE id = '$id_producto'";
    $consulta = mysqli_query($mysqli, $sql);

    $resultado = $consulta != false ? mysqli_fetch_assoc($consulta) : false;

    mysqli_close(mysql: $mysqli);
    return $resultado;
}


function obtener_precio_actual_sanatorio($cedula)
{
    require '../../../_conexion.php';

    $sql = "SELECT * FROM padron_producto_socio WHERE cedula = '$cedula' AND servicio = 01 GROUP BY servicio";
    $consulta = mysqli_query($mysqli, $sql);

    mysqli_close(mysql: $mysqli);
    return $consulta;
}


function obtener_lista_precios_promo_estaciones()
{
    require '../../../_conexion1310.php';

    $sql = "SELECT * FROM precios_promo_estaciones WHERE activo = 1";
    $consulta = mysqli_query($mysqli1310, $sql);

    mysqli_close(mysql: $mysqli1310);
    return $consulta;
}
