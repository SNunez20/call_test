<?php
$mysqli = mysqli_connect('localhost', 'root', 'sist.2k8','call');

if (mysqli_connect_errno())
  {
    echo "Error al conectar a MySql: " . mysqli_connect_error();
  }
  
$q = "select * from numeros where flag = 'libre' and grupo = 'A'";
$result = mysqli_query($mysqli,$q);
$A = mysqli_num_rows($result);
if($A == 0){
    $q = "delete from relacion where idgrupotel = 'A'";
    $result = mysqli_query($mysqli,$q);
}

$q2 = "select * from numeros where flag = 'libre' and grupo = 'B'";
$result2 = mysqli_query($mysqli,$q2);
$B = mysqli_num_rows($result2);
if($B == 0){
    $q2 = "delete from relacion where idgrupotel = 'B'";
    $result2 = mysqli_query($mysqli,$q2);
}

$q3 = "select * from numeros where flag = 'libre' and grupo = 'C'";
$result3 = mysqli_query($mysqli,$q3);
$C = mysqli_num_rows($result3);
if($C == 0){
    $q3 = "delete from relacion where idgrupotel = 'C'";
    $result3 = mysqli_query($mysqli,$q3);
}

$q4 = "select * from numeros where flag = 'libre' and grupo = 'D'";
$result4 = mysqli_query($mysqli,$q4);
$D = mysqli_num_rows($result4);
if($D == 0){
    $q4 = "delete from relacion where idgrupotel = 'D'";
    $result4 = mysqli_query($mysqli,$q4);
}

$q5 = "select * from numeros where flag = 'libre' and grupo = 'E'";
$result5 = mysqli_query($mysqli,$q5);
$E = mysqli_num_rows($result5);
if($E == 0){
    $q5 = "delete from relacion where idgrupotel = 'E'";
    $result5 = mysqli_query($mysqli,$q5);
}

$q6 = "select * from numeros where flag = 'libre' and grupo = 'F'";
$result6 = mysqli_query($mysqli,$q6);
$F = mysqli_num_rows($result6);
if($F == 0){
    $q6 = "delete from relacion where idgrupotel = 'F'";
    $result6 = mysqli_query($mysqli,$q6);
}

$q7 = "select * from numeros where flag = 'libre' and grupo = 'G'";
$result7 = mysqli_query($mysqli,$q7);
$G = mysqli_num_rows($result7);
if($G == 0){
    $q7 = "delete from relacion where idgrupotel = 'G'";
    $result7 = mysqli_query($mysqli,$q7);
}

$q8 = "select * from numeros where flag = 'libre' and grupo = 'H'";
$result8 = mysqli_query($mysqli,$q8);
$H = mysqli_num_rows($result8);
if($H == 0){
    $q8 = "delete from relacion where idgrupotel = 'H'";
    $result8 = mysqli_query($mysqli,$q8);
}

$q9 = "select * from numeros where flag = 'libre' and grupo = 'I'";
$result9 = mysqli_query($mysqli,$q9);
$I = mysqli_num_rows($result9);
if($I == 0){
    $q9 = "delete from relacion where idgrupotel = 'I'";
    $result9 = mysqli_query($mysqli,$q9);
}

$q10 = "select * from numeros where flag = 'libre' and grupo = 'J'";
$result10 = mysqli_query($mysqli,$q10);
$J = mysqli_num_rows($result10);
if($J == 0){
    $q10 = "delete from relacion where idgrupotel = 'J'";
    $result10 = mysqli_query($mysqli,$q10);
}

?>