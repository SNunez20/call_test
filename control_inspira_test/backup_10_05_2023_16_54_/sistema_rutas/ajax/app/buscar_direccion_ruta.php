<?php

include_once '../app.php';

$request_socio = $_POST['socio'];

if (!isset($request_socio) || empty($request_socio)) dieJson(true, 'El ID o cedula del socio no puede enviarse vacia');

$id_request_socio = (int) $request_socio;


$conexion_call = conexion(DB_CALL);

$request_socio = escapeString($request_socio, $conexion_call);

$direccion_socio = query(
    $conexion_call,
    "SELECT CONVERT(CAST(CONVERT(referencia USING latin1) AS BINARY) USING UTF8) AS referencia_utf8, direcciones_socios.*,padron_datos_socio.nombre  FROM direcciones_socios,padron_datos_socio 
    WHERE
        direcciones_socios.id_socio=padron_datos_socio.id
        AND direcciones_socios.cedula_socio='{$request_socio}'
        OR  direcciones_socios.id_socio=padron_datos_socio.id
        AND direcciones_socios.id_socio = $id_request_socio
        LIMIT 1
    "
);

if (mysqli_num_rows($direccion_socio) === 0) dieJson(true, "No se encontro ningún socio con el ID o  la Cédula ingresada ({$request_socio})");


$direccion_socio = mysqli_fetch_assoc($direccion_socio);

unset($direccion_socio['id'], $direccion_socio['id_socio']);

$conexion_call = conexion(DB_CALL);

$socio_call = query(
    $conexion_call,
    "SELECT CONVERT(CAST(CONVERT(ciudades.nombre USING latin1) AS BINARY) USING UTF8) AS localidad,direccion FROM ciudades,padron_datos_socio 
    WHERE 
        padron_datos_socio.cedula='{$direccion_socio['cedula_socio']}'
        AND padron_datos_socio.localidad = ciudades.id
    "
);

$socio_call = mysqli_num_rows($socio_call) > 0 ? mysqli_fetch_assoc($socio_call) : ['localidad' => ''];

$socio = array_merge($socio_call, $direccion_socio);

dieJson(false, '', $socio);
