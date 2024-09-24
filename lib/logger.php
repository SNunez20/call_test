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


