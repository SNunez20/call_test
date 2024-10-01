<?php
include '../../../_conexion.php';
include '../funciones.php';
header('Content-Type: text/html; charset=UTF-8');


$id = $_REQUEST['id'];
$campo_prestamo = $_REQUEST['campo_prestamo'];
$monto_prestamo = $_REQUEST['monto_prestamo'];
$motivo_rechazo = $_REQUEST['motivo_rechazo'];
$fecha_accion = $_REQUEST['fecha_accion'];
$fecha_actual = date("Y-m-d");

if ($id == "" || $campo_prestamo == "" || $fecha_accion == "") devolver_error("Campos vacíos");
if ($campo_prestamo == 1 && $monto_prestamo == "") devolver_error("Debe ingresar el monto del préstamo");
if ($campo_prestamo == 2 && $motivo_rechazo == "") devolver_error("Debe ingresar el motivo del rechazo");
if ($campo_prestamo < 1 || $campo_prestamo > 2) devolver_error("Debe seleccionar una opción válida en el desplegable Campo Prestamo");
if ($campo_prestamo == 1 && !is_numeric($monto_prestamo)) devolver_error("El monto del préstamo debe de ser un valor numérico");
if ($fecha_actual > $fecha_accion) devolver_error("La fecha de acción no puede ser menor a la fecha actual");


/** Cargo el registro en la tabla **/
$dejar_registro = cargar_registro($id, $monto_prestamo, $motivo_rechazo, $fecha_accion);
if ($dejar_registro === false) devolver_error("Ocurrieron errores al registrar");

/** Retiro el registro de pendientes **/
$retirar_pendiente = retirar_pendiente($id);
if ($dejar_registro === false) devolver_error("Ocurrieron errores al retirar de pendiente");



$response['error'] = false;
$response['mensaje'] = "Se cargo el registro con éxito";

echo json_encode($response);




function cargar_registro($id, $monto_prestamo, $motivo_rechazo, $fecha_accion)
{
    global $mysqli;
    $mysqli->set_charset('utf8mb4');

    if ($motivo_rechazo != "") $motivo_rechazo = mysqli_real_escape_string($mysqli, $motivo_rechazo);

    $sql = $monto_prestamo != "" ?
        "INSERT INTO registros_crediv (id_crediv, monto_prestamo, fecha_accion, estado) VALUES ('$id', '$monto_prestamo', '$fecha_accion', 1)" :
        "INSERT INTO registros_crediv (id_crediv, motivo_rechazo, fecha_accion, estado) VALUES ('$id', '$motivo_rechazo', '$fecha_accion', 0)";
    $consulta = mysqli_query($mysqli, $sql);

    return $consulta;
}

function retirar_pendiente($id)
{
    global $mysqli;

    $sql = "UPDATE crediv SET pendiente = 0 WHERE id = '$id'";
    $consulta = mysqli_query($mysqli, $sql);

    return $consulta;
}
