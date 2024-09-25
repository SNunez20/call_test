<?php
require_once "logger.php";

$mysqli1310 = mysqli_connect('localhost', 'root', '', 'motor_de_precios_inspira');

if (mysqli_connect_errno()) {
    logger("[ERROR]: Conexión 192.168.13.10" . mysqli_connect_error());
    die();
}
