<?php
set_time_limit(10000);

$mysqli = mysqli_connect('192.168.1.233', 'maria', 'maria', 'call');
if (mysqli_connect_errno()) {
    exit("Error al conectar a MySql: " . mysqli_connect_error());
}

// $prefijos = ['091', '092', '093', '094', '095', '096', '097', '098', '099'];
$prefijos = ['095', '096', '097', '098', '099'];

// $sql = "SELECT id, numero FROM numeros_nuevos ORDER BY id desc limit 1";
// $result = mysqli_query($mysqli, $sql);

# initial phone
// $tel = str_pad(0, 6, 0, STR_PAD_LEFT);
// $telef = $prefijos[0] . $tel;
// $cont = 0;

// if (mysqli_num_rows($result) == 0) {
//     mysqli_query($mysqli, "INSERT INTO numeros_nuevos (numero) VALUES ('$telef')");
//     $cont = 2;
// }

# toma el ultimo número
// $digitos = intval(substr($telef, -6));
// $digitos++;

$digitos=0;
$cont = 0;

$maxreg = 500000; #****MODIFICAR ESTA VARIABLE PARA CAMBIAR LA CANTIDAD DE REGISTROS POR CADA PREFIJO****
$inicio = microtime(true);
foreach ($prefijos as $p) {
 
    while ($cont <= $maxreg && $digitos <= 999999) {
        $tel = str_pad($digitos, 6, 0, STR_PAD_LEFT);
        $telef = $p . $tel;
       
        $insert = "INSERT INTO numeros_nuevos (numero) VALUES ('$telef')";
        mysqli_query($mysqli, $insert);
        $cont++;
        $digitos++;
    }

    $digitos = 0;
    $cont = 0;
}
$fin = microtime(true) - $inicio;
$total = count($prefijos) * $maxreg;
$query = "INSERT INTO tiempo_ejecucion_script (tiempo_ejecucion,cant_registros) VALUES ($fin,$total)";
mysqli_query($mysqli, $query);

echo "Tiempo de ejecución: $fin";