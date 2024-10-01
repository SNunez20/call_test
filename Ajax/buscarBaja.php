<?php
session_start();



if (
  !isset($_SESSION['idusuario']) &&
  (
    !isset($_POST['ptbbddvp']) ||
    $_POST['ptbbddvp'] !== 'true'
  )
)
  die(json_encode([
    'result' => false,
    'message' => 'Sin Sesion'
  ]));

require_once __DIR__ . '/../_conexion250.php';
require_once __DIR__ . '/../_conexion.php';
global $mysqli;
global $mysqli250;

$response = [
  'result' => false,
  'message' => 'Intentelo nuevamente mas tarde !!'
];

$data = array_map('stripslashes', $_POST);

$cedula = $data['cedula'];
date_default_timezone_set('America/Argentina/Buenos_Aires');
$fecha = date("Y-m-d H:i:s");
$tRojo = "No autorizado, consultar con Comercial";
$tRojo2 = "Figura en padrón.";
$tRojo3 = "¡¡ SOCIO INSPIRA !!";
$tVerdeClaro = "A esta persona se le puede vender SOLO productos acotados con cualquier medio de pago.";
$tVerdeClaro2 = "A esta persona se le puede vender SOLO productos acotados SOLO con tarjeta de crédito.";
$tVerde = "A esta persona se le puede vender con TODO (tradicional) o productos acotados con cualquier medio de pago.";
$meses = "none";

$q = <<<SQL
SELECT
  *
FROM
  `padron_datos_socio`
WHERE
  `cedula` = '{$cedula}';
SQL;
$result = $mysqli250->query($q);

/**
 * 0 - Figura en padrón
 * 1 - No es socio ni esta en baja
 * 2 - Clearing
 * 3 - Todos los productos con cualquier medio de pago
 * 4 - Solo productos acotados con culaquier medio de pago
 * 5 - Solo productos actotados SOLO con tarjeta de crédito
 */
$code = 0;
$vuelve_antes = false;
$noPuedeIncrementar = false;

//ACA SI ESTA EN PADRON
if ($result->num_rows > 0) {
  $row = $result->fetch_assoc();
  $modificado = count(consultarTablaBajas($cedula)) !== 0;
  $sucursal = $row['sucursal'];
  $color = "rojo_claro";
  $color_code = "#edb9b9";
  $font_color = "black";
  $texto = (
    $sucursal == 1372 ||
    $sucursal == 1373 ||
    $sucursal == 1374
  )
    ? $tRojo3
    : $tRojo2;

  if ($modificado) {
    $noPuedeIncrementar = true;
    $texto = 'Esta persona no puede incrementar.<br/>Comunicarse con Comercial.';
  }
} else {
  $baja = consultarTablaBajas($cedula);

  if (count($baja) > 0) {
      $fecha_baja = $baja['fecha_baja'];
      $tipo_producto = $baja['tipo_producto'];
      $clearing = $baja['clearing'];
      $count = $baja['count'];

    if ($clearing == 1) {
      $color = "rojo";
      $color_code = "red";
      $font_color = "white";
      $texto = $tRojo;
      $code = 2;
    } else {
      $fechaBaja = new DateTime($fecha_baja);
      $fechaHoy = new DateTime($fecha);
      $interval = $fechaHoy->diff($fechaBaja);
      $meses = ($interval->y * 12) + $interval->m;

      if ($meses >= 7 || ($count >= 36 && $meses >= 3)) {
        //ACA SI ESTA EN BAJA PERO YA PASO LAS 7 EMISIONES
        $color = "verde";
        $color_code = "green";
        $font_color = "white";
        $texto = $tVerde;
        $code = 3;
        $vuelve_antes = ($meses < 7) ? true : false;
      } elseif ($tipo_producto == "T") {
        //ACA SI ES BAJA HACE MENOS DE 7 EMISIONES DE UN PRODUCTO TRADICIONAL
        $color = "verde_claro";
        $color_code = "#9FF781";
        $font_color = "black";
        $texto = $tVerdeClaro;
        $code = 4;
      } elseif ($tipo_producto == "A") {
        //ACA SI ES BAJA HACE MENOS DE 7 EMISIONES DE UN PRODUCTO ACOTADO
        $color = "verde_claro2";
        $color_code = "#cef5c1";
        $font_color = "black";
        $texto = $tVerdeClaro2;
        $code = 5;
      }
    }
  } else {
    //ACA SI NO ES SOCIO NI ESTA DE BAJA
    $color = "verde";
    $color_code = "green";
    $font_color = "white";
    $texto = $tVerde;
    $code = 1;
  }
}

if ($color != "") {
  $response = array(
    'result' => true,
    'message' => 'Correcto',
    'meses' => $meses,
    'color' => $color,
    'color_code' => $color_code,
    'font_color' => $font_color,
    'texto' => $texto,
    "code" => $code,
    "vuelve_antes" => $vuelve_antes,
    "no_puede_incrementar" => $noPuedeIncrementar
  );
}


die(json_encode($response));


/**
 * Consulta si la cédula se encuentra en la tabla bajas en el 1.13
 *
 * @param string $cedula Cédula a buscar
 * @return array
 */
function consultarTablaBajas($cedula) {
  global $mysqli;

  $qSelect = <<<SQL
  SELECT
    `fecha_baja` ,`tipo_producto`, `clearing`, `count`
  FROM
    `bajas`
  WHERE
    `cedula` = '{$cedula}'
  SQL;
  $query = $mysqli->query($qSelect);

  return $query->num_rows !== 0
  ? $query->fetch_assoc()
  : [];
}