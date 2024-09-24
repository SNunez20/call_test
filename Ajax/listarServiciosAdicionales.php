<?php
session_start();
// require "../_conexion1310.php";
require "../_conexion.php";

$response = array('result' => false, 'message' => 'Intentelo nuevamente mas tarde !!');

if (!isset($_SESSION['idusuario']))
    die(json_encode(['result' => false, 'message' => 'Sin sesion']));

$data = $_POST['array'];
/**
 * Code - Estado
 * 0 - Figura en padrón
 * 1 - No es socio ni esta en baja (ALTA)
 * 2 - Clearing
 * 3 - Todos los productos con cualquier medio de pago
 * 4 - Solo productos acotados con culaquier medio de pago
 * 5 - Solo productos actotados SOLO con tarjeta de crédito
 */
$code = $_POST["code"];
$localidad = $_POST["localidad"];

$esGrupo = isset($_POST["esGrupo"]) ? $_POST["esGrupo"] : false; //seba

$where = ($localidad == '20' || $localidad == '23') ? "AND id <> 3" : "";

// Si el código es 2 indica que se encuentra en el clearing
if ($code == 2) {
    $response["message"] = "Persona no autorizada, consultar con Comercial";

    die(json_encode($response));
}

if ($code == 0) {
    // si todos los productos que tiene son tradicionales ofresco solo tradicionales,
    // de lo contrario ofresco todos
    $where .= ' AND id NOT IN (22, 31, 32, 33, 34, 35, 38, 39, 40, 41, 139)';
    $where .= " AND id IN (" . join(",", array_diff([1, 2, 3, 5, 4, 11, 29, 22, 30, 31, 32, 36, 42, 107, 109, 110, 111, 114, 117, 118, 119, 120, 121, 122, 123, 124, 129, 135, 138, 140, 142], $data)) . ")";
    $where .= count(array_filter($data, fn ($x) => in_array($x, [2, 3, 5]))) > 0 ? " AND id <> 1" : '';
} elseif ($code == 1) {
    if (count(array_filter($data, fn ($x) => in_array($x, [1, 2, 3, 5, 42, 107, 140]))) >= 1) { //si elige tradicionales
        $where .= ' AND id NOT IN (22, 30, 31, 32, 33, 34, 35, 38, 39, 40, 41, 1)';
    } else if (count(array_filter($data, fn ($x) => in_array($x, [11, 22, 30, 31, 32, 33, 34, 35, 38, 39, 40, 41, 125, 127]))) >= 1) {
        $where .= " AND id NOT IN (" . join(",", array_diff([1, 2, 5, 11, 22, 25, 27, 29, 36, 37, 42, 48, 30, 31, 32, 33, 34, 35, 38, 39, 40, 41, 125, 126, 127], $data)) . ")";
    } else {
        $where .= " AND id IN (" . join(",", array_diff([1, 11, 22, 30, 31, 32, 33, 34, 35, 38, 39, 40, 41, 107, 110, 111, 109, 114, 117, 118, 119, 120, 121, 122, 123, 124, 125, 127, 129, 140, 142], $data)) . ")";
    }
} elseif ($code == 4 || $code == 5) { //SOLO OFRECE
    $where .= " AND id IN (" . join(",", array_diff([11, 22, 25, 27, 30, 31, 32, 33, 34, 35, 38, 39, 40, 41, 107, 110, 111, 109, 114, 117, 118, 119, 120, 121, 122, 123, 124, 129, 140, 142], $data)) . ")";
} else {
    // Si es un producto tradicional no vuelve a listar el sanatorio
    $where .= count(array_filter($data, fn ($x) => in_array($x, [2, 3, 5]))) > 0 ? " AND id <> 1" : '';
}

$resultArray = count(array_filter($data, fn ($x) => in_array($x, [1, 18, 11, 8, 30, 31, 32, 12, 110, 35, 41, 46, 47, 23, 24, 25, 26, 27, 28, 114]))) == 0;
if ($resultArray) { //newproducts
    array_push($data, '117', '118', '119', '120', '121', '122', '124', '142');
}

if ($esGrupo) { //seba
    $where .= ' AND id NOT IN (130, 133, 134, 136, 139)';
}

$where .= " AND id NOT IN (" . join(",", $data) . ")";
$where .= " AND id NOT IN(130, 133, 134, 136)"; //seba PROMO INTERNADOS NUNCA VA EN INCREMENTOS NI CON NINGUN OTRO SERVICIO

if ($code != 0) $where .= " AND id NOT IN(135)"; //PRODUCTOS SOLO INCREMENTOS
if ($result = mysqli_query($mysqli, "SELECT * FROM servicios WHERE mostrar = 1 $where")) {
    $servicios = [];
    while ($row = mysqli_fetch_array($result)) {
        $servicios[] = array(
            'id' => $row['id'],
            'nro_servicio' => $row['nro_servicio'],
            'servicio' => $row['nombre_servicio'],
        );
    }
    $response = array('result' => true, 'message' => 'Exito', 'servicios' => $servicios);
}

mysqli_close($mysqli);
die(json_encode($response));
