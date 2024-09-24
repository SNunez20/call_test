<?php

require_once "../../_conexion.php";
$response = array(
    "result"  => false,
    "session" => false,
);

$response["session"] = true;
$id                  = $_POST["id"] ?? '';

$qCedula = "SELECT cedula,servicio,fecha_registro FROM padron_producto_socio WHERE id = $id";
if ($rCedula = mysqli_query($mysqli, $qCedula)) {
    $row = mysqli_fetch_assoc($rCedula);
    $cedula   = $row['cedula'];
    $fechareg = $row['fecha_registro'];
    $elimServ = $row['servicio'];

    $qEliminar = "DELETE FROM padron_producto_socio WHERE id = $id";

    if ($rEliminar = mysqli_query($mysqli, $qEliminar)) {

        $qTotal = "SELECT sum(importe) FROM padron_producto_socio WHERE cedula='$cedula'";

        if ($rTotal = mysqli_query($mysqli, $qTotal)) {
            $total       = mysqli_fetch_array($rTotal)[0];
            $qActualizar = "UPDATE padron_datos_socio SET total_importe=$total WHERE cedula=$cedula";

            if ($rAct = mysqli_query($mysqli, $qActualizar)) {
                $response["result"]  = true;
                $response["sesion"]  = true;
                $response["message"] = 'Datos eliminados correctamente';
            }
        }
    } else {
        $response["result"]  = false;
        $response["sesion"]  = true;
        $response["message"] = 'Ocurrio un error al eliminar';
    }
} else {
    $response["result"]  = false;
    $response["sesion"]  = true;
    $response["message"] = 'Ocurrio un error al consultar la cedula';
}

mysqli_close($mysqli);
echo json_encode($response);
