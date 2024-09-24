<?php


function VerificarToken($array_response_error = [])
{

    $nombre = array_key_exists('app-nombre_url', getallheaders()) ?  getallheaders()['app-nombre_url'] : '';

    $token = array_key_exists('app-token', getallheaders()) ? getallheaders()["app-token"] : '';

    $error = array_merge(['error' => true], $array_response_error);

    if (empty($nombre) || strlen($nombre) <= 2 || strlen($nombre) > 100)  die(json_encode($error));

    if (empty($token) || strlen($token) <= 2 || strlen($token) > 100)  die(json_encode($error));

    $conexion = conexion();

    $nombre = escapeString($nombre, $conexion);

    $token = escapeString($token, $conexion);

    $query = query($conexion, "SELECT id,email,nombre,cantidad,fecha,fecha_fin  FROM usuarios WHERE nombre='{$nombre}' AND token='{$token}' LIMIT 1");

    return mysqli_num_rows($query) > 0 ? $query : die(json_encode($error));
}
