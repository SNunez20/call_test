<?php

if (!isset($_POST['documentoSocio']) || empty($_POST['documentoSocio']))
  die(json_encode(['error' => true, 'params' => true]));

require_once '../../_conexion.php';

$documentoSocio = $mysqli->real_escape_string($_POST['documentoSocio']);
$idPatologia = 1;
$observacion = '';

$qSelect = "SELECT * FROM patologias_socio WHERE documento_socio  = '{$documentoSocio}' ORDER BY id DESC LIMIT 1";
$select = mysqli_query($mysqli, $qSelect);

if (mysqli_num_rows($select) > 0)
  while ($row = mysqli_fetch_assoc($select)) {
    $idPatologia = $row['id_patologia'];
    $observacion = $row['observacion'];
  }


die(json_encode([
  'error'          => false,
  'documentoSocio' => $documentoSocio,
  'idPatologia'    => $idPatologia,
  'observacion'    => $observacion,
]));
