<?php
require '../../_conexion.php';
$res= array('result' => false,'filiales' => array());

$qFiliales = "SELECT id, nro_filial, nombre_filial  FROM filiales WHERE mostrar='1'";

$rFiliales = mysqli_query($mysqli, $qFiliales);

if ($rFiliales->num_rows !== 0) {
    $res['result'] =true;
    while ($row = mysqli_fetch_array($rFiliales)) {
        $res['filiales'][]= array('id' => $row['id'], 'nroFilial' =>$row['nro_filial'], 'nombreFilial' => $row['nombre_filial']);
    }
    $res['filiales'][]= array('id' => 0, 'nroFilial' => '00', 'nombreFilial' => 'Todas');
} 

mysqli_close($mysqli);
echo json_encode($res);