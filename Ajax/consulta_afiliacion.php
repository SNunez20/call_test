<?php
require_once './../_conexion250.php';
require_once './../_conexion.php';

$response = ['error' => true, 'mensaje' => 'Ocurrió un error, intente mas tarde.'];

if (empty($_POST['codigo_afiliacion']))
  die(json_encode($response));

$codigo = $_POST['codigo_afiliacion'];

$q = "SELECT
            pd2.cedula,
            pd2.nombre,
            pd2.total_importe
        FROM
            padron_datos_socio_piscina AS pd1
        INNER JOIN padron_datos_socio AS pd2 ON pd1.cedula = pd2.cedula
        WHERE
            CONCAT( pd1.cedula, '-', pd1.id ) = '$codigo'";
$result = mysqli_query($mysqli, $q);

if (mysqli_num_rows($result) == 0) {
  $q = "SELECT cedula, nombre, total_importe FROM padron_datos_socio WHERE CONCAT(cedula, '-' , id) = '$codigo'";
  $result = mysqli_query($mysqli, $q);
  if (mysqli_num_rows($result) == 0) {
    $response['mensaje'] = 'No se encontró su código de afiliación, o su afiliación se encuentra aún en proceso';
    die(json_encode($response));
  }
}

$row = mysqli_fetch_assoc($result);
$cedula = $row['cedula'];
$nombre = $row['nombre'];
$total_importe = $row['total_importe'] . ' Gs';

$socio = ['cedula' => $cedula, 'nombre' => $nombre, 'total_importe' => $total_importe, 'productos' => []];

$qProd = "SELECT
                s.nombre AS servicio,
                if(s.tiene_modulos, count(pp.id) * 8, '-') AS horas
            FROM
                padron_producto_socio AS pp
            INNER JOIN motor_precios_py.servicios AS s ON pp.servicio = s.numero_servicio 
            WHERE
                pp.cedula = '$cedula' AND
                s.nombre != 'Grupo Familiar'
            GROUP BY 
                pp.servicio";
$rProd = mysqli_query($mysqli, $qProd);

if (mysqli_num_rows($rProd) == 0) die(json_encode($response));

while ($row = mysqli_fetch_array($rProd)) {
  $servicio = $row['servicio'];
  $horas = $row['horas'] != '-' ? $row['horas'] . ' hs' : $row['horas'];

  $socio['productos'][] = ['servicio' => $servicio, 'horas' => $horas];
}

$response['error'] = false;
$response['mensaje'] = '';
$response['socio'] = $socio;

mysqli_close($mysqli);
echo json_encode($response);


function nombreServicio($nroServicio)
{
  global $mysqli;

  $qSelect = <<<SQL
  SELECT
    `nombre_servicio`
  FROM
    `servicios`
  WHERE
    `nro_servicio` = '{$nroServicio}'
SQL;
  $select = $mysqli->query($qSelect);

  return ($select->num_rows > 0)
    ? $select->fetch_assoc()['nombre_servicio']
    : 'desconocido';
}
