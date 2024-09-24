<?php
require_once "logger.php";

$mysqli = mysqli_connect('localhost', 'root', '', 'call');

if (mysqli_connect_errno()) {
    logger("[ERROR]: Conexión localhost" . mysqli_connect_error());
    die();
}
