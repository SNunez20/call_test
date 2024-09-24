<?php

$response["result"] = false;
$response["session"] = true;


$id_producto = $_POST["id"] ?? '';

$consulta_cedula = obtener_cedula_producto($id_producto);
if (!$consulta_cedula) {
    $response["result"]  = false;
    $response["sesion"]  = true;
    $response["message"] = 'Ocurrió un error al consultar la cédula';
    die(json_encode($response));
}


$cedula = mysqli_fetch_array($consulta_cedula)[0];


$eliminar_producto = eliminar_producto($id_producto);
if (!$eliminar_producto) {
    $response["result"]  = false;
    $response["sesion"]  = true;
    $response["message"] = 'Ocurrió un error en la consulta de eliminar';
    die(json_encode($response));
}


$lista_servicios_activos = obtener_servicios_activos($cedula);
if (!$lista_servicios_activos) {
    $response["result"]  = false;
    $response["sesion"]  = true;
    $response["message"] = 'Ocurrió un error en la consulta de servicios activos';
    die(json_encode($response));
}


$importe_total = 0;
while ($row = mysqli_fetch_assoc($lista_servicios_activos)) {
    $servicio_activo = $row['servicio'];
    $consultar_importe = obtener_nuevo_importe_producto($cedula, $servicio_activo);

    if (!$consultar_importe) {
        $response["result"]  = false;
        $response["sesion"]  = true;
        $response["message"] = 'Ocurrió un error al consultar el importe';
        die(json_encode($response));
    }

    $importe_total += $consultar_importe['importe'];
}


$actualizar_importe = modifico_importe_padron_socio($importe_total, $cedula);
if ($actualizar_importe) {
    $response["result"]  = true;
    $response["sesion"]  = true;
    $response["message"] = 'Datos procesados correctamente';
    die(json_encode($response));
}



echo json_encode($response);




function obtener_cedula_producto($id_producto)
{
    require "../../../_conexion.php";

    $sql = "SELECT cedula FROM padron_producto_socio WHERE id = $id_producto";
    $consulta = mysqli_query($mysqli, $sql);

    mysqli_close($mysqli);
    return $consulta;
}


function eliminar_producto($id_producto)
{
    require "../../../_conexion.php";

    $sql = "UPDATE padron_producto_socio set accion = '2' WHERE id = $id_producto";
    $consulta = mysqli_query($mysqli, $sql);

    mysqli_close($mysqli);
    return $consulta;
}


function obtener_servicios_activos($cedula)
{
    require "../../../_conexion.php";

    $sql = "SELECT servicio FROM padron_producto_socio WHERE cedula = '$cedula' AND accion = '1' GROUP BY servicio";
    $consulta = mysqli_query($mysqli, $sql);

    mysqli_close($mysqli);
    return $consulta;
}


function obtener_nuevo_importe_producto($cedula, $servicio)
{
    require "../../../_conexion.php";

    $sql = "SELECT importe FROM padron_producto_socio WHERE cedula = '$cedula' AND servicio = '$servicio' GROUP BY servicio";
    $consulta = mysqli_query($mysqli, $sql);

    $resultados = $consulta != false ? mysqli_fetch_assoc($consulta) : false;

    mysqli_close($mysqli);
    return $resultados;
}


function modifico_importe_padron_socio($importe_total, $cedula)
{
    require "../../../_conexion.php";

    $sql = "UPDATE padron_datos_socio SET total_importe = '$importe_total' WHERE cedula = '$cedula'";
    $consulta = mysqli_query($mysqli, $sql);

    mysqli_close($mysqli);
    return $consulta;
}
