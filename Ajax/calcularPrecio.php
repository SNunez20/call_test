<?php
session_start();

if (isset($_SESSION['idusuario'])) {
    $data             = array_map('stripslashes', $_POST);
    $response         = array('result' => false, 'message' => 'Intentelo nuevamente mas tarde!!');
    $servicio         = $data['servicio'];
    $hrs_sanatorio    = $data['hrs_sanatorio'];
    $localidad        = $data['localidad'];
    $fecha_nacimiento = $data['fecha_nacimiento'];
    $base             = $data['base'] == 'false' ? false : true;
    $hrs_servicio     = $data['hrs_servicio'];

    if ($hrs_sanatorio == "null" || $hrs_sanatorio == "" || $hrs_sanatorio == 0) {
        $hrs_sanatorio = false;
    }
    if ($hrs_servicio == "null" || $hrs_servicio == "" || $hrs_servicio == 0) {
        $hrs_servicio = 8;
    }

    $getdata = http_build_query(
        array(
            'consulta'               => 'calcularTotal',
            'idFilial'               => $localidad,
            'fechaNacimiento'        => $fecha_nacimiento,
            'idServicio'             => $servicio,
            'cantidadHoras'          => $hrs_servicio,
            'cantidadHorasSanatorio' => $hrs_sanatorio,
            'socio'                  => false,
            'calcular_con_base'      => $base,
        )
    );

    $opts = array(
        'http' => array(
            'header'  => "Content-Type: application/x-www-form-urlencoded\r\n" .
                "Content-Length: " . strlen($getdata) . "\r\n" .
                "User-Agent:MyAgent/1.0\r\n",
            'method'  => 'GET',
            'content' => $getdata,
        ),
    );

    $context   = stream_context_create($opts);
    $resultado = file_get_contents(
        'https://vida-apps.com/motorDePrecios_new/PHP/clases/Precios.php?' . $getdata,
        false,
        $context
    );
    $resultado = json_decode($resultado);

    if (!$resultado->error) {
        $response = array(
            'result'            => true,
            'precio'            => $resultado->precio,
            'tiene_precio_base' => $resultado->tiene_precio_base,
            'precio_base'       => $resultado->precio_base,
            'es_sanatorio'      => $resultado->es_sanatorio,
            'precio_sanatorio'  => $resultado->precio_sanatorio,
            'precio_servicio'   => $resultado->precio_servicio,
        );
    }
} else {
    $response = array('result' => false, 'message' => 'Sin sesión');
}

echo json_encode($response);
