<?php

if (
  !isset(
    $_POST['documentoSocio'],
    $_POST['idPatologia'],
    $_POST['observacion']
  ) ||
  empty($_POST['documentoSocio']) ||
  empty($_POST['idPatologia']) ||
  empty($_POST['observacion'])
)
  die(json_encode([
    'error' => true,
    'params' => true
  ]));

require_once './../../_conexion.php';

$documentoSocio = $mysqli->real_escape_string($_POST['documentoSocio']);
$idPatologia = $mysqli->real_escape_string($_POST['idPatologia']);
$observacion = $mysqli->real_escape_string($_POST['observacion']);
$_idPatologia = null;
$_observacion = null;

$qSelect = <<<SQL
SELECT
  *
FROM
  `patologias_socio`
WHERE
  `documento_socio` = '{$documentoSocio}'
ORDER BY
  `id` DESC
LIMIT
  1
SQL;
$select = $mysqli->query($qSelect);

if ($select->num_rows > 0)
  while ($row = $select->fetch_assoc()) {
    $_idPatologia = $row['id_patologia'];
    $_observacion = $row['observacion'];
  }

if (is_null($_idPatologia) || ($_idPatologia == $idPatologia && $_observacion == $observacion))
  die(json_encode([
    'error' => false,
    'detalles' => 'no se agregó patología, duplica la última existente.'
  ]));



$qInsert = <<<SQL
INSERT INTO
  `patologias_socio`
  (`documento_socio`, `id_patologia`, `observacion`, `fecha`)
VALUES
  ("{$documentoSocio}", "{$idPatologia}", "{$observacion}", NOW())
SQL;
$insert = $mysqli->query($qInsert);

die(json_encode([
  'error' => !$insert,
  'detalles' => 'patología agregada con éxito.'
]));
