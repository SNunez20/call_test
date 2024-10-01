<?php
include('../_conexion.php');

$qVendedores = "SELECT cedula FROM eliminar_vendedores";
$rVendedores =  mysqli_query($mysqli,$qVendedores);

while($row = mysqli_fetch_array($rVendedores)){
    $cedula = $row['cedula'];
    $qU = "SELECT id FROM usuarios WHERE usuario = '$cedula'";
    $rU = mysqli_query($mysqli,$qU);

    while($row2 = mysqli_fetch_assoc($rU)){
        $usuario = $row2['id'];
    }

    $q = "update usuarios set activo = 2 where id = $usuario";
    $result = mysqli_query($mysqli,$q);
    if($result){
        $q2 = "update numeros as n inner join agendados as a on n.numero = a.numero set n.flag = 'libre', n.no_contesta = 0 where a.usuarioid = $usuario";
        $result2 = mysqli_query($mysqli,$q2);
        if($result2){
            $q3 = "update numeros as n inner join referidos as r on n.numero = r.numero set n.flag = 'libre', n.no_contesta = 0 where r.usuarioid = $usuario";
            $result3 = mysqli_query($mysqli,$q3);
            if($result3){
                $q4 = "delete from agendados where usuarioid = $usuario";
                $result4 = mysqli_query($mysqli,$q4);
                if($result4){
                    $q5 = "delete from referidos where usuarioid = $usuario";
                    $result5 = mysqli_query($mysqli,$q5);
                    if($result5){
                        $q6 = "update numeros inner join `session` on numeros.numero = `session`.numero set numeros.flag = 'libre' where `session`.idusuario = $usuario";
                        $result6 = mysqli_query($mysqli,$q6);
                        if($result6){
                            $q7 = "delete from `session` where idusuario = $usuario";
                            $result7 = mysqli_query($mysqli,$q7);
                            if($result7){
                                $q8 = "update numeros as n inner join referidoscuaderno as r on n.numero = r.numero set n.flag = 'libre', n.no_contesta = 0 where r.idusuario = $usuario";
                                $result8 = mysqli_query($mysqli,$q8);
                                if($result8){
                                    $q9 = "delete from referidoscuaderno where idusuario = $usuario";
                                    $result9 = mysqli_query($mysqli,$q9);
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}

mysqli_close($mysqli);
?>