<?php
require_once "../../_conexion.php";
$response = array(
    "result"  => false,
    "session" => false,
);

$response["session"] = true;
$id                  = $_POST["id"] ?? '';

$qCedula = "SELECT cedula FROM padron_producto_socio WHERE id = $id";
if ($rCedula = mysqli_query($mysqli, $qCedula)) {
    $cedula = mysqli_fetch_array($rCedula)[0];

    $qEliminar = "UPDATE padron_producto_socio set accion='2' WHERE id = $id ";

    if ($rEliminar = mysqli_query($mysqli, $qEliminar)) {

        $qTotal = "SELECT sum(importe) FROM padron_producto_socio WHERE accion='1' AND cedula='$cedula'";

        if ($rTotal = mysqli_query($mysqli, $qTotal)) {
            $total       = mysqli_fetch_array($rTotal)[0];
            $qActualizar = "UPDATE padron_datos_socio SET total_importe=$total WHERE cedula=$cedula";

            if ($rAct = mysqli_query($mysqli, $qActualizar)) {
                $response["result"]  = true;
                $response["sesion"]  = true;
                $response["message"] = 'Datos procesados correctamente';
            }
        }
    } else {
        $response["result"]  = false;
        $response["sesion"]  = true;
        $response["message"] = 'Ocurrio un error en la consulta de eliminar';
    }
} else {
    $response["result"]  = false;
    $response["sesion"]  = true;
    $response["message"] = 'Ocurrio un error al consultar la cedula';
}

mysqli_close($mysqli);
echo json_encode($response);
