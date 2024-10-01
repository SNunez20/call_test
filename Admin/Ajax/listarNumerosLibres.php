<?php
include('../../_conexion.php');
session_start();
$response 	= array( 'result' => false, 'message' => 'Intentelo nuevamente mas tarde !!');

if(isset($_SESSION['idadmin'])){
    $q = "select * from numeros where flag = 'libre' and grupo = 'A'";
    $result = mysqli_query($mysqli,$q);
    $A = mysqli_num_rows($result);
    $q2 = "select * from numeros where flag = 'libre' and grupo = 'B'";
    $result2 = mysqli_query($mysqli,$q2);
    $B = mysqli_num_rows($result2);
    $q3 = "select * from numeros where flag = 'libre' and grupo = 'C'";
    $result3 = mysqli_query($mysqli,$q3);
    $C = mysqli_num_rows($result3);
    $q4 = "select * from numeros where flag = 'libre' and grupo = 'D'";
    $result4 = mysqli_query($mysqli,$q4);
    $D = mysqli_num_rows($result4);
    $q5 = "select * from numeros where flag = 'libre' and grupo = 'E'";
    $result5 = mysqli_query($mysqli,$q5);
    $E = mysqli_num_rows($result5);
    $q6 = "select * from numeros where flag = 'libre' and grupo = 'F'";
    $result6 = mysqli_query($mysqli,$q6);
    $F = mysqli_num_rows($result6);
    $q7 = "select * from numeros where flag = 'libre' and grupo = 'G'";
    $result7 = mysqli_query($mysqli,$q7);
    $G = mysqli_num_rows($result7);
    $q8 = "select * from numeros where flag = 'libre' and grupo = 'H'";
    $result8 = mysqli_query($mysqli,$q8);
    $H = mysqli_num_rows($result8);
    $q9 = "select * from numeros where flag = 'libre' and grupo = 'I'";
    $result9 = mysqli_query($mysqli,$q9);
    $I = mysqli_num_rows($result9);
    $q10 = "select * from numeros where flag = 'libre' and grupo = 'J'";
    $result10 = mysqli_query($mysqli,$q10);
    $J = mysqli_num_rows($result10);
    $q11 = "select * from numeros where flag = 'libre' and grupo = 'K'";
    $result11 = mysqli_query($mysqli,$q11);
    $K = mysqli_num_rows($result11);
    $q12 = "select * from numeros where flag = 'libre' and grupo = 'L'";
    $result12 = mysqli_query($mysqli,$q12);
    $L = mysqli_num_rows($result12);
    $q13 = "select * from numeros where flag = 'libre' and grupo = 'M'";
    $result13 = mysqli_query($mysqli,$q13);
    $M = mysqli_num_rows($result13);
    $q14 = "select * from numeros where flag = 'libre' and grupo = 'N'";
    $result14 = mysqli_query($mysqli,$q14);
    $N = mysqli_num_rows($result14);
    $q15 = "select * from numeros where flag = 'libre' and grupo = 'O'";
    $result15 = mysqli_query($mysqli,$q15);
    $O = mysqli_num_rows($result15);

    $q16 = "select * from numeros where flag = 'libre' and grupo = 'P'";
    $result16 = mysqli_query($mysqli,$q16);
    $P = mysqli_num_rows($result16);
    $q17 = "select * from numeros where flag = 'libre' and grupo = 'Q'";
    $result17 = mysqli_query($mysqli,$q17);
    $Q = mysqli_num_rows($result17);
    $q18 = "select * from numeros where flag = 'libre' and grupo = 'R'";
    $result18 = mysqli_query($mysqli,$q18);
    $R = mysqli_num_rows($result18);
    $q19 = "select * from numeros where flag = 'libre' and grupo = 'S'";
    $result19 = mysqli_query($mysqli,$q19);
    $S = mysqli_num_rows($result19);
    $q20 = "select * from numeros where flag = 'libre' and grupo = 'T'";
    $result20 = mysqli_query($mysqli,$q20);
    $T = mysqli_num_rows($result20);
    $q21 = "select * from numeros where flag = 'libre' and grupo = 'U'";
    $result21 = mysqli_query($mysqli,$q21);
    $U = mysqli_num_rows($result21);

    $response 	= array( 'result' => true, 'A' => $A, 'B' => $B, 'C' => $C, 'D' => $D, 'E' => $E, 'F' => $F, 'G' => $G, 'H' => $H, 'I' => $I, 'J' => $J, 'K' => $K,'L' => $L,'M' => $M, 'N' => $N, 'O' => $O, 'P' => $P, 'Q' => $Q, 'R' => $R, 'S' => $S, 'T' => $T, 'U' => $U);
    
}else{
    $response 	= array( 'result' => false, 'message' => 'Sin Sesion'); 
}

mysqli_close($mysqli);
echo json_encode( $response );
?>