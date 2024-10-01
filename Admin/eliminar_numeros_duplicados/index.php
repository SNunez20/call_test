<?php
include '../../_conexion.php';

$q = "SELECT numero FROM numeros_borrar";
$result = mysqli_query($mysqli, $q);

while ($row = mysqli_fetch_array($result)){
    $numero = $row['numero'];
    $q2 = "DELETE FROM numeros WHERE numero = '$numero' LIMIT 1";
    $result2 = mysqli_query($mysqli, $q2);
}