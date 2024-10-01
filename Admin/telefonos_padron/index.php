<?php
require '../../_conexion250.php';
require '../../_conexion.php';
$tiempo_inicio = microtime(true);

$q = "SELECT id, Tel FROM numeros_agregar WHERE id > 677848 LIMIT 50000";
$result = mysqli_query($mysqli, $q);

while($row = mysqli_fetch_array($result)){
    $id_telefono = $row['id'];
    $telefono = $row['Tel'];

    $q2 = "SELECT numero FROM numeros_padron WHERE numero = '$telefono'";
    $result2 = mysqli_query($mysqli, $q2);

    if(mysqli_num_rows($result2) != 0){
        $q3 = "UPDATE numeros_agregar SET En_padron = 1 WHERE id = $id_telefono";
        $result3 = mysqli_query($mysqli, $q3);
    }
}

/**$q2 = "SELECT tel FROM padron_datos_socio";
$result2 = mysqli_query($mysqli250, $q2);

while($row = mysqli_fetch_array($result2)){
    foreach(explode(" ", $row['tel']) as $telefono){
        if($telefono != '' && $telefono != 0){
            $q = "INSERT INTO numeros_padron VALUES(null, '$telefono')";
            $result = mysqli_query($mysqli, $q);
        }
    }
}*/



$tiempo_fin = microtime(true);
echo "<br><br><br>";
echo "Tiempo empleado: " . ($tiempo_fin - $tiempo_inicio);
echo "<br><br><br>";
echo "Ultimo id: " . $id_telefono;
echo "<br><br><br>";
echo "TERMINO";