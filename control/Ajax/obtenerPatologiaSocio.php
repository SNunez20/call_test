<?php

if (
  !isset($_POST['documentoSocio']) ||
  empty($_POST['documentoSocio'])
)
  die(json_encode([
    'error' => true,
    'params' => true
  ]));

require_once './../../_conexion.php';

$documentoSocio = $mysqli->real_escape_string($_POST['documentoSocio']);
$idPatologia = 1;
$observacion = '';

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
    $idPatologia = $row['id_patologia'];
    $observacion = $row['observacion'];
  }


die(json_encode([
  'error' => false,
  'documentoSocio' => $documentoSocio,
  'idPatologia' => $idPatologia,
  'observacion' => $observacion,
]));
