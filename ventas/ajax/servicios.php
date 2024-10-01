<?php

include_once './_init.php';

$productos = [];

$cin = $_POST['cin'];

$query = "SELECT   sum(hora) as horas, sum(importe) as importe,servicio
    FROM padron_producto_socio    
    WHERE  cedula='$cin' 
    GROUP BY servicio ORDER BY id ASC ";

$query = mysqli_query($mysqli, $query);

if (mysqli_num_rows($query) > 0) {
    while ($producto = mysqli_fetch_assoc($query)) {
        $servicio_query = mysqli_query($mysqli_motor, "SELECT * FROM servicios WHERE numero_servicio = '{$producto['servicio']}'");
        if (mysqli_num_rows($servicio_query) > 0) {
            $servicio = mysqli_fetch_assoc($servicio_query)['nombre'];
            if ($producto['servicio'] == '02') $horas = 'No tiene(Reintegro)';
            else $horas = (int) $producto['horas'];
        } else $servicio = '';

        $productos[] = [
            'Servicio' => $servicio,
            'Horas' => $horas,
            'Importe' => (int) $producto['importe'],

        ];
    }
}

die(json_encode($productos));
