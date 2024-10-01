<?php
include('_conexionDesafio.php');

$q = "select * from jugadores_referidos where cast(fecha as date) = '2018-08-30' or cast(fecha as date) = '2018-08-31' order by idusuario_call";
$result = mysqli_query($mysqli,$q);
while($row = mysqli_fetch_array($result)){
    $referidos[]= $row['idusuario_call'];
}
//var_dump($referidos);
mysqli_close($mysqli);
include('_conexion.php');
$q = "select usuarios.id from usuarios inner join gruposusuarios on usuarios.idgrupo = gruposusuarios.id where gruposusuarios.id = 2 or gruposusuarios.id = 9 or gruposusuarios.id = 13";
$result = mysqli_query($mysqli,$q);
while($row = mysqli_fetch_array($result)){
    $usuarios_call[] = $row['id'];
}
//var_dump($usuarios_call);
//$z = array_intersect_key($referidos,$usuarios_call);
//var_dump($z);
$total = 0;
for($i = 0; $i < count($referidos); $i++){
    for($e = 0; $e < count($usuarios_call); $e++){
        if($referidos[$i] == $usuarios_call[$e]){
            $total = $total + 1;
        }
    }
}
echo $total;
?>