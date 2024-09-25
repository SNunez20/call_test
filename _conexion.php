<?php
require_once "logger.php";

$mysqli = mysqli_connect('localhost', 'root', '', 'call');

if (mysqli_connect_errno()) {
    logger("[ERROR]: Conexión 192.168.1.13" . mysqli_connect_error());
    die();
}
