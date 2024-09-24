<?php
require_once "../../_conexion.php";
require_once "../../_conexion250.php";

$response = array(
    "result"  => false,
    "session" => true,
    "message" => "",
    "existe"  => false,
);

$cedula = $_POST["cedula"];
$id     = $_POST["id"];
$existe = false;
$origenVenta = obtenerOrigenVenta($cedula);
// Comprueba si existe en padrón
$query = "SELECT * FROM padron_datos_socio WHERE cedula='$cedula'";
if ($result = mysqli_query($mysqli250, $query)) {
    if (mysqli_num_rows($result) == 1) {
        $response["existe"] = true;
    } else if($origenVenta!='6') {
        // Comprueba si existe en la tabla de pagos
        $result = mysqli_query($mysqli, "SELECT * FROM pagos WHERE id_afiliado=$id AND estado != 'rejected' AND estado != 'refunded'");
        if (mysqli_num_rows($result) == 1) {
            $response["existe"] = true;
        }
    }
}

function obtenerOrigenVenta($cedulaAfiliado){
    $origenVenta = false;
    global $mysqli;

    $query = "SELECT origen_venta FROM padron_datos_socio WHERE cedula = '$cedulaAfiliado'";
    if ($result = mysqli_query($mysqli, $query)) {
        while ($row = mysqli_fetch_assoc($result)) {
            // Valido cada beneficiario en padron
            $origenVenta = $row["origen_venta"];
        }
    }

    return $origenVenta;
}

mysqli_close($mysqli);
mysqli_close($mysqli250);
echo json_encode($response);
