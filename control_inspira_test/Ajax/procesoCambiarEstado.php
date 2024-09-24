<?php
require_once "../../_conexion.php";
require_once "../../_conexion250.php";

$response = array(
    "result"  => false,
    "session" => false,
);

if (!isset($_POST["typeAdmin"]))
    die(json_encode($response));

$data          = array_map(fn ($data) => strip_tags(mysqli_real_escape_string($mysqli, $data)), $_POST);
$id            = $data['id'];
$idUser        = $data['idUser'];
$fecha         = date('Y-m-d H:i');
$observacion   = $data["observacion"];
$motivoRechazo = $data["motivo"] ?? 11;

/**
 * ########################################################################################################################################
 *
 * Si el estado == "false" será derivado a morosidad si el método de pago es 2 y es tarjeta vida
 *  => Tarjeta vida ingresa a MOROSIDAD
 *  => Altas con MERCADO PAGO u otro medio ingresan directo a BIENVENIDA
 *
 * Si el estado != false, se cambiara al estado indicado, ya sea rechazadao o aprobado dependiendo del secetor ( Morosidad | Bienvenida )
 *
 * #########################################################################################################################################
 */
$estado = $data['estado'];
if ($estado == "false") {
    $query = "SELECT estado, tarjeta_vida, metodo_pago, alta FROM padron_datos_socio WHERE id=$id";
    if ($result = mysqli_query($mysqli, $query)) {
        // COMERCIAL
        // Continua el proceso para ingresar en padrón
        // 2 - MOROSIDAD
        // 3 - BIENVENIDA
        $row = mysqli_fetch_assoc($result);

        // MEDIO DE PAGO TARJETA, MERCADO PAGO, ALTA  => DERIVA A BIENVENIDA
        if (
            ($row["metodo_pago"] == 2
                && $row["tarjeta_vida"] == "0"
                && $row["alta"] == "1"
            )
            ||
            $row["metodo_pago"] == 1
            || $row["metodo_pago"] == 3
            || $row["metodo_pago"] == 5
        ) {
            $estado = 3;
            // MEDIO DE PAGO TARJETA VIDA (ALTA O INCREMENTO) => DERIVA A MOROSIDAD
        } else if ($row["metodo_pago"] == 2) {
            $estado = 2;
        }
    }
}
$observacion = strtoupper($observacion);
if ($estado != "false") {
    $response["result"] = mysqli_query($mysqli, "UPDATE padron_datos_socio SET estado=$estado WHERE id=$id");
}

$qHistorico         = "INSERT INTO historico_venta VALUES (null,$idUser,$id,$estado,'$fecha','$observacion', $motivoRechazo)";
$response["result"] = mysqli_query($mysqli, $qHistorico);


die(json_encode($response));
