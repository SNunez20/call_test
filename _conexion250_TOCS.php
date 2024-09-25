<?php
require_once "logger.php";

$mysqli250_TOCS = mysqli_connect('localhost', 'root', '', 'terminos_y_condiciones');

if (mysqli_connect_errno()) {
    logger("[ERROR]: Conexión 192.168.1.250" . mysqli_connect_error());
    die();
}
