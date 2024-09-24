<?php



function conexion($db )
{
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    date_default_timezone_set('America/Argentina/Buenos_Aires');
    try {
        $conexion = mysqli_connect($db["host"], $db["user"],  $db["password"], $db["db"]);
        mysqli_set_charset($conexion, "utf8");
    } catch (mysqli_sql_exception $e) {
        echo $e->getMessage();
        logDB('conexion', 'Error de conexion a la BD', $e);
    }
    return $conexion;
}

function escapeString($fields, $conexion)
{
    if (is_array($fields)) {
        foreach ($fields as $key => $field) {
            $fields[$key] = is_numeric($fields[$key]) ? $fields[$key] : mysqli_real_escape_string($conexion, trim($fields[$key]));
        }
    } else mysqli_real_escape_string($conexion, trim($fields));
    return $fields;
}

function query($conexion, $query, $nombre_mod = false)
{
    try {
        return mysqli_query($conexion, $query);
    } catch (Exception $e) {
        $error = debug_backtrace();
        $nombre = isset($error[0]['file']) ? $error[0]['file'] : '';
        $linea = isset($error[0]['line'])  ? $error[0]['line'] : '';
        $err = isset($error[0]['args'][0]) ? $error[0]['args'][0]->error : 'Error desconocido';
        $e = [
            'linea' => $linea,
            'error' => $err,
        ];
        LogDB($nombre, $query, $e);
        return false;
    }
}

function WhileFetchAssoc($consulta)
{
    $resultado = [];
    if (mysqli_num_rows($consulta) > 0) {
        while ($r = mysqli_fetch_assoc($consulta)) {
            $resultado[] =  $r;
        }
    }
    return $resultado;
}

function LogDB($nombre, $consulta, $e)
{
    Logger('', " Archivo: {$nombre} | Error : {$e['error']} | Consulta: {$consulta}    ", "error");
}
