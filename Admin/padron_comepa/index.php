<?php
include '../../_conexion.php';

$q = "SELECT * FROM padron_comepa WHERE celular like '09%' ORDER BY RAND()";
$result = mysqli_query($mysqli, $q);

while($row = mysqli_fetch_array($result)){
    $numero = $row['Celular'];
    $grupo = 'COM';
    $flag = 'libre';
    $no_contesta = 0;
    $dep_localidad = $row['Apellidos_nombres'];
    $qInsert = "INSERT INTO numeros (numero, grupo, flag, no_contesta, dep_localidad) VALUES('$numero', '$grupo', '$flag', $no_contesta, '$dep_localidad')";
    $rInsert = mysqli_query($mysqli, $qInsert);
}

$q2 = "SELECT * FROM padron_comepa WHERE ISNULL(Celular) AND !ISNULL(Telefono) ORDER BY RAND()";
$result2 = mysqli_query($mysqli, $q2);

while($row2 = mysqli_fetch_array($result2)){
    $numero = $row2['Telefono'];
    $grupo = 'COM';
    $flag = 'libre';
    $no_contesta = 0;
    $dep_localidad = $row2['Apellidos_nombres'];
    $qInsert2 = "INSERT INTO numeros (numero, grupo, flag, no_contesta, dep_localidad) VALUES('$numero', '$grupo', '$flag', $no_contesta, '$dep_localidad')";
    $rInsert2 = mysqli_query($mysqli, $qInsert2);
}