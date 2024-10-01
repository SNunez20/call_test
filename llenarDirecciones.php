<?php

require_once "_conexion.php";


$qSocios = "SELECT id, cedula FROM padron_datos_socio WHERE direccion =''";
$rSocios = mysqli_query($mysqli,$qSocios);

if ($rSocios && mysqli_num_rows($rSocios) > 0) {
    
    while ($row = mysqli_fetch_assoc($rSocios)) {
        $id = $row['id'];
        $cedula = $row['cedula'];
        // echo $cedula;

        $qDireccion = "SELECT * FROM direcciones_socios WHERE cedula_socio = '$cedula'";
        $rDireccion = mysqli_query($mysqli,$qDireccion);
        if ($rDireccion && mysqli_num_rows($rDireccion) > 0) {
            while ($row2 = mysqli_fetch_assoc($rDireccion)) {
                $calle      = $row2['calle'];
                $puerta     = $row2['puerta'];
                $manzana    = $row2['manzana'];
                $solar      = $row2['solar'];
                $apartamento = $row2['apartamento'];
                $esquina    = $row2['esquina'];
                $referencia = $row2['referencia'];

                if ($puerta!='') { //dir2
                    $direccionNueva =  ($apartamento!='') ? substr($calle,0,14).' '.$puerta.'/'.$apartamento.' E:'.substr($esquina,0,6) : substr($calle,0,17).' '.$puerta.' E:'.substr($esquina,0,8);
                }else{
                    $direccionNueva =  ($apartamento!='') ? substr($calle,0,14).' M:'.$manzana.' S:'.$solar.'/'.$apartamento : substr($calle,0,14).' M:'.$manzana.' S:'.$solar.' E:'.substr($esquina,0,6);
                }

                // echo $direccionNueva;

                $qUpdateDir = "UPDATE padron_datos_socio SET  direccion = '$direccionNueva' WHERE id = $id";
                $rUpdateDir = mysqli_query($mysqli,$qUpdateDir);
                // echo mysqli_error($mysqli);

            }
        }

    }
    echo 'listo';
}

mysqli_close($mysqli);
