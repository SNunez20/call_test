<?php

function logger($message, $error = true)
{
    $filename = "error.";
    $folder  = "errors_bienvenida";
    if (!$error) {
        $filename = "info.";
        $folder  = "logs_bienvenida";
    }
    $filename = $filename . date("Y-m-d") . ".log";
    $date     = date("d/m/Y H:i:s");


    file_put_contents(__DIR__ . "/logs/$folder/$filename", "[$date] $message \n", FILE_APPEND);
}


function Logger_inspira($query_result, $crud, $tabla, $name_id, $param, $error, $query, $mensaje_referencia, $linea_log)
{
    $param = $param != "" ? $param : "";

    $texto_informativo = "{ \n";
    $texto_informativo .= $query_result ? "[ERROR]: \n" : "[OK]: \n";
    if ($crud != "") $texto_informativo .= mb_strtoupper($crud) . "\n";
    if ($tabla != "") $texto_informativo .= "[TABLE]: $tabla \n";
    if (!in_array($error, ["", false])) $texto_informativo .= "[DETAILS]: $error \n";
    if ($name_id != "") $texto_informativo .= "[" . mb_strtoupper($name_id) . "]: $param \n";
    if ($query != "") $texto_informativo .= "[QUERY]: $query \n";
    if ($mensaje_referencia != "") $texto_informativo .= "[MENSAJE_REFERENCIA]: " . mb_strtoupper($mensaje_referencia) . "\n";
    if ($linea_log != "") $texto_informativo .= "[LINEA_LOG]: $linea_log \n";
    $texto_informativo .= "} \n";

    $filename = $query_result ? "info." : "error.";
    $filename = $filename . date("Y-m-d") . ".log";
    $folder_filename = $query_result ? "logs_inspira/$filename" : "errors_inspira/$filename";

    $date     = date("d/m/Y H:i:s");
    file_put_contents(__DIR__ . "/logs/$folder_filename", "[$date] $texto_informativo \n", FILE_APPEND);
}
