<?php

include_once 'vendor/autoload.php';

use Monolog\Logger;
use Monolog\Handler\StreamHandler;


function Logger($nombre, $error, $tipo)
{
    $path = LOGS_DIR;
    $nombre .= '_' . date('Y-m-d') . '.log';
    $log = new Logger($error);
    $log->pushHandler(new StreamHandler("{$path}/{$nombre}"));
    $log->$tipo($error);
}



